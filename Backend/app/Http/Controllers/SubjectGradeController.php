<?php

namespace App\Http\Controllers;

use App\Models\SubjectGradeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectGradeController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'Student_ID' => 'required|exists:students,Student_ID',
                'Subject_ID' => 'required|exists:subjects,Subject_ID',
                'Teacher_ID' => 'required|exists:teachers,Teacher_ID',
                'Q1' => 'nullable|numeric|min:0|max:100',
                'Q2' => 'nullable|numeric|min:0|max:100',
                'Q3' => 'nullable|numeric|min:0|max:100',
                'Q4' => 'nullable|numeric|min:0|max:100',
                'FinalGrade' => 'nullable|numeric|min:0|max:100',
                'Remarks' => 'nullable|string',
            ]);
            
            // Check if record already exists for this student and subject
            $existingGrade = SubjectGradeModel::where('Student_ID', $validatedData['Student_ID'])
                ->where('Subject_ID', $validatedData['Subject_ID'])
                ->first();
            
            if ($existingGrade) {
                // Update existing grade
                $existingGrade->update($validatedData);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Grade updated successfully',
                    'grade' => $existingGrade
                ]);
            } else {
                // Create new grade
                $grade = SubjectGradeModel::create($validatedData);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Grade saved successfully',
                    'grade' => $grade
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Error saving grade: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save grade: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStore(Request $request) 
    {
        try {
            // Validate the incoming request array
            $validatedData = $request->validate([
                'grades' => 'required|array',
                'grades.*.Student_ID' => 'required|exists:students,Student_ID',
                'grades.*.Subject_ID' => 'required|exists:subjects,Subject_ID',
                'grades.*.Teacher_ID' => 'required|exists:teachers,Teacher_ID',
                'grades.*.Q1' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q2' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q3' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q4' => 'nullable|numeric|min:0|max:100',
                'grades.*.FinalGrade' => 'nullable|numeric|min:0|max:100',
                'grades.*.Remarks' => 'nullable|string',
            ]);
            
            $results = [];
            
            foreach ($validatedData['grades'] as $gradeData) {
                $existingGrade = SubjectGradeModel::where('Student_ID', $gradeData['Student_ID'])
                    ->where('Subject_ID', $gradeData['Subject_ID'])
                    ->first();
                
                if ($existingGrade) {
                    // Update existing grade
                    $existingGrade->update($gradeData);
                    $results[] = $existingGrade;
                } else {
                    // Create new grade
                    $grade = SubjectGradeModel::create($gradeData);
                    $results[] = $grade;
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => count($results) . ' grades saved successfully',
                'grades' => $results
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving grades: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save grades: ' . $e->getMessage()
            ], 500);
        }
    }
}