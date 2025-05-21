<?php

namespace App\Http\Controllers;

use App\Models\TeacherModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Research;
use App\Models\ClassesModel;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum'); 
    }

    public function createTeacherAccount(Request $request)
    {
   
        $request->validate([
            'Teacher_ID' => 'required|unique:teachers,Teacher_ID',
            'Email' => 'required|email|unique:teachers,Email',
            'Password' => 'required|min:8',
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'MiddleName' => 'nullable|string|max:255',
            'BirthDate' => 'required|date',
            'Sex' => 'required|in:M,F',
            'Position' => 'required|in:Admin,Coord,Teacher',
            'ContactNumber' => 'required|string|max:15',
            'Address' => 'required|string|max:255',
        ]);

        $authenticatedTeacher = Auth::user(); 
        if ($authenticatedTeacher->Position !== 'Admin') {
            return response()->json([
                'error' => 'Only Admins can create teacher accounts.',
            ], 403);
        }

        $teacher = TeacherModel::create([
            'Teacher_ID' => $request->Teacher_ID,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'MiddleName' => $request->MiddleName,
            'BirthDate' => $request->BirthDate,
            'Sex' => $request->Sex,
            'Position' => $request->Position,
            'ContactNumber' => $request->ContactNumber,
            'Address' => $request->Address,
        ]);

        return response()->json([
            'message' => 'Teacher account created successfully.',
            'teacher' => $teacher,
        ], 201);
    }

    public function getProfile(Request $request)
    {
        $teacher = $request->user()->load('researches');
        return response()->json([
            'teacher' => [
                'firstName' => $teacher->FirstName,
                'lastName' => $teacher->LastName,
                'middleName' => $teacher->MiddleName,
                'employeeNo' => $teacher->EmployeeNo,
                'position' => $teacher->Position,
                'email' => $teacher->Email,
                'contactNumber' => $teacher->ContactNumber,
                'address' => $teacher->Address,
                'avatar' => $teacher->Avatar,
                'research' => $teacher->researches->map(function($research) {
                    return [
                        'Research_ID' => $research->Research_ID,
                        'Title' => $research->Title,
                        'Abstract' => $research->Abstract,
                        'created_at' => $research->created_at
                    ];
                }),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $teacher = $request->user();
        
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'employeeNo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNumber' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $teacher->update([
            'FirstName' => $validatedData['firstName'],
            'LastName' => $validatedData['lastName'],
            'MiddleName' => $validatedData['middleName'],
            'EmployeeNo' => $validatedData['employeeNo'],
            'Email' => $validatedData['email'],
            'ContactNumber' => $validatedData['contactNumber'],
            'Address' => $validatedData['address'],
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'teacher' => [
                'firstName' => $teacher->FirstName,
                'lastName' => $teacher->LastName,
                'middleName' => $teacher->MiddleName,
                'employeeNo' => $teacher->EmployeeNo,
                'email' => $teacher->Email,
                'contactNumber' => $teacher->ContactNumber,
                'address' => $teacher->Address,
            ]
        ]);
    }

    public function addResearch(Request $request) {
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Abstract' => 'required|string',
        ]);

        $research = auth()->user()->researches()->create([
            'Title' => $validated['Title'],
            'Abstract' => $validated['Abstract']
        ]);

        return response()->json($research, 201);
    }

    public function getAdvisoryStudents(Request $request)
{
    try {
        // Get the authenticated teacher
        $teacher = $request->user();
        
        if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Teacher not authenticated'
            ], 401);
        }

        // Get the teacher's ID
        $teacherId = $teacher->Teacher_ID;

        $advisoryClass = DB::table('classes')
            ->where('Teacher_ID', $teacherId) 
            ->where('isAdvisory', true)
            ->first();

        if (!$advisoryClass) {
            return response()->json([
                'status' => 'success',
                'message' => 'No advisory class found for this teacher',
                'students' => []
            ]);
        }

        // Get students in this advisory class
        $students = DB::table('students')
            ->join('student_class', 'students.Student_ID', '=', 'student_class.Student_ID')
            ->where('student_class.Class_ID', $advisoryClass->Class_ID)
            ->select(
                'students.Student_ID as student_id',
                'students.LRN as lrn',
                'students.FirstName as firstName',
                'students.LastName as lastName',
                'students.MiddleName as middleName',
                'students.Sex as sex',
                'students.BirthDate as birthDate', 
                'students.Curriculum as curriculum',
                DB::raw("CONCAT(IFNULL(students.HouseNo, ''), ', ', IFNULL(students.Barangay, ''), ', ', 
                     IFNULL(students.Municipality, ''), ', ', IFNULL(students.Province, '')) as address")
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Advisory students retrieved successfully',
            'students' => $students
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve advisory students: ' . $e->getMessage(),
            'students' => []
        ], 500);
    }
}

}
