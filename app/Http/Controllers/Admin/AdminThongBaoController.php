<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use Illuminate\Http\Request;

class AdminThongBaoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');
        
        $query = ThongBao::with('khachHang');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('TieuDe', 'LIKE', "%{$search}%")
                  ->orWhere('NoiDung', 'LIKE', "%{$search}%")
                  ->orWhereHas('khachHang', function($sub) use ($search) {
                      $sub->where('HoTen', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($type && $type !== 'all') {
            $query->where('LoaiTB', $type);
        }

        $recent = $query->orderBy('NgayGui', 'desc')->paginate(10)->withQueryString();
        
        $ds_khach = \App\Models\KhachHang::whereHas('taiKhoan', function($q) {
            $q->where('TrangThai', 1);
        })->get();
        
        return view('admin.thongbao.index', compact('recent', 'ds_khach', 'search', 'type'));
    }

    public function create()
    {
        return view('admin.thongbao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TieuDe' => 'required',
            'NoiDung' => 'required',
        ]);

        $guiCho = $request->input('gui_cho');
        $data = $request->only(['TieuDe', 'NoiDung', 'LoaiTB', 'LienKet']);
        $data['NgayGui'] = now();
        $data['TrangThaiDoc'] = false;

        if ($guiCho === 'all') {
            $customers = \App\Models\KhachHang::all();
            foreach ($customers as $kh) {
                $item = $data;
                $item['MaKH'] = $kh->MaKH;
                ThongBao::create($item);
            }
            return redirect()->route('admin.thongbao.index')->with('success', 'Đã gửi thông báo cho tất cả khách hàng!');
        } else {
            $data['MaKH'] = $request->input('MaKH');
            if (empty($data['MaKH'])) {
                return back()->with('error', 'Vui lòng chọn khách hàng!');
            }
            ThongBao::create($data);
            return redirect()->route('admin.thongbao.index')->with('success', 'Gửi thông báo thành công!');
        }
    }

    public function edit($id)
    {
        $thongBao = ThongBao::findOrFail($id);
        return view('admin.thongbao.edit', compact('thongBao'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TieuDe' => 'required',
            'NoiDung' => 'required',
        ]);

        $thongBao = ThongBao::findOrFail($id);
        $thongBao->update($request->all());

        return redirect()->route('admin.thongbao.index')->with('success', 'Cập nhật thông báo thành công!');
    }

    public function destroy($id)
    {
        $thongBao = ThongBao::findOrFail($id);
        $thongBao->delete();

        return redirect()->route('admin.thongbao.index')->with('success', 'Xóa thông báo thành công!');
    }
}
