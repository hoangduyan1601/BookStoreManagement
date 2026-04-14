<?php
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$orders = DB::table('donhang')->where('TrangThai', 'DaGiao')->get();
echo "Found " . $orders->count() . " delivered orders.\n";
foreach ($orders as $order) {
    echo "MaDH: {$order->MaDH}, NgayDat: {$order->NgayDat}, TongTien: {$order->TongTien}, TrangThai: {$order->TrangThai}\n";
}

$years = DB::table('donhang')->select(DB::raw('YEAR(NgayDat) as year'))->distinct()->pluck('year');
echo "Order years in DB: " . implode(', ', $years->toArray()) . "\n";
