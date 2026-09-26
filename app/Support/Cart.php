<?php
namespace App\Support;

use App\Models\Product;
use App\Models\Setting;

class Cart {
    protected const KEY = 'cart.items';
    protected const COUPON_KEY = 'cart.coupon';

    public static function all(): array { return session(self::KEY, []); }

    public static function count(): int {
        return (int) collect(self::details()['lines'] ?? [])->sum('quantity');
    }

    public static function add(int $productId, int $qty = 1): void {
        $items = self::all();
        $items[$productId] = ($items[$productId] ?? 0) + $qty;
        session([self::KEY => $items]);
    }

    public static function update(int $productId, int $qty): void {
        $items = self::all();
        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = $qty;
        }
        session([self::KEY => $items]);
    }

    public static function remove(int $productId): void {
        $items = self::all();
        unset($items[$productId]);
        session([self::KEY => $items]);
    }

    public static function clear(): void { session()->forget(self::KEY); }

    public static function details(): array {
        $items = self::all();
        if (empty($items)) {
            return ['lines' => [], 'subtotal' => 0, 'discount' => 0, 'shipping' => 0, 'tax' => 0, 'total' => 0, 'coupon' => self::coupon()];
        }
        $products = Product::whereIn('id', array_keys($items))->get();
        $validProductIds = $products->pluck('id')->map(fn ($id) => (int) $id)->all();
        $validItems = array_intersect_key($items, array_flip($validProductIds));
        if (count($validItems) !== count($items)) {
            session([self::KEY => $validItems]);
            $items = $validItems;
        }
        $lines = [];
        $subtotal = 0;
        foreach ($products as $p) {
            $qty = $items[$p->id] ?? 0;
            if ($qty <= 0) continue;
            $line = [
                'product' => $p,
                'quantity' => $qty,
                'price' => $p->price,
                'line_total' => $p->price * $qty,
            ];
            $subtotal += $line['line_total'];
            $lines[] = $line;
        }
        $discount = self::computeDiscount($subtotal);

        $shipping = 0.0;
        $tax = 0.0;
        $settings = Setting::getCached();
        if ($settings) {
            if ($settings->shipping_flat_rate !== null) {
                $shipping = (float) $settings->shipping_flat_rate;
            }
            if ($settings->tax_rate !== null) {
                $rate = (float) $settings->tax_rate;
                if ($rate > 0) {
                    $taxBase = max(0, $subtotal - $discount);
                    $tax = round($taxBase * ($rate / 100), 2);
                }
            }
        }

        $total = max(0, $subtotal - $discount + $shipping + $tax);
        return [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'coupon' => self::coupon(),
        ];
    }

    public static function coupon(): ?array
    {
        return session(self::COUPON_KEY);
    }

    // Very simple demo coupon validation. In production, back this with a DB table.
    public static function applyCoupon(string $code): ?array
    {
        $code = strtoupper(trim($code));
        $promotion = \App\Models\Promotion::whereRaw('UPPER(code) = ?', [$code])->first();
        if(!$promotion || !$promotion->isActive()) return null;
        $coupon = [
            'code' => $promotion->code,
            'type' => $promotion->type,
            'value' => $promotion->value,
            'promotion_id' => $promotion->id,
            'label' => $promotion->type === 'percent' ? ($promotion->value.'% off') : ('KES '.number_format($promotion->value,2).' off'),
            'min_subtotal' => $promotion->min_subtotal,
        ];
        session([self::COUPON_KEY => $coupon]);
        return $coupon;
    }

    public static function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    protected static function computeDiscount(float $subtotal): float
    {
        $c = self::coupon();
        if (!$c) return 0.0;
        if (!empty($c['min_subtotal']) && $subtotal < (float) $c['min_subtotal']) {
            return 0.0;
        }
        if ($c['type'] === 'percent') {
            return round($subtotal * ($c['value'] / 100), 2);
        }
        if ($c['type'] === 'amount') {
            return min($subtotal, (float) $c['value']);
        }
        return 0.0;
    }
}
