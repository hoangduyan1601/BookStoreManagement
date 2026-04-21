@extends('layouts.admin')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Thêm Sản Phẩm Mới</h3>
        <p class="text-muted small mb-0">Tạo một sản phẩm mới trong cửa hàng của bạn</p>
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

<form action="{{ route('admin.sanpham.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Thông tin cơ bản</h5>
                
                <div class="mb-3">
                    <label class="admin-form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="TenSP" class="form-control form-control-luxury" value="{{ old('TenSP') }}" placeholder="Nhập tên sách..." required>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Mô tả sản phẩm</label>
                    <textarea name="MoTa" class="form-control form-control-luxury" rows="8" placeholder="Viết mô tả ngắn gọn...">{{ old('MoTa') }}</textarea>
                </div>
            </div>

            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-list me-2 text-primary"></i>Thông số kỹ thuật</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="admin-form-label">Số trang</label>
                        <input type="number" name="SoTrang" class="form-control form-control-luxury" value="{{ old('SoTrang', 200) }}" placeholder="Ví dụ: 350">
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label">Kích thước</label>
                        <input type="text" name="KichThuoc" class="form-control form-control-luxury" value="{{ old('KichThuoc', '14.5 x 20.5 cm') }}" placeholder="Ví dụ: 14.5 x 20.5 cm">
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label">Loại bìa</label>
                        <select name="LoaiBia" class="form-select form-control-luxury">
                            <option value="">-- Chọn loại bìa --</option>
                            <option value="Bìa mềm" {{ old('LoaiBia', 'Bìa mềm') == 'Bìa mềm' ? 'selected' : '' }}>Bìa mềm</option>
                            <option value="Bìa cứng" {{ old('LoaiBia') == 'Bìa cứng' ? 'selected' : '' }}>Bìa cứng</option>
                            <option value="Bìa da" {{ old('LoaiBia') == 'Bìa da' ? 'selected' : '' }}>Bìa da</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label">Trọng lượng (gr)</label>
                        <input type="number" name="TrongLuong" class="form-control form-control-luxury" value="{{ old('TrongLuong', 300) }}" placeholder="Ví dụ: 500">
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label">Năm xuất bản</label>
                        <input type="number" name="NamXuatBan" class="form-control form-control-luxury" value="{{ old('NamXuatBan', date('Y')) }}" placeholder="Ví dụ: 2024">
                    </div>
                </div>
            </div>

            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-file-alt me-2 text-primary"></i>Nội dung chi tiết</h5>
                <div class="mb-3">
                    <label class="admin-form-label">Nội dung/Review chi tiết (Dùng cho trang sản phẩm)</label>
                    <textarea name="NoiDungChiTiet" class="form-control form-control-luxury" rows="12" placeholder="Nhập nội dung review, giới thiệu chi tiết, mục lục...">{{ old('NoiDungChiTiet') }}</textarea>
                </div>
            </div>

            <div class="admin-card p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-images me-2 text-primary"></i>Hình ảnh sản phẩm</h5>
                
                <div class="mb-4">
                    <label class="admin-form-label">Chọn hình ảnh (Có thể chọn nhiều)</label>
                    <div class="upload-zone p-5 border-2 border-dashed rounded-4 text-center bg-light transition-all" id="drop-zone" style="border-style: dashed !important; border-color: var(--border-color) !important;">
                        <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-3"></i>
                        <h5>Kéo thả hoặc nhấn để tải ảnh lên</h5>
                        <p class="text-muted small">Hỗ trợ JPG, PNG, WEBP. Tối đa 5MB mỗi ảnh.</p>
                        <input type="file" name="images[]" id="file-input" class="d-none" multiple accept="image/*" required>
                        <button type="button" class="btn btn-luxury-outline btn-sm mt-2" onclick="document.getElementById('file-input').click()">Chọn tệp</button>
                    </div>
                    <div id="image-preview" class="row g-2 mt-3"></div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label">Thứ tự ảnh chính</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-star text-warning"></i></span>
                            <input type="number" name="anh_chinh" class="form-control form-control-luxury border-start-0 ps-0" value="0" min="0">
                        </div>
                        <small class="text-muted">Nhập vị trí ảnh làm đại diện (0 là ảnh đầu tiên)</small>
                    </div>
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
                        <input type="number" name="DonGia" class="form-control form-control-luxury border-end-0" value="{{ old('DonGia') }}" min="1000" placeholder="0" required>
                        <span class="input-group-text bg-light border-start-0 text-muted">₫</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Danh mục <span class="text-danger">*</span></label>
                    <select name="MaDM" class="form-select form-control-luxury" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($all_categories as $cat)
                            <option value="{{ $cat->MaDM }}" {{ old('MaDM') == $cat->MaDM ? 'selected' : '' }}>{{ $cat->TenDM }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="admin-form-label">Tồn kho ban đầu <span class="text-danger">*</span></label>
                        <input type="number" name="SoLuong" class="form-control form-control-luxury" value="{{ old('SoLuong', 0) }}" min="0" required>
                        <input type="hidden" name="SoLuongDaBan" value="0">
                        <small class="text-muted">Nhập số lượng thực tế hiện có trong kho.</small>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="admin-form-label">Nhà xuất bản <span class="text-danger">*</span></label>
                    <select name="MaNXB" class="form-select form-control-luxury" required>
                        <option value="">-- Chọn NXB --</option>
                        @foreach($all_nxbs as $nxb)
                            <option value="{{ $nxb->MaNXB }}" {{ old('MaNXB') == $nxb->MaNXB ? 'selected' : '' }}>{{ $nxb->TenNXB }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-card p-4">
                <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-lightbulb me-2"></i>Lưu ý</h5>
                <ul class="small text-muted ps-3 mb-4">
                    <li class="mb-2">Tên sản phẩm nên bao gồm cả tên sách và tập (nếu có).</li>
                    <li class="mb-2">Mô tả đầy đủ giúp tăng tỷ lệ chốt đơn hàng.</li>
                    <li>Hình ảnh rõ nét, chuyên nghiệp sẽ thu hút khách hàng hơn.</li>
                </ul>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-luxury-primary py-3">
                        <i class="fas fa-save me-2"></i> Lưu sản phẩm
                    </button>
                    <button type="reset" class="btn btn-luxury-outline py-3">
                        Làm mới form
                    </button>
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
                    col.className = 'col-3';
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
        // Trigger change event to show preview
        const event = new Event('change');
        document.getElementById('file-input').dispatchEvent(event);
    });
</script>
@endpush
@endsection
