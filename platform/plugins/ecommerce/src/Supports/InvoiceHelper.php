<?php

namespace Botble\Ecommerce\Supports;

use Barryvdh\DomPDF\PDF as PDFHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Pdf;
use Botble\Ecommerce\Enums\InvoiceStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper as EcommerceHelperFacade;
use Botble\Ecommerce\Models\Invoice;
use Botble\Ecommerce\Models\InvoiceItem;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InvoiceHelper
{
    public function store(Order $order)
    {
        if ($order->invoice()->exists()) {
            return $order->invoice()->first();
        }

        $address = $order->shippingAddress;

        if (EcommerceHelperFacade::isBillingAddressEnabled() && $order->billingAddress->id) {
            $address = $order->billingAddress;
        }

        $taxInformation = EcommerceHelperFacade::isDisplayTaxFieldsAtCheckoutPage() ? $order->taxInformation()->first() : null;

        $invoiceData = [
            'reference_id' => $order->getKey(),
            'reference_type' => Order::class,
            'customer_name' => $taxInformation ? $taxInformation->company_name : ($address->name ?: $order->user->name),
            'company_name' => '',
            'company_logo' => null,
            'customer_email' => $taxInformation ? $taxInformation->company_email : ($address->email ?: $order->user->email),
            'customer_phone' => $taxInformation ? $taxInformation->company_phone : $address->phone,
            'customer_address' => $taxInformation ? $taxInformation->company_address : $address->full_address,
            'customer_tax_id' => $taxInformation?->company_tax_code,
            'payment_id' => null,
            'status' => InvoiceStatusEnum::COMPLETED,
            'paid_at' => Carbon::now(),
            'tax_amount' => $order->tax_amount,
            'shipping_amount' => $order->shipping_amount,
            'discount_amount' => $order->discount_amount,
            'sub_total' => $order->sub_total,
            'amount' => $order->amount,
            'shipping_method' => $order->shipping_method,
            'shipping_option' => $order->shipping_option,
            'coupon_code' => $order->coupon_code,
            'discount_description' => $order->discount_description,
            'description' => $order->description,
        ];

        if (is_plugin_active('payment')) {
            $invoiceData = array_merge($invoiceData, [
                'payment_id' => $order->payment->id,
                'status' => $order->payment->status,
                'paid_at' => $order->payment->status == PaymentStatusEnum::COMPLETED ? Carbon::now() : null,
            ]);
        }

        $invoice = new Invoice($invoiceData);

        $invoice->created_at = $order->created_at;

        $invoice->save();

        foreach ($order->products as $orderProduct) {
            $invoice->items()->create([
                'reference_id' => $orderProduct->product_id,
                'reference_type' => Product::class,
                'name' => $orderProduct->product_name,
                'description' => null,
                'image' => $orderProduct->product_image,
                'qty' => $orderProduct->qty,
                'price' => $orderProduct->price,
                'sub_total' => $orderProduct->price * $orderProduct->qty,
                'tax_amount' => $orderProduct->tax_amount,
                'discount_amount' => 0,
                'amount' => $orderProduct->price * $orderProduct->qty + $orderProduct->tax_amount,
                'options' => array_merge(
                    $orderProduct->options,
                    $orderProduct->product_options_implode ? [
                        'product_options' => $orderProduct->product_options_implode,
                    ] : [],
                    $orderProduct->license_code ? [
                        'license_code' => $orderProduct->license_code,
                    ] : [],
                ),
            ]);
        }

        do_action(INVOICE_PAYMENT_CREATED, $invoice);

        return $invoice;
    }

    public function makeInvoicePDF(Invoice $invoice): PDFHelper
    {
        return (new Pdf())
            ->templatePath($this->getInvoiceTemplatePath())
            ->destinationPath($this->getInvoiceTemplateCustomizedPath())
            ->supportLanguage($this->getLanguageSupport())
            ->paperSizeA4()
            ->data($this->getDataForInvoiceTemplate($invoice))
            ->twigExtensions([
                new TwigExtension(),
            ])
            ->compile();
    }

    public function generateInvoice(Invoice $invoice): string
    {
        $storageDisk = Storage::disk('local');

        $invoiceFile = sprintf('ecommerce/invoices/invoice-%s.pdf', $invoice->code);

        $invoicePath = $storageDisk->path($invoiceFile);

        if ($storageDisk->exists($invoiceFile)) {
            return $invoicePath;
        }

        File::ensureDirectoryExists(dirname($invoicePath));

        $this->makeInvoicePDF($invoice)->save($invoicePath);

        return $invoicePath;
    }

    public function downloadInvoice(Invoice $invoice): Response
    {
        return $this->makeInvoicePDF($invoice)->download(sprintf('invoice-%s.pdf', $invoice->code));
    }

    public function streamInvoice(Invoice $invoice): Response
    {
        return $this->makeInvoicePDF($invoice)->stream();
    }

    public function getInvoiceTemplate(): string
    {
        return (new Pdf())->getContent($this->getInvoiceTemplatePath(), $this->getInvoiceTemplateCustomizedPath());
    }

    public function getInvoiceTemplatePath(): string
    {
        return plugin_path('ecommerce/resources/templates/invoice.tpl');
    }

    public function getInvoiceTemplateCustomizedPath(): string
    {
        return storage_path('app/templates/ecommerce/invoice.tpl');
    }

    protected function getDataForInvoiceTemplate(Invoice $invoice): array
    {
        $logo = get_ecommerce_setting('company_logo_for_invoicing') ?: (theme_option(
            'logo_in_invoices'
        ) ?: theme_option('logo'));

        $paymentDescription = null;

        if (
            is_plugin_active('payment') &&
            $invoice->payment->payment_channel == PaymentMethodEnum::BANK_TRANSFER &&
            $invoice->payment->status == PaymentStatusEnum::PENDING
        ) {
            $paymentDescription = BaseHelper::clean(
                get_payment_setting('description', $invoice->payment->payment_channel)
            );
        }

        $companyName = get_ecommerce_setting('company_name_for_invoicing') ?: get_ecommerce_setting('store_name');

        $companyAddress = get_ecommerce_setting('company_address_for_invoicing');

        $country = EcommerceHelperFacade::getCountryNameById($this->getCompanyCountry());
        $state = $this->getCompanyState();
        $city = $this->getCompanyCity();

        if (! $companyAddress) {
            $companyAddress = implode(', ', array_filter([
                get_ecommerce_setting('company_address_for_invoicing', get_ecommerce_setting('store_address')),
                $city,
                $state,
                $country,
            ]));
        }

        $companyPhone = get_ecommerce_setting('company_phone_for_invoicing') ?: get_ecommerce_setting('store_phone');
        $companyEmail = get_ecommerce_setting('company_email_for_invoicing') ?: get_ecommerce_setting('store_email');
        $companyTaxId = get_ecommerce_setting('company_tax_id_for_invoicing') ?: get_ecommerce_setting(
            'store_vat_number'
        );

        $invoice->loadMissing(['items', 'reference']);

        $data = [
            'invoice' => $invoice->toArray(),
            'logo' => $logo,
            'logo_full_path' => RvMedia::getRealPath($logo),
            'site_title' => theme_option('site_title'),
            'company_logo_full_path' => RvMedia::getRealPath($logo),
            'company_name' => $companyName,
            'company_address' => $companyAddress,
            'company_country' => $country,
            'company_state' => $state,
            'company_city' => $city,
            'company_zipcode' => get_ecommerce_setting('company_zipcode_for_invoicing') ?: get_ecommerce_setting(
                'store_zip_code'
            ),
            'company_phone' => $companyPhone,
            'company_email' => $companyEmail,
            'company_tax_id' => $companyTaxId,
            'total_quantity' => $invoice->items->sum('qty'),
            'payment_description' => $paymentDescription,
            'is_tax_enabled' => EcommerceHelperFacade::isTaxEnabled(),
            'settings' => [
                'using_custom_font_for_invoice' => (bool) get_ecommerce_setting('using_custom_font_for_invoice'),
                'custom_font_family' => get_ecommerce_setting('invoice_font_family', 'DejaVu Sans'),
                'font_family' => (int) get_ecommerce_setting('using_custom_font_for_invoice', 0) == 1
                    ? get_ecommerce_setting('invoice_font_family', 'DejaVu Sans')
                    : 'DejaVu Sans',
                'enable_invoice_stamp' => get_ecommerce_setting('enable_invoice_stamp'),
                'date_format' => get_ecommerce_setting('invoice_date_format', 'F d, Y'),
            ],
            'invoice_header_filter' => apply_filters('ecommerce_invoice_header', null, $invoice),
            'invoice_body_filter' => apply_filters('ecommerce_invoice_body', null, $invoice),
            'ecommerce_invoice_footer' => apply_filters('ecommerce_invoice_footer', null, $invoice),
            'invoice_payment_info_filter' => apply_filters('invoice_payment_info_filter', null, $invoice),
            'tax_classes_name' => $invoice->taxClassesName,
        ];

        $data['settings']['font_css'] = null;

        if ($data['settings']['using_custom_font_for_invoice'] && $data['settings']['font_family']) {
            $data['settings']['font_css'] = BaseHelper::googleFonts(
                'https://fonts.googleapis.com/css2?family=' .
                urlencode($data['settings']['font_family']) .
                ':wght@400;600;700&display=swap'
            );
        }

        $data['settings']['extra_css'] = apply_filters('ecommerce_invoice_extra_css', null, $invoice);

        $data['settings']['header_html'] = apply_filters('ecommerce_invoice_header_html', null, $invoice);

        $order = $invoice->reference;

        if ($order) {
            $address = $order->shippingAddress;

            if (EcommerceHelperFacade::isBillingAddressEnabled() && $order->billingAddress->id) {
                $address = $order->billingAddress;
            }

            $data['customer_country'] = $address->country_name;
            $data['customer_state'] = $address->state_name;
            $data['customer_city'] = $address->city_name;
            $data['customer_zip_code'] = $address->zip_code;
        }

        if (is_plugin_active('payment')) {
            $invoice->loadMissing(['payment']);

            $data['payment_method'] = $invoice->payment->payment_channel->label();
            $data['payment_status'] = $invoice->payment->status->getValue();
            $data['payment_status_label'] = $invoice->payment->status->label();
        }

        return apply_filters('ecommerce_invoice_variables', $data, $invoice);
    }

    public function getDataForPreview(): Invoice
    {
        $invoice = new Invoice([
            'code' => 'INV-1',
            'customer_name' => 'Odie Miller',
            'store_name' => 'LinkedIn',
            'store_address' => '701 Norman Street Los Angeles California 90008',
            'customer_email' => 'contact@example.com',
            'customer_phone' => '+0123456789',
            'customer_address' => '14059 Triton Crossroad South Lillie, NH 84777-1634',
            'status' => InvoiceStatusEnum::PENDING,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        $items = [];

        foreach (range(1, 3) as $i) {
            $amount = rand(10, 1000);
            $qty = rand(1, 10);

            $items[] = new InvoiceItem([
                'name' => "Item $i",
                'description' => "Description of item $i",
                'sub_total' => $amount * $qty,
                'amount' => $amount,
                'qty' => $qty,
            ]);

            $invoice->amount += $amount * $qty;
            $invoice->sub_total = $invoice->amount;
        }

        $payment = new Payment([
            'payment_channel' => PaymentMethodEnum::BANK_TRANSFER,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $invoice->setRelation('payment', $payment);
        $invoice->setRelation('items', collect($items));

        return $invoice;
    }

    public function getVariables(): array
    {
        return [
            'invoice.*' => trans('plugins/ecommerce::invoice-template.variables.invoice_data'),
            'logo_full_path' => trans('plugins/ecommerce::invoice-template.variables.site_logo'),
            'company_logo_full_path' => trans('plugins/ecommerce::setting.invoice.form.company_logo'),
            'site_title' => trans('plugins/ecommerce::invoice-template.variables.site_title'),
            'company_name' => trans('plugins/ecommerce::invoice-template.variables.company_name'),
            'company_address' => trans('plugins/ecommerce::invoice-template.variables.company_address'),
            'company_country' => trans('plugins/ecommerce::invoice-template.variables.company_country'),
            'company_state' => trans('plugins/ecommerce::invoice-template.variables.company_state'),
            'company_city' => trans('plugins/ecommerce::invoice-template.variables.company_city'),
            'company_zipcode' => trans('plugins/ecommerce::invoice-template.variables.company_zipcode'),
            'company_phone' => trans('plugins/ecommerce::invoice-template.variables.company_phone'),
            'company_email' => trans('plugins/ecommerce::invoice-template.variables.company_email'),
            'company_tax_id' => trans('plugins/ecommerce::invoice-template.variables.company_tax_id'),
            'payment_method' => __('Payment method'),
            'payment_status' => __('Payment status'),
            'payment_description' => __('Payment description'),
        ];
    }

    public function getCompanyCountry(): ?string
    {
        return get_ecommerce_setting('company_country_for_invoicing', get_ecommerce_setting('store_country'));
    }

    public function getCompanyState(): ?string
    {
        return get_ecommerce_setting('company_state_for_invoicing', get_ecommerce_setting('store_state'));
    }

    public function getCompanyCity(): ?string
    {
        return get_ecommerce_setting('company_city_for_invoicing', get_ecommerce_setting('store_city'));
    }

    public function getCompanyZipCode(): ?string
    {
        return get_ecommerce_setting('company_zipcode_for_invoicing', get_ecommerce_setting('store_zip_code'));
    }

    public function getLanguageSupport(): string
    {
        $languageSupport = get_ecommerce_setting('invoice_language_support');

        if (! empty($languageSupport)) {
            return $languageSupport;
        }

        if (get_ecommerce_setting('invoice_support_arabic_language', false)) {
            return 'arabic';
        }

        if (get_ecommerce_setting('invoice_support_bangladesh_language', false)) {
            return 'bangladesh';
        }

        return '';
    }

    public function supportedDateFormats(): array
    {
        $formats = [
            'M d, Y',
            'F j, Y',
            'F d, Y',
            'Y-m-d',
            'Y-M-d',
            'd-m-Y',
            'd-M-Y',
            'm/d/Y',
            'M/d/Y',
            'd/m/Y',
            'd/M/Y',
        ];

        return apply_filters('invoice_date_formats', $formats);
    }
}
