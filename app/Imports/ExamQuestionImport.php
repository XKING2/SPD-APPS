<?php

namespace App\Imports;

use App\Models\ExamQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExamQuestionImport implements ToCollection,WithHeadingRow,SkipsEmptyRows
{
    protected int $examId;
    protected string $tempImagePath;


    public function __construct(int $examId, string $tempImagePath)
    {
        $this->examId = $examId;
        $this->tempImagePath = $tempImagePath;
    }

    public function collection(Collection $rows): void
    {

        $imageMap = collect(File::allFiles($this->tempImagePath))
            ->mapWithKeys(fn ($file) => [
                $file->getFilename() => $file->getRealPath()
            ]);

        try {

            DB::transaction(function () use ($rows, $imageMap) {

                foreach ($rows as $index => $row) {

                    $rowNumber = $index + 1;
                    $subject  = trim($row['subject']);
                    $code     = trim($row['code_pertanyaan']);
                    $jawaban  = strtoupper(trim($row['jawaban_benar']));

                    $gambarPath = null;

                    if (!empty($row['image_name'])) {


                        if (!$imageMap->has($row['image_name'])) {
                            throw new \Exception(
                                "Gambar '{$row['image_name']}' tidak ditemukan (baris {$rowNumber})"
                            );
                        }

                        $gambarPath = 'soal/' . strtolower($subject) . '/' . uniqid() . '_' . $row['image_name'];

                        Storage::disk('public')->put(
                            $gambarPath,
                            file_get_contents($imageMap[$row['image_name']])
                        );

                    }


                    $question = ExamQuestion::updateOrCreate(
                        [
                            'id_exam' => $this->examId,
                            'code_pertanyaan' => $code,
                        ],
                        [
                            'subject'    => $subject,
                            'pertanyaan' => trim($row['pertanyaan']),
                            'image_name' => $gambarPath,
                        ]
                    );
;

                    $question->options()->delete();
                    $optionsMap = [];

                    foreach ([
                        'A' => $row['option_a'],
                        'B' => $row['option_b'],
                        'C' => $row['option_c'],
                        'D' => $row['option_d'],
                    ] as $label => $text) {

                        if (trim($text) === '') {
                            throw new \Exception(
                                "Opsi {$label} kosong pada soal {$code} (baris {$rowNumber})"
                            );
                        }

                        $option = $question->options()->create([
                            'label' => $label,
                            'opsi_tulisan' => trim($text),
                        ]);

                        $optionsMap[$label] = $option->id;
;
                    }

                    if (!isset($optionsMap[$jawaban])) {

                        throw new \Exception(
                            "Jawaban benar '{$jawaban}' tidak valid pada soal '{$code}' (baris {$rowNumber})"
                        );
                    }

                    $question->update([
                        'correct_option_id' => $optionsMap[$jawaban]
                    ]);
                }
            });

        } catch (\Throwable $e) {


            throw $e;
        }

        Log::info('[IMPORT] Import soal TPU selesai');
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
