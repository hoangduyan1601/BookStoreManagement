@extends('layouts.admin')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Thêm Sản Phẩm Mới
                    </h3>
                </div>

                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success text-center py-5 rounded-4 border border-success border-3">
                            <i class="fas fa-check-circle fa-5x mb-4 text-success"></i>
                            <h2 class="text-success">THÊM SẢN PHẨM THÀNH CÔNG!</h2>
                            <p class="lead">
                                {{ session('success') }}
                            </p>
                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <a href="{{ route('admin.sanpham.index') }}" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-list"></i> Xem danh sách
                                </a>
                            </div>
                        </div>
                    @else
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><strong>{{ $error }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.sanpham.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-success">Tên sản phẩm</label>
                                    <input type="text" name="TenSP" class="form-control form-control-lg" value="{{ old('TenSP') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-success">Giá bán</label>
                                    <input type="number" name="DonGia" class="form-control form-control-lg" value="{{ old('DonGia') }}" min="1000" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-success">Mô tả sản phẩm</label>
                                    <textarea name="MoTa" class="form-control" rows="5" placeholder="Mô tả chi tiết về sách...">{{ old('MoTa') }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Danh mục</label>
                                    <select name="MaDM" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($all_categories as $cat)
                                            <option value="{{ $cat->MaDM }}" {{ old('MaDM') == $cat->MaDM ? 'selected' : '' }}>{{ $cat->TenDM }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Nhà xuất bản</label>
                                    <select name="MaNXB" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn NXB --</option>
                                        @foreach($all_nxbs as $nxb)
                                            <option value="{{ $nxb->MaNXB }}" {{ old('MaNXB') == $nxb->MaNXB ? 'selected' : '' }}>{{ $nxb->TenNXB }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-success">Ảnh sản phẩm (chọn nhiều)</label>
                                    <input type="file" name="images[]" class="form-control form-control-lg" multiple required accept="image/*">
                                    <small class="text-muted">Giữ Ctrl để chọn nhiều ảnh</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Ảnh chính là ảnh số mấy? (bắt đầu từ 0)</label>
                                    <input type="number" name="anh_chinh" class="form-control form-control-lg" value="0" min="0">
                                </div>

                                <div class="col-12 text-center mt-5">
                                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg">
                                        <i class="fas fa-save"></i> Lưu sản phẩm
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 1.5rem !important; }
    .card-header { border-radius: 1.5rem 1.5rem 0 0 !important; }
    .btn { border-radius: 1rem; }
</style>
@endsection
