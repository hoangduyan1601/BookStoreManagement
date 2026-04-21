<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\TacGia;
use Illuminate\Support\Facades\DB;

class AssignAuthorsSeeder extends Seeder
{
    public function run(): void
    {
        $products = SanPham::all();
        $authors = TacGia::all();

        if ($authors->isEmpty()) {
            // Nếu chưa có tác giả nào, tạo một vài tác giả mẫu
            $authors = collect([
                TacGia::create(['TenTacGia' => 'Haruki Murakami', 'QuocTich' => 'Nhật Bản']),
                TacGia::create(['TenTacGia' => 'Dale Carnegie', 'QuocTich' => 'Mỹ']),
                TacGia::create(['TenTacGia' => 'Nguyễn Nhật Ánh', 'QuocTich' => 'Việt Nam']),
                TacGia::create(['TenTacGia' => 'Paulo Coelho', 'QuocTich' => 'Brazil']),
                TacGia::create(['TenTacGia' => 'Higashino Keigo', 'QuocTich' => 'Nhật Bản']),
            ]);
        }

        foreach ($products as $product) {
            // Chỉ gán nếu sản phẩm chưa có tác giả
            if ($product->tacgias()->count() === 0) {
                // Gán ngẫu nhiên 1 tác giả
                $randomAuthors = $authors->random(rand(1, min(2, $authors->count())));
                foreach ($randomAuthors as $author) {
                    $product->tacgias()->attach($author->MaTacGia, ['VaiTro' => 'Tác giả chính']);
                }
            }
        }
    }
}
