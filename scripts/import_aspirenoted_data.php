<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$jsonPath = getenv('AN_IMPORT_JSON') ?: (__DIR__.'/../../aspirenoted_data.json');
$data = json_decode(file_get_contents($jsonPath), true);
if (!$data) {
    fwrite(STDERR, "Could not read/parse JSON at {$jsonPath}\n");
    exit(1);
}

$storageDir = storage_path('app/public/products');
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

function download_image(string $url, string $destDir): ?string
{
    $filename = basename(parse_url($url, PHP_URL_PATH));
    $dest = $destDir.DIRECTORY_SEPARATOR.$filename;
    if (file_exists($dest)) {
        return 'products/'.$filename;
    }
    $ctx = stream_context_create(['http' => ['timeout' => 20]]);
    $bytes = @file_get_contents($url, false, $ctx);
    if ($bytes === false) {
        return null;
    }
    file_put_contents($dest, $bytes);
    return 'products/'.$filename;
}

DB::transaction(function () use ($data, $storageDir) {
    DB::table('products')->delete();
    DB::table('categories')->delete();

    $categoryIds = [];
    foreach ($data['categories'] as $cat) {
        $imagePath = null;
        if (!empty($cat['image'])) {
            $imagePath = download_image($cat['image'], $storageDir);
        }
        $model = Category::create([
            'name' => $cat['name'],
            'slug' => $cat['slug'],
            'image_path' => $imagePath,
            'is_featured' => $cat['featured'] ?? false,
            'sort_order' => $cat['sort_order'] ?? 999,
        ]);
        $categoryIds[$cat['name']] = $model->id;
    }
    echo "Categories created: ".count($categoryIds).PHP_EOL;

    $created = 0;
    $skippedImages = [];
    foreach ($data['products'] as $p) {
        $imagePath = null;
        if (!empty($p['image'])) {
            $imagePath = download_image($p['image'], $storageDir);
            if (!$imagePath) {
                $skippedImages[] = $p['name'];
            }
        }
        Product::create([
            'category_id' => $categoryIds[$p['category']] ?? null,
            'name' => $p['name'],
            'slug' => $p['slug'],
            'description' => null,
            'price' => $p['price'],
            'image' => $imagePath,
            'stock' => 25,
        ]);
        $created++;
    }
    echo "Products created: {$created}".PHP_EOL;
    if ($skippedImages) {
        echo "Images failed to download for: ".implode(', ', $skippedImages).PHP_EOL;
    }
});

echo "Done.\n";
