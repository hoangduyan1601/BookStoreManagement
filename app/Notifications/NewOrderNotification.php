<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('🔔 Đơn hàng mới #' . $this->order->MaDH)
                    ->greeting('Chào Admin,')
                    ->line('Bạn vừa nhận được một đơn hàng mới từ hệ thống.')
                    ->line('**Mã đơn hàng:** #' . $this->order->MaDH)
                    ->line('**Khách hàng:** ' . $this->order->khachHang->HoTen)
                    ->line('**Tổng tiền:** ' . number_format($this->order->TongTien, 0, ',', '.') . 'đ')
                    ->line('**Phương thức thanh toán:** ' . $this->order->PhuongThucThanhToan)
                    ->action('Xem chi tiết đơn hàng', route('admin.donhang.show', $this->order->MaDH))
                    ->line('Vui lòng kiểm tra và xác nhận đơn hàng sớm nhất có thể.')
                    ->line('Cảm ơn!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
