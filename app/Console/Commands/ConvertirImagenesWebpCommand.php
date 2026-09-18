<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class ConvertirImagenesWebpCommand extends Command
{
    protected $signature = 'catalogo:convertir-webp
                            {--quality=82 : Calidad WebP entre 0 y 100}
                            {--width=900 : Ancho maximo de la imagen convertida}
                            {--delete-originals : Elimina los archivos originales despues de actualizar el catalogo}';

    protected $description = 'Convierte las imagenes del catalogo a WebP y actualiza sus referencias';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('La extension GD de PHP no esta disponible o no soporta WebP.');

            return self::FAILURE;
        }

        $quality = (int) $this->option('quality');
        $maxWidth = (int) $this->option('width');

        if ($quality < 0 || $quality > 100) {
            $this->error('La calidad debe estar entre 0 y 100.');

            return self::INVALID;
        }

        if ($maxWidth < 1) {
            $this->error('El ancho maximo debe ser mayor que cero.');

            return self::INVALID;
        }

        $catalogPath = public_path('camisas');

        if (! is_dir($catalogPath)) {
            $this->error("No existe el directorio {$catalogPath}.");

            return self::FAILURE;
        }

        $converted = 0;
        $skipped = 0;
        $failed = 0;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($catalogPath, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'png') {
                continue;
            }

            $sourcePath = $file->getPathname();
            $targetPath = $file->getPath().DIRECTORY_SEPARATOR.$file->getBasename('.png').'.webp';

            if (is_file($targetPath)) {
                $this->updateCatalogReference($sourcePath, $targetPath, $catalogPath);
                $skipped++;
                continue;
            }

            try {
                $this->convertFile($sourcePath, $targetPath, $quality, $maxWidth);
                $this->updateCatalogReference($sourcePath, $targetPath, $catalogPath);

                if ($this->option('delete-originals')) {
                    unlink($sourcePath);
                }

                $converted++;
                $this->line('Convertida: '.str_replace(public_path().DIRECTORY_SEPARATOR, '', $sourcePath));
            } catch (RuntimeException $exception) {
                $failed++;
                $this->error('Error: '.$exception->getMessage());
            }
        }

        $this->newLine();
        $this->info("Conversion finalizada: {$converted} convertidas, {$skipped} omitidas, {$failed} con error.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function convertFile(string $sourcePath, string $targetPath, int $quality, int $maxWidth): void
    {
        $source = imagecreatefrompng($sourcePath);

        if ($source === false) {
            throw new RuntimeException("No se pudo leer {$sourcePath}.");
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetWidth = min($sourceWidth, $maxWidth);
        $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        if (! imagewebp($canvas, $targetPath, $quality)) {
            imagedestroy($source);
            imagedestroy($canvas);
            throw new RuntimeException("No se pudo escribir {$targetPath}.");
        }

        imagedestroy($source);
        imagedestroy($canvas);
    }

    private function updateCatalogReference(string $sourcePath, string $targetPath, string $catalogPath): void
    {
        $sourceReference = $this->catalogReference($sourcePath, $catalogPath);
        $targetReference = $this->catalogReference($targetPath, $catalogPath);

        DB::table('color_franja')
            ->where('imagen', $sourceReference)
            ->update(['imagen' => $targetReference]);
    }

    private function catalogReference(string $path, string $catalogPath): string
    {
        $relativePath = str_replace($catalogPath.DIRECTORY_SEPARATOR, '', $path);
        $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
        $file = array_pop($parts);

        return '/camisas/'.implode('/', array_map('rawurlencode', $parts)).'/'.rawurlencode($file);
    }
}
