<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function createProduct(float $price = 1000.0): Product
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => $price,
            'stock' => 10,
        ]);
    }

    public function test_checkout_with_cod_creates_order_and_clears_cart(): void
    {
        Setting::query()->create([
            'site_name' => 'Shoply',
            'currency_code' => 'KES',
            'currency_symbol' => 'KSh',
            'currency_position' => 'left',
            'enable_mpesa' => false,
            'enable_cod' => true,
        ]);
        Setting::clearCache();

        $product = $this->createProduct(1500.0);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->post(route('checkout.process'), [
            'full_name' => 'Test Buyer',
            'email' => 'buyer@example.com',
            'phone' => '0712345678',
            'payment_method' => 'cod',
            'address' => 'Test Street 123',
            'city' => 'Nairobi',
            'state' => 'Nairobi',
            'postal_code' => '00100',
            'billing_same' => 1,
        ]);

        $this->assertEquals(1, Order::count());
        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.success', $order));
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('awaiting_delivery', $order->status);
        $this->assertEquals(3000.0, $order->total);

        $this->assertSame([], session('cart.items', []));
    }

    public function test_checkout_rejects_disabled_mpesa_method(): void
    {
        Setting::query()->create([
            'site_name' => 'Shoply',
            'currency_code' => 'KES',
            'currency_symbol' => 'KSh',
            'currency_position' => 'left',
            'enable_mpesa' => false,
            'enable_cod' => true,
        ]);
        Setting::clearCache();

        $product = $this->createProduct(500.0);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->from(route('checkout.index'))->post(route('checkout.process'), [
            'full_name' => 'Test Buyer',
            'email' => 'buyer@example.com',
            'payment_method' => 'mpesa',
            'address' => 'Test Street 123',
            'city' => 'Nairobi',
            'state' => 'Nairobi',
            'postal_code' => '00100',
            'billing_same' => 1,
        ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHasErrors('payment_method');
        $this->assertEquals(0, Order::count());
    }
}
