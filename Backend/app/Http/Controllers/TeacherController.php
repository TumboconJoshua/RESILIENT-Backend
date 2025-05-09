<?php

namespace App\Http\Controllers;

use App\Models\TeacherModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
