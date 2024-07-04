<?php

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        rescue(function () {
            DB::table('settings')->insertOrIgnore([
                'id' => BaseModel::isUsingIntegerId() ? null : (new BaseModel())->newUniqueId(),
                'key' => 'enable_recaptcha_botble_newsletter_forms_fronts_newsletter_form',
                'value' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (setting('enable_math_captcha_for_newsletter_form')) {
                DB::table('settings')->insertOrIgnore([
                    'id' => BaseModel::isUsingIntegerId() ? null : (new BaseModel())->newUniqueId(),
                    'key' => 'enable_math_captcha_botble_newsletter_forms_fronts_newsletter_form',
                    'value' => '1',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });
    }
};
