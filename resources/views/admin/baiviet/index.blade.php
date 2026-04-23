@extends('layouts.admin')

@section('title', 'Content Management - Articles')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
    .article-thumb { width: 80px; height: 50px; border-radius: 0.5rem; object-fit: cover; }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Quản Lý Bài Viết</h2>
            <p class="mb-0 text-white-50">Biên tập nội dung blog và tin tức hệ thống</p>
        </div>
        <a href="{{ route('admin.baiviet.create') }}" class="btn btn-light rounded-pill px-4 fw-bold">
            <i class="fas fa-pen-nib me-2 text-success"></i> Viết Bài Mới
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.baiviet.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Tìm tiêu đề bài viết..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Lọc</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Ảnh</th>
                        <th class="py-3 text-uppercase small fw-bold">Tiêu Đề</th>
                        <th class="py-3 text-uppercase small fw-bold">Ngày Đăng</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Trạng Thái</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articles as $bv)
                    <tr>
                        <td class="ps-4">
                            <img src="{{ $bv->HinhAnh ? asset($bv->HinhAnh) : 'https://via.placeholder.com/80x50' }}" class="article-thumb shadow-sm">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ Str::limit($bv->TieuDe, 60) }}</div>
                            <small class="text-muted">{{ Str::limit(strip_tags($bv->NoiDung), 50) }}</small>
                        </td>
                        <td>
                            <div class="small fw-medium text-dark">{{ date('d/m/Y', strtotime($bv->NgayDang)) }}</div>
                        </td>
                        <td class="text-center">
                            @if($bv->TrangThai)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Công khai</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">Bản nháp</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.baiviet.edit', $bv->MaBV) }}" class="btn btn-sm btn-light rounded-pill px-3 me-2">
                                <i class="fas fa-edit text-warning"></i>
                            </a>
                            <form action="{{ route('admin.baiviet.destroy', $bv->MaBV) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 text-danger" onclick="return confirm('Xóa bài viết?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-light border-top">
            {{ $articles->links() }}
        </div>
    </div>
</div>
@endsection
