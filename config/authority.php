<?php

$csv = static function (?string $value): array {
    $value = strtolower((string) $value);
    if (trim($value) === '') {
        return [];
    }

    return array_values(array_filter(array_map(
        static fn (string $item) => trim($item),
        explode(',', $value)
    )));
};

return [
    // When enabled, unresolved configured slugs will intentionally return no products.
    'strict_slug_binding' => (bool) env('AUTHORITY_STRICT_SLUG_BINDING', true),

    'categories' => [
        'phones' => $csv(env('AUTHORITY_PHONE_CATEGORY_SLUGS', 'phones,mobile-phones,smartphones')),
        'laptops' => $csv(env('AUTHORITY_LAPTOP_CATEGORY_SLUGS', 'laptops,laptop-computers,notebooks')),
    ],

    'brands' => [
        'iphone-kenya' => $csv(env('AUTHORITY_BRAND_IPHONE_SLUGS', 'apple,iphone')),
        'samsung-kenya' => $csv(env('AUTHORITY_BRAND_SAMSUNG_SLUGS', 'samsung')),
        'hp-laptops-kenya' => $csv(env('AUTHORITY_BRAND_HP_SLUGS', 'hp,hewlett-packard')),
        'dell-laptops-kenya' => $csv(env('AUTHORITY_BRAND_DELL_SLUGS', 'dell')),
        'lenovo-laptops-kenya' => $csv(env('AUTHORITY_BRAND_LENOVO_SLUGS', 'lenovo')),
        'xiaomi-phones-kenya' => $csv(env('AUTHORITY_BRAND_XIAOMI_SLUGS', 'xiaomi,redmi,poco')),
    ],
];
