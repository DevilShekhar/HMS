<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPreparedNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'order-prepared',
            'order_id' => $this->order->id,
            'token_no' => $this->order->token_no,
            'customer_name' => $this->order->customer_name,
            'restaurant_slug' => $this->order->restaurant->slug,
            'branch_slug' => $this->order->branch?->slug,
            'message' => 'Order is Prepared',
            'status' => 'prepared',
        ];
    }
}
