<?php

namespace App\Imports;

use App\Models\OrbOption;
use App\Models\OrbQuest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OrbQuestionImport implements  ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param Collection $collection
    */
    protected string $tempImagePath;

    public function __construct(string $tempImagePath)
    {
        $this->tempImagePath = $tempImagePath;
    }

    public function collection(Collection $rows): void
    {
        Log::info('[WAWANCARA IMPORT] Total rows', [
            'rows' => $rows->count()
        ]);

        /** =====================
         * IMAGE MAP (SEKALI)
         * ===================== */
        $imageMap = collect(File::allFiles($this->tempImagePath))
            ->mapWithKeys(fn ($file) => [
                $file->getFilename() => $file->getRealPath()
            ]);

        DB::transaction(function () use ($rows, $imageMap) {

            foreach ($rows as $index => $row) {

                $rowNumber = $index + 2;

                /** =====================
                 * HANDLE GAMBAR
                 * ===================== */
                $imagePath = null;

                if (!empty($row['image_path'])) {

                    if (!$imageMap->has($row['image_path'])) {
                        throw new \Exception(
                            "Gambar '{$row['image_path']}' tidak ditemukan (baris {$rowNumber})"
                        );
                    }

                    $imagePath = 'soal/wawancara/' . uniqid() . '_' . $row['image_path'];

                    Storage::disk('public')->put(
                        $imagePath,
                        file_get_contents($imageMap[$row['image_path']])
                    );
                }

                /** =====================
                 * SIMPAN SOAL (GLOBAL)
                 * ===================== */
                $question = OrbQuest::create([
                    'subject'    => trim($row['subject']),
                    'pertanyaan' => trim($row['pertanyaan']),
                    'image_path' => $imagePath,
                ]);

                /** =====================
                 * OPSI (TIDAK DIUBAH)
                 * ===================== */
                $question->options()->delete();

                $options = [
                    ['label' => 'A', 'text' => 'option_a', 'point' => 'point_a'],
                    ['label' => 'B', 'text' => 'option_b', 'point' => 'point_b'],
                    ['label' => 'C', 'text' => 'option_c', 'point' => 'point_c'],
                    ['label' => 'D', 'text' => 'option_d', 'point' => 'point_d'],
                    ['label' => 'E', 'text' => 'option_e', 'point' => 'point_e'],
                ];

                foreach ($options as $opt) {
                    if (!empty($row[$opt['text']])) {
                        OrbOption::create([
                            'id_orb'       => $question->id,
                            'label'        => $opt['label'],
                            'opsi_tulisan' => $row[$opt['text']],
                            'point'        => (int) ($row[$opt['point']] ?? 0),
                        ]);
                    }
                }
            }
        });

        Log::info('[WAWANCARA IMPORT] Import selesai');
    }
}
