<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    protected $gemini;

    public function __construct(\App\Services\GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

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

            // 1.5 Lấy lịch sử 5 tin nhắn gần nhất
            $history = ChatMessage::where(function($q) use ($maKH, $sessionId) {
                if ($maKH) $q->where('MaKH', $maKH);
                else $q->where('session_id', $sessionId);
            })->orderBy('created_at', 'desc')->limit(5)->get()->reverse()->toArray();

            // 2. Lấy phản hồi từ AI thật (Gemini)
            $aiReply = $this->gemini->chat($message, $history, $maKH);

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
            if ($sessionId) $q->orWhere('session_id', $sessionId);
        })->orderBy('created_at', 'asc')->get();

        // Đánh dấu là đã đọc khi lấy lịch sử
        ChatMessage::where(function($q) use ($maKH, $sessionId) {
            if ($maKH) $q->where('MaKH', $maKH);
            if ($sessionId) $q->orWhere('session_id', $sessionId);
        })->whereIn('sender', ['ai', 'admin'])->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function checkUnread()
    {
        $sessionId = session()->getId();
        $maKH = null;
        if (Auth::check()) {
            $kh = KhachHang::where('MaTK', Auth::user()->MaTK)->first();
            $maKH = $kh ? $kh->MaKH : null;
        }

        $count = ChatMessage::where(function($q) use ($maKH, $sessionId) {
            if ($maKH) $q->where('MaKH', $maKH);
            if ($sessionId) $q->orWhere('session_id', $sessionId);
        })->whereIn('sender', ['ai', 'admin'])
          ->where('is_read', false)
          ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function markAsRead()
    {
        $sessionId = session()->getId();
        $maKH = null;
        if (Auth::check()) {
            $kh = KhachHang::where('MaTK', Auth::user()->MaTK)->first();
            $maKH = $kh ? $kh->MaKH : null;
        }

        ChatMessage::where(function($q) use ($maKH, $sessionId) {
            if ($maKH) $q->where('MaKH', $maKH);
            if ($sessionId) $q->orWhere('session_id', $sessionId);
        })->whereIn('sender', ['ai', 'admin'])->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
