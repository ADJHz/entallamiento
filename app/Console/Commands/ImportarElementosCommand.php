<?php

namespace App\Console\Commands;

use App\Models\Elemento;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class ImportarElementosCommand extends Command
{
    protected $signature = 'elementos:importar
                            {archivo : Ruta del archivo Excel que se importara}
                            {--hoja= : Nombre de la hoja; por defecto se usa la hoja activa}';

    protected $description = 'Importa elementos desde un archivo Excel y registra el resultado de cada fila';

    /**
     * @var array<int, string>
     */
    private const REQUIRED_COLUMNS = [
        'csp',
        'nombre',
        'cuip',
        'genero',
        'tipo_uniforme',
        'coordinacion_area',
        'direccion',
        'color',
        'franja',
    ];

    public function handle(): int
    {
        $path = $this->argument('archivo');
        $runId = (string) str()->uuid();
        $startedAt = now();

        if (! is_readable($path)) {
            $this->error("No se puede leer el archivo: {$path}");
            Log::channel('elementos_import')->error('Importacion de elementos rechazada: archivo no legible.', [
                'run_id' => $runId,
                'archivo' => $path,
            ]);

            return self::FAILURE;
        }

        Log::channel('elementos_import')->info('Importacion de elementos iniciada.', [
            'run_id' => $runId,
            'archivo' => $path,
            'hoja' => $this->option('hoja'),
            'iniciada_at' => $startedAt->toIso8601String(),
        ]);

        try {
            $spreadsheet = IOFactory::load($path);
            $worksheet = $this->getWorksheet($spreadsheet);
            $rows = $worksheet->toArray(null, true, true, true);
            $headers = $this->getHeaders($rows[1] ?? []);
            $missingColumns = array_diff(self::REQUIRED_COLUMNS, array_values($headers));

            if ($missingColumns !== []) {
                throw new \RuntimeException('Faltan columnas requeridas: '.implode(', ', $missingColumns));
            }
        } catch (Throwable $exception) {
            $this->error('No se pudo preparar la importacion: '.$exception->getMessage());
            Log::channel('elementos_import')->error('Importacion de elementos detenida al leer el archivo.', [
                'run_id' => $runId,
                'archivo' => $path,
                'error' => $exception->getMessage(),
            ]);

            return self::FAILURE;
        }

        $processed = 0;
        $imported = 0;
        $failed = 0;

        foreach (array_slice($rows, 1, null, true) as $rowNumber => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $processed++;
            $attributes = $this->getAttributes($row, $headers);

            try {
                $elemento = DB::transaction(
                    fn (): Elemento => Elemento::create($attributes),
                );
                $imported++;

                Log::channel('elementos_import')->info('Registro de elemento procesado correctamente.', [
                    'run_id' => $runId,
                    'fila' => $rowNumber,
                    'elemento_id' => $elemento->id,
                    'cuip' => $attributes['cuip'],
                ]);
                $this->line("Fila {$rowNumber}: importada");
            } catch (Throwable $exception) {
                $failed++;
                Log::channel('elementos_import')->error('Error al procesar registro de elemento.', [
                    'run_id' => $runId,
                    'fila' => $rowNumber,
                    'datos' => $attributes,
                    'error' => $exception->getMessage(),
                ]);
                $this->warn("Fila {$rowNumber}: {$exception->getMessage()}");
            }
        }

        $summary = [
            'run_id' => $runId,
            'archivo' => $path,
            'procesados' => $processed,
            'importados' => $imported,
            'errores' => $failed,
            'finalizada_at' => now()->toIso8601String(),
        ];
        Log::channel('elementos_import')->info('Importacion de elementos finalizada.', $summary);

        $this->newLine();
        $this->info("Importacion finalizada: {$imported} importados, {$failed} con error.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function getWorksheet(Spreadsheet $spreadsheet): Worksheet
    {
        $sheetName = $this->option('hoja');

        if (is_string($sheetName) && $sheetName !== '') {
            return $spreadsheet->getSheetByName($sheetName)
                ?? throw new \RuntimeException("No existe la hoja '{$sheetName}'.");
        }

        return $spreadsheet->getActiveSheet();
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, string>
     */
    private function getHeaders(array $row): array
    {
        $headers = [];

        foreach ($row as $column => $value) {
            $header = mb_strtolower(trim((string) $value));
            $header = str_replace([' ', '-'], '_', $header);
            $headers[$column] = $header;
        }

        return $headers;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, string>  $headers
     * @return array<string, string|null>
     */
    private function getAttributes(array $row, array $headers): array
    {
        $attributes = [];

        foreach ($headers as $column => $header) {
            if (in_array($header, self::REQUIRED_COLUMNS, true)) {
                $value = trim((string) ($row[$column] ?? ''));
                $attributes[$header] = $value === '' ? null : $value;
            }
        }

        return array_replace(array_fill_keys(self::REQUIRED_COLUMNS, null), $attributes);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        return count(array_filter(
            $row,
            static fn (mixed $value): bool => trim((string) $value) !== '',
        )) === 0;
    }
}
