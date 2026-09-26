<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        $catalog = [
            'Books' => [
                ['Clean Code', 29.99],
                ['The Pragmatic Programmer', 34.99],
                ['Refactoring', 39.99],
            ],
            'Electronics' => [
                ['Wireless Headphones', 59.99],
                ['Smart Watch', 99.00],
                ['USB-C Charger', 19.50],
            ],
            'Clothing' => [
                ['Classic T-Shirt', 15.00],
                ['Hoodie', 45.00],
                ['Jeans', 49.99],
            ],
            'Home & Kitchen' => [
                ['Coffee Maker', 79.00],
                ['Chef Knife', 25.00],
                ['Non-stick Pan', 22.50],
            ],
        ];

        foreach ($catalog as $categoryName => $items) {
            $category = Category::firstWhere('slug', Str::slug($categoryName));
            if (!$category) continue;
            foreach ($items as [$name, $price]) {
                $product = Product::withTrashed()->updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'description' => $name . ' description',
                        'price' => $price,
                        'stock' => 100,
                    ]
                );
                if ($product->trashed()) {
                    $product->restore();
                }
            }
        }
    }
}
