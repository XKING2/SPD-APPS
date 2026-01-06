<?php

namespace App\Imports;

use App\Models\ExamOption;
use App\Models\ExamQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class ExamQuestionImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected string $tempImagePath;

    public function __construct(string $tempImagePath)
    {
        $this->tempImagePath = $tempImagePath;
    }

    public function collection(Collection $rows): void
    {
        $imageMap = collect(File::allFiles($this->tempImagePath))
            ->mapWithKeys(fn ($file) => [
                $file->getFilename() => $file->getRealPath()
            ]);

        DB::transaction(function () use ($rows, $imageMap) {

            foreach ($rows as $index => $row) {

                $rowNumber = $index + 2; // karena heading
                $jawaban   = strtoupper(trim($row['jawaban_benar']));

                /** =====================
                 * HANDLE GAMBAR (OPTIONAL)
                 * ===================== */
                $gambarPath = null;

                if (!empty($row['image_name'])) {

                    if (!$imageMap->has($row['image_name'])) {
                        throw new \Exception(
                            "Gambar '{$row['image_name']}' tidak ditemukan (baris {$rowNumber})"
                        );
                    }

                    $gambarPath = 'soal/' . uniqid() . '_' . $row['image_name'];

                    Storage::disk('public')->put(
                        $gambarPath,
                        file_get_contents($imageMap[$row['image_name']])
                    );
                }

                /** =====================
                 * SIMPAN SOAL
                 * ===================== */
                $question = ExamQuestion::create([
                    'subject'    => trim($row['subject']),
                    'pertanyaan' => trim($row['pertanyaan']),
                    'image_name' => $gambarPath,
                ]);

                /** =====================
                 * SIMPAN OPSI
                 * ===================== */
                $optionsMap = [];

                foreach ([
                    'A' => $row['option_a'],
                    'B' => $row['option_b'],
                    'C' => $row['option_c'],
                    'D' => $row['option_d'],
                ] as $label => $text) {

                    if (trim($text) === '') {
                        throw new \Exception(
                            "Opsi {$label} kosong (baris {$rowNumber})"
                        );
                    }

                    $option = ExamOption::create([
                        'id_Pertanyaan' => $question->id,
                        'label'         => $label,
                        'opsi_tulisan'  => trim($text),
                    ]);

                    $optionsMap[$label] = $option->id;
                }

                /** =====================
                 * SET JAWABAN BENAR
                 * ===================== */
                if (!isset($optionsMap[$jawaban])) {
                    throw new \Exception(
                        "Jawaban benar '{$jawaban}' tidak valid (baris {$rowNumber})"
                    );
                }

                $question->update([
                    'correct_option_id' => $optionsMap[$jawaban]
                ]);
            }
        });

        Log::info('[IMPORT] Import soal TPU selesai');
    }

    public function rules(): array
    {
        return [
            '*.subject'       => 'required|string',
            '*.pertanyaan'    => 'required|string',
            '*.option_a'      => 'required|string',
            '*.option_b'      => 'required|string',
            '*.option_c'      => 'required|string',
            '*.option_d'      => 'required|string',
            '*.jawaban_benar' => 'required|in:A,B,C,D',
            '*.image_name'    => 'nullable|string',
        ];
    }
}

