@extends('layouts.admin')

@section('title', 'Chỉnh sửa bài viết')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.baiviet.index') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
    </a>
    <h3 class="fw-bold mt-2">Chỉnh sửa bài viết</h3>
</div>

<form action="{{ route('admin.baiviet.update', $article->MaBV) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề bài viết</label>
                    <input type="text" name="TieuDe" class="form-control rounded-3" value="{{ old('TieuDe', $article->TieuDe) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tóm tắt</label>
                    <textarea name="TomTat" class="form-control rounded-3" rows="3">{{ old('TomTat', $article->TomTat) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung chi tiết</label>
                    <textarea name="NoiDung" id="editor" class="form-control rounded-3" rows="15">{{ old('NoiDung', $article->NoiDung) }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">Ảnh đại diện hiện tại</label>
                    @if($article->HinhAnh)
                        <img src="{{ asset($article->HinhAnh) }}" class="img-fluid rounded-3 mb-3 shadow-sm" style="max-height: 200px; width: 100%; object-fit: cover;">
                    @endif
                    <input type="file" name="HinhAnh" class="form-control rounded-3 mb-2">
                    <small class="text-muted">Chọn ảnh mới nếu muốn thay đổi. Tối đa 2MB.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <select name="TrangThai" class="form-select rounded-3">
                        <option value="1" {{ $article->TrangThai ? 'selected' : '' }}>Công khai</option>
                        <option value="0" {{ !$article->TrangThai ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                </div>

                <hr class="opacity-50">

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 rounded-3">
                        <i class="fas fa-save me-2"></i> Cập nhật bài viết
                    </button>
                    <a href="{{ route('admin.baiviet.index') }}" class="btn btn-light py-2 rounded-3">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
