@extends('layouts.app')

@section('title', 'Tạp chí tri thức | LUXURY BOOKSTORE')

@section('content')
<section class="container py-24">
    <div class="text-center mb-16 reveal-on-scroll">
        <span class="section-tag">Editorial & Journal</span>
        <h1 class="font-luxury display-3 mb-4">Tạp Chí Tri Thức</h1>
        <p class="text-muted mx-auto fs-5" style="max-width: 700px;">Nơi lưu giữ những câu chuyện về văn chương, nghệ thuật sống và những giá trị tinh hoa vượt thời gian.</p>
    </div>

    <div class="row g-5">
        @foreach($articles as $bv)
        <div class="col-md-4 reveal-on-scroll">
            <article class="article-card h-100 group">
                <div class="overflow-hidden mb-6 position-relative rounded-4 shadow-sm">
                    <img src="{{ $bv->HinhAnh ? (Str::startsWith($bv->HinhAnh, 'http') ? $bv->HinhAnh : asset($bv->HinhAnh)) : 'https://via.placeholder.com/600x400' }}" 
                         class="img-fluid w-100 hover-scale transition-all duration-700" 
                         style="height: 320px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 p-4 bg-white bg-opacity-90 backdrop-blur-sm rounded-tr-4">
                         <span class="extra-small text-dark fw-bold ls-2">{{ \Carbon\Carbon::parse($bv->NgayDang)->translatedFormat('d F, Y') }}</span>
                    </div>
                </div>
                <h3 class="font-luxury h4 mb-4 lh-base">
                    <a href="{{ route('baiviet.show', $bv->Slug) }}" class="text-decoration-none text-dark hover-gold trans-fast">
                        {{ $bv->TieuDe }}
                    </a>
                </h3>
                <p class="text-muted small mb-6 lh-lg">{{ Str::limit($bv->TomTat, 150) }}</p>
                <a href="{{ route('baiviet.show', $bv->Slug) }}" class="text-dark fw-bold text-decoration-none border-bottom border-dark border-2 pb-1 ls-2 extra-small trans-fast hover-gold-border">
                    TIẾP TỤC ĐỌC
                </a>
            </article>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-16 pagination-luxury reveal-on-scroll">
        {{ $articles->links() }}
    </div>
</section>

<style>
    .hover-scale { transition: transform 0.8s var(--ease-out); }
    .article-card:hover .hover-scale { transform: scale(1.08); }
    .hover-gold-border:hover { border-bottom-color: var(--gold-primary) !important; color: var(--gold-primary) !important; }
    .rounded-tr-4 { border-top-right-radius: 1.5rem; }
    .backdrop-blur-sm { backdrop-filter: blur(4px); }
</style>
@endsection
