@extends('layouts.admin')

@section('title', 'Sửa Sản Phẩm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-4-top">
                    <h2 class="card-title mb-0 h4"><i class="fas fa-edit me-2"></i>Sửa Sản Phẩm</h2>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><strong>{{ $error }}</strong></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.sanpham.update', $product->MaSP) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="TenSP" class="form-label fw-bold">Tên sản phẩm</label>
                                    <input type="text" class="form-control form-control-lg" id="TenSP" name="TenSP" value="{{ old('TenSP', $product->TenSP) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="DonGia" class="form-label fw-bold">Giá</label>
                                    <input type="number" class="form-control form-control-lg" id="DonGia" name="DonGia" value="{{ old('DonGia', $product->DonGia) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="MoTa" class="form-label fw-bold">Mô tả</label>
                            <textarea class="form-control" id="MoTa" name="MoTa" rows="5">{{ old('MoTa', $product->MoTa) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="MaDM" class="form-label fw-bold">Danh mục</label>
                                    <select class="form-select form-select-lg" id="MaDM" name="MaDM">
                                        @foreach($all_categories as $cat)
                                            <option value="{{ $cat->MaDM }}" {{ $cat->MaDM == $product->MaDM ? 'selected' : '' }}>
                                                {{ $cat->TenDM }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="MaNXB" class="form-label fw-bold">Nhà xuất bản</label>
                                    <select class="form-select form-select-lg" id="MaNXB" name="MaNXB">
                                        @foreach($all_nxbs as $nxb)
                                            <option value="{{ $nxb->MaNXB }}" {{ $nxb->MaNXB == $product->MaNXB ? 'selected' : '' }}>
                                                {{ $nxb->TenNXB }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 fw-bold"><i class="fas fa-images me-2"></i>Ảnh hiện tại</h5>
                        <div class="row">
                            @foreach($product->hinhanhsanpham as $img)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                                    <img src="{{ asset('assets/images/products/' . $img->DuongDan) }}" class="card-img-top" alt="Ảnh sản phẩm" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-2 bg-light">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="xoa_anh[]" value="{{ $img->MaHinh }}" id="xoa_{{ $img->MaHinh }}">
                                            <label class="form-check-label small" for="xoa_{{ $img->MaHinh }}">
                                                Xóa ảnh này
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="anh_chinh" value="{{ $img->MaHinh }}" id="chinh_{{ $img->MaHinh }}" {{ $img->LaAnhChinh == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold" for="chinh_{{ $img->MaHinh }}">
                                                Đặt làm ảnh chính
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <h5 class="mt-4 fw-bold"><i class="fas fa-file-upload me-2"></i>Thêm ảnh mới</h5>
                        <div class="mb-4">
                            <input type="file" class="form-control form-control-lg" name="images[]" multiple accept="image/*">
                            <div class="form-text">Bạn có thể chọn nhiều ảnh cùng lúc.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.sanpham.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i>Lưu cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4-top { border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important; }
</style>
@endsection
