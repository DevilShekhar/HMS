<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderAssignedNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'order-notification',
            'order_id'      => $this->order->id,
            'token_no'      => $this->order->token_no,
            'customer_name' => $this->order->customer_name,
            'message'       => 'New Order Assigned',
        ];
    }
}
