<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = trim((string) $request->query('q', ''));

        $query = Order::query()->with('user')->orderByDesc('id');

        if ($status) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('id', $q)
                   ->orWhere('full_name', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('mpesa_phone', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders', 'status', 'q'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function markPaid(Request $request, Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('error', 'Order is already marked as paid.');
        }

        $data = $request->validate([
            'receipt' => ['required','string','max:100'],
            'amount' => ['nullable','numeric','min:0'],
            'phone' => ['nullable','string','max:20'],
        ]);

        $order->update([
            'status' => 'paid',
            'mpesa_receipt' => $data['receipt'],
            'mpesa_amount' => $data['amount'] ?? $order->total,
            'mpesa_phone' => $data['phone'] ?? $order->mpesa_phone,
            'payment_method' => $order->payment_method ?? 'mpesa',
            'paid_at' => now(),
        ]);
        if ($order->promotion_id) {
            \App\Models\Promotion::where('id', $order->promotion_id)->increment('used');
        }

        return back()->with('success', 'Order marked as paid.');
    }

    public function markAwaitingDelivery(Order $order)
    {
        if ($this->isLocked($order)) {
            return back()->with('error', 'Cannot move a paid order.');
        }

        $order->update(['status' => 'awaiting_delivery']);

        return back()->with('success', 'Order moved to awaiting delivery.');
    }

    public function markPending(Order $order)
    {
        if ($this->isLocked($order)) {
            return back()->with('error', 'Cannot move a paid order.');
        }

        $order->update(['status' => 'pending', 'paid_at' => null]);

        return back()->with('success', 'Order moved to pending.');
    }

    public function markProcessing(Order $order)
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Cannot move a cancelled order.');
        }
        $order->update(['status' => 'processing']);

        return back()->with('success', 'Order marked as processing.');
    }

    public function markShipped(Order $order)
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Cannot move a cancelled order.');
        }
        $order->update(['status' => 'shipped']);

        return back()->with('success', 'Order marked as shipped.');
    }

    public function markDelivered(Order $order)
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Cannot deliver a cancelled order.');
        }

        $order->update(['status' => 'delivered']);

        return back()->with('success', 'Order marked as delivered.');
    }

    public function markFailed(Order $order)
    {
        if ($this->isLocked($order, true)) {
            return back()->with('error', 'Cannot mark this order as failed.');
        }

        $order->update([
            'status' => 'failed',
            'paid_at' => null,
        ]);

        return back()->with('success', 'Order marked as failed.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('error', 'Cannot cancel a paid order.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,awaiting_delivery,processing,shipped,delivered,paid,failed,cancelled'],
            'receipt' => ['required_if:status,paid', 'nullable', 'string', 'max:100'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Prevent moving paid orders to another status
        // Prevent changing cancelled orders (except re-cancelling)
        if ($order->status === 'cancelled' && $data['status'] !== 'cancelled') {
            return back()->with('error', 'Cannot change a cancelled order.');
        }

        // Prevent moving delivered orders back
        if ($order->status === 'delivered' && $data['status'] !== 'delivered') {
            return back()->with('error', 'Cannot change a delivered order.');
        }

        switch ($data['status']) {
            case 'paid':
                if ($order->status === 'paid') {
                    return back()->with('error', 'Order is already marked as paid.');
                }
                $shouldIncrementPromo = $order->promotion_id && $order->status !== 'paid';
                $order->update([
                    'status' => 'paid',
                    'mpesa_receipt' => $data['receipt'],
                    'mpesa_amount' => $data['amount'] ?? $order->total,
                    'mpesa_phone' => $data['phone'] ?? $order->mpesa_phone ?? $order->phone,
                    'payment_method' => $order->payment_method ?? 'mpesa',
                    'paid_at' => now(),
                ]);
                if ($shouldIncrementPromo) {
                    \App\Models\Promotion::where('id', $order->promotion_id)->increment('used');
                }
                $message = 'Order marked as paid.';
                break;
            case 'awaiting_delivery':
                if ($order->status === 'paid') {
                    return back()->with('error', 'Cannot move a paid order to awaiting delivery.');
                }
                if ($order->status === 'delivered') {
                    return back()->with('error', 'Cannot move a delivered order.');
                }
                $order->update(['status' => 'awaiting_delivery', 'paid_at' => null]);
                $message = 'Order moved to awaiting delivery.';
                break;
            case 'pending':
                if ($order->status === 'paid') {
                    return back()->with('error', 'Cannot move a paid order to pending.');
                }
                if ($order->status === 'delivered') {
                    return back()->with('error', 'Cannot move a delivered order.');
                }
                $order->update(['status' => 'pending', 'paid_at' => null]);
                $message = 'Order moved to pending.';
                break;
            case 'processing':
                if ($order->status === 'cancelled' || $order->status === 'delivered') {
                    return back()->with('error', 'Cannot move this order to processing.');
                }
                $order->update(['status' => 'processing']);
                $message = 'Order moved to processing.';
                break;
            case 'shipped':
                if ($order->status === 'cancelled' || $order->status === 'delivered') {
                    return back()->with('error', 'Cannot move this order to shipped.');
                }
                $order->update(['status' => 'shipped']);
                $message = 'Order marked as shipped.';
                break;
            case 'delivered':
                if ($order->status === 'cancelled') {
                    return back()->with('error', 'Cannot deliver a cancelled order.');
                }
                $order->update(['status' => 'delivered']);
                $message = 'Order marked as delivered.';
                break;
            case 'failed':
                if ($this->isLocked($order, true)) {
                    return back()->with('error', 'Cannot mark this order as failed.');
                }
                $order->update(['status' => 'failed', 'paid_at' => null]);
                $message = 'Order marked as failed.';
                break;
            case 'cancelled':
                if ($order->status === 'paid') {
                    return back()->with('error', 'Cannot cancel a paid order.');
                }
                $order->update(['status' => 'cancelled', 'paid_at' => null]);
                $message = 'Order cancelled.';
                break;
            default:
                $message = 'Status updated.';
        }

        return back()->with('success', $message);
    }

    private function isLocked(Order $order, bool $includeCancelled = false): bool
    {
        if ($order->status === 'paid') {
            return true;
        }

        if ($includeCancelled && $order->status === 'cancelled') {
            return true;
        }

        return false;
    }
}
