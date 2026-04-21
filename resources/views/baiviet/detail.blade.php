@extends('layouts.app')

@section('title', $article->TieuDe . ' | LUXURY BOOKSTORE')

@section('content')
<article class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb justify-content-center small text-uppercase ls-2">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted text-decoration-none">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('baiviet.index') }}" class="text-muted text-decoration-none">Bài viết</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $article->TieuDe }}</li>
                    </ol>
                </nav>
                <h1 class="font-luxury display-4 mb-4">{{ $article->TieuDe }}</h1>
                <div class="d-flex align-items-center justify-content-center gap-3 text-muted small text-uppercase ls-1">
                    <span>{{ \Carbon\Carbon::parse($article->NgayDang)->translatedFormat('d F, Y') }}</span>
                    <span>•</span>
                    <span>By Luxury Editorial</span>
                </div>
            </div>

            <div class="mb-5 overflow-hidden shadow-2xl">
                <img src="{{ $article->HinhAnh ? (Str::startsWith($article->HinhAnh, 'http') ? $article->HinhAnh : asset($article->HinhAnh)) : 'https://via.placeholder.com/1200x600' }}" 
                     class="img-fluid w-100" style="max-height: 500px; width:100%; object-fit: cover;">
            </div>

            <div class="article-content fs-5 lh-lg text-dark">
                {!! $article->NoiDung !!}
            </div>

            <hr class="my-5 opacity-25">

            <div class="related-articles py-4">
                <h4 class="font-luxury mb-4">Các bài viết liên quan</h4>
                <div class="row g-4">
                    @foreach($relatedArticles as $rel)
                    <div class="col-md-4">
                        <div class="overflow-hidden mb-3">
                            <img src="{{ $rel->HinhAnh ? (Str::startsWith($rel->HinhAnh, 'http') ? $rel->HinhAnh : asset($rel->HinhAnh)) : 'https://via.placeholder.com/400x250' }}" 
                                 class="img-fluid w-100 hover-scale transition-all" style="height: 150px; object-fit: cover;">
                        </div>
                        <h6 class="fw-bold small">
                            <a href="{{ route('baiviet.show', $rel->Slug) }}" class="text-decoration-none text-dark hover-gold">
                                {{ $rel->TieuDe }}
                            </a>
                        </h6>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</article>

<style>
    .article-content p { margin-bottom: 1.5rem; text-align: justify; }
    .ls-1 { letter-spacing: 1px; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
    .hover-scale:hover { transform: scale(1.05); }
</style>
@endsection
