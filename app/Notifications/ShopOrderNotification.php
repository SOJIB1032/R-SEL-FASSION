<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopOrderNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Order $order,
        protected string $eventName,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'payment_status' => $this->order->payment_status,
            'event' => $this->eventName,
            'message' => $this->eventName.' — order '.$this->order->order_number,
            'url' => $notifiable->role === 'admin'
                ? route('admin.orders.show', $this->order)
                : route('account.orders.show', $this->order),
        ];
    }
}
