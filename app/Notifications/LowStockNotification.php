<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public $item;

    public function __construct(InventoryItem $item)
    {
        $this->item = $item;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'low-stock',
            'inventory_id' => $this->item->id,
            'item_name' => $this->item->name,
            'remaining_stock' => $this->item->remaining_stock,
            'minimum_stock' => $this->item->minimum_stock,
            'branch_id' => $this->item->branch_id,
            'message' => "{$this->item->name} stock is running low."
        ];
    }
}
