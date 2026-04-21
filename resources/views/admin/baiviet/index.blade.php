@extends('layouts.admin')

@section('title', 'Quản lý bài viết')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Quản lý bài viết</h3>
    <a href="{{ route('admin.baiviet.create') }}" class="btn btn-primary px-4 rounded-3">
        <i class="fas fa-plus me-2"></i> Thêm bài viết mới
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Hình ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $bv)
                <tr>
                    <td class="ps-4">
                        <img src="{{ $bv->HinhAnh ? asset($bv->HinhAnh) : 'https://via.placeholder.com/80x50' }}" 
                             class="rounded-3" style="width: 80px; height: 50px; object-fit: cover;">
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $bv->TieuDe }}</div>
                        <small class="text-muted">{{ Str::limit($bv->TomTat, 50) }}</small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($bv->NgayDang)->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($bv->TrangThai)
                            <span class="badge bg-success-subtle text-success px-3">Công khai</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary px-3">Bản nháp</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.baiviet.edit', $bv->MaBV) }}" class="btn btn-sm btn-light rounded-3" title="Chỉnh sửa">
                                <i class="fas fa-edit text-primary"></i>
                            </a>
                            <form action="{{ route('admin.baiviet.destroy', $bv->MaBV) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light rounded-3" title="Xóa">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
