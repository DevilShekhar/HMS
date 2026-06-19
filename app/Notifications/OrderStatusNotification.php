<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public $order;
    public $status;

    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'order-status-notification',
            'order_id' => $this->order->id,
            'token_no' => $this->order->token_no,
            'restaurant_slug' => $this->order->restaurant->slug,
            'branch_slug' => $this->order->branch?->slug,
            'message' => "Order {$this->status}",
        ];
    }
}
