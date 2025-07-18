<?php

namespace App\Models\acadbase;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CsvModel extends Model
{
    protected $table = 'acadbase';

    protected $fillable = [
        'lrn',
        'name',
        'track',
        'batch',
        'curriculum',
        'status',
        'birthdate',
        'faculty_name',
        'gender'
    ];

    public static function importFromFile($file)
    {
        $handle = fopen($file->getPathname(), 'r');
        
        // Get headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            throw new \Exception('Invalid CSV file: No headers found');
        }

        // Normalize headers
        $normalizedHeaders = [];
        foreach ($headers as $header) {
            $normalizedHeaders[] = strtolower(trim(str_replace(' ', '_', $header)));
        }

        $stats = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        // Process rows
        while (($row = fgetcsv($handle)) !== false) {
            $stats['total']++;
            $data = array_combine($normalizedHeaders, $row);
            
            try {
                // Map your CSV headers to expected fields
                $lrn = self::getLrnValue($data);
                $name = self::getValueByKeys($data, ['student_name', 'name', 'student']);
                $track = self::getValueByKeys($data, ['academic_track', 'track']);
                $curriculum = self::getValueByKeys($data, ['curriculum']);
                $batch = self::getValueByKeys($data, ['batch', 's.y_batch', 'sy_batch', 'school_year']);
                $birthdate = self::getValueByKeys($data, ['birthday', 'birthdate', 'birth_date']);
                $gender = self::getValueByKeys($data, ['gender', 'sex']);

                // Format birthdate
                $formattedBirthdate = self::formatBirthdate($birthdate);

                // Skip if required fields are missing
                if (empty($lrn) || empty($name)) {
                    $stats['skipped']++;
                    $stats['errors'][] = "Row {$stats['total']}: Missing required fields (LRN or Name)";
                    continue;
                }

                // Check if student exists
                $existingStudent = MasterlistModel::where('lrn', $lrn)->first();

                if ($existingStudent) {
                    // Update existing student
                    $existingStudent->update([
                        'name' => $name,
                        'track' => $track ?: 'SPJ',
                        'batch' => $batch ?: (date('Y') . '-' . (date('Y') + 1)),
                        'curriculum' => $curriculum ?: 'JHS',
                        'status' => 'Not-Applicable',
                        'birthdate' => $formattedBirthdate,
                        'faculty_name' => null, // Leave blank for now
                        'gender' => $gender ?: null
                    ]);
                    $stats['updated']++;
                } else {
                    // Create new student
                    MasterlistModel::create([
                        'lrn' => $lrn,
                        'name' => $name,
                        'track' => $track ?: 'SPJ',
                        'batch' => $batch ?: (date('Y') . '-' . (date('Y') + 1)),
                        'curriculum' => $curriculum ?: 'JHS',
                        'status' => 'Not-Applicable',
                        'birthdate' => $formattedBirthdate,
                        'faculty_name' => null, // Leave blank for now
                        'gender' => $gender ?: null
                    ]);
                    $stats['imported']++;
                }
            } catch (\Exception $e) {
                $stats['skipped']++;
                $stats['errors'][] = "Row {$stats['total']}: " . $e->getMessage();
            }
        }

        fclose($handle);
        return $stats;
    }

    private static function getLrnValue($row)
    {
        $lrn = self::getValueByKeys($row, ['lrn']);
        
        if (empty($lrn)) {
            return null;
        }

        // Handle scientific notation
        if (strpos($lrn, 'E+') !== false) {
            $lrn = number_format($lrn, 0, '', '');
        }

        // Remove any decimal points and ensure it's a string
        $lrn = str_replace('.', '', (string)$lrn);
        
        return $lrn;
    }

    private static function getValueByKeys($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
        }
        return null;
    }

    private static function formatBirthdate($birthdate)
    {
        if (empty($birthdate)) {
            return null;
        }

        try {
            // If it's an Excel date number
            if (is_numeric($birthdate)) {
                $date = Date::excelToDateTimeObject($birthdate);
                return $date->format('Y-m-d');
            }

            // Try different date formats
            $formats = [
                'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d',
                'd-m-Y', 'm-d-Y', 'Y-m-d',
                'd.m.Y', 'm.d.Y', 'Y.m.d'
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $birthdate);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // If all else fails, try PHP's date parser
            $date = new \DateTime($birthdate);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::error("Failed to parse birthdate: " . $birthdate . " - Error: " . $e->getMessage());
            return null;
        }
    }
}