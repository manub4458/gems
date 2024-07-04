<?php

namespace Botble\Theme\ThemeOption;

use Illuminate\Contracts\Support\Arrayable;

abstract class ThemeOptionField implements Arrayable
{
    protected string $sectionId;

    protected string $id;

    protected string $name;

    protected string $label;

    protected ?string $value = null;

    protected mixed $defaultValue = null;

    protected ?string $helperText = null;

    protected array $attributes = [];

    public static function make(): static
    {
        return new static();
    }

    abstract public function fieldType(): string;

    public function sectionId(string $sectionId): static
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function defaultValue(mixed $defaultValue): static
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function helperText(string $helperText): static
    {
        $this->helperText = $helperText;

        return $this;
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value ?: $this->defaultValue;
    }

    public function toArray(): array
    {
        $attributes = [];

        if (isset($this->sectionId)) {
            $attributes['section_id'] = $this->sectionId;
        }

        if ($this->helperText) {
            $attributes['helper'] = $this->helperText;
        }

        $attributes = [
            ...$attributes,
            'id' => $this->id ?? $this->name,
            'type' => $this->fieldType(),
            'label' => $this->label,
            'attributes' => [
                'name' => $this->name,
            ],
        ];

        if (! empty($this->attributes)) {
            $attributes['attributes']['options'] = $this->attributes;
        }

        return $attributes;
    }
}
