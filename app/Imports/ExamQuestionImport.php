<?php

namespace App\Imports;

use App\Models\ExamQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\File;

class ExamQuestionImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows
{
    protected int $examId;
    protected string $tempImagePath;

    public function __construct(int $examId, string $tempImagePath)
    {
        $this->examId = $examId;
        $this->tempImagePath = $tempImagePath;
    }



    public function collection(Collection $rows)
{
    Log::info('[DEBUG HEADER]', [
        'keys' => $rows->first()?->keys()
    ]);

    Log::info('[IMPORT] Jumlah baris Excel', [
        'total_rows' => $rows->count()
    ]);

    // 🔑 BUILD MAP: filename => fullpath
    $imageMap = collect(File::allFiles($this->tempImagePath))
        ->mapWithKeys(fn ($file) => [
            $file->getFilename() => $file->getRealPath()
        ]);

    Log::info('[IMPORT] Total file gambar ditemukan', [
        'count' => $imageMap->count()
    ]);

    DB::transaction(function () use ($rows, $imageMap) {

        foreach ($rows as $index => $row) {

            Log::info('[IMPORT] Proses baris', [
                'row' => $index + 1,
                'code_pertanyaan' => $row['code_pertanyaan'] ?? null,
            ]);

            $gambarPath = null;

                if (!empty($row['image_name']) && $imageMap->has($row['image_name'])) {

                    $gambarPath = 'soal/' . strtolower($row['subject']) . '/' . $row['image_name'];

                    Storage::disk('public')->put(
                        $gambarPath,
                        file_get_contents($imageMap[$row['image_name']])
                    );

                    Log::info('[IMPORT] Gambar ditemukan & disimpan', [
                        'file' => $row['image_name'],
                        'path' => $gambarPath,
                    ]);

                } elseif (!empty($row['image_name'])) {

                    Log::warning('[IMPORT] Gambar TIDAK ditemukan', [
                        'image_name' => $row['image_name'],
                    ]);
                }

            $question = ExamQuestion::updateOrCreate(
                [
                    'id_exam' => $this->examId,
                    'code_pertanyaan' => $row['code_pertanyaan']
                ],
                [
                    'subject'       => $row['subject'],
                    'pertanyaan'    => $row['pertanyaan'],
                    'image_name'    => $gambarPath,
                    'jawaban_benar' => strtoupper($row['jawaban_benar']),
                ]
            );

            $question->options()->delete();

            $question->options()->createMany([
                ['label' => 'A', 'opsi_tulisan' => $row['option_a']],
                ['label' => 'B', 'opsi_tulisan' => $row['option_b']],
                ['label' => 'C', 'opsi_tulisan' => $row['option_c']],
                ['label' => 'D', 'opsi_tulisan' => $row['option_d']],
            ]);
        }
    });

    Log::info('[IMPORT] Transaction selesai');
}


    public function rules(): array
    {
        return [
            '*.subject'         => 'required|string',
            '*.code_pertanyaan' => 'required|string',
            '*.pertanyaan'      => 'required|string',
            '*.option_a'        => 'required|string',
            '*.option_b'        => 'required|string',
            '*.option_c'        => 'required|string',
            '*.option_d'        => 'required|string',
            '*.jawaban_benar'   => 'required|in:A,B,C,D',
            '*.image_name'      => 'nullable|string',
        ];
    }

}
