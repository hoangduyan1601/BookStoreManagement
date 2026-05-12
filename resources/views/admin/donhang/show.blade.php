@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . str_pad($order->MaDH, 5, '0', STR_PAD_LEFT))

@section('content')
<!-- Web View Header (Hidden when printing) -->
<div class="d-md-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Đơn Hàng</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.donhang.index') }}" class="text-decoration-none">Đơn hàng</a></li>
                <li class="breadcrumb-item active">Chi tiết #{{ $order->MaDH }}</li>
            </ol>
        </nav>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark rounded-pill px-4">
            <i class="fas fa-print me-2"></i> In hóa đơn chuyên nghiệp
        </button>
        <a href="{{ route('admin.donhang.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>
</div>

<!-- PROFESSIONAL INVOICE SECTION (Optimized for Print) -->
<div id="invoice-printable" class="invoice-container">
    <!-- Invoice Header -->
    <div class="invoice-header d-flex justify-content-between align-items-start mb-5">
        <div class="company-info">
            <h1 class="font-luxury fw-bold mb-1" style="color: #000; font-size: 2.5rem;">LUXURY<span style="color: #D4AF37;">.</span></h1>
            <p class="mb-0 fw-bold text-uppercase" style="letter-spacing: 2px;">Showroom Thời Trang Cao Cấp</p>
            <p class="text-muted small mb-0">Địa chỉ: 123 Đường Bưởi, Ba Đình, Hà Nội</p>
            <p class="text-muted small mb-0">Hotline: 1900 8888 | Email: contact@luxury.vn</p>
            <p class="text-muted small mb-0">MST: 0101234567</p>
        </div>
        <div class="invoice-meta text-end">
            <h2 class="text-uppercase fw-bold mb-3" style="letter-spacing: 5px; color: #D4AF37;">HÓA ĐƠN</h2>
            <div class="d-flex flex-column gap-1">
                <p class="mb-0"><strong>Số hóa đơn:</strong> #INV-{{ date('Y', strtotime($order->NgayDat)) }}-{{ str_pad($order->MaDH, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="mb-0"><strong>Ngày lập:</strong> {{ date('d/m/Y H:i') }}</p>
                <p class="mb-0"><strong>Trạng thái:</strong> 
                    @php
                        $statusText = match($order->TrangThai) {
                            'ChoThanhToan' => 'Chờ thanh toán',
                            'ChoXacNhan'   => 'Chờ xác nhận',
                            'DangGiao'     => 'Đang giao',
                            'DaGiao'       => 'Đã giao (Hoàn tất)',
                            'DaHuy'        => 'Đã hủy',
                            default        => $order->TrangThai
                        };
                    @endphp
                    {{ $statusText }}
                </p>
            </div>
        </div>
    </div>

    <hr class="my-4 border-2">

    <!-- Customer & Shipping Info -->
    <div class="row mb-5">
        <div class="col-6">
            <h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing: 1px;">Khách hàng / Bill To:</h6>
            <h5 class="fw-bold mb-1">{{ $order->khachHang->HoTen ?? 'Khách vãng lai' }}</h5>
            <p class="mb-1"><i class="fas fa-phone me-2 text-muted small"></i>{{ $order->khachHang->SDT ?? 'N/A' }}</p>
            <p class="mb-1"><i class="fas fa-envelope me-2 text-muted small"></i>{{ $order->khachHang->Email ?? 'N/A' }}</p>
        </div>
        <div class="col-6 text-end">
            <h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing: 1px;">Địa chỉ giao hàng / Ship To:</h6>
            <p class="mb-1 fw-medium">{{ $order->DiaChiGiaoHang ?? 'N/A' }}</p>
            <p class="mb-0 text-muted small">Phương thức: 
                <strong>
                    @if($order->PhuongThucThanhToan === 'ChuyenKhoan')
                        Chuyển khoản
                    @elseif($order->PhuongThucThanhToan === 'VNPay')
                        VNPay Online
                    @else
                        Thanh toán khi nhận hàng (COD)
                    @endif
                </strong>
            </p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="invoice-body mb-5">
        <table class="table table-bordered align-middle">
            <thead class="bg-light text-center border-dark">
                <tr class="text-uppercase small fw-bold">
                    <th width="5%">STT</th>
                    <th width="15%">Mã SP</th>
                    <th>Tên Sản Phẩm / Dịch vụ</th>
                    <th width="10%">SL</th>
                    <th width="20%">Đơn giá</th>
                    <th width="20%">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->chiTietDonHangs as $index => $r)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center fw-bold">#{{ $r->MaSP }}</td>
                        <td>
                            <div class="fw-bold">{{ $r->sanPham->TenSP ?? 'Sản phẩm đã xóa' }}</div>
                            @if($r->sanPham && $r->sanPham->danhmuc)
                                <small class="text-muted italic">{{ $r->sanPham->danhmuc->TenDM }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ (int)$r->SoLuong }}</td>
                        <td class="text-end">{{ number_format($r->DonGia, 0, ',', '.') }}₫</td>
                        <td class="text-end fw-bold">{{ number_format($r->ThanhTien, 0, ',', '.') }}₫</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-dark">
                <tr>
                    <td colspan="4" rowspan="4" class="align-top p-4">
                        <h6 class="fw-bold text-uppercase small mb-2">Ghi chú & Điều khoản:</h6>
                        <p class="small text-muted mb-0">- Hàng đã mua vui lòng không đổi trả sau 7 ngày.</p>
                        <p class="small text-muted mb-0">- Bảo hành chính hãng theo tiêu chuẩn Luxury.</p>
                        <p class="small text-muted mt-2"><strong>Ghi chú đơn hàng:</strong> {{ $order->GhiChu ?: 'Không có ghi chú đặc biệt.' }}</p>
                    </td>
                    <td class="text-end fw-bold">Tạm tính:</td>
                    <td class="text-end">{{ number_format($order->TongTien + ($order->SoTienGiam ?? 0), 0, ',', '.') }}₫</td>
                </tr>
                @if($order->SoTienGiam > 0)
                <tr class="text-danger">
                    <td class="text-end fw-bold">Giảm giá:</td>
                    <td class="text-end">-{{ number_format($order->SoTienGiam, 0, ',', '.') }}₫</td>
                </tr>
                @endif
                <tr>
                    <td class="text-end fw-bold">Phí vận chuyển:</td>
                    <td class="text-end">0₫</td>
                </tr>
                <tr class="bg-light">
                    <td class="text-end fw-bold text-uppercase" style="font-size: 1.1rem;">Tổng cộng:</td>
                    <td class="text-end fw-bold text-primary" style="font-size: 1.2rem;">{{ number_format($order->TongTien, 0, ',', '.') }}₫</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Payment Status Info (Web view helper, but styled for print) -->
    @php
        $soTienDaThanhToan = $order->SoTienDaThanhToan ?? 0;
        $soTienCanThu = max(0, $order->TongTien - $soTienDaThanhToan);
    @endphp
    @if($soTienDaThanhToan > 0)
    <div class="row justify-content-end mb-4">
        <div class="col-md-5">
            <div class="alert alert-success p-2 d-flex justify-content-between mb-0 border-0 rounded-0 border-start border-4 border-success">
                <span class="fw-bold">Đã thanh toán trước:</span>
                <span class="fw-bold">{{ number_format($soTienDaThanhToan, 0, ',', '.') }}₫</span>
            </div>
            @if($soTienCanThu > 0)
            <div class="alert alert-warning p-2 d-flex justify-content-between mb-0 border-0 rounded-0 border-start border-4 border-warning mt-1">
                <span class="fw-bold text-dark">Số tiền cần thu hộ (COD):</span>
                <span class="fw-bold text-dark">{{ number_format($soTienCanThu, 0, ',', '.') }}₫</span>
            </div>
            @else
            <div class="alert alert-primary p-2 d-flex justify-content-center mb-0 border-0 rounded-0 border-start border-4 border-primary mt-1">
                <span class="fw-bold text-uppercase">HÓA ĐƠN ĐÃ TẤT TOÁN</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Invoice Footer / Signatures -->
    <div class="invoice-footer mt-5 pt-4">
        <div class="row text-center">
            <div class="col-4">
                <p class="fw-bold mb-5">Người mua hàng</p>
                <p class="text-muted small mt-5">(Ký, ghi rõ họ tên)</p>
            </div>
            <div class="col-4">
                <p class="fw-bold mb-5">Người giao hàng</p>
                <p class="text-muted small mt-5">(Ký, ghi rõ họ tên)</p>
            </div>
            <div class="col-4">
                <p class="fw-bold mb-1">Người lập hóa đơn</p>
                <p class="text-muted small mb-5">Luxury Admin</p>
                <div class="mt-4">
                    <p class="mb-0 fw-bold">Hệ Thống Luxury Store</p>
                    <p class="text-muted small">(Đã ký điện tử)</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5 pt-5 no-print">
            <hr>
            <p class="text-muted small mb-0">Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!</p>
            <p class="text-muted" style="font-family: 'Playfair Display', serif; font-style: italic;">Where Luxury Meets Elegance</p>
        </div>
    </div>
