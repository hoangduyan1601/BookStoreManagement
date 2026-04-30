<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
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
        $statusMap = [
            'ChoXacNhan' => 'Đang chờ xác nhận',
            'DaXacNhan' => 'Đã xác nhận',
            'DangGiao' => 'Đang giao hàng',
            'DaGiao' => 'Đã giao hàng thành công',
            'DaHuy' => 'Đã hủy',
        ];

        $statusLabel = $statusMap[$this->order->TrangThai] ?? $this->order->TrangThai;

        return (new MailMessage)
                    ->subject('📦 Cập nhật trạng thái đơn hàng #' . $this->order->MaDH)
                    ->greeting('Chào ' . $this->order->khachHang->HoTen . ',')
                    ->line('Đơn hàng của bạn đã có sự thay đổi về trạng thái.')
                    ->line('**Mã đơn hàng:** #' . $this->order->MaDH)
                    ->line('**Trạng thái mới:** ' . $statusLabel)
                    ->line('**Tổng tiền:** ' . number_format($this->order->TongTien, 0, ',', '.') . 'đ')
                    ->action('Xem chi tiết đơn hàng', route('customer.profile')) // Assuming customer can see orders in profile
                    ->line('Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->MaDH,
            'status' => $this->order->TrangThai,
        ];
    }
}
