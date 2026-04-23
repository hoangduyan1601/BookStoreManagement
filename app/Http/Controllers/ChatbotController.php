<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');
        
        // Đảm bảo session bắt đầu để lấy ID ổn định
        if (!$request->session()->has('chat_started')) {
            $request->session()->put('chat_started', true);
        }
        
        $sessionId = $request->session()->getId();
        $maKH = null;

        if (Auth::check()) {
            $kh = KhachHang::where('MaTK', Auth::user()->MaTK)->first();
            $maKH = $kh ? $kh->MaKH : null;
        }

        try {
            // 1. Lưu tin nhắn của người dùng
            ChatMessage::create([
                'MaKH' => $maKH,
                'session_id' => $sessionId,
                'message' => $message,
                'sender' => 'user'
            ]);

            // 2. Lấy phản hồi từ AI
            $aiReply = $this->getAIResponse($message);

            // 3. Lưu phản hồi của AI
            ChatMessage::create([
                'MaKH' => $maKH,
                'session_id' => $sessionId,
                'message' => $aiReply,
                'sender' => 'ai'
            ]);

            return response()->json([
                'reply' => $aiReply
            ]);
        } catch (\Exception $e) {
            \Log::error('Chatbot Error: ' . $e->getMessage());
            return response()->json(['reply' => 'Hệ thống đang bận, vui lòng thử lại sau.'], 500);
        }
    }

    private function getAIResponse($message)
    {
        $message = mb_strtolower($message);
        if (str_contains($message, 'chào')) return "Chào bạn! Chúc bạn một ngày tốt lành.";
        if (str_contains($message, 'giá')) return "Giá sản phẩm được hiển thị ngay bên cạnh nút 'Mua ngay' bạn nhé.";
        if (str_contains($message, 'nhân viên') || str_contains($message, 'gặp người')) return "Dạ, tôi đã chuyển yêu cầu đến nhân viên hỗ trợ. Vui lòng đợi trong giây lát!";
        
        return "Tôi là Trợ lý ảo Luxury. Bạn có câu hỏi nào về sách hay dịch vụ không?";
    }

    // Lấy lịch sử chat (Để khách xem lại sau khi load trang)
    public function getHistory()
    {
        $sessionId = session()->getId();
        $maKH = null;
        if (Auth::check()) {
            $kh = KhachHang::where('MaTK', Auth::user()->MaTK)->first();
            $maKH = $kh ? $kh->MaKH : null;
        }

        $messages = ChatMessage::where(function($q) use ($maKH, $sessionId) {
            if ($maKH) $q->where('MaKH', $maKH);
            else $q->where('session_id', $sessionId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
