<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ClassesModel;
use App\Models\User;
use App\Models\TeacherModel;
use App\Models\SchoolYearModel;
use App\Models\StudentModel;
use App\Models\SubjectModel;
use App\Models\SubjectGradeModel;
use App\Models\LessonPlan;
use App\Models\ClassSubjectModel;



class SuperAdminController extends Controller
{

    public function getStudentsPerGradeLevel()
    {
        $studentsPerGradeLevel = SubjectGradeModel::select(
                'students.Grade_Level',
                DB::raw("SUM(CASE WHEN subject_grades.FinalGrade BETWEEN 90 AND 100 THEN 1 ELSE 0 END) as very_good"),
                DB::raw("SUM(CASE WHEN subject_grades.FinalGrade BETWEEN 75 AND 89 THEN 1 ELSE 0 END) as good"),
                DB::raw("SUM(CASE WHEN subject_grades.FinalGrade BETWEEN 60 AND 74 THEN 1 ELSE 0 END) as failed")
            )
            ->join('students', 'subject_grades.Student_ID', '=', 'students.Student_ID')
            ->whereNotNull('subject_grades.FinalGrade')
            ->groupBy('students.Grade_Level')
            ->orderBy('students.Grade_Level')
            ->get();

        return response()->json($studentsPerGradeLevel);
    }



    public function getCountByStatus()
    {
        $rawCounts = SubjectGradeModel::select(
                'Status',
                DB::raw('count(*) as total')
            )
            ->whereIn('Status', ['Approved', 'Declined', 'Pending'])
            ->groupBy('Status')
            ->get()
            ->pluck('total', 'Status')
            ->toArray();

        $mappedStatuses = [
            'Approved' => 'approved',
            'Declined' => 'decline', // 👈 match frontend key exactly
            'Pending' => 'pending',
        ];

        $statusCounts = collect($mappedStatuses)->map(function ($frontendKey, $dbKey) use ($rawCounts) {
            return [
                'Status' => $frontendKey,
                'total' => $rawCounts[$dbKey] ?? 0
            ];
        })->values();

        return response()->json($statusCounts);
    }







