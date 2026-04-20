# BẢNG PHÂN CHIA CÔNG VIỆC DỰ ÁN BOOKSTORE

| STT | Thành viên | Vai trò | Model đảm nhiệm | Công việc chi tiết |
|:---:|:---|:---|:---|:---|
| 1 | **Thành viên 1** | **Làm chính (Lead)** | TaiKhoan, User, SanPham, HinhAnhSanPham, GioHang, ChiTietGioHang, DonHang, ChiTietDonHang, DoanhThu | - Thiết lập cấu trúc dự án, Auth (Đăng nhập/Đăng ký).<br>- Quản lý Sản phẩm (CRUD, upload ảnh).<br>- Xây dựng luồng Giỏ hàng và Thanh toán.<br>- Quản lý Đơn hàng và trạng thái đơn hàng.<br>- Thống kê doanh thu và Dashboard Admin. |
| 2 | **Thành viên 2** | **Thành viên** | DanhMuc, TacGia, NhaXuatBan, NhaCungCap, KhuyenMai, ThongBao, LichSuNhapHang, ChiTietNhapHang | - Quản lý Danh mục, Tác giả, Nhà xuất bản.<br>- Quản lý Nhà cung cấp và quy trình Nhập hàng (Inventory).<br>- Xây dựng hệ thống Mã giảm giá (Khuyến mãi).<br>- Hệ thống Thông báo cho người dùng.<br>- Hỗ trợ thiết kế giao diện (Blade templates). |

## Ghi chú:
- **Người làm chính** tập trung vào các tính năng cốt lõi (Core) ảnh hưởng trực tiếp đến luồng mua hàng.
- **Người thứ hai** tập trung vào quản lý dữ liệu danh mục (Master Data) và các tính năng bổ trợ.
- Cả hai cần phối hợp chặt chẽ khi làm việc với Model `SanPham` vì nó liên kết với hầu hết các Model của người thứ hai (DanhMuc, TacGia, NhaXuatBan, NhapHang).


 @startuml
 skinparam classAttributeIconSize 0
 skinparam linetype ortho

 package "Models" {
     class SanPham {
         + int MaSP
         + string TenSP
         + decimal DonGia
         + int SoLuong
         + string MoTa
         + string HinhAnh
         + int MaDM
         + int MaNXB
         + danhmuc()
         + nhaxuatban()
         + tacgias()
         + getGiaHienTaiAttribute()
     }

     class DanhMuc {
         + int MaDM
         + string TenDM
         + string MoTa
         + sanphams()
     }

     class TacGia {
         + int MaTacGia
         + string TenTacGia
         + date NgaySinh
         + string QuocTich
         + sanphams()
     }

     class DonHang {
         + int MaDH
         + datetime NgayDat
         + decimal TongTien
         + string TrangThai
         + int MaKH
         + int MaKM
         + khachhang()
         + chitiet()
     }

     class KhachHang {
         + int MaKH
         + string HoTen
         + string Email
         + string SDT
         + string DiaChi
         + int MaTK
         + taikhoan()
         + donhangs()
     }

     class TaiKhoan {
         + int MaTK
         + string TenDangNhap
         + string MatKhau
         + string VaiTro
         + int TrangThai
     }

     class ChiTietDonHang {
         + int MaDH
         + int MaSP
         + int SoLuong
         + decimal DonGia
         + decimal ThanhTien
     }

     class GioHang {
         + int MaGH
         + int MaKH
         + items()
     }

     class KhuyenMai {
         + int MaKM
         + string TenKM
         + decimal PhanTramGiam
         + string MaGiamGia
         + datetime NgayBatDau
         + datetime NgayKetThuc
     }

     class NhaXuatBan {
         + int MaNXB
         + string TenNXB
         + string DiaChi
     }
 }

 package "Controllers" {
     class SanPhamController {
         + index()
         + show(id)
            + search(request)
        }
   
        class CartController {
            + index()
            + add(id)
            + update(request)
            + remove(id)
        }
   
        class CheckoutController {
            + index()
            + process(request)
        }
   
        class AdminSanPhamController {
            + index()
            + store(request)
            + update(request, id)
            + destroy(id)
        }
    }
   
    ' Relationships
    SanPham "n" --> "1" DanhMuc : BelongsTo
    SanPham "n" --> "1" NhaXuatBan : BelongsTo
    SanPham "n" -- "m" TacGia : BelongsToMany
    SanPham "1" *-- "n" ChiTietDonHang : Has
    DonHang "1" *-- "n" ChiTietDonHang : Has
    DonHang "n" --> "1" KhachHang : BelongsTo
    DonHang "n" --> "0..1" KhuyenMai : Uses
    KhachHang "1" -- "1" TaiKhoan : LinkedTo
    GioHang "1" -- "1" KhachHang : OwnedBy
    GioHang "1" *-- "n" SanPham : Contains
   
    ' Controller associations
    SanPhamController ..> SanPham : Uses
    CartController ..> GioHang : Manages
    AdminSanPhamController ..> SanPham : Manages
   
    @enduml