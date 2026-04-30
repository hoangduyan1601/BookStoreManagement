<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\KhachHang;

class GeminiService
{
    protected ?string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * @param string $message
     * @param array $history
     * @param int|null $maKH
     * @return string
     */
    public function chat($message, array $history = [], $maKH = null)
    {
        if (!$this->apiKey) {
            return "Hệ thống chưa cấu hình API Key cho AI. Vui lòng liên hệ quản trị viên.";
        }

        // 1. Chuẩn bị ngữ cảnh cá nhân hóa
        $userProfile = $this->getUserProfile($maKH);

        // 2. Chuẩn bị ngữ cảnh hệ thống
        $systemInstruction = "Bạn là 'Luxury Assistant', trợ lý AI cao cấp của Luxury BookStore.
        
        PHONG CÁCH PHẢN HỒI:
        - Ngắn gọn, súc tích, chuyên nghiệp.
        - Xưng 'Tôi', gọi 'Quý khách'.
        
        NGUỒN DỮ LIỆU CÁ NHÂN HÓA:
        Bạn có quyền truy cập vào sở thích và lịch sử của Quý khách dưới đây:
        $userProfile
        
        NHIỆM VỤ:
        - Sử dụng hồ sơ cá nhân hóa để đưa ra các gợi ý 'đúng gu' Quý khách một cách tinh tế.
        - Luôn hiển thị thông tin khuyến mãi nếu sản phẩm đang được giảm giá.
        - Nếu Quý khách chưa có lịch sử, hãy gợi ý những sách bán chạy nhất hiện nay.";

        // 3. Tra cứu dữ liệu bổ sung (RAG)
        $contextData = $this->getRelevantData($message, $maKH);

        // 4. Xây dựng prompt
        $historyContext = "";
        foreach(array_slice($history, -5) as $h) {
            $role = $h['sender'] == 'user' ? 'Khách' : 'AI';
            $historyContext .= "{$role}: {$h['message']}\n";
        }

        $prompt = "--- HỒ SƠ QUÝ KHÁCH ---\n" . $userProfile . "\n\n" .
                  "--- NGỮ CẢNH HỆ THỐNG ---\n" . $contextData . "\n\n" .
                  "--- LỊCH SỬ GẦN ĐÂY ---\n" . $historyContext . "\n\n" .
                  "--- CÂU HỎI HIỆN TẠI ---\n" . $message;

        try {
            $response = Http::post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $systemInstruction . "\n\n" . $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Tôi đang suy nghĩ, Quý khách vui lòng đợi chút nhé.";
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Xin lỗi, tôi gặp chút gián đoạn khi kết nối với máy chủ AI. Quý khách vui lòng thử lại sau.";
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return "Hệ thống AI đang được bảo trì để phục vụ Quý khách tốt hơn.";
        }
    }

