<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:webp {--quality=82 : WebP quality (0-100)} {--force : Reconvert even if .webp exists}';
    protected $description = 'Convert JPG/PNG images in storage/app/public to WebP (lossy, quality default 82).';

    public function handle(): int
    {
        $root = storage_path('app/public');
        $quality = (int) $this->option('quality');
        $force = (bool) $this->option('force');

        if (!function_exists('imagewebp')) {
            $this->error('GD WebP support (imagewebp) not available. Enable PHP GD with WebP or use an external tool.');
            return Command::FAILURE;
        }

        $count = 0; $skipped = 0; $errors = 0;
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['jpg','jpeg','png'])) continue;
            $srcPath = $file->getPathname();
            $rel = ltrim(str_replace($root, '', $srcPath), DIRECTORY_SEPARATOR);
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $srcPath);
            if (!$force && is_file($webpPath)) { $skipped++; continue; }
            try {
                if ($ext === 'png') {
                    $img = imagecreatefrompng($srcPath);
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                } else {
                    $img = imagecreatefromjpeg($srcPath);
                }
                if (!$img) throw new \RuntimeException('Unable to open image');
                if (!imagewebp($img, $webpPath, $quality)) throw new \RuntimeException('imagewebp failed');
                imagedestroy($img);
                $count++;
                $this->line("✓ {$rel} -> ".basename($webpPath));
            } catch (\Throwable $e) {
                $errors++;
                $this->warn("! Failed {$rel}: ".$e->getMessage());
            }
        }
        $this->info("Done. Converted: {$count}, Skipped: {$skipped}, Errors: {$errors}");
        return Command::SUCCESS;
    }
}

