<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\KhachHang;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index()
    {
        // Lấy toàn bộ tin nhắn mới nhất để phân nhóm trong PHP cho chính xác
        $allMessages = ChatMessage::orderBy('created_at', 'desc')->get();
        
        $grouped = [];
        foreach ($allMessages as $msg) {
            $identifier = $msg->MaKH ?? $msg->session_id;
            if (!isset($grouped[$identifier])) {
                $grouped[$identifier] = (object)[
                    'identifier' => $identifier,
                    'MaKH' => $msg->MaKH,
                    'session_id' => $msg->session_id,
                    'lastMessage' => $msg,
                    'customer' => $msg->MaKH ? KhachHang::find($msg->MaKH) : null
                ];
            }
        }

        $chats = collect(array_values($grouped));

        return view('admin.chat.index', compact('chats'));
    }

    public function show($identifier)
    {
        // Identifier có thể là MaKH hoặc session_id
        $messages = ChatMessage::where('MaKH', $identifier)
            ->orWhere('session_id', $identifier)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Đánh dấu đã đọc
        ChatMessage::where(function($q) use ($identifier) {
            $q->where('MaKH', $identifier)->orWhere('session_id', $identifier);
        })->whereIn('sender', ['ai', 'admin'])->update(['is_read' => true]);

        // Cũng đánh dấu đã đọc cho phía Admin (tin nhắn từ user)
        ChatMessage::where(function($q) use ($identifier) {
            $q->where('MaKH', $identifier)->orWhere('session_id', $identifier);
        })->where('sender', 'user')->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'identifier' => 'required'
        ]);

        $identifier = $request->identifier;
        $maKH = is_numeric($identifier) ? $identifier : null;
        $sessionId = !is_numeric($identifier) ? $identifier : null;

        // Cố gắng tìm thông tin còn thiếu để đồng bộ thông báo
        if ($maKH && !$sessionId) {
            $sessionId = ChatMessage::where('MaKH', $maKH)->whereNotNull('session_id')->orderBy('created_at', 'desc')->value('session_id');
        } elseif (!$maKH && $sessionId) {
            $maKH = ChatMessage::where('session_id', $sessionId)->whereNotNull('MaKH')->value('MaKH');
        }

        $msg = ChatMessage::create([
            'MaKH' => $maKH,
            'session_id' => $sessionId,
            'message' => $request->message,
            'sender' => 'admin'
        ]);

        return response()->json($msg);
    }
}