</div>

<!-- Extra web UI for updating status (Hidden when printing) -->
<div class="admin-card p-4 mt-4 no-print border-start border-4 border-primary">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold mb-1">Quản Lý Vận Hành</h5>
            <p class="text-muted small mb-0">Cập nhật trạng thái xử lý đơn hàng cho hệ thống và khách hàng.</p>
        </div>
        <form action="{{ route('admin.donhang.update_status', $order->MaDH) }}" method="POST" class="d-flex gap-2">
            @csrf
            <select name="status" class="form-select rounded-pill px-4" style="min-width: 200px;">
                <option value="ChoThanhToan" {{ $order->TrangThai == 'ChoThanhToan' ? 'selected' : '' }}>Chờ thanh toán</option>
                <option value="ChoXacNhan" {{ $order->TrangThai == 'ChoXacNhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="DangGiao" {{ $order->TrangThai == 'DangGiao' ? 'selected' : '' }}>Đang giao</option>
                <option value="DaGiao" {{ $order->TrangThai == 'DaGiao' ? 'selected' : '' }}>Đã giao</option>
                <option value="DaHuy" {{ $order->TrangThai == 'DaHuy' ? 'selected' : '' }}>Hủy đơn hàng</option>
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Cập nhật</button>
        </form>
    </div>
</div>

<style>
    /* Professional Invoice Styling */
    .invoice-container {
        background: #fff;
        padding: 40px;
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .invoice-container h1, .invoice-container h2, .invoice-container h3 {
        color: #000;
    }

    .table-bordered > :not(caption) > * > * {
        border-width: 1px;
        border-color: #dee2e6;
    }

    .italic { font-style: italic; }

    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            background: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print, .sidebar, .topbar, .navbar, .btn, .breadcrumb, footer {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .container, .container-fluid {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .invoice-container {
            padding: 0;
            box-shadow: none !important;
            border: none !important;
        }

        .admin-card {
            border: none !important;
            box-shadow: none !important;
        }

        /* Ensure table borders appear in print */
        .table {
            border-collapse: collapse !important;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
        }
        .bg-light {
            background-color: #f8f9fa !important;
        }
        .text-primary {
            color: #2563eb !important;
        }
    }

    /* Web styling enhancement */
    .invoice-container {
        box-shadow: 0 1rem 3rem rgba(0,0,0,.05);
        border-radius: 1rem;
        border: 1px solid rgba(0,0,0,.05);
        margin-bottom: 2rem;
    }
</style>

@if(request('print'))
<script>
    window.onload = function() {
        window.print();
        // Tự động đóng tab sau khi in xong (tùy chọn)
        window.onafterprint = function() {
            window.close();
        };
        // Fallback cho trình duyệt không hỗ trợ onafterprint
        setTimeout(function() {
            // window.close(); 
        }, 1000);
    }
</script>
@endif
@endsection