    /**
     * @param int|null $maKH
     * @return string
     */
    private function getUserProfile($maKH)
    {
        if (!$maKH) return "Khách vãng lai (Chưa có lịch sử).";

        $profile = "Hồ sơ hành vi của Quý khách:\n";

        // 1. Sản phẩm yêu thích
        $favorites = \App\Models\YeuThich::where('MaKH', $maKH)
            ->with('sanPham')
            ->limit(5)
            ->get();
        if ($favorites->count() > 0) {
            $profile .= "- Sách quan tâm/Yêu thích: " . $favorites->map(fn($f) => $f->sanPham->TenSP)->implode(', ') . ".\n";
        }

        // 2. Danh mục hay mua/quan tâm
        $boughtCats = \App\Models\DonHang::where('MaKH', $maKH)
            ->join('chitietdonhang', 'donhang.MaDH', '=', 'chitietdonhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->join('danhmuc', 'sanpham.MaDM', '=', 'danhmuc.MaDM')
            ->select('danhmuc.TenDM', DB::raw('count(*) as count'))
            ->groupBy('danhmuc.TenDM')
            ->orderBy('count', 'desc')
            ->limit(2)
            ->get();
        if ($boughtCats->count() > 0) {
            $profile .= "- Thể loại ưu thích (dựa trên mua sắm): " . $boughtCats->pluck('TenDM')->implode(', ') . ".\n";
        }

        // 3. Đơn hàng gần đây nhất
        $lastOrder = DonHang::where('MaKH', $maKH)->orderBy('NgayDat', 'desc')->first();
        if ($lastOrder) {
            $profile .= "- Trạng thái đơn hàng gần nhất (#{$lastOrder->MaDH}): {$lastOrder->TrangThai}.\n";
        }

        return $profile;
    }

    /**
     * @param string $message
     * @param int|null $maKH
     * @return string
     */
    private function getRelevantData($message, $maKH)
    {
        $msg = mb_strtolower($message);
        $context = "";

        // 1. Sách mới nhất
        if (str_contains($msg, 'mới') || str_contains($msg, 'vừa về')) {
            $newBooks = SanPham::orderBy('NgayCapNhat', 'desc')->limit(3)->get();
            $context .= "Sách mới về: " . $newBooks->map(function($b) {
                $priceInfo = number_format($b->DonGia) . "đ";
                if ($b->gia_hien_tai < $b->DonGia) {
                    $priceInfo = "Đang giảm còn " . number_format($b->gia_hien_tai) . "đ (Giá gốc " . number_format($b->DonGia) . "đ)";
                }
                return "{$b->TenSP} ($priceInfo)";
            })->implode(', ') . ".\n";
        }

        // 2. Sách bán chạy / hot
        if (str_contains($msg, 'bán chạy') || str_contains($msg, 'hot') || str_contains($msg, 'hay nhất') || str_contains($msg, 'gợi ý')) {
            $hotBooks = SanPham::orderBy('SoLuongDaBan', 'desc')->limit(3)->get();
            $context .= "Sách đang hot/bán chạy: " . $hotBooks->map(function($b) {
                $priceInfo = number_format($b->DonGia) . "đ";
                if ($b->gia_hien_tai < $b->DonGia) {
                    $priceInfo = "Giảm sốc: " . number_format($b->gia_hien_tai) . "đ";
                }
                return "{$b->TenSP} ($priceInfo)";
            })->implode(', ') . ".\n";
        }

        // 3. Tìm kiếm theo tên sách hoặc nội dung
        $products = SanPham::where('TenSP', 'like', "%$msg%")
            ->orWhere('MoTa', 'like', "%$msg%")
            ->limit(5)->get();
        if ($products->count() > 0) {
            $context .= "Kết quả tìm kiếm sản phẩm:\n";
            foreach ($products as $p) {
                $priceLine = "Giá: " . number_format($p->DonGia) . " VNĐ";
                if ($p->gia_hien_tai < $p->DonGia) {
                    $km = $p->khuyen_mai_active;
                    $priceLine = "Giá Ưu Đãi: " . number_format($p->gia_hien_tai) . " VNĐ (Giảm {$km->PhanTramGiam}% từ giá gốc " . number_format($p->DonGia) . " VNĐ)";
                }
                $context .= "- {$p->TenSP}. $priceLine. Tình trạng: " . ($p->SoLuong > 0 ? 'Còn hàng' : 'Hết hàng') . ". Mô tả: " . Str::limit($p->MoTa, 100) . "\n";
            }
        }

        // 4. Danh mục sách
        if (str_contains($msg, 'danh mục') || str_contains($msg, 'thể loại')) {
            $cats = \App\Models\DanhMuc::limit(10)->pluck('TenDM')->toArray();
            $context .= "Các thể loại sách: " . implode(', ', $cats) . ".\n";
        }

        // 5. Khuyến mãi (Đang diễn ra và Sắp diễn ra)
        if (str_contains($msg, 'khuyến mãi') || str_contains($msg, 'giảm giá') || str_contains($msg, 'voucher') || str_contains($msg, 'ưu đãi')) {
            $now = now();
            
            // Khuyến mãi đang áp dụng
            $activePromos = \App\Models\KhuyenMai::where('NgayBatDau', '<=', $now)
                ->where('NgayKetThuc', '>=', $now)
                ->get();
                
            if ($activePromos->count() > 0) {
                $context .= "CÁC CHƯƠNG TRÌNH ĐANG DIỄN RA:\n";
                foreach ($activePromos as $pm) {
                    $dateRange = "Từ " . date('d/m', strtotime($pm->NgayBatDau)) . " đến " . date('d/m', strtotime($pm->NgayKetThuc));
                    $context .= "- {$pm->TenKM}: Giảm {$pm->PhanTramGiam}%" . ($pm->MaGiamGia ? " (Nhập mã: '{$pm->MaGiamGia}')" : "") . ". Thời gian: $dateRange.\n";
                }
            }

            // Khuyến mãi sắp diễn ra (trong vòng 7 ngày tới)
            $upcomingPromos = \App\Models\KhuyenMai::where('NgayBatDau', '>', $now)
                ->where('NgayBatDau', '<=', $now->copy()->addDays(7))
                ->get();
                
            if ($upcomingPromos->count() > 0) {
                $context .= "\nƯU ĐÃI SẮP RA MẮT (Đừng bỏ lỡ):\n";
                foreach ($upcomingPromos as $up) {
                    $startDate = date('d/m', strtotime($up->NgayBatDau));
                    $context .= "- {$up->TenKM}: Ưu đãi {$up->PhanTramGiam}%. Bắt đầu từ ngày $startDate.\n";
                }
            }

            if ($activePromos->count() == 0 && $upcomingPromos->count() == 0) {
                $context .= "Hiện tại chưa có chương trình khuyến mãi mới, nhưng Quý khách có thể theo dõi để nhận tin sớm nhất.\n";
            }
        }

        return $context;
    }
}
