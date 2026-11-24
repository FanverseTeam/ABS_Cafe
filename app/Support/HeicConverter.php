<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class HeicConverter
{
    /**
     * Mengecek apakah file merupakan format HEIC atau HEIF.
     */
    public static function isHeic(UploadedFile|string $file): bool
    {
        if ($file instanceof UploadedFile) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, ['heic', 'heif'], true)) {
                return true;
            }
            $mime = strtolower((string) $file->getClientMimeType());

            return in_array($mime, ['image/heic', 'image/heif'], true);
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        return in_array($ext, ['heic', 'heif'], true);
    }

    /**
     * Konversi UploadedFile berformat HEIC/HEIF menjadi UploadedFile berformat JPEG.
     * Jika bukan HEIC atau konversi gagal, mengembalikan file aslinya.
     */
    public static function convertUploadedFile(UploadedFile $file): UploadedFile
    {
        if (! self::isHeic($file)) {
            return $file;
        }

        $sourcePath = $file->getRealPath();
        if (! $sourcePath || ! file_exists($sourcePath)) {
            return $file;
        }

        $tempJpg = tempnam(sys_get_temp_dir(), 'heic_conv_').'.jpg';

        $converted = self::convertFile($sourcePath, $tempJpg);
        if ($converted && file_exists($tempJpg) && filesize($tempJpg) > 0) {
            $originalName = $file->getClientOriginalName();
            $newOriginalName = preg_replace('/\.(heic|heif)$/i', '.jpg', $originalName) ?: ($originalName.'.jpg');

            return new UploadedFile(
                $tempJpg,
                $newOriginalName,
                'image/jpeg',
                null,
                true // test mode agar dapat menggunakan file lokal
            );
        }

        return $file;
    }

    /**
     * Konversi file fisik HEIC di disk menjadi file JPG.
     * Mengembalikan true jika sukses.
     */
    public static function convertFile(string $inputPath, string $outputPath, int $quality = 90): bool
    {
        if (! file_exists($inputPath)) {
            return false;
        }

        $scriptPath = storage_path('app/convert-heic.ps1');
        if (! file_exists($scriptPath)) {
            self::ensureScriptExists($scriptPath);
        }

        $cmd = sprintf(
            'powershell -NoProfile -ExecutionPolicy Bypass -File %s -InputPath %s -OutputPath %s -Quality %d 2>&1',
            escapeshellarg($scriptPath),
            escapeshellarg($inputPath),
            escapeshellarg($outputPath),
            $quality
        );

        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
            return true;
        }

        Log::warning('Gagal mengonversi file HEIC ke JPG: '.implode("\n", $output));

        return false;
    }

    /**
     * Pastikan script konversi PowerShell tersedia di storage/app.
     */
    private static function ensureScriptExists(string $scriptPath): void
    {
        $dir = dirname($scriptPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $scriptContent = <<<'POWERSHELL'
param(
    [Parameter(Mandatory=$true)]
    [string]$InputPath,

    [Parameter(Mandatory=$true)]
    [string]$OutputPath,

    [int]$Quality = 90
)

try {
    Add-Type -AssemblyName PresentationCore
    $resolvedIn = (Resolve-Path $InputPath).Path
    $uri = New-Object System.Uri($resolvedIn)
    $decoder = [System.Windows.Media.Imaging.BitmapDecoder]::Create($uri, [System.Windows.Media.Imaging.BitmapCreateOptions]::None, [System.Windows.Media.Imaging.BitmapCacheOption]::OnLoad)
    $encoder = New-Object System.Windows.Media.Imaging.JpegBitmapEncoder
    $encoder.QualityLevel = $Quality
    $encoder.Frames.Add($decoder.Frames[0])
    
    $outDir = [System.IO.Path]::GetDirectoryName($OutputPath)
    if (-not [System.IO.Directory]::Exists($outDir)) {
        [System.IO.Directory]::CreateDirectory($outDir) | Out-Null
    }

    $outStream = [System.IO.File]::Create($OutputPath)
    $encoder.Save($outStream)
    $outStream.Close()
    Write-Output "OK"
    exit 0
} catch {
    Write-Error $_.Exception.Message
    exit 1
}
POWERSHELL;

        @file_put_contents($scriptPath, $scriptContent);
    }
}
