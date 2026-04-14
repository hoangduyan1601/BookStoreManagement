@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="d-flex align-items-center mb-4">
        <h3 class="fw-bold text-uppercase m-0 border-start border-4 border-danger ps-3">Giỏ hàng của bạn</h3>
        <span class="ms-3 badge bg-danger">{{ !empty($cart) ? count($cart) : 0 }} sản phẩm</span>
    </div>

    @if(empty($cart))
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="120" class="mb-3 opacity-75">
            <h5 class="text-muted">Giỏ hàng của bạn đang trống</h5>
            <p class="small text-secondary mb-4">Hãy chọn thêm sách để nuôi dưỡng tâm hồn nhé!</p>
            <a href="{{ route('sanpham.index') }}" class="btn btn-alpha px-4 py-2">TIẾP TỤC MUA SẮM</a>
        </div>
    @else
        <form action="{{ route('cart.update') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">Ảnh</th>
                                        <th>Tên sách</th>
                                        <th class="text-center">Đơn giá</th>
                                        <th class="text-center" style="width:100px;">SL</th>
                                        <th class="text-center">Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $item)
                                    <tr>
                                        <td class="p-3 text-center">
                                            <img src="{{ $item['image'] ? asset('assets/images/products/' . $item['image']) : 'https://via.placeholder.com/100' }}"
                                                 class="img-fluid border rounded" style="width: 60px; height: 85px; object-fit: cover;">
                                        </td>                                        <td>
                                            <a href="{{ route('sanpham.detail', $item['id']) }}" class="text-decoration-none text-dark fw-bold">
                                                {{ $item['name'] }}
                                            </a>
                                        </td>
                                        <td class="text-center text-muted">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                        <td>
                                            <input type="number" name="qty[{{ $id }}]" value="{{ $item['qty'] }}" class="form-control text-center form-control-sm" min="1">
                                        </td>
                                        <td class="text-center text-danger fw-bold">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} đ</td>
                                        <td class="text-center">
                                            <a href="{{ route('cart.remove', $id) }}" class="text-secondary" onclick="return confirm('Xóa khỏi giỏ?')" title="Xóa">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sanpham.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-chevron-left"></i> Mua thêm
                        </a>
                        <button type="submit" class="btn btn-outline-primary btn-sm fw-bold">
                            <i class="fa-solid fa-rotate"></i> Cập nhật số lượng
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm bg-white">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Đơn hàng</h5>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <span class="fw-bold">TỔNG CỘNG:</span>
                                <span class="fw-bold text-danger fs-4">{{ number_format($totalPrice, 0, ',', '.') }} đ</span>
                            </div>

                            <div class="mt-3 small text-muted">
                                Thông tin giao hàng và phương thức thanh toán sẽ được nhập ở bước tiếp theo.
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('checkout.index') }}" class="btn btn-alpha w-100 py-2 text-uppercase fw-bold text-center">
                                    Thanh toán
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted text-center"><i class="fa-solid fa-shield-halved"></i> Bảo mật 100%</div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
