<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\StudentClassModel;

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
    
    public function getAllAcceptedStudents()
    {
        $students = StudentModel::where('Status', 'accepted')->get();
        
        return response()->json([
            'students' => $students
        ]);
    }

    public function getAcceptedStudents()
    {
        // Get all student IDs that already exist in student_class
        $assignedStudentIds = StudentClassModel::pluck('Student_ID');
    
        // Get only accepted students who are NOT already in student_class
        $students = StudentModel::where('Status', 'Accepted')
            ->whereNotIn('Student_ID', $assignedStudentIds)
            ->get();
    
        return response()->json([
            'students' => $students
        ]);
    }
    

    public function getNoClassStudents()
    {
        // Get the list of student IDs that are already in the student_class table
        $excludedStudentIDs = DB::table('student_class')->pluck('Student_ID');
    
        // Get accepted students who are not in the student_class table
        $students = StudentModel::whereNotIn('Student_ID', $excludedStudentIDs)
            ->get();
    
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

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'gradeLevel' => 'required|in:7,8,9,10,11,12',
            'curriculum' => 'required|in:JHS,SHS',
            'track' => 'required|string|max:255',
        ]);

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0); // First row as header

        $records = (new Statement())->process($csv);

        $inserted = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($records as $index => $record) {
                $data = [
                    'LRN' => $record['LRN'],
                    'Grade_Level' => $request->gradeLevel,
                    'FirstName' => $record['FirstName'],
                    'LastName' => $record['LastName'],
                    'MiddleName' => $record['MiddleName'] ?? null,
                    'Suffix' => ($record['Suffix'] && strtolower($record['Suffix']) !== 'none') ? $record['Suffix'] : null,
                    'BirthDate' => $record['BirthDate'],
                    'Sex' => $record['Sex'],
                    'Age' => $record['Age'],
                    'Religion' => $record['Religion'] ?? null,
                    'HouseNo' => $record['HouseNo'],
                    'Barangay' => $record['Barangay'],
                    'Municipality' => $record['Municipality'],
                    'Province' => $record['Province'],
                    'MotherName' => $record['MotherName'],
                    'FatherName' => $record['FatherName'],
                    'Guardian' => $record['Guardian'],
                    'Relationship' => $record['Relationship'],
                    'ContactNumber' => $record['ContactNumber'],
                    'Curriculum' => $request->curriculum,
                    'Track' => $request->track,
                    'status' => 'pending'
                ];

                $validator = Validator::make($data, [
                    'LRN' => 'required|string|unique:students,LRN',
                    'Grade_Level' => 'required|in:7,8,9,10,11,12',
                    'FirstName' => 'required|string|max:255',
                    'LastName' => 'required|string|max:255',
                    'MiddleName' => 'nullable|string|max:255',
                    'Suffix' => 'nullable|string|in:Jr.,Sr.,II,III,None',
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

                if ($validator->fails()) {
                    $errors[] = [
                        'line' => $index + 1,
                        'errors' => $validator->errors()->all()
                    ];
                    continue;
                }

                StudentModel::create($data);
                $inserted++;
            }

            DB::commit();

            return response()->json([
                'message' => "Bulk upload completed. $inserted students added.",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error processing CSV.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Find the student by Student_ID (assuming $id here is Student_ID)
        $student = StudentModel::where('Student_ID', $id)->firstOrFail();
    
        // List of updatable fields
        $fields = [
            'LRN', 'Grade_Level', 'FirstName', 'LastName', 'MiddleName', 'Suffix',
            'BirthDate', 'Sex', 'Age', 'Religion', 'HouseNo', 'Barangay',
            'Municipality', 'Province', 'MotherName', 'FatherName', 'Guardian',
            'Relationship', 'ContactNumber', 'Curriculum', 'Track'
        ];
    
        // Prepare validation rules only for provided fields
        $rules = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                switch ($field) {
                    case 'LRN':
                        $rules[$field] = 'string|unique:students,LRN,' . $student->Student_ID . ',Student_ID';
                        break;
                    case 'Grade_Level':
                        $rules[$field] = 'in:7,8,9,10,11,12';
                        break;
                    case 'Suffix':
                        $rules[$field] = 'nullable|in:Jr.,Sr.,II,III';
                        break;
                    case 'Sex':
                        $rules[$field] = 'in:M,F';
                        break;
                    case 'BirthDate':
                        $rules[$field] = 'date';
                        break;
                    case 'Age':
                        $rules[$field] = 'string|max:2';
                        break;
                    case 'ContactNumber':
                        $rules[$field] = 'string|max:20';
                        break;
                    case 'Curriculum':
                        $rules[$field] = 'in:JHS,SHS';
                        break;
                    default:
                        $rules[$field] = 'string|max:255';
                }
            }
        }
    
        // Validate only the incoming fields
        $validatedData = $request->validate($rules);
    
        // Set status to 'Pending' whenever updating
        $validatedData['Status'] = 'Pending';

        $validatedData['comments'] = null;
    
        // Update the student only with the new fields + status
        $student->update($validatedData);
    
        return response()->json([
            'message' => 'Student updated successfully.',
            'student' => $student
        ]);
    }
    

    public function acceptProfile(Request $request, $id)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'gradeLevel' => 'required|in:7,8,9,10,11,12',
            'curriculum' => 'required|in:JHS,SHS',
            'track' => 'required|string|max:255',
        ]);

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0); // First row as header

        $records = (new Statement())->process($csv);

        $inserted = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($records as $index => $record) {
                $data = [
                    'LRN' => $record['LRN'],
                    'Grade_Level' => $request->gradeLevel,
                    'FirstName' => $record['FirstName'],
                    'LastName' => $record['LastName'],
                    'MiddleName' => $record['MiddleName'] ?? null,
                    'Suffix' => $record['Suffix'] ?? null,
                    'BirthDate' => $record['BirthDate'],
                    'Sex' => $record['Sex'],
                    'Age' => $record['Age'],
                    'Religion' => $record['Religion'] ?? null,
                    'HouseNo' => $record['HouseNo'],
                    'Barangay' => $record['Barangay'],
                    'Municipality' => $record['Municipality'],
                    'Province' => $record['Province'],
                    'MotherName' => $record['MotherName'],
                    'FatherName' => $record['FatherName'],
                    'Guardian' => $record['Guardian'],
                    'Relationship' => $record['Relationship'],
                    'ContactNumber' => $record['ContactNumber'],
                    'Curriculum' => $request->curriculum,
                    'Track' => $request->track,
                    'status' => 'pending'
                ];

                $validator = Validator::make($data, [
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

                if ($validator->fails()) {
                    $errors[] = [
                        'line' => $index + 1,
                        'errors' => $validator->errors()->all()
                    ];
                    continue;
                }

                StudentModel::create($data);
                $inserted++;
            }

            DB::commit();

            return response()->json([
                'message' => "Bulk upload completed. $inserted students added.",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error processing CSV.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function bulkUpload(Request $request)
    // {
    //     $request->validate([
    //         'csv_file' => 'required|mimes:csv,txt',
    //         'gradeLevel' => 'required|in:7,8,9,10,11,12',
    //         'curriculum' => 'required|in:JHS,SHS',
    //         'track' => 'required|string|max:255',
    //     ]);

    //     $file = $request->file('csv_file');
    //     $csv = Reader::createFromPath($file->getPathname(), 'r');
    //     $csv->setHeaderOffset(0); // First row as header

    //     $records = (new Statement())->process($csv);

    //     $inserted = 0;
    //     $errors = [];

    //     DB::beginTransaction();

    //     try {
    //         foreach ($records as $index => $record) {
    //             $data = [
    //                 'LRN' => $record['LRN'],
    //                 'Grade_Level' => $request->gradeLevel,
    //                 'FirstName' => $record['FirstName'],
    //                 'LastName' => $record['LastName'],
    //                 'MiddleName' => $record['MiddleName'] ?? null,
    //                 'Suffix' => $record['Suffix'] ?? null,
    //                 'BirthDate' => $record['BirthDate'],
    //                 'Sex' => $record['Sex'],
    //                 'Age' => $record['Age'],
    //                 'Religion' => $record['Religion'] ?? null,
    //                 'HouseNo' => $record['HouseNo'],
    //                 'Barangay' => $record['Barangay'],
    //                 'Municipality' => $record['Municipality'],
    //                 'Province' => $record['Province'],
    //                 'MotherName' => $record['MotherName'],
    //                 'FatherName' => $record['FatherName'],
    //                 'Guardian' => $record['Guardian'],
    //                 'Relationship' => $record['Relationship'],
    //                 'ContactNumber' => $record['ContactNumber'],
    //                 'Curriculum' => $request->curriculum,
    //                 'Track' => $request->track,
    //                 'status' => 'pending'
    //             ];

    //             $validator = Validator::make($data, [
    //                 'LRN' => 'required|string|unique:students,LRN',
    //                 'Grade_Level' => 'required|in:7,8,9,10,11,12',
    //                 'FirstName' => 'required|string|max:255',
    //                 'LastName' => 'required|string|max:255',
    //                 'MiddleName' => 'nullable|string|max:255',
    //                 'Suffix' => 'nullable|in:Jr.,Sr.,II,III',
    //                 'BirthDate' => 'required|date',
    //                 'Sex' => 'required|in:M,F',
    //                 'Age' => 'required|string|max:2',
    //                 'Religion' => 'nullable|string|max:255',
    //                 'HouseNo' => 'required|string|max:255',
    //                 'Barangay' => 'required|string|max:255',
    //                 'Municipality' => 'required|string|max:255',
    //                 'Province' => 'required|string|max:255',
    //                 'MotherName' => 'required|string|max:255',
    //                 'FatherName' => 'required|string|max:255',
    //                 'Guardian' => 'required|string|max:255',
    //                 'Relationship' => 'required|string|max:255',
    //                 'ContactNumber' => 'required|string|max:20',
    //                 'Curriculum' => 'required|in:JHS,SHS',
    //                 'Track' => 'required|string|max:255',
    //             ]);

    //             if ($validator->fails()) {
    //                 $errors[] = [
    //                     'line' => $index + 1,
    //                     'errors' => $validator->errors()->all()
    //                 ];
    //                 continue;
    //             }

    //             StudentModel::create($data);
    //             $inserted++;
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'message' => "Bulk upload completed. $inserted students added.",
    //             'errors' => $errors
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Error processing CSV.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function acceptProfile(Request $request, $id)
    // {
    //     $student = StudentModel::find($id);

    //     if (!$student) {
    //         return response()->json(['message' => 'Student not found.'], 404);
    //     }

    //     $student->Status = 'accepted';
    //     $student->save();

    //     return response()->json(['message' => 'Student profile accepted successfully.']);
    // }

   public function massAcceptFromDataHolder(Request $request)
{
    // Validate incoming data format
    $validated = $request->validate([
        'data' => 'required|array',
        'data.*.Student_ID' => 'required|integer|exists:students,Student_ID',
    ]);

    // Extract all Student_IDs
    $studentIds = collect($validated['data'])->pluck('Student_ID')->toArray();

    // Mass update the Status field
    $updated = StudentModel::whereIn('Student_ID', $studentIds)
        ->update(['Status' => 'Accepted']);

    return response()->json([
        'message' => "$updated student profile(s) accepted successfully.",
        'accepted_ids' => $studentIds
    ]);
}

public function getStudentsNoClass()
{
    try {
        // Get the list of student IDs that are already in the student_class table
        $excludedStudentIDs = DB::table('student_class')->pluck('Student_ID');
    
        // Get accepted students who are not in the student_class table
        $students = StudentModel::where('Status', 'accepted')
            ->whereNotIn('Student_ID', $excludedStudentIDs)
            ->get();
    
        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch students without class: ' . $e->getMessage()
        ], 500);
    }
}

public function markAsDropOut(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,Student_ID',
        'drop_out_comments' => 'nullable|string'
    ]);

    $student = StudentModel::find($request->student_id);

    $student->status = 'Drop-Out';
    $student->drop_out_comments = $request->drop_out_comments; // Save the comment
    $student->save();

    return response()->json([
        'message' => 'Student marked as Drop-Out successfully.',
        'student' => $student
    ]);
}

public function approveDropOut(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,Student_ID'
    ]);

    $student = StudentModel::find($request->student_id);

    $student->status = 'Dropped-Out';
    $student->save();

    return response()->json([
        'message' => 'Student marked as Drop-Out successfully.',
        'student' => $student
    ]);
}

public function rejectDropout(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,Student_ID',
    ]);

    $student = StudentModel::find($request->student_id);

    if ($student->Status !== 'Drop-Out') {
        return response()->json([
            'message' => 'Action not allowed. Student is not marked as Drop-Out.',
        ], 400); // Bad Request
    }

    $student->Status = 'Pending';
    $student->drop_out_comments = null; // Clear the comment
    $student->save();

    return response()->json([
        'message' => 'Student status updated to Pending and drop-out comments cleared.',
        'student' => $student
    ]);
}


}
