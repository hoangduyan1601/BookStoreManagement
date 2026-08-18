<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\PaymentTransaction;
use App\Models\ThongBao;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $configuredToken = config('services.payment.webhook_token');
        $providedToken = $request->bearerToken() ?: $request->header('Secure-Token');
        if (! $configuredToken || ! $providedToken || ! hash_equals($configuredToken, $providedToken)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'provider' => 'sometimes|string|max:50',
            'data' => 'required|array|min:1',
            'data.*.transaction_id' => 'required|string|max:191',
            'data.*.amount' => 'required|numeric|min:0',
            'data.*.description' => 'nullable|string|max:500',
            'data.*.content' => 'nullable|string|max:500',
        ]);

        $provider = strtolower($validated['provider'] ?? 'bank-webhook');
        $results = [];

        foreach ($validated['data'] as $transaction) {
            $results[] = $this->processTransaction($provider, $transaction);
        }

        Log::info('Payment webhook processed.', [
            'provider' => $provider,
            'transaction_count' => count($results),
        ]);

        return response()->json(['status' => 'success', 'processed' => $results]);
    }

    private function processTransaction(string $provider, array $transaction): array
    {
        $description = $transaction['description'] ?? ($transaction['content'] ?? '');
        if (! preg_match('/CK\s*(\d+)/i', $description, $matches)) {
            return ['transaction_id' => $transaction['transaction_id'], 'result' => 'order_reference_missing'];
        }

        $orderId = (int) $matches[1];
        $result = DB::transaction(function () use ($provider, $transaction, $orderId): array {
            $order = DonHang::with('khachHang')->lockForUpdate()->find($orderId);
            if (! $order) {
                return ['transaction_id' => $transaction['transaction_id'], 'result' => 'order_not_found'];
            }

            $existing = PaymentTransaction::where('provider', $provider)
                ->where('transaction_id', $transaction['transaction_id'])
                ->first();
            if ($existing) {
                return ['transaction_id' => $transaction['transaction_id'], 'result' => 'already_processed'];
            }

            if ((float) $transaction['amount'] < (float) $order->TongTien) {
                return ['transaction_id' => $transaction['transaction_id'], 'result' => 'amount_mismatch'];
            }

            if (! in_array($order->TrangThai, ['ChoThanhToan', 'ChoXacNhan'], true)) {
                return ['transaction_id' => $transaction['transaction_id'], 'result' => 'order_already_finalized'];
            }

            PaymentTransaction::create([
                'provider' => $provider,
                'transaction_id' => $transaction['transaction_id'],
                'MaDH' => $order->MaDH,
                'amount' => $transaction['amount'],
                'processed_at' => now(),
            ]);

            $order->update([
                'TrangThai' => 'DaXacNhan',
                'SoTienDaThanhToan' => $transaction['amount'],
            ]);

            ThongBao::create([
                'MaKH' => $order->MaKH,
                'TieuDe' => 'Thanh toán thành công!',
                'NoiDung' => "Đơn hàng #{$order->MaDH} đã được ngân hàng xác nhận thanh toán.",
                'NgayGui' => now(),
                'TrangThaiDoc' => false,
                'LoaiTB' => 'DonHang',
                'LienKet' => '/profile',
            ]);

            return ['transaction_id' => $transaction['transaction_id'], 'result' => 'processed', 'order' => $order];
        }, 3);

        if (($result['result'] ?? null) === 'processed') {
            $order = $result['order'];
            unset($result['order']);
            $this->sendNotifications($order);
        }

        return $result;
    }

    private function sendNotifications(DonHang $order): void
    {
        try {
            Notification::route('mail', config('mail.from.address'))
                ->notify(new NewOrderNotification($order));

            if ($order->khachHang?->Email) {
                Notification::route('mail', $order->khachHang->Email)
                    ->notify(new OrderStatusNotification($order));
            }
        } catch (\Throwable $exception) {
            Log::error("Payment notification failed for order #{$order->MaDH}.", [
                'exception' => $exception::class,
            ]);
        }
    }
}
