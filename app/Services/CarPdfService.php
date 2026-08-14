<?php

namespace App\Services;

use App\Models\Car;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CarPdfService
{
    /**
     * Generate a specification sheet PDF for a car and save it to storage.
     *
     * @param Car $car
     * @return string|null The relative path of the saved PDF file or null on failure.
     */
    public function generateAndSave(Car $car): ?string
    {
        $filePath = "cars/specs/car-{$car->id}-spec.pdf";

        try {
            // Resolve DomPDF safely regardless of server package casing or facade availability
            $pdf = null;
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('store.cars.pdf_report', ['car' => $car, 'pdfService' => $this]);
            } elseif (class_exists(\Barryvdh\DomPDF\Facade\PDF::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('store.cars.pdf_report', ['car' => $car, 'pdfService' => $this]);
            } elseif (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper')->loadView('store.cars.pdf_report', ['car' => $car, 'pdfService' => $this]);
            }

            if (!$pdf) {
                \Illuminate\Support\Facades\Log::warning("DomPDF package not found on server for car ID {$car->id}");
                return null;
            }

            $pdf->setPaper('a4', 'portrait')->setOption('isRemoteEnabled', true);

            // Ensure directories exist
            if (!Storage::disk('public')->exists('cars/specs')) {
                Storage::disk('public')->makeDirectory('cars/specs');
            }

            // Load necessary relations
            $car->load(['brand', 'category', 'specifications', 'features_list', 'images']);

            // Save file content
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($filePath, $pdfContent);

            return $filePath;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Car PDF generation skipped: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Resolve and return Base64 Data URI or absolute path for an image.
     *
     * @param string|null $path
     * @return string|null
     */
    public function getCleanImagePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $localPath = null;

        // If path is already a valid absolute file path on disk
        if (file_exists($path)) {
            $localPath = $path;
        } else {
            // Strip http/https domain if present
            $clean = preg_replace('#^https?://[^/]+/#i', '', $path);
            $clean = ltrim($clean, '/');

            // Strip storage/ prefix if present
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }

            $absolutePath = Storage::disk('public')->path($clean);
            if (file_exists($absolutePath)) {
                $localPath = $absolutePath;
            } else {
                $publicPath = public_path(ltrim($clean, '/'));
                if (file_exists($publicPath)) {
                    $localPath = $publicPath;
                }
            }
        }

        if ($localPath && file_exists($localPath) && is_readable($localPath)) {
            try {
                $info = @getimagesize($localPath);
                if ($info !== false && !empty($info['mime'])) {
                    $mime = $info['mime'];
                    $data = @file_get_contents($localPath);
                    if ($data !== false) {
                        return 'data:' . $mime . ';base64,' . base64_encode($data);
                    }
                }
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }
}
