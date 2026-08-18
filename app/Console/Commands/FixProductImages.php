<?php

namespace App\Console\Commands;

use App\Models\HinhAnhSanPham;
use App\Models\SanPham;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FixProductImages extends Command
{
    protected $signature = 'bookstore:fix-product-images {--dry-run : Show changes without writing to the database}';

    protected $description = 'Assign existing image files to products that do not have a primary image';

    public function handle(): int
    {
        $directory = public_path('assets/images/products');
        if (! File::isDirectory($directory)) {
            $this->error("Image directory does not exist: {$directory}");

            return self::FAILURE;
        }

        $images = collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true))
            ->map(fn ($file) => $file->getFilename())
            ->sort()
            ->values();

        if ($images->isEmpty()) {
            $this->warn('No supported image files were found.');

            return self::SUCCESS;
        }

        $products = SanPham::whereNull('HinhAnh')->orWhere('HinhAnh', '')->orderBy('MaSP')->get();
        if ($products->isEmpty()) {
            $this->info('All products already have an image. No changes were needed.');

            return self::SUCCESS;
        }

        $this->info("Products without images: {$products->count()}");
        if ($this->option('dry-run')) {
            $this->table(['Product ID', 'Image'], $products->values()->map(
                fn ($product, $index) => [$product->MaSP, $images[$index % $images->count()]]
            ));

            return self::SUCCESS;
        }

        DB::transaction(function () use ($products, $images): void {
            foreach ($products->values() as $index => $product) {
                $image = $images[$index % $images->count()];
                $product->update(['HinhAnh' => $image]);
                HinhAnhSanPham::updateOrCreate(
                    ['MaSP' => $product->MaSP, 'DuongDan' => $image],
                    ['LaAnhChinh' => true]
                );
            }
        });

        $this->info("Updated {$products->count()} products.");

        return self::SUCCESS;
    }
}
