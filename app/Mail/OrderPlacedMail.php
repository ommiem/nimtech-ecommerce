<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing('items.product');
    }

    public function build(): self
    {
        $site = \App\Models\Setting::getCached();
        $subject = ($site->site_name ?? config('app.name', 'Shoply')).' - Order #'.$this->order->id.' placed';

        return $this->subject($subject)
            ->view('emails.orders.placed');
    }
}

