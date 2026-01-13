<?php

namespace App\Http\Controllers;

use App\Models\rankings;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SawExport extends Controller
{

    public function convertRankingSawToPdf($seleksiId)
    {
        try {

            $rankings = rankings::with(['user', 'seleksi'])
                ->where('id_seleksi', $seleksiId)
                ->orderBy('peringkat')
                ->get();

            if ($rankings->isEmpty()) {
                throw new \Exception('Data ranking SAW belum tersedia.');
            }

            $seleksi = $rankings->first()->seleksi;

            $templatePath = storage_path('app/public/template_ranking_saw.docx');
            $outputDocx   = storage_path("app/public/ranking_saw_{$seleksiId}.docx");
            $outputPdfDir = storage_path('app/public');
            $outputPdf    = "{$outputPdfDir}/ranking_saw_{$seleksiId}.pdf";

            if (!file_exists($templatePath)) {
                throw new \Exception('Template ranking SAW tidak ditemukan.');
            }

            $template = new TemplateProcessor ($templatePath);

            // Header
            $template->setValue('judul_seleksi', $seleksi->judul ?? '-');
            $template->setValue(
                'tanggal_generate',
                Carbon::now()->translatedFormat('d F Y')
            );

            // ================================
            // 4. Clone tabel ranking
            // ================================
            $template->cloneRow('nama_user', $rankings->count());

            foreach ($rankings as $i => $ranking) {
                $n = $i + 1;

                $template->setValue("no#{$n}", $n);
                $template->setValue("nama_user#{$n}", $ranking->user->name ?? '-');
                $template->setValue("nilai_saw#{$n}", number_format($ranking->nilai_saw, 4));
                $template->setValue("peringkat#{$n}", $ranking->peringkat);
            }

            // ================================
            // 5. Simpan DOCX
            // ================================
            $template->saveAs($outputDocx);

            // ================================
            // 6. Convert ke PDF (LibreOffice)
            // ================================
            $command = "soffice --headless --convert-to pdf --outdir "
                . escapeshellarg($outputPdfDir) . " "
                . escapeshellarg($outputDocx);

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Gagal konversi PDF. LibreOffice tidak tersedia.');
            }

            // ================================
            // 7. Simpan ke storage public
            // ================================
            if (file_exists($outputPdf)) {
                Storage::disk('public')->putFileAs(
                    'ranking_saw',
                    new \Illuminate\Http\File($outputPdf),
                    "ranking_saw_{$seleksiId}.pdf"
                );
            }

            return response()->json([
                'status' => 'success',
                'file'   => asset("storage/ranking_saw/ranking_saw_{$seleksiId}.pdf")
            ]);

            activity_log(
                'Generate File',
                'Generate File ranking SAW seleksi',
                null,
                null,
                [
                    'id_seleksi' => $seleksiId,
                    'method' => 'SAW + Fuzzy'
                ]
            );

        } catch (\Exception $e) {

            Log::error('Gagal generate PDF Ranking SAW: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
