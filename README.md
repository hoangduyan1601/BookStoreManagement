# 📚 Hệ Thống Quản Lý Cửa Hàng Sách (BMS)
### Dự án Kết thúc Học phần: Chuyên đề 1 - Lập trình Framework (Laravel)

---

## 📖 Giới Thiệu Dự Án
Hệ thống Quản lý Cửa hàng Sách (Bookstore Management System - BMS) là một giải pháp quản trị doanh nghiệp (ERP) thu nhỏ dành cho các đại lý kinh doanh sách. Dự án được xây dựng trên nền tảng **Laravel 11**, tập trung vào tính toàn vẹn của dữ liệu, quy trình nghiệp vụ chặt chẽ và trải nghiệm người dùng tối ưu.

---

## 👥 Đội Ngũ Thực Hiện (Nhóm 2)
*   **Hoàng Duy An**: Phụ trách Kiến trúc hệ thống & Backend Core.
*   **Vũ Đình Hoàn**: Phụ trách Giao diện người dùng (UI/UX) & Logic Nghiệp vụ.

---

## 🛠 Nền Tảng Công Nghệ
Hệ thống tận dụng các công nghệ hiện đại nhất trong hệ sinh thái PHP:
*   **Core Framework**: Laravel 11.x (PHP 8.2+)
*   **Database Engine**: MySQL / MariaDB
*   **Frontend Stack**: Blade Template Engine, Vite Asset Bundling, Axios.
*   **Security**: CSRF Protection, Password Hashing (Bcrypt), Laravel Sanctum.
*   **Storage**: Local Driver with Symbolic Links for media management.

---

## 🚀 Các Phân Hệ Chức Năng Chính

### 1. Quản Trị Kho Hàng & Sản Phẩm
*   **Quản lý Sách**: Hệ thống hóa thông tin theo Tác giả, Nhà xuất bản và Danh mục đa cấp.
*   **Logistics**: Theo dõi lịch sử nhập hàng chi tiết, quản lý danh sách nhà cung cấp và số lượng tồn kho theo thời gian thực.

### 2. Quản Lý Giao Dịch & Bán Hàng
*   **🛒 Shopping Flow**: Quy trình từ Giỏ hàng -> Đặt hàng -> Xử lý đơn hàng hoàn chỉnh.
*   **Quản lý Đơn hàng**: Theo dõi trạng thái đơn hàng và chi tiết các mặt hàng trong từng giao dịch.

### 3. Tài Chính & Tiếp Thị
*   **Báo cáo Doanh thu**: Tự động tổng hợp chỉ số kinh doanh theo chu kỳ.
*   **Promotion**: Triển khai các mã khuyến mãi và chiến dịch giảm giá linh hoạt.

### 4. Hệ Thống Người Dùng
*   **Khách hàng**: Quản lý hồ sơ cá nhân và lịch sử mua hàng.
*   **Xác thực**: Hệ thống tài khoản bảo mật cao, phân định rõ vai trò quản trị và người dùng cuối.

---

## 🏗 Kiến Trúc Dữ Liệu
Dự án được xây dựng dựa trên mô hình quan hệ thực thể (ERD) phức hợp với các Model lõi:
- **Core Entities**: `SanPham`, `DanhMuc`, `TacGia`, `NhaXuatBan`.
- **Business Logic**: `DonHang`, `ChiTietDonHang`, `LichSuNhapHang`, `DoanhThu`.
- **Interaction**: `GioHang`, `KhuyenMai`, `ThongBao`.

---

## ⚙️ Thiết Lập Môi Trường
1.  **Dependencies**: Thực hiện `composer install` và `npm install` để cài đặt các thư viện cần thiết.
2.  **Environment**: Cấu hình tệp `.env` với các thông số kết nối Database chính xác.
3.  **Migration & Seeding**: Chạy `php artisan migrate --seed` để thiết lập cấu trúc bảng và dữ liệu mẫu.
4.  **Application Launch**: Sử dụng `php artisan serve` để khởi động ứng dụng và `npm run dev` cho quá trình biên dịch tài nguyên frontend.

---

© 2026 **Nhóm 2** - Đại học [Tên trường của bạn]. Toàn bộ mã nguồn được phát triển cho mục đích giáo dục và nghiên cứu.
