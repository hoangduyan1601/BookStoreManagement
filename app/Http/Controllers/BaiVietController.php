<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaiViet;

class BaiVietController extends Controller
{
    public function index()
    {
        $articles = BaiViet::where('TrangThai', true)->orderBy('NgayDang', 'desc')->paginate(9);
        return view('baiviet.index', compact('articles'));
    }

    public function show($slug)
    {
        $article = BaiViet::where('Slug', $slug)->where('TrangThai', true)->firstOrFail();
        $relatedArticles = BaiViet::where('MaBV', '!=', $article->MaBV)
            ->where('TrangThai', true)
            ->limit(3)
            ->get();
            
        return view('baiviet.detail', compact('article', 'relatedArticles'));
    }
}
