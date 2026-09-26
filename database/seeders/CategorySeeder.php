<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Books', 'description' => 'Fiction, non-fiction, and more.'],
            ['name' => 'Electronics', 'description' => 'Gadgets and devices.'],
            ['name' => 'Clothing', 'description' => 'Men and Women apparel.'],
            ['name' => 'Home & Kitchen', 'description' => 'Everything for your home.'],
        ];

        foreach ($data as $cat) {
            $category = Category::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'description' => $cat['description']]
            );
            if ($category->trashed()) {
                $category->restore();
            }
        }
    }
}
