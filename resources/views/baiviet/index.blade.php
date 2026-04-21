@extends('layouts.app')

@section('title', 'Tạp chí tri thức | LUXURY BOOKSTORE')

@section('content')
<section class="container py-5">
    <div class="text-center mb-5">
        <span class="section-tag">Editorial & Journal</span>
        <h1 class="font-luxury display-3">Tạp Chí Tri Thức</h1>
        <p class="text-muted mx-auto" style="max-width: 600px;">Nơi lưu giữ những câu chuyện về văn chương, nghệ thuật sống và những giá trị tinh hoa vượt thời gian.</p>
    </div>

    <div class="row g-5">
        @foreach($articles as $bv)
        <div class="col-md-4">
            <article class="article-card">
                <div class="overflow-hidden mb-4 position-relative">
                    <img src="{{ $bv->HinhAnh ? (Str::startsWith($bv->HinhAnh, 'http') ? $bv->HinhAnh : asset($bv->HinhAnh)) : 'https://via.placeholder.com/600x400' }}" 
                         class="img-fluid w-100 hover-scale transition-all" 
                         style="height: 300px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 p-3 bg-white bg-opacity-75">
                         <span class="extra-small text-dark ls-2">{{ \Carbon\Carbon::parse($bv->NgayDang)->format('d/m/Y') }}</span>
                    </div>
                </div>
                <h3 class="font-luxury h4 mb-3">
                    <a href="{{ route('baiviet.show', $bv->Slug) }}" class="text-decoration-none text-dark hover-gold">
                        {{ $bv->TieuDe }}
                    </a>
                </h3>
                <p class="text-muted small mb-4">{{ Str::limit($bv->TomTat, 120) }}</p>
                <a href="{{ route('baiviet.show', $bv->Slug) }}" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1 ls-2" style="font-size: 0.7rem;">
                    ĐỌC TIẾP
                </a>
            </article>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-5 pagination-luxury">
        {{ $articles->links('pagination::bootstrap-5') }}
    </div>
</section>

<style>
    .hover-scale { transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1); }
    .article-card:hover .hover-scale { transform: scale(1.05); }
    .ls-2 { letter-spacing: 2px; }
    .extra-small { font-size: 0.65rem; font-weight: 700; }
    .pagination-luxury .page-link { border: none; color: #1a1a1a; font-weight: 700; }
    .pagination-luxury .page-item.active .page-link { background-color: var(--gold-primary); color: white; }
</style>
@endsection
