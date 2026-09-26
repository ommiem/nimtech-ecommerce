<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AuthorityPageController extends Controller
{
    public function phones(): View
    {
        return $this->renderPage('phones');
    }

    public function laptops(): View
    {
        return $this->renderPage('laptops');
    }

    public function iphoneKenya(): View
    {
        return $this->renderPage('iphone-kenya');
    }

    public function samsungKenya(): View
    {
        return $this->renderPage('samsung-kenya');
    }

    public function hpLaptopsKenya(): View
    {
        return $this->renderPage('hp-laptops-kenya');
    }

    public function dellLaptopsKenya(): View
    {
        return $this->renderPage('dell-laptops-kenya');
    }

    public function lenovoLaptopsKenya(): View
    {
        return $this->renderPage('lenovo-laptops-kenya');
    }

    public function xiaomiPhonesKenya(): View
    {
        return $this->renderPage('xiaomi-phones-kenya');
    }

    private function renderPage(string $slug): View
    {
        $definitions = $this->pageDefinitions();
        abort_unless(isset($definitions[$slug]), 404);

        $page = $definitions[$slug];
        $baseQuery = Product::query()->with(['category', 'brand']);

        $categorySlugs = $this->cleanSlugs($page['category_slugs'] ?? []);
        if (!empty($categorySlugs)) {
            $categoryIds = Category::query()->whereIn('slug', $categorySlugs)->pluck('id')->all();
            if (!empty($categoryIds)) {
                $baseQuery->whereIn('category_id', $categoryIds);
            } elseif ($this->strictSlugBindingEnabled()) {
                $baseQuery->whereRaw('1 = 0');
            }
        }

        $brand = null;
        $brandSlugs = $this->cleanSlugs($page['brand_slugs'] ?? []);
        if (!empty($brandSlugs)) {
            $matchedBrands = Brand::query()
                ->whereIn('slug', $brandSlugs)
                ->get(['id', 'name', 'slug']);
            $brand = $matchedBrands->first();

            if ($matchedBrands->isNotEmpty()) {
                $baseQuery->whereIn('brand_id', $matchedBrands->pluck('id')->all());
            } elseif ($this->strictSlugBindingEnabled()) {
                $baseQuery->whereRaw('1 = 0');
            }
        }

        $inStockQuery = (clone $baseQuery)->where('stock', '>', 0);
        $hasInStock = (clone $inStockQuery)->exists();
        $baseQuery = $hasInStock ? $inStockQuery : $baseQuery;

        $featuredProducts = (clone $baseQuery)->orderByDesc('id')->take(8)->get();
        $affordableProducts = (clone $baseQuery)->orderBy('price')->take(8)->get();
        $premiumProducts = (clone $baseQuery)->orderByDesc('price')->take(8)->get();

        $budgetProducts = collect();
        $budgetCap = $page['budget_cap'] ?? null;
        if (is_numeric($budgetCap)) {
            $budgetProducts = (clone $baseQuery)
                ->where('price', '<=', (float) $budgetCap)
                ->orderBy('price')
                ->take(8)
                ->get();
        }

        $segmentBlocks = [];
        foreach ($page['segments'] ?? [] as $segment) {
            $segmentQuery = clone $baseQuery;
            if (!empty($segment['keywords'])) {
                $this->applyKeywordFilter($segmentQuery, $segment['keywords']);
            }

            $sort = $segment['sort'] ?? 'price_asc';
            if ($sort === 'latest') {
                $segmentQuery->orderByDesc('id');
            } elseif ($sort === 'price_desc') {
                $segmentQuery->orderByDesc('price');
            } else {
                $segmentQuery->orderBy('price');
            }

            $segmentProducts = $segmentQuery->take(6)->get();
            if ($segmentProducts->isEmpty()) {
                $segmentProducts = $affordableProducts->take(6);
            }

            $segmentBlocks[] = [
                'title' => $segment['title'],
                'description' => $segment['description'] ?? null,
                'products' => $segmentProducts,
            ];
        }

        $faqSchema = null;
        if (!empty($page['faq'])) {
            $faqSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(function (array $faqItem) {
                    return [
                        '@type' => 'Question',
                        'name' => $faqItem['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faqItem['answer'],
                        ],
                    ];
                }, $page['faq']),
            ];
        }

        $relatedLinks = collect($page['related_links'] ?? [])
            ->map(function (array $link) {
                if (!empty($link['route']) && Route::has($link['route'])) {
                    $link['url'] = route($link['route']);
                }

                if (empty($link['url']) || empty($link['label'])) {
                    return null;
                }

                return [
                    'label' => $link['label'],
                    'url' => $link['url'],
                ];
            })
            ->filter()
            ->values();

        return view('theme::seo.authority', [
            'page' => $page,
            'brand' => $brand,
            'featuredProducts' => $featuredProducts,
            'affordableProducts' => $affordableProducts,
            'premiumProducts' => $premiumProducts,
            'budgetProducts' => $budgetProducts,
            'segmentBlocks' => $segmentBlocks,
            'faqSchema' => $faqSchema,
            'relatedLinks' => $relatedLinks,
        ]);
    }

    private function cleanSlugs(array $slugs): array
    {
        $normalized = array_map(
            fn ($slug) => strtolower(trim((string) $slug)),
            $slugs
        );

        return array_values(array_unique(array_filter($normalized)));
    }

    private function strictSlugBindingEnabled(): bool
    {
        return (bool) config('authority.strict_slug_binding', true);
    }

    private function categorySlugsForType(string $type, array $fallback = []): array
    {
        $configured = $this->cleanSlugs((array) config("authority.categories.{$type}", []));
        if (!empty($configured)) {
            return $configured;
        }

        return $this->cleanSlugs($fallback);
    }

    private function brandSlugsForPage(string $pageSlug, array $fallback = []): array
    {
        $configured = $this->cleanSlugs((array) config("authority.brands.{$pageSlug}", []));
        if (!empty($configured)) {
            return $configured;
        }

        return $this->cleanSlugs($fallback);
    }

    private function defaultCategorySlugs(string $type): array
    {
        return match ($type) {
            'phones' => ['phones', 'mobile-phones', 'smartphones'],
            'laptops' => ['laptops', 'laptop-computers', 'notebooks'],
            default => [],
        };
    }

    private function applyKeywordFilter(Builder $query, array $keywords): void
    {
        if (empty($keywords)) {
            return;
        }

        $query->where(function (Builder $outerQuery) use ($keywords) {
            foreach ($keywords as $index => $keyword) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $term = '%'.$keyword.'%';
                $slugTerm = '%'.str_replace(' ', '-', strtolower($keyword)).'%';

                $outerQuery->{$method}(function (Builder $subQuery) use ($term, $slugTerm) {
                    $subQuery->where('name', 'like', $term)
                        ->orWhere('slug', 'like', $slugTerm)
                        ->orWhere('description', 'like', $term);
                });
            }
        });
    }

    private function pageDefinitions(): array
    {
        return [
            'phones' => $this->phonesPageDefinition(),
            'laptops' => $this->laptopsPageDefinition(),
            ...$this->brandPageDefinitions(),
        ];
    }

    private function phonesPageDefinition(): array
    {
        return [
            'meta_title' => 'Buy phones in Kenya',
            'meta_title_exact' => true,
            'meta_description' => 'Buy phones in Kenya at Nimtech. Compare original smartphones, transparent prices, warranty support, and fast delivery in Nairobi and across Kenya.',
            'heading' => 'Buy phones in Kenya - Original Smartphones at Competitive Prices',
            'category_slugs' => $this->categorySlugsForType('phones', $this->defaultCategorySlugs('phones')),
            'budget_cap' => 30000,
            'budget_title' => 'Best phones under KES 30,000',
            'budget_description' => 'Value smartphones with strong performance, good cameras, and reliable battery life for everyday use in Kenya.',
            'keyword_targets' => [
                'Buy phones in Kenya',
                'Phones in Kenya',
                'iPhone price in Kenya',
                'Samsung phones Kenya',
                'Best phones under KES 30,000',
                'Phone shop in Nairobi',
            ],
            'intro' => [
                'Buy phones in Kenya from Nimtech with transparent pricing, verified stock, and fast fulfillment. Whether you need a daily-use Android, a premium iPhone, or a camera-focused phone, this page gives you a clear path from comparison to checkout.',
                'We stock popular phone brands and keep product information practical: storage options, network support, camera setup, battery size, and performance level. That makes it easier to match a phone to your budget and usage before you pay.',
                'Every order is backed by warranty support, responsive after-sale assistance, and delivery options across Nairobi and the rest of Kenya.',
            ],
            'guide_title' => 'Phone Buying Guide for Kenya',
            'guide_points' => [
                'Choose RAM and storage based on your workload: 6GB RAM and 128GB storage is a practical baseline for most users.',
                'Pick battery size based on mobility needs. For long days away from power, prioritize 5000mAh and above.',
                'Match camera features to your real use case: daylight photos, low-light shots, social media video, or business content.',
                'Confirm software support lifecycle before buying, especially if you want long-term security updates.',
                'Use 4G/5G compatibility and dual-SIM needs as a final filter when comparing options.',
                'Balance value and durability by selecting devices with official accessories and genuine replacement support.',
            ],
            'extended_title' => 'How to choose the right smartphone in Kenya',
            'extended_content' => [
                'Smartphone buying in Kenya is no longer just about brand preference. Buyers now compare performance, camera quality, software support, and long-term reliability before making a decision. The right phone should fit your daily workload and remain useful for years without forcing expensive upgrades after a few months.',
                'For social media creators and business owners, camera consistency and storage become key. A phone with stable image processing, enough internal storage, and dependable app performance can save time and reduce frustration. If your phone is a work tool, prioritize performance and battery strength before cosmetic features.',
                'Budget buyers can still get strong value by focusing on practical specs. A balanced processor, enough RAM for multitasking, and good battery optimization will usually outperform a device that only looks premium on paper. Compare what actually affects daily speed, app behavior, and charging frequency.',
                'For premium users, software experience and ecosystem reliability matter most. If you already use a specific ecosystem, choosing a compatible device can improve productivity and convenience. Nimtech helps you evaluate these tradeoffs clearly so you can buy with confidence and avoid mismatched purchases.',
            ],
            'warranty' => 'All smartphones are sold with clear warranty terms and straightforward defect reporting support.',
            'delivery' => 'We deliver in Nairobi and upcountry across Kenya through trusted courier options.',
            'after_sale' => 'After-sale support covers setup guidance, accessories advice, and escalation on technical issues.',
            'why_buy' => [
                'Transparent pricing in KES with no hidden steps during checkout.',
                'Original devices from trusted supply channels.',
                'Fast response on WhatsApp and phone for pre-sale and post-sale support.',
                'Practical recommendations based on budget, not guesswork.',
            ],
            'faq' => [
                [
                    'question' => 'Do you sell original phones in Kenya?',
                    'answer' => 'Yes. Nimtech focuses on genuine phones from trusted channels and provides warranty guidance for each purchase.',
                ],
                [
                    'question' => 'Can I buy a phone online and get delivery outside Nairobi?',
                    'answer' => 'Yes. We ship orders across Kenya and provide delivery updates after checkout confirmation.',
                ],
                [
                    'question' => 'How do I choose between Android and iPhone?',
                    'answer' => 'Choose based on your preferred ecosystem, camera style, software experience, and long-term budget for accessories and upgrades.',
                ],
                [
                    'question' => 'Do you have phones under KES 30,000?',
                    'answer' => 'Yes. We maintain budget-friendly options and update stock regularly for the under KES 30,000 segment.',
                ],
            ],
            'related_links' => [
                ['label' => 'Laptops in Kenya', 'route' => 'seo.laptops'],
                ['label' => 'iPhone in Kenya', 'route' => 'seo.iphone-kenya'],
                ['label' => 'Samsung Phones Kenya', 'route' => 'seo.samsung-kenya'],
                ['label' => 'Xiaomi Phones Kenya', 'route' => 'seo.xiaomi-phones-kenya'],
            ],
        ];
    }

    private function laptopsPageDefinition(): array
    {
        return [
            'meta_title' => 'Laptops in Kenya - Affordable and High-Performance Devices',
            'meta_description' => 'Buy laptops in Kenya for school, work, and gaming. Compare HP, Dell, Lenovo and more with warranty support, expert guidance, and delivery across Nairobi and Kenya.',
            'heading' => 'Laptops in Kenya - Affordable and High-Performance Devices',
            'category_slugs' => $this->categorySlugsForType('laptops', $this->defaultCategorySlugs('laptops')),
            'keyword_targets' => [
                'Laptops in Kenya',
                'Buy laptop in Kenya',
                'HP laptops Kenya',
                'Dell laptops Kenya',
                'Gaming laptops Kenya',
                'Affordable laptops in Nairobi',
            ],
            'intro' => [
                'Nimtech makes laptop shopping in Kenya practical by focusing on real-world use cases: student work, office productivity, business reliability, creative workloads, and gaming performance. Instead of generic specs, we help you match device capability to your daily tasks and budget.',
                'We stock a wide laptop range and keep model details clear so you can compare processor generation, RAM, SSD capacity, graphics performance, and battery behavior before you order. This helps reduce wrong purchases and improves long-term value.',
                'Each laptop order is backed by warranty clarity, after-sale support, and delivery options across Kenya for both Nairobi and upcountry buyers.',
            ],
            'guide_title' => 'Laptop Buying Guide for Kenya',
            'guide_points' => [
                'Students should prioritize battery life, portability, and at least 8GB RAM with SSD storage.',
                'Business users should focus on build quality, keyboard comfort, security features, and reliability.',
                'Gaming and creator workflows need stronger CPUs, dedicated graphics, and efficient cooling.',
                'Processor guide: Intel Core i3/i5/i7 or AMD Ryzen 3/5/7 should match your workload and budget.',
                'RAM and SSD guide: 8GB/256GB is a baseline; 16GB/512GB is better for heavy multitasking.',
                'Check upgrade paths and port availability to avoid early replacement costs.',
            ],
            'extended_title' => 'Laptop planning for students, business users, and creators',
            'extended_content' => [
                'Laptop selection in Kenya should start with use case, not just price. Students need portability and battery life, business users need reliability and productivity speed, and creators need stronger processing and graphics capability. Defining your workload first prevents overspending on specs you do not use or underspending on performance you actually need.',
                'A practical baseline for many buyers is 8GB RAM with SSD storage, but this can change quickly for heavier multitasking. If you use many browser tabs, large spreadsheets, design tools, or editing software, stepping up to 16GB RAM provides smoother long-term performance and better responsiveness under pressure.',
                'Business buyers should also consider keyboard comfort, build quality, and port availability. A laptop used daily for office work, online meetings, and reports should feel stable and efficient throughout the day. Small ergonomic advantages can have a large impact on productivity over months of continuous use.',
                'For gaming and advanced workloads, thermal behavior and power efficiency are critical. A powerful processor and graphics chip only deliver real value when cooling and power management are strong. Nimtech helps buyers compare these details clearly so each purchase aligns with real use, budget, and expected lifespan.',
            ],
            'segments' => [
                [
                    'title' => 'Student laptops',
                    'description' => 'Affordable and dependable models for research, assignments, browsing, and online classes.',
                    'keywords' => ['student', 'education', 'chromebook', 'office'],
                    'sort' => 'price_asc',
                ],
                [
                    'title' => 'Business laptops',
                    'description' => 'Stable, productivity-focused laptops designed for office and professional workflows.',
                    'keywords' => ['business', 'probook', 'thinkpad', 'latitude', 'elitebook', 'vostro'],
                    'sort' => 'latest',
                ],
                [
                    'title' => 'Gaming laptops',
                    'description' => 'Higher-performance options for gaming, editing, rendering, and demanding applications.',
                    'keywords' => ['gaming', 'rtx', 'gtx', 'rog', 'tuf', 'legion', 'victus', 'predator', 'omen'],
                    'sort' => 'price_desc',
                ],
            ],
            'warranty' => 'Laptop purchases include documented warranty information and guidance on support workflows.',
            'delivery' => 'Safe packaging and shipping options are available for Nairobi and nationwide delivery.',
            'after_sale' => 'Our team supports setup basics, upgrade planning, and issue follow-up after purchase.',
            'why_buy' => [
                'Clear specs explanation before checkout so you buy the right machine once.',
                'Coverage for budget, mid-range, and high-performance laptop needs.',
                'Responsive support for pre-purchase and after-sale questions.',
                'Reliable fulfillment with practical communication from order to delivery.',
            ],
            'faq' => [
                [
                    'question' => 'Which laptop is best for students in Kenya?',
                    'answer' => 'Most students get the best value from 8GB RAM laptops with SSD storage, reliable battery life, and lightweight build.',
                ],
                [
                    'question' => 'Do you sell gaming laptops in Kenya?',
                    'answer' => 'Yes. Nimtech stocks gaming-focused laptops with stronger processors, dedicated GPUs, and better cooling setups.',
                ],
                [
                    'question' => 'How do I pick between HP, Dell, and Lenovo?',
                    'answer' => 'Compare by budget, keyboard preference, durability, upgrade options, and workload requirements rather than brand only.',
                ],
                [
                    'question' => 'Can I get laptop delivery outside Nairobi?',
                    'answer' => 'Yes. We deliver to many locations across Kenya and provide updates after your order is confirmed.',
                ],
            ],
            'related_links' => [
                ['label' => 'Phones in Kenya', 'route' => 'seo.phones'],
                ['label' => 'HP Laptops Kenya', 'route' => 'seo.hp-laptops-kenya'],
                ['label' => 'Dell Laptops Kenya', 'route' => 'seo.dell-laptops-kenya'],
                ['label' => 'Lenovo Laptops Kenya', 'route' => 'seo.lenovo-laptops-kenya'],
            ],
        ];
    }

    private function brandPageDefinitions(): array
    {
        return [
            'iphone-kenya' => $this->brandPageDefinition(
                pageSlug: 'iphone-kenya',
                metaTitle: 'iPhone in Kenya - Price, Models, and Warranty',
                metaDescription: 'Buy iPhone in Kenya at Nimtech with genuine Apple devices, transparent prices, warranty support, and fast delivery in Nairobi and across Kenya.',
                heading: 'iPhone in Kenya - Genuine Apple Devices at Nimtech',
                categoryType: 'phones',
                defaultBrandSlugs: ['apple', 'iphone'],
                keywordTargets: ['iPhone in Kenya', 'iPhone price in Kenya', 'Buy iPhone in Nairobi'],
                brandSummary: 'iPhone is a premium smartphone line known for stable performance, strong cameras, long software support, and smooth ecosystem integration for users who value reliability and resale strength.',
                faq: [
                    [
                        'question' => 'Do you offer iPhone warranty in Kenya?',
                        'answer' => 'Yes. Every iPhone purchase includes clear warranty guidance and support steps if an issue occurs.',
                    ],
                    [
                        'question' => 'Can I compare iPhone models before buying?',
                        'answer' => 'Yes. We help compare storage options, camera capabilities, and performance levels to match your budget.',
                    ],
                    [
                        'question' => 'Do you deliver iPhones outside Nairobi?',
                        'answer' => 'Yes. iPhone orders can be delivered across Kenya through our supported courier options.',
                    ],
                ],
                relatedLinks: [
                    ['label' => 'Phones in Kenya', 'route' => 'seo.phones'],
                    ['label' => 'Samsung Phones Kenya', 'route' => 'seo.samsung-kenya'],
                    ['label' => 'Xiaomi Phones Kenya', 'route' => 'seo.xiaomi-phones-kenya'],
                ],
            ),
            'samsung-kenya' => $this->brandPageDefinition(
                pageSlug: 'samsung-kenya',
                metaTitle: 'Samsung Phones Kenya - Latest Models and Prices',
                metaDescription: 'Shop Samsung phones in Kenya at Nimtech with verified stock, transparent prices, warranty support, and fast delivery in Nairobi and across Kenya.',
                heading: 'Samsung Phones Kenya - Reliable Android Choices',
                categoryType: 'phones',
                defaultBrandSlugs: ['samsung'],
                keywordTargets: ['Samsung phones Kenya', 'Buy Samsung phone in Kenya', 'Samsung Galaxy price Kenya'],
                brandSummary: 'Samsung offers a wide Android lineup from budget to flagship, making it easier for buyers in Kenya to choose a device based on performance, camera quality, and battery expectations.',
                faq: [
                    [
                        'question' => 'Are Samsung phones at Nimtech original?',
                        'answer' => 'Yes. We focus on genuine Samsung phones and provide warranty direction for every eligible model.',
                    ],
                    [
                        'question' => 'Which Samsung series should I choose?',
                        'answer' => 'The right series depends on your budget and use case, from entry-level daily use to high-end camera and performance needs.',
                    ],
                    [
                        'question' => 'Can I order Samsung phones online in Kenya?',
                        'answer' => 'Yes. You can order online and get delivery updates for Nairobi and other locations in Kenya.',
                    ],
                ],
                relatedLinks: [
                    ['label' => 'Phones in Kenya', 'route' => 'seo.phones'],
                    ['label' => 'iPhone in Kenya', 'route' => 'seo.iphone-kenya'],
                    ['label' => 'Xiaomi Phones Kenya', 'route' => 'seo.xiaomi-phones-kenya'],
                ],
            ),
            'hp-laptops-kenya' => $this->brandPageDefinition(
                pageSlug: 'hp-laptops-kenya',
                metaTitle: 'HP Laptops Kenya - Student, Business, and Performance Models',
                metaDescription: 'Buy HP laptops in Kenya at Nimtech with warranty support, practical specs guidance, transparent prices, and fast delivery in Nairobi and across Kenya.',
                heading: 'HP Laptops Kenya - Trusted for School and Business',
                categoryType: 'laptops',
                defaultBrandSlugs: ['hp', 'hewlett-packard'],
                keywordTargets: ['HP laptops Kenya', 'Buy HP laptop in Kenya', 'Affordable HP laptops Nairobi'],
                brandSummary: 'HP laptops are popular in Kenya for balancing price, reliability, and broad model variety across student use, office productivity, and higher-performance workloads.',
                faq: [
                    [
                        'question' => 'Which HP laptop is best for students?',
                        'answer' => 'Budget-friendly HP models with SSD storage and at least 8GB RAM are a practical starting point for student workloads.',
                    ],
                    [
                        'question' => 'Do HP laptops come with warranty?',
                        'answer' => 'Yes. HP laptop purchases include clear warranty terms and support direction after purchase.',
                    ],
                    [
                        'question' => 'Can I get HP laptop delivery in Kenya?',
                        'answer' => 'Yes. We support delivery in Nairobi and many upcountry destinations.',
                    ],
                ],
                relatedLinks: [
                    ['label' => 'Laptops in Kenya', 'route' => 'seo.laptops'],
                    ['label' => 'Dell Laptops Kenya', 'route' => 'seo.dell-laptops-kenya'],
                    ['label' => 'Lenovo Laptops Kenya', 'route' => 'seo.lenovo-laptops-kenya'],
                ],
            ),
            'dell-laptops-kenya' => $this->brandPageDefinition(
                pageSlug: 'dell-laptops-kenya',
                metaTitle: 'Dell Laptops Kenya - Work and Performance Models',
                metaDescription: 'Shop Dell laptops in Kenya at Nimtech with clear specs, transparent prices, warranty support, and delivery in Nairobi and across Kenya.',
                heading: 'Dell Laptops Kenya - Business Stability and Performance',
                categoryType: 'laptops',
                defaultBrandSlugs: ['dell'],
                keywordTargets: ['Dell laptops Kenya', 'Buy Dell laptop Nairobi', 'Best Dell business laptops Kenya'],
                brandSummary: 'Dell laptops are known for durable business lines, reliable performance, and practical upgrade options, making them a strong fit for professionals and students in Kenya.',
                faq: [
                    [
                        'question' => 'Are Dell business laptops available?',
                        'answer' => 'Yes. We list Dell models suitable for office productivity, remote work, and professional tasks.',
                    ],
                    [
                        'question' => 'How do I choose between Inspiron and Latitude?',
                        'answer' => 'Inspiron generally targets mainstream use while Latitude focuses more on business reliability and enterprise features.',
                    ],
                    [
                        'question' => 'Do you provide delivery across Kenya?',
                        'answer' => 'Yes. Dell laptop orders can be delivered to Nairobi and many counties across Kenya.',
                    ],
                ],
                relatedLinks: [
                    ['label' => 'Laptops in Kenya', 'route' => 'seo.laptops'],
                    ['label' => 'HP Laptops Kenya', 'route' => 'seo.hp-laptops-kenya'],
                    ['label' => 'Lenovo Laptops Kenya', 'route' => 'seo.lenovo-laptops-kenya'],
                ],
            ),
            'lenovo-laptops-kenya' => $this->brandPageDefinition(
                pageSlug: 'lenovo-laptops-kenya',
                metaTitle: 'Lenovo Laptops Kenya - ThinkPad, IdeaPad, and More',
                metaDescription: 'Buy Lenovo laptops in Kenya at Nimtech with warranty support, model comparison help, transparent prices, and fast delivery across Kenya.',
                heading: 'Lenovo Laptops Kenya - Practical Value and Productivity',
                categoryType: 'laptops',
                defaultBrandSlugs: ['lenovo'],
                keywordTargets: ['Lenovo laptops Kenya', 'ThinkPad Kenya', 'Affordable Lenovo laptops Nairobi'],
                brandSummary: 'Lenovo gives buyers in Kenya a strong mix of dependable business laptops, student-friendly value models, and gaming-focused configurations.',
                faq: [
                    [
                        'question' => 'Do you stock ThinkPad and IdeaPad models?',
                        'answer' => 'Yes. Lenovo stock may include both productivity-focused and value-focused model families based on availability.',
                    ],
                    [
                        'question' => 'Is Lenovo good for business use?',
                        'answer' => 'Yes. Many Lenovo models are built for stable productivity, keyboard comfort, and long-term reliability.',
                    ],
                    [
                        'question' => 'Can I buy Lenovo laptops online in Kenya?',
                        'answer' => 'Yes. You can order online and receive delivery updates through the checkout process.',
                    ],
                ],
                relatedLinks: [
                    ['label' => 'Laptops in Kenya', 'route' => 'seo.laptops'],
                    ['label' => 'HP Laptops Kenya', 'route' => 'seo.hp-laptops-kenya'],
                    ['label' => 'Dell Laptops Kenya', 'route' => 'seo.dell-laptops-kenya'],
                ],
            ),
            'xiaomi-phones-kenya' => $this->brandPageDefinition(
                pageSlug: 'xiaomi-phones-kenya',
                metaTitle: 'Xiaomi Phones Kenya - Budget and Performance Value',
                metaDescription: 'Shop Xiaomi phones in Kenya at Nimtech with strong value pricing, verified stock, warranty support, and fast delivery in Nairobi and across Kenya.',
                heading: 'Xiaomi Phones Kenya - Strong Specs at Competitive Prices',
                categoryType: 'phones',
                defaultBrandSlugs: ['xiaomi', 'redmi', 'poco'],
                keywordTargets: ['Xiaomi phones Kenya', 'Redmi phones in Kenya', 'Buy Xiaomi Nairobi'],
                brandSummary: 'Xiaomi phones are popular in Kenya for offering strong specifications at competitive prices, making them ideal for users who prioritize value and performance.',
                faq: [
                    [
                        'question' => 'Are Xiaomi phones good for value buyers?',
                        'answer' => 'Yes. Xiaomi devices usually provide strong hardware value within budget and mid-range price bands.',
                    ],
                    [
                        'question' => 'Do Xiaomi phones include warranty support?',
                        'answer' => 'Yes. We provide warranty guidance and support channels for eligible Xiaomi purchases.',
                    ],
                    [
                        'question' => 'Can I get Xiaomi phone delivery in Kenya?',
                        'answer' => 'Yes. Delivery is available in Nairobi and other locations across Kenya.',
                    ],
                ],
                relatedLinks: [
                    ['label' => 'Phones in Kenya', 'route' => 'seo.phones'],
                    ['label' => 'iPhone in Kenya', 'route' => 'seo.iphone-kenya'],
                    ['label' => 'Samsung Phones Kenya', 'route' => 'seo.samsung-kenya'],
                ],
            ),
        ];
    }

    private function brandPageDefinition(
        string $pageSlug,
        string $metaTitle,
        string $metaDescription,
        string $heading,
        string $categoryType,
        array $defaultBrandSlugs,
        array $keywordTargets,
        string $brandSummary,
        array $faq,
        array $relatedLinks
    ): array {
        return [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'heading' => $heading,
            'category_slugs' => $this->categorySlugsForType($categoryType, $this->defaultCategorySlugs($categoryType)),
            'brand_slugs' => $this->brandSlugsForPage($pageSlug, $defaultBrandSlugs),
            'keyword_targets' => $keywordTargets,
            'intro' => [
                $brandSummary,
                'This page gives you a direct path to available models, practical buying guidance, and clear warranty and delivery details before you place an order.',
                'Nimtech is focused on helping Kenyan buyers make informed choices through accurate product information and responsive after-sale support.',
            ],
            'guide_title' => 'What to check before buying',
            'guide_points' => [
                'Confirm your performance needs first, then match them to processor, RAM, and storage.',
                'Compare display size and battery behavior against your daily mobility and usage pattern.',
                'Choose a model with clear long-term support and practical accessory availability.',
                'Review warranty terms and support channels before checkout.',
            ],
            'warranty' => 'All purchases include clear warranty details and escalation support if issues appear after delivery.',
            'delivery' => 'Delivery options are available in Nairobi and across Kenya based on destination.',
            'after_sale' => 'After-sale help includes setup guidance, compatibility checks, and support follow-up.',
            'why_buy' => [
                'Focused stock selection based on demand in Kenya.',
                'Transparent product information and practical model comparison help.',
                'Trusted support channels before and after purchase.',
            ],
            'faq' => $faq,
            'related_links' => $relatedLinks,
        ];
    }
}
