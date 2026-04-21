@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-5 pb-2" style="border-bottom: 2px solid var(--gold-primary);">
        <h2 class="font-luxury fw-bold m-0 text-dark">KIỆT TÁC TRONG GIỎ</h2>
        <span class="ms-3 badge rounded-pill bg-dark px-3 py-2" style="font-size: 0.7rem;">{{ !empty($cart) ? count($cart) : 0 }} ĐẦU SÁCH</span>
    </div>

    @if(empty($cart))
        <div class="glass-panel text-center py-5 rounded-4 bg-white border-0">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="100" class="mb-4 opacity-25">
            <h5 class="text-dark fw-bold">Giỏ hàng của bạn đang chờ đợi...</h5>
            <p class="text-muted small mb-4">Hãy tiếp tục hành trình khám phá tri thức cùng chúng tôi.</p>
            <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold">TIẾP TỤC KHÁM PHÁ</a>
        </div>
    @else
        <form action="{{ route('cart.update') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="glass-panel border-0 rounded-4 overflow-hidden bg-white shadow-sm mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead style="background: var(--bg-soft);">
                                    <tr class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">
                                        <th class="ps-4 py-3">Tác Phẩm</th>
                                        <th class="text-center py-3">Đơn Giá</th>
                                        <th class="text-center py-3" style="width:120px;">Số Lượng</th>
                                        <th class="text-center py-3">Thành Tiền</th>
                                        <th class="py-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $item)
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <td class="ps-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['image'] ? asset('assets/images/products/' . $item['image']) : 'https://via.placeholder.com/100' }}"
                                                     class="rounded-3 shadow-sm me-3" style="width: 70px; height: 100px; object-fit: contain; background: white;">
                                                <div>
                                                    <a href="{{ route('sanpham.detail', $item['id']) }}" class="text-decoration-none text-dark fw-bold mb-1 d-block" style="font-size: 0.95rem;">
                                                        {{ $item['name'] }}
                                                    </a>
                                                    <span class="extra-small text-muted text-uppercase">Mã SP: #{{ $item['id'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-dark fw-medium">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm rounded-pill overflow-hidden border">
                                                <input type="number" name="qty[{{ $id }}]" value="{{ $item['qty'] }}" class="form-control text-center border-0 bg-transparent" min="1" style="font-weight: 700;">
                                            </div>
                                        </td>
                                        <td class="text-center text-dark fw-bold">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫</td>
                                        <td class="text-center pe-4">
                                            <a href="{{ route('cart.remove', $id) }}" class="text-muted hover-gold" onclick="return confirm('Xóa tuyệt tác này khỏi giỏ?')" title="Xóa">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('sanpham.index') }}" class="text-dark fw-bold text-decoration-none small hover-gold">
                            <i class="fa-solid fa-arrow-left me-2"></i> TIẾP TỤC KHÁM PHÁ
                        </a>
                        <button type="submit" class="btn btn-outline-dark rounded-pill px-4 py-2 small fw-bold">
                            CẬP NHẬT GIỎ HÀNG
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="glass-panel border-0 rounded-4 bg-white shadow-sm p-4 sticky-top" style="top: 100px;">
                        <h5 class="font-luxury fw-bold mb-4 border-bottom pb-2">TÓM TẮT ĐƠN HÀNG</h5>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span class="small">Tạm tính:</span>
                            <span class="small fw-bold">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 text-muted">
                            <span class="small">Vận chuyển:</span>
                            <span class="small fw-bold">Tính ở bước sau</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 pt-3 border-top border-2 border-dark">
                            <span class="fw-bold text-dark">TỔNG CỘNG:</span>
                            <span class="fw-bold fs-4 text-dark">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                        </div>

                        <div class="mb-4 p-3 rounded-4 bg-light">
                            <small class="text-muted d-block lh-base" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-circle-info me-1"></i> Miễn phí vận chuyển cho đơn hàng từ 500.000₫. Cam kết bảo mật thông tin 100%.
                            </small>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 rounded-pill py-3 fw-bold text-uppercase shadow-sm" style="letter-spacing: 1px;">
                            TIẾN HÀNH THANH TOÁN
                        </a>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<style>
    .hover-gold:hover { color: var(--gold-primary) !important; transition: all 0.3s; }
    .extra-small { font-size: 0.7rem; }
</style>
@endsection
