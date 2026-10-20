<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class OptimizeLandingImages extends Command
{
    /**
     * @var string
     */
    protected $signature = 'landing:optimize';

    /**
     * @var string
     */
    protected $description = 'Buat ulang aset foto latar landing /staf dari landing.jpg';

    public function handle(): int
    {
        $source = base_path('landing.jpg');

        if (! is_file($source)) {
            $this->error('File master landing.jpg tidak ditemukan di root proyek.');

            return self::FAILURE;
        }

        $binary = $this->imageMagickBinary();

        if ($binary === null) {
            $this->error('ImageMagick tidak ditemukan. Pasang ImageMagick agar perintah ini dapat dijalankan.');

            return self::FAILURE;
        }

        $targetDirectory = public_path('images');

        if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0755, true) && ! is_dir($targetDirectory)) {
            $this->error("Tidak dapat membuat direktori {$targetDirectory}.");

            return self::FAILURE;
        }

        $variants = [
            ['file' => 'landing-bg-1920.webp', 'width' => 1920, 'quality' => 78, 'jpeg' => false],
            ['file' => 'landing-bg-1280.webp', 'width' => 1280, 'quality' => 78, 'jpeg' => false],
            ['file' => 'landing-bg-768.webp', 'width' => 768, 'quality' => 78, 'jpeg' => false],
            ['file' => 'landing-bg-1920.jpg', 'width' => 1920, 'quality' => 72, 'jpeg' => true],
        ];

        $rows = [];

        foreach ($variants as $variant) {
            $target = $targetDirectory.DIRECTORY_SEPARATOR.$variant['file'];

            $arguments = [
                $binary,
                $source,
                '-auto-orient',
                '-resize',
                $variant['width'].'x',
                '-strip',
            ];

            if ($variant['jpeg']) {
                $arguments = array_merge($arguments, [
                    '-interlace',
                    'Plane',
                    '-sampling-factor',
                    '4:2:0',
                ]);
            }

            $arguments = array_merge($arguments, [
                '-quality',
                (string) $variant['quality'],
                $target,
            ]);

            $result = Process::timeout(300)->run($arguments);

            if ($result->failed()) {
                $this->error("Gagal membuat {$variant['file']}: ".trim($result->errorOutput()));

                return self::FAILURE;
            }

            $bytes = is_file($target) ? filesize($target) : 0;

            $rows[] = [
                $variant['file'],
                number_format($bytes / 1024, 0).' KB',
                $bytes <= 400 * 1024 ? 'OK' : 'Di atas target 400 KB',
            ];
        }

        $this->table(['File', 'Ukuran', 'Target < 400 KB'], $rows);
        $this->info('Aset foto latar landing berhasil dibuat ulang.');

        return self::SUCCESS;
    }

    private function imageMagickBinary(): ?string
    {
        foreach (['magick', 'convert'] as $binary) {
            try {
                if (Process::timeout(10)->run([$binary, '-version'])->successful()) {
                    return $binary;
                }
            } catch (\Throwable) {
                //
            }
        }

        return null;
    }
}
