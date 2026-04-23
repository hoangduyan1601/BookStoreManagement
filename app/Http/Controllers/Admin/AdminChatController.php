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
        // Lấy danh sách các cuộc hội thoại gần nhất
        // Group theo session_id hoặc MaKH
        $chats = ChatMessage::select('session_id', 'MaKH', \DB::raw('max(created_at) as last_msg'))
            ->groupBy('session_id', 'MaKH')
            ->orderBy('last_msg', 'desc')
            ->get();

        foreach ($chats as $chat) {
            $chat->lastMessage = ChatMessage::where('session_id', $chat->session_id)
                ->where('MaKH', $chat->MaKH)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($chat->MaKH) {
                $chat->customer = KhachHang::find($chat->MaKH);
            }
        }

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
        })->update(['is_read' => true]);

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

        $msg = ChatMessage::create([
            'MaKH' => $maKH,
            'session_id' => $sessionId,
            'message' => $request->message,
            'sender' => 'admin'
        ]);

        return response()->json($msg);
    }
}
