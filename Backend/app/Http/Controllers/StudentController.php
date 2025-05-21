<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;


class StudentController extends Controller
{
   public function getAll()
    {
        $students = StudentModel::all();

        return response()->json([
            'students' => $students
        ]);
    }

    public function getPendingStudents()
    {
        
         $students = StudentModel::where('Status', 'pending')->get();

        return response()->json([
            'students' => $students
        ]);
    }

        public function getAcceptedStudents()
    {
        
         $students = StudentModel::where('Status', 'accepted')->get();

        return response()->json([
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'LRN' => 'required|string|unique:students,LRN',
            'Grade_Level' => 'required|in:7,8,9,10,11,12',
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'MiddleName' => 'nullable|string|max:255',
            'Suffix' => 'nullable|in:Jr.,Sr.,II,III',
            'BirthDate' => 'required|date',
            'Sex' => 'required|in:M,F',
            'Age' => 'required|string|max:2',
            'Religion' => 'nullable|string|max:255',
            'HouseNo' => 'required|string|max:255',
            'Barangay' => 'required|string|max:255',
            'Municipality' => 'required|string|max:255',
            'Province' => 'required|string|max:255',
            'MotherName' => 'required|string|max:255',
            'FatherName' => 'required|string|max:255',
            'Guardian' => 'required|string|max:255',
            'Relationship' => 'required|string|max:255',
            'ContactNumber' => 'required|string|max:20',
            'Curriculum' => 'required|in:JHS,SHS',
            'Track' => 'required|string|max:255',
        ]);

        // Add default status
        $validatedData['status'] = 'pending';

        $student = StudentModel::create($validatedData);

        return response()->json([
            'message' => 'Student created successfully.',
            'student' => $student
        ], 201);
    }

    public function acceptProfile(Request $request, $id)
    {
        $student = StudentModel::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $student->Status = 'accepted';
        $student->save();

        return response()->json(['message' => 'Student profile accepted successfully.']);
    }

    public function getStudentSubjects($studentId)
{
    try {
        // Get the student's classes
        $studentClasses = DB::table('student_class')
            ->where('Student_ID', $studentId)
            ->pluck('Class_ID');
            
        // Get subjects for these classes
        $subjects = DB::table('subjects')
            ->whereIn('Class_ID', $studentClasses)
            ->select(
                'Subject_ID as subject_id',
                'SubjectName as subjectName',
                'Class_ID as class_id'
            )
            ->get();
            
        // For each subject, get the student's grades if available
        foreach ($subjects as $subject) {
            // You would add grade retrieval logic here
            // For example:
            // $subject->grades = DB::table('grades')
            //    ->where('Student_ID', $studentId)
            //    ->where('Subject_ID', $subject->subject_id)
            //    ->first();
                
            // Add student_id as an array for compatibility with your front-end code
            $subject->student_id = [$studentId];
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Subjects retrieved successfully',
            'subjects' => $subjects
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve subjects: ' . $e->getMessage(),
            'subjects' => []
        ], 500);
    }
}
}