    //Dashboard "Teacher" count pending grades, teacher and students.
    public function getSummaryStats()
    {
        try {
            $teacherCount = TeacherModel::count();
            $studentCount = StudentModel::count();
            $pendingGradesCount = SubjectGradeModel::where('Status', 'Pending')->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_teachers' => $teacherCount,
                    'total_students' => $studentCount,
                    'pending_grades' => $pendingGradesCount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //Dashboard "Teacher" get the Recently Added Faculties
    public function getRecentFaculties(Request $request)
    {
        try {
            // Optional: accept a limit parameter (default to 5)
            $limit = $request->input('limit', 5);

            $recentFaculties = TeacherModel::whereIn('Position', ['Teacher', 'Admin'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $recentFaculties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function acceptSubjectGrades(Request $request)
    {
        $request->validate([
            'student_id' => ['required'],
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'integer|exists:subject_grades,Grade_ID',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        $validStudentCount = StudentModel::whereIn('Student_ID', $studentIds)->count();
        if ($validStudentCount !== count($studentIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'One or more student_id values are invalid.'
            ], 422);
        }
    
        try {
            SubjectGradeModel::whereIn('Student_ID', $studentIds)
                ->whereIn('Grade_ID', $request->grade_ids)
                ->update(['Status' => 'Approved']);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Subject grades have been accepted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function declineSubjectGrades(Request $request)
    {
        $request->validate([
            'student_id' => ['required'],
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'integer|exists:subject_grades,Grade_ID',
            'comments' => 'nullable|string|max:1000',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        $validStudentCount = StudentModel::whereIn('Student_ID', $studentIds)->count();
        if ($validStudentCount !== count($studentIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'One or more student_id values are invalid.'
            ], 422);
        }

        try {
            SubjectGradeModel::whereIn('Student_ID', $studentIds)
                ->whereIn('Grade_ID', $request->grade_ids)
                ->update([
                    'Status' => 'Declined',
                    'Comments' => $request->comments,
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subject grades have been declined successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    

    public function getAcceptedClassesWithSubjectsTeachersAndStudents()
    {
        try {
            $classes = ClassesModel::where('Status', 'Accepted')
                ->with(['students.subjectGrades']) // eager load students and their grades
                ->get();

            $classIds = $classes->pluck('Class_ID');
            $subjectTeacherLinks = \App\Models\StudentClassTeacherSubject::whereHas('studentClass', function($q) use ($classIds) {
                    $q->whereIn('Class_ID', $classIds);
                })
                ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'studentClass'])
                ->get()
                ->groupBy(function($item) {
                    return $item->studentClass->Class_ID ?? null;
                });

            $data = $classes->map(function ($class) use ($subjectTeacherLinks) {
                // Get all subject-teacher pairs for this class
                $subjects = collect($subjectTeacherLinks[$class->Class_ID] ?? [])->map(function ($link) {
                    return [
                        'subject_id' => $link->teacherSubject->subject?->Subject_ID,
                        'subjectName' => $link->teacherSubject->subject?->SubjectName,
                        'teacher_id' => $link->teacherSubject->teacher?->Teacher_ID,
                        'teacherName' => $link->teacherSubject->teacher
                            ? $link->teacherSubject->teacher->FirstName . ' ' . $link->teacherSubject->teacher->LastName
                            : null,
                    ];
                })->unique('subject_id')->values();

                return [
                    'class_id' => $class->Class_ID,
                    'className' => $class->ClassName ?? null,
                    'gradeLevel' => $class->Grade_Level ?? null,
                    'curriculum' => $class->Curriculum ?? null,
                    'track' => $class->Track ?? null,
                    'subjects' => $subjects,
                    'students' => $class->students->map(function ($student) {
                        return [
                            'student_id' => $student->Student_ID,
                            'firstName' => $student->FirstName,
                            'lastName' => $student->LastName,
                            'middleName' => $student->MiddleName,
                            'lrn' => $student->LRN,
                            'sex' => $student->Sex,
                            'birthDate' => $student->BirthDate,
                            'contactNumber' => $student->ContactNumber,
                            'address' => $student->Address,
                            'track' => $student->Track,
                            'curriculum' => $student->Curriculum,
                            'subject_grades' => $student->subjectGrades->map(function ($grade) {
                                return [
                                    'grade_id' => $grade->Grade_ID,
                                    'subject_id' => $grade->Subject_ID,
                                    'teacher_id' => $grade->Teacher_ID,
                                    'Q1' => $grade->Q1,
                                    'Q2' => $grade->Q2,
                                    'Q3' => $grade->Q3,
                                    'Q4' => $grade->Q4,
                                    'FinalGrade' => $grade->FinalGrade,
                                    'Remarks' => $grade->Remarks,
                                    'Status' => $grade->Status,
                                ];
                            }),
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //Classes API
    public function getAllWithStudentCount()
        {
            return ClassesModel::select(
                    'classes.*',
                    DB::raw('(SELECT COUNT(*) FROM students 
                            WHERE students.Track = classes.Track 
                            AND students.Curriculum = classes.Curriculum) as student_added')
                )
                ->with(['adviser:Teacher_ID,FirstName,LastName,MiddleName', 'schoolYear'])
                ->get();
        }


    //Get All the data from "Student Table"
    public function getAllStudentsData()
        {
            $students = StudentModel::all(); 

            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        }

    //Student Modal for "Individual Registration Form"
    public function getStudentById($id)
    {
        $student = StudentModel::find($id);

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $student,
        ]);
    }


    // Accept Student Tab
    public function acceptStudent($id)
    {
        $student = StudentModel::findOrFail($id);
        $student->Status = 'Accepted';
        $student->save();

        return response()->json([
            'message' => 'Student has been accepted.',
            'student' => $student
        ], 200);
    }

    // Decline Student Tab
    public function declineStudent(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $student = StudentModel::findOrFail($id);
        $student->Status = 'Declined';
        $student->comments = $request->input('comment'); // ✅ Save the rejection comment
        $student->save();

        return response()->json([
            'message' => 'Student has been declined.',
            'student' => $student
        ], 200);
    }

    //Lesson Plan Get All
    public function getAllLessonPlans()
    {
        $lessonPlans = LessonPlan::with('teacher')->get();

        $formatted = $lessonPlans->map(function ($lesson) {
            return [
                'LessonPlan_ID' => $lesson->LessonPlan_ID,
                'Teacher_ID' => $lesson->Teacher_ID,
                'TeacherName' => $lesson->teacher_name,  // Using the accessor here
                'lesson_plan_no' => $lesson->lesson_plan_no,
                'grade_level' => $lesson->grade_level,
                'section' => $lesson->section,
                'category' => $lesson->category,
                'link' => $lesson->link,
                'status' => $lesson->status,
                'comments' => $lesson->comments,
                'created_at' => $lesson->created_at,
                'updated_at' => $lesson->updated_at,
            ];
        });

        return response()->json($formatted);
    }

    //Lesson Plan Accept
    public function approveLessonPlan($id)
    {
        $lessonPlan = LessonPlan::find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Lesson Plan not found'], 404);
        }

        $lessonPlan->status = 'Approved';
        $lessonPlan->save();

        return response()->json(['message' => 'Lesson Plan approved successfully']);
    }

    // Lesson Plan Reject
    public function rejectLessonPlan($id)
    {
        $lessonPlan = LessonPlan::find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Lesson Plan not found'], 404);
        }

        $lessonPlan->status = 'Declined';
        $lessonPlan->save();

        return response()->json(['message' => 'Lesson Plan declined successfully']);
    }

    // Get Specific Lesson Plan 

    public function getLessonPlanById($id)
    {
        $lessonPlan = LessonPlan::find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Lesson plan not found'], 404);
        }

        return response()->json($lessonPlan);
    }


// Settings
public function create(Request $request)
    {
        $request->validate([
            'SubjectName' => 'required|string',
            'GradeLevel' => 'required|integer',
            'SubjectCode' => 'required|integer|unique:subjects,SubjectCode',
        ]);

        $subject = SubjectModel::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $subject
        ], 201);
    }

    // READ ALL
    public function Sample()
    {
        $subjects = SubjectModel::all();
        return response()->json([
            'status' => 'success',
            'data' => $subjects
        ]);
    }

    // READ ONE
    public function show($id)
    {
        $subject = SubjectModel::find($id);

        if (!$subject) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subject not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $subject
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $subject = SubjectModel::find($id);

        if (!$subject) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subject not found'
            ], 404);
        }

        $request->validate([
            'SubjectName' => 'sometimes|required|string',
            'GradeLevel' => 'sometimes|required|integer',
            'SubjectCode' => 'sometimes|required|integer|unique:subjects,SubjectCode,' . $id . ',Subject_ID',
        ]);

        $subject->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $subject
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $subject = SubjectModel::find($id);

        if (!$subject) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subject` not found'
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subject deleted successfully'
        ]);
    }


    public function getAllSchoolYears()
{
    $schoolYears = SchoolYearModel::all();

    return response()->json([
        'status' => 'success',
        'data' => $schoolYears
    ]);
}

public function getAllSections()
{
   $sections = ClassesModel::all();

    return response()->json([
        'status' => 'success',
        'data' => $sections
    ]);
}
public function CreateSchoolYear(Request $request)
{
    $validatedData = $request->validate([
        'Start_Date' => 'required|date',
        'End_Date' => 'required|date|after:Start_Date',
        'SY_Year' => 'required|string|unique:school_years,SY_Year',
    ]);

    $schoolYear = new SchoolYearModel();
    $schoolYear->Start_Date = $validatedData['Start_Date'];
    $schoolYear->End_Date = $validatedData['End_Date'];
    $schoolYear->SY_Year = $validatedData['SY_Year'];
    $schoolYear->created_at = Carbon::now();
    $schoolYear->updated_at = Carbon::now();
    $schoolYear->save();

    return response()->json([
        'status' => 'success',
        'message' => 'School year created successfully',
        'data' => $schoolYear
    ], 201);
}

public function TestSection(Request $request)
{
    $validated = $request->validate([
        'ClassName'    => 'required|string|max:255',
        'Section'      => 'required|string|max:255',
        'SY_ID'        => 'required|exists:school_years,SY_ID',
        'Grade_Level'  => 'required|in:7,8,9,10,11,12',
        'Track'        => 'nullable|string',
        'Adviser_ID'   => 'nullable|exists:teachers,Teacher_ID',
        'Curriculum'   => 'nullable|in:JHS,SHS',
        'comments'     => 'nullable|string',
    ]);

  $validated['Status'] = 'Incomplete';

    $class = ClassesModel::create($validated);

    return response()->json([
        'message' => 'Class created successfully',
        'data' => $class,
    ], 201);
}

public function deleteClass($id)
{
    $class = ClassesModel::find($id);

    if (!$class) {
        return response()->json([
            'message' => 'Class not found.'
        ], 404);
    }

    $class->delete();

    return response()->json([
        'message' => 'Class deleted successfully.'
    ], 200);
}

}
