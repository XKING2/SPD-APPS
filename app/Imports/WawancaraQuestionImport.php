<?php

namespace App\Imports;

use App\Models\ExamWawancara;
use App\Models\WawancaraOption;
use App\Models\wawancaraquest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\File;

class WawancaraQuestionImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param Collection $collection
    */
    protected int $examId;
    protected string $tempImagePath;

    public function __construct(int $examId, string $tempImagePath)
    {
        $this->examId = $examId;
        $this->tempImagePath = $tempImagePath;
    }

    public function collection(Collection $rows)
    {
        Log::info('[WAWANCARA IMPORT] Total rows', [
            'rows' => $rows->count()
        ]);

        DB::transaction(function () use ($rows) {

            foreach ($rows as $index => $row) {

                Log::info('[WAWANCARA IMPORT] Proses baris', [
                    'row' => $index + 1,
                    'code' => $row['code_pertanyaan'] ?? null
                ]);


            $imageMap = collect(File::allFiles($this->tempImagePath))
                ->mapWithKeys(fn ($file) => [
                    $file->getFilename() => $file->getRealPath()
                ]);

            Log::info('[WAWANCARA IMPORT] Total image', [
                    'count' => $imageMap->count()
                ]);

            $imagePath = null;


            if (!empty($row['image_path']) && $imageMap->has($row['image_path'])) {

                $imagePath = 'soal/' . strtolower($row['subject']) . '/' . $row['image_path'];

                Storage::disk('public')->put(
                    $imagePath,
                    file_get_contents($imageMap[$row['image_path']])
                );

                Log::info('[WAWANCARA IMPORT] Gambar disimpan', [
                    'file' => $row['image_path'],
                    'path' => $imagePath
                ]);

            } elseif (!empty($row['image_path'])) {

                Log::warning('[WAWANCARA IMPORT] Gambar tidak ditemukan', [
                    'image_path' => $row['image_path']
                ]);
            }

                $question = wawancaraquest::updateOrCreate(
                    [   'id_exams' => $this->examId,
                        'code_pertanyaan' => $row['code_pertanyaan']],
                    [
                        'subject'     => $row['subject'],
                        'pertanyaan'  => $row['pertanyaan'],
                        'image_path'  => $imagePath,
                    ]
                );

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
                        WawancaraOption::create([
                            'id_wwn'      => $question->id,
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
