@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Chỉnh Sửa Sản Phẩm</h3>
        <p class="text-muted small mb-0">Cập nhật thông tin cho sản phẩm: <strong>{{ $product->TenSP }}</strong></p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('admin.sanpham.index') }}" class="btn btn-luxury-outline">
            <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.sanpham.update', $product->MaSP) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Thông tin cơ bản</h5>
                
                <div class="mb-3">
                    <label class="admin-form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="TenSP" class="form-control form-control-luxury" value="{{ old('TenSP', $product->TenSP) }}" placeholder="Nhập tên sách..." required>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Mô tả sản phẩm</label>
                    <textarea name="MoTa" class="form-control form-control-luxury" rows="8" placeholder="Viết mô tả ngắn gọn...">{{ old('MoTa', $product->MoTa) }}</textarea>
                </div>
            </div>

            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-list me-2 text-primary"></i>Thông số kỹ thuật</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="admin-form-label">Số trang</label>
                        <input type="number" name="SoTrang" class="form-control form-control-luxury" value="{{ old('SoTrang', $product->chiTiet->SoTrang ?? '') }}" placeholder="Ví dụ: 350">
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label">Kích thước</label>
                        <input type="text" name="KichThuoc" class="form-control form-control-luxury" value="{{ old('KichThuoc', $product->chiTiet->KichThuoc ?? '') }}" placeholder="Ví dụ: 14.5 x 20.5 cm">
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label">Loại bìa</label>
                        <select name="LoaiBia" class="form-select form-control-luxury">
                            <option value="">-- Chọn loại bìa --</option>
                            <option value="Bìa mềm" {{ (old('LoaiBia', $product->chiTiet->LoaiBia ?? '') == 'Bìa mềm') ? 'selected' : '' }}>Bìa mềm</option>
                            <option value="Bìa cứng" {{ (old('LoaiBia', $product->chiTiet->LoaiBia ?? '') == 'Bìa cứng') ? 'selected' : '' }}>Bìa cứng</option>
                            <option value="Bìa da" {{ (old('LoaiBia', $product->chiTiet->LoaiBia ?? '') == 'Bìa da') ? 'selected' : '' }}>Bìa da</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label">Trọng lượng (gr)</label>
                        <input type="number" name="TrongLuong" class="form-control form-control-luxury" value="{{ old('TrongLuong', $product->chiTiet->TrongLuong ?? '') }}" placeholder="Ví dụ: 500">
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label">Năm xuất bản</label>
                        <input type="number" name="NamXuatBan" class="form-control form-control-luxury" value="{{ old('NamXuatBan', $product->chiTiet->NamXuatBan ?? '') }}" placeholder="Ví dụ: 2024">
                    </div>
                </div>
            </div>

            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-file-alt me-2 text-primary"></i>Nội dung chi tiết</h5>
                <div class="mb-3">
                    <label class="admin-form-label">Nội dung/Review chi tiết (Dùng cho trang sản phẩm)</label>
                    <textarea name="NoiDungChiTiet" class="form-control form-control-luxury" rows="12" placeholder="Nhập nội dung review, giới thiệu chi tiết, mục lục...">{{ old('NoiDungChiTiet', $product->chiTiet->NoiDungChiTiet ?? '') }}</textarea>
                </div>
            </div>

            <div class="admin-card p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-images me-2 text-primary"></i>Quản lý hình ảnh</h5>
                
                <div class="row g-3 mb-4">
                    <label class="admin-form-label px-3">Ảnh hiện tại</label>
                    @foreach($product->hinhanhsanpham as $img)
                    <div class="col-md-4 col-sm-6">
                        <div class="admin-card overflow-hidden h-100 border-0 shadow-sm">
                            <div class="position-relative" style="padding-top: 100%;">
                                <img src="{{ asset('assets/images/products/' . $img->DuongDan) }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                                @if($img->LaAnhChinh)
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Ảnh chính</span>
                                @endif
                            </div>
                            <div class="p-3 bg-light">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="xoa_anh[]" value="{{ $img->MaHinh }}" id="xoa_{{ $img->MaHinh }}">
                                    <label class="form-check-label small text-danger" for="xoa_{{ $img->MaHinh }}">
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

                <div class="mb-4">
                    <label class="admin-form-label">Thêm ảnh mới</label>
                    <div class="upload-zone p-4 border-2 border-dashed rounded-4 text-center bg-light transition-all" id="drop-zone" style="border-style: dashed !important; border-color: var(--border-color) !important;">
                        <i class="fas fa-cloud-upload-alt fs-2 text-muted mb-2"></i>
                        <p class="mb-2">Kéo thả hoặc nhấn để chọn thêm ảnh</p>
                        <input type="file" name="images[]" id="file-input" class="d-none" multiple accept="image/*">
                        <button type="button" class="btn btn-luxury-outline btn-sm" onclick="document.getElementById('file-input').click()">Chọn tệp</button>
                    </div>
                    <div id="image-preview" class="row g-2 mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-tag me-2 text-primary"></i>Phân loại & Giá</h5>
                
                <div class="mb-3">
                    <label class="admin-form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="DonGia" class="form-control form-control-luxury border-end-0" value="{{ old('DonGia', $product->DonGia) }}" min="1000" placeholder="0" required>
                        <span class="input-group-text bg-light border-start-0 text-muted">₫</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Danh mục <span class="text-danger">*</span></label>
                    <select name="MaDM" class="form-select form-control-luxury" required>
                        @foreach($all_categories as $cat)
                            <option value="{{ $cat->MaDM }}" {{ old('MaDM', $product->MaDM) == $cat->MaDM ? 'selected' : '' }}>{{ $cat->TenDM }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="admin-form-label">Tồn kho hiện tại</label>
                        <input type="number" class="form-control form-control-luxury bg-light" value="{{ $product->SoLuong }}" readonly>
                        <input type="hidden" name="SoLuong" value="{{ $product->SoLuong }}">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Quản lý qua <a href="{{ route('admin.nhaphang.index') }}" class="text-decoration-none">Nhập hàng</a></small>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label">Tổng đã bán</label>
                        <input type="number" class="form-control form-control-luxury bg-light" value="{{ $product->SoLuongDaBan }}" readonly>
                        <input type="hidden" name="SoLuongDaBan" value="{{ $product->SoLuongDaBan }}">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Tự động cập nhật từ <a href="{{ route('admin.donhang.index') }}" class="text-decoration-none">Đơn hàng</a></small>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="admin-form-label">Nhà xuất bản <span class="text-danger">*</span></label>
                    <select name="MaNXB" class="form-select form-control-luxury" required>
                        @foreach($all_nxbs as $nxb)
                            <option value="{{ $nxb->MaNXB }}" {{ old('MaNXB', $product->MaNXB) == $nxb->MaNXB ? 'selected' : '' }}>{{ $nxb->TenNXB }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-card p-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-luxury-primary py-3">
                        <i class="fas fa-save me-2"></i> Lưu cập nhật
                    </button>
                    <a href="{{ route('admin.sanpham.index') }}" class="btn btn-luxury-outline py-3">
                        Hủy bỏ
                    </a>
                </div>
                <hr class="my-4 opacity-50">
                <div class="text-center">
                    <small class="text-muted">Cập nhật lần cuối: <br> {{ $product->updated_at ? $product->updated_at->format('d/m/Y H:i') : 'N/A' }}</small>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Preview images before upload
    document.getElementById('file-input').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        
        if (this.files) {
            [].forEach.call(this.files, function(file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    col.innerHTML = `
                        <div class="position-relative rounded-3 overflow-hidden" style="padding-top: 100%;">
                            <img src="${event.target.result}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Simple Drag and Drop
    const dropZone = document.getElementById('drop-zone');
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-primary-subtle');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('bg-primary-subtle');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-primary-subtle');
        const files = e.dataTransfer.files;
        document.getElementById('file-input').files = files;
        const event = new Event('change');
        document.getElementById('file-input').dispatchEvent(event);
    });
</script>
@endpush
@endsection
