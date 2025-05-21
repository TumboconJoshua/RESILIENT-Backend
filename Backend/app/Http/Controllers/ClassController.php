<?php

namespace App\Http\Controllers;

use App\Models\ClassesModel;
use App\Models\ClassSubjectModel;
use App\Models\StudentClassModel;
use App\Models\SubjectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function getClasses()
    {
        try {
            $classes = ClassesModel::with(['subjects.subject', 'students'])
                ->get()
                ->map(function ($class) {
                    return [
                        'class_id' => $class->Class_ID,
                        'trackStand' => $class->Track,
                        'classType' => ($class->students->first() && $class->students->first()->pivot && $class->students->first()->pivot->isAdvisory)
                            ? 'Advisory'
                            : 'Subject',
                        'className' => $class->ClassName,
                        'gradeLevel' => $class->Grade_Level,
                        'subject_id' => $class->subjects->first()?->Subject_ID,
                        'subjectName' => $class->subjects->first()?->subject?->SubjectName ?? 'No Subject',
                        'totalStudents' => $class->students->count()
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getClassDetails($classId)
    {
        try {
            $class = ClassesModel::with(['subjects', 'students'])
                ->findOrFail($classId);

            $maleCount = $class->students->whereIn('Sex', ['M', 'Male'])->count();
            $femaleCount = $class->students->whereIn('Sex', ['F', 'Female'])->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'class_id' => $class->Class_ID,
                    'trackStand' => $class->Track,
                    'classType' => ($class->students->first() && $class->students->first()->pivot && $class->students->first()->pivot->isAdvisory)
                        ? 'Advisory'
                        : 'Subject',
                    'className' => $class->ClassName,
                    'gradeLevel' => $class->Grade_Level,
                    'subject_id' => $class->subjects->first()?->Subject_ID,
                    'subjectName' => $class->subjects->first()?->subject?->SubjectName ?? 'No Subject',
                    'maleCount' => $maleCount,
                    'femaleCount' => $femaleCount,
                    'totalStudents' => $class->students->count(),
                    'students' => $class->students->map(function ($student) {
                        return [
                            'student_id' => $student->Student_ID,
                            'lrn' => $student->LRN,
                            'firstName' => $student->FirstName,
                            'lastName' => $student->LastName,
                            'middleName' => $student->MiddleName,
                            'sex' => $student->Sex,
                            'birthDate' => $student->BirthDate,
                            'contactNumber' => $student->ContactNumber,
                            'address' => $student->Address
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getClassStudents($classId)
    {
        try {
            $class = ClassesModel::with('students')->findOrFail($classId);
            
            return response()->json([
                'status' => 'success',
                'data' => $class->students->map(function ($student) {
                    return [
                        'student_id' => $student->Student_ID,
                        'lrn' => $student->LRN,
                        'firstName' => $student->FirstName,
                        'lastName' => $student->LastName,
                        'middleName' => $student->MiddleName,
                        'sex' => $student->Sex,
                        'birthDate' => $student->BirthDate,
                        'contactNumber' => $student->ContactNumber,
                        'address' => $student->Address
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 