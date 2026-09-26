@php($settings = \App\Models\Setting::getCached())
<!DOCTYPE html>
<html>
  <body style="font-family: Arial, Helvetica, sans-serif; color:#111; line-height:1.5;">
    <h2 style="margin:0 0 10px;">Thank you for your order!</h2>
    <p style="margin:0 0 10px;">Order <strong>#{{ $order->id }}</strong> has been placed successfully.</p>
    <p style="margin:0 0 10px;">Status: <strong>{{ ucfirst($order->status) }}</strong></p>

    <h3 style="margin:20px 0 8px;">Order Summary</h3>
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
      <thead>
        <tr>
          <th align="left" style="border-bottom:1px solid #ddd;">Product</th>
          <th align="right" style="border-bottom:1px solid #ddd;">Qty</th>
          <th align="right" style="border-bottom:1px solid #ddd;">Price</th>
          <th align="right" style="border-bottom:1px solid #ddd;">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td style="border-bottom:1px solid #eee;">{{ $item->product?->name ?? ('Product #'.$item->product_id) }}</td>
          <td align="right" style="border-bottom:1px solid #eee;">{{ $item->quantity }}</td>
          <td align="right" style="border-bottom:1px solid #eee;">{{ currency_format($item->price) }}</td>
          <td align="right" style="border-bottom:1px solid #eee;">{{ currency_format($item->line_total) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <p style="margin:10px 0 0;">Total: <strong>{{ currency_format($order->total) }}</strong></p>

    <p style="margin:20px 0 0;">You can view this order anytime from your account:</p>
    <p style="margin:6px 0 20px;"><a href="{{ route('account.orders.show', $order) }}">View Order #{{ $order->id }}</a> (login required)</p>

    <p style="margin:0; color:#555;">— {{ $settings->site_name ?? config('app.name', 'Shoply') }}</p>
  </body>
  </html>
