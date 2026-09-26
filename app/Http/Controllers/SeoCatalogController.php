<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoCatalogController extends Controller
{
    public const LANDINGS = [
        'phone-prices-in-kenya' => ['Phone Prices in Kenya', 'Compare current phone prices, stock and brands at Nimtech.', 'phone'],
        'laptop-prices-in-kenya' => ['Laptop Prices in Kenya', 'Compare current laptop prices, specifications and stock at Nimtech.', 'laptop'],
        'iphone-prices-in-kenya' => ['iPhone Prices in Kenya', 'Compare in-stock Apple iPhone models and current Kenya prices.', 'phone', 'iphone'],
        'samsung-phone-prices-in-kenya' => ['Samsung Phone Prices in Kenya', 'Compare in-stock Samsung Galaxy phones and current Kenya prices.', 'phone', 'samsung'],
        'tecno-phone-prices-in-kenya' => ['Tecno Phone Prices in Kenya', 'Compare in-stock Tecno phones and current Kenya prices.', 'phone', 'tecno'],
        'infinix-phone-prices-in-kenya' => ['Infinix Phone Prices in Kenya', 'Compare in-stock Infinix phones and current Kenya prices.', 'phone', 'infinix'],
        'xiaomi-phone-prices-in-kenya' => ['Xiaomi Phone Prices in Kenya', 'Compare in-stock Xiaomi and Redmi phones and current Kenya prices.', 'phone', 'xiaomi|redmi'],
        'hp-laptop-prices-in-kenya' => ['HP Laptop Prices in Kenya', 'Compare in-stock HP laptops, specifications and current Kenya prices.', 'laptop', 'hp'],
        'dell-laptop-prices-in-kenya' => ['Dell Laptop Prices in Kenya', 'Compare in-stock Dell laptops, specifications and current Kenya prices.', 'laptop', 'dell'],
        'lenovo-laptop-prices-in-kenya' => ['Lenovo Laptop Prices in Kenya', 'Compare in-stock Lenovo laptops, specifications and current Kenya prices.', 'laptop', 'lenovo'],
        'macbook-prices-in-kenya' => ['MacBook Prices in Kenya', 'Compare in-stock Apple MacBook models and current Kenya prices.', 'laptop', 'macbook|apple'],
        'refurbished-phones-in-kenya' => ['Refurbished Phones in Kenya', 'Compare tested refurbished and EX-UK phones with clear stock and pricing.', 'phone', 'refurbished|ex-uk|ex uk|pre-owned'],
        'refurbished-laptops-in-kenya' => ['Refurbished Laptops in Kenya', 'Compare tested refurbished and EX-UK laptops with clear stock and pricing.', 'laptop', 'refurbished|ex-uk|ex uk|pre-owned'],
        'student-laptops-in-kenya' => ['Student Laptops in Kenya', 'Compare practical in-stock laptops for university, college and school work.', 'laptop', 'student|chromebook|core i3|ryzen 3'],
        'business-laptops-in-kenya' => ['Business Laptops in Kenya', 'Compare reliable business laptops including EliteBook, Latitude and ThinkPad.', 'laptop', 'elitebook|latitude|thinkpad|business'],
        'gaming-laptops-in-kenya' => ['Gaming Laptops in Kenya', 'Compare in-stock gaming laptops with capable processors and graphics.', 'laptop', 'gaming|nvidia|geforce|rtx|gtx'],
        'programming-laptops-in-kenya' => ['Programming Laptops in Kenya', 'Compare in-stock laptops suited to coding and software development.', 'laptop', 'core i5|core i7|ryzen 5|ryzen 7|16gb'],
        'graphic-design-laptops-in-kenya' => ['Graphic Design Laptops in Kenya', 'Compare in-stock laptops for design, editing and creative workloads.', 'laptop', 'nvidia|geforce|rtx|16gb|32gb'],
        'touchscreen-laptops-in-kenya' => ['Touchscreen Laptops in Kenya', 'Compare in-stock touchscreen and 2-in-1 laptops.', 'laptop', 'touch|2-in-1|x360'],
        'core-i5-laptops-in-kenya' => ['Core i5 Laptops in Kenya', 'Compare current Intel Core i5 laptop prices and stock.', 'laptop', 'core i5|i5-'],
        'core-i7-laptops-in-kenya' => ['Core i7 Laptops in Kenya', 'Compare current Intel Core i7 laptop prices and stock.', 'laptop', 'core i7|i7-'],
        'ryzen-5-laptops-in-kenya' => ['Ryzen 5 Laptops in Kenya', 'Compare current AMD Ryzen 5 laptop prices and stock.', 'laptop', 'ryzen 5'],
        '16gb-ram-laptops-in-kenya' => ['16GB RAM Laptops in Kenya', 'Compare in-stock laptops with 16GB RAM.', 'laptop', '16gb'],
        '512gb-ssd-laptops-in-kenya' => ['512GB SSD Laptops in Kenya', 'Compare in-stock laptops with 512GB SSD storage.', 'laptop', '512gb'],
        'core-i5-8th-gen-laptops-in-kenya' => ['Core i5 8th Gen Laptops in Kenya', 'Compare in-stock Core i5 8th generation laptops and current prices.', 'laptop', 'i5 8th|i5-8'],
        'core-i5-10th-gen-laptops-in-kenya' => ['Core i5 10th Gen Laptops in Kenya', 'Compare in-stock Core i5 10th generation laptops and current prices.', 'laptop', 'i5 10th|i5-10'],
        'core-i5-11th-gen-laptops-in-kenya' => ['Core i5 11th Gen Laptops in Kenya', 'Compare in-stock Core i5 11th generation laptops and current prices.', 'laptop', 'i5 11th|i5-11'],
        'core-i5-12th-gen-laptops-in-kenya' => ['Core i5 12th Gen Laptops in Kenya', 'Compare in-stock Core i5 12th generation laptops and current prices.', 'laptop', 'i5 12th|i5-12'],
        'video-editing-laptops-in-kenya' => ['Video Editing Laptops in Kenya', 'Compare current laptops suited to video editing workloads.', 'laptop', 'nvidia|geforce|rtx|16gb|32gb'],
        'engineering-laptops-in-kenya' => ['Engineering Laptops in Kenya', 'Compare current laptops suited to engineering software and technical work.', 'laptop', 'workstation|quadro|nvidia|core i7|ryzen 7'],
        'work-from-home-laptops-in-kenya' => ['Work From Home Laptops in Kenya', 'Compare current laptops suited to remote work, meetings and productivity.', 'laptop', 'core i3|core i5|ryzen 3|ryzen 5'],
        'best-camera-phones-in-kenya' => ['Best Camera Phones Available in Kenya', 'Compare in-stock phones positioned for photography based on their published product details.', 'phone', 'camera|iphone|pixel|galaxy s'],
        'best-gaming-phones-in-kenya' => ['Gaming Phones Available in Kenya', 'Compare in-stock phones positioned for gaming based on their published product details.', 'phone', 'gaming|rog|poco|redmagic'],
        'best-phones-for-students-in-kenya' => ['Phones for Students in Kenya', 'Compare affordable in-stock phones suitable for study and everyday communication.', 'phone', 'phone'],
        'iphone-17-series-in-kenya' => ['iPhone 17 Series in Kenya', 'Compare available iPhone 17 family models, prices and stock.', 'phone', 'iphone 17'],
        'samsung-galaxy-s-series-in-kenya' => ['Samsung Galaxy S Series in Kenya', 'Compare available Samsung Galaxy S family models, prices and stock.', 'phone', 'galaxy s'],
        'samsung-galaxy-a-series-in-kenya' => ['Samsung Galaxy A Series in Kenya', 'Compare available Samsung Galaxy A family models, prices and stock.', 'phone', 'galaxy a'],
        'redmi-note-series-in-kenya' => ['Redmi Note Series in Kenya', 'Compare available Redmi Note family models, prices and stock.', 'phone', 'redmi note'],
        'tecno-camon-series-in-kenya' => ['Tecno Camon Series in Kenya', 'Compare available Tecno Camon family models, prices and stock.', 'phone', 'tecno camon|camon'],
        'infinix-note-series-in-kenya' => ['Infinix Note Series in Kenya', 'Compare available Infinix Note family models, prices and stock.', 'phone', 'infinix note'],
        'refurbished-hp-laptops-in-kenya' => ['Refurbished HP Laptops in Kenya', 'Compare current refurbished and EX-UK HP laptops.', 'laptop', 'hp refurbished|hp ex-uk|hp ex uk|elitebook'],
        'refurbished-dell-laptops-in-kenya' => ['Refurbished Dell Laptops in Kenya', 'Compare current refurbished and EX-UK Dell laptops.', 'laptop', 'dell refurbished|dell ex-uk|dell ex uk|latitude'],
        'refurbished-lenovo-laptops-in-kenya' => ['Refurbished Lenovo Laptops in Kenya', 'Compare current refurbished and EX-UK Lenovo laptops.', 'laptop', 'lenovo refurbished|lenovo ex-uk|lenovo ex uk|thinkpad'],
    ];

    public const PHONE_BUDGETS = [10000, 15000, 20000, 30000, 50000, 100000];
    public const LAPTOP_BUDGETS = [20000, 30000, 40000, 50000, 70000, 100000];

    public function landing(Request $request, string $slug)
    {
        abort_unless(isset(self::LANDINGS[$slug]), 404);
        [$heading, $intro, $type, $terms] = array_pad(self::LANDINGS[$slug], 4, null);
        $query = $this->baseQuery($type);
        if ($terms) $this->whereTerms($query, $terms);
        return $this->render($query, $slug, $heading, $intro);
    }

    public function budget(string $type, int $amount)
    {
        $budgets = $type === 'phones' ? self::PHONE_BUDGETS : ($type === 'laptops' ? self::LAPTOP_BUDGETS : []);
        abort_unless(in_array($amount, $budgets, true), 404);
        $heading = Str::title($type).' Under KSh '.number_format($amount).' in Kenya';
        $intro = 'Compare currently available '.$type.' priced up to KSh '.number_format($amount).', with live stock and direct product links.';
        return $this->render($this->baseQuery(Str::singular($type))->where('price', '<=', $amount), "$type-under-$amount-in-kenya", $heading, $intro);
    }

    public function updated()
    {
        return $this->render(Product::query()->with(['category', 'brand'])->where('stock', '>', 0)->latest('updated_at'), 'recently-updated-prices', 'Recently Updated Prices', 'See newly updated prices, restocked products and recent additions from the live Nimtech catalogue.', true);
    }

    public function compare(Request $request)
    {
        $ids = collect($request->query('products', []))->map(fn ($id) => (int) $id)->filter()->unique()->take(4);
        $products = Product::query()->with(['category', 'brand'])->whereIn('id', $ids)->get()->sortBy(fn ($p) => $ids->search($p->id));
        $candidates = Product::query()->with(['category', 'brand'])->where('stock', '>', 0)->latest('updated_at')->take(50)->get();
        return view('theme::seo.compare', compact('products', 'candidates'));
    }

    private function render(Builder $query, string $slug, string $heading, string $intro, bool $alreadyOrdered = false)
    {
        if (!$alreadyOrdered) $query->orderBy('price');
        $minPrice = (clone $query)->reorder()->min('price');
        $maxPrice = (clone $query)->reorder()->max('price');
        $products = $query->paginate(24)->withQueryString();
        return view('theme::seo.catalog', compact('products', 'slug', 'heading', 'intro', 'minPrice', 'maxPrice'));
    }

    private function baseQuery(string $type): Builder
    {
        $query = Product::query()->with(['category', 'brand'])->where('stock', '>', 0);
        $this->whereTerms($query, $type === 'phone' ? 'phone|iphone|smartphone|samsung galaxy|tecno|infinix|redmi|xiaomi|oppo|vivo' : 'laptop|notebook|macbook|elitebook|latitude|thinkpad|chromebook');
        return $query;
    }

    private function whereTerms(Builder $query, string $terms): void
    {
        $words = explode('|', strtolower($terms));
        $query->where(function (Builder $q) use ($words) {
            foreach ($words as $word) {
                $q->orWhereRaw('LOWER(products.name) LIKE ?', ['%'.$word.'%'])
                    ->orWhereRaw('LOWER(COALESCE(products.description, ?)) LIKE ?', ['', '%'.$word.'%'])
                    ->orWhereHas('brand', fn (Builder $brand) => $brand->whereRaw('LOWER(name) LIKE ?', ['%'.$word.'%']))
                    ->orWhereHas('category', fn (Builder $category) => $category->whereRaw('LOWER(name) LIKE ?', ['%'.$word.'%']));
            }
        });
    }
}
