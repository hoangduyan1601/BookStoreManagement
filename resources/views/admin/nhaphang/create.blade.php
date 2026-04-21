@extends('layouts.admin')

@section('title', 'Tạo Phiếu Nhập Hàng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Tạo Phiếu Nhập Hàng
            </h2>
            <p class="text-muted mb-0">Thêm sản phẩm mới vào kho</p>
        </div>
        <a href="{{ route('admin.nhaphang.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.nhaphang.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Thông tin chung -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">Thông tin chung</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Ngày nhập hàng</label>
                            <input type="datetime-local" name="NgayNhap" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nhà cung cấp</label>
                            <select name="MaNCC" class="form-select" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach($suppliers as $ncc)
                                    <option value="{{ $ncc->MaNCC }}">{{ $ncc->TenNCC }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tổng tiền phiếu:</span>
                            <h4 class="text-primary mb-0 fw-bold" id="totalAmount">0₫</h4>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-4 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i>LƯU PHIẾU NHẬP
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Chi tiết sản phẩm nhập</h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="addRow()">
                            <i class="fas fa-plus me-1"></i>Thêm sản phẩm
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="productTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="45%">Sản phẩm</th>
                                        <th width="20%">Số lượng</th>
                                        <th width="25%">Giá nhập (₫)</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be added here via JS -->
                                </tbody>
                            </table>
                        </div>
                        <div id="emptyMessage" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                            <p>Chưa có sản phẩm nào trong danh sách nhập</p>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow()">Thêm ngay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let rowCount = 0;
    const products = {!! json_encode($products) !!};

    function addRow() {
        document.getElementById('emptyMessage').style.display = 'none';
        const tbody = document.querySelector('#productTable tbody');
        const rowId = rowCount++;
        
        const tr = document.createElement('tr');
        tr.id = `row-${rowId}`;
        tr.innerHTML = `
            <td class="ps-3">
                <select name="products[${rowId}][MaSP]" class="form-select form-select-sm" required onchange="updatePrice(this, ${rowId})">
                    <option value="">-- Chọn sản phẩm --</option>
                    ${products.map(p => `<option value="${p.MaSP}" data-price="${p.DonGia}">${p.TenSP}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="products[${rowId}][SoLuong]" class="form-control form-control-sm text-center" value="1" min="1" required onchange="calculateTotal()">
            </td>
            <td>
                <input type="number" name="products[${rowId}][GiaNhap]" class="form-control form-control-sm text-end" value="0" min="0" required onchange="calculateTotal()">
            </td>
            <td class="text-center pe-3">
                <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeRow(${rowId})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calculateTotal();
    }

    function removeRow(id) {
        document.getElementById(`row-${id}`).remove();
        if (document.querySelectorAll('#productTable tbody tr').length === 0) {
            document.getElementById('emptyMessage').style.display = 'block';
        }
        calculateTotal();
    }

    function updatePrice(select, id) {
        const option = select.options[select.selectedIndex];
        const basePrice = option.getAttribute('data-price');
        if (basePrice) {
            // Mặc định giá nhập = 60% giá bán
            const importPrice = Math.round(basePrice * 0.6);
            document.querySelector(`input[name="products[${id}][GiaNhap]"]`).value = importPrice;
        }
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#productTable tbody tr');
        rows.forEach(row => {
            const qty = row.querySelector('input[name*="[SoLuong]"]').value || 0;
            const price = row.querySelector('input[name*="[GiaNhap]"]').value || 0;
            total += (qty * price);
        });
        document.getElementById('totalAmount').textContent = total.toLocaleString('vi-VN') + '₫';
    }

    // Khởi tạo hàng đầu tiên
    document.addEventListener('DOMContentLoaded', () => {
        addRow();
    });
</script>
@endsection
