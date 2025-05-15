<?php

namespace App\Http\Controllers;

use App\Models\ClassesModel;
use Illuminate\Http\Request;


class ClassessController extends Controller
{
    /**
     * Fetch all classes with their relationships.
     */
    public function index()
    {
        $classes = ClassesModel::with(['schoolYear', 'subjects', 'students'])
            ->get()
            ->map(function ($class) {
                return [
                    'Class_ID' => $class->Class_ID,
                    'ClassName' => $class->ClassName,
                    'Track' => $class->Track,
                    'classType' => $class->classType ?? 'Subject',
                    'Grade_Level' => $class->Grade_Level,
                    'subject_id' => $class->subjects->first()->Subject_ID ?? null
                ];
            });
        
        return response()->json($classes);
    }

    /**
     * Fetch a single class by ID.
     */
    public function show($id)
    {
        $class = ClassesModel::with(['schoolYear', 'subjects', 'students'])->find($id);
        return response()->json($class);
    }

    /**
     * Create a new class.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ClassName' => 'required|string',
            'Section' => 'required|string',
            'SY_ID' => 'required|exists:school_years,SY_ID',
            'Grade_Level' => 'required|in:7,8,9,10,11,12',
            'Track' => 'required|string',
            'classType' => 'nullable|string',
            'Teacher_ID' => 'required|exists:teachers,Teacher_ID',
        ]);

        $class = ClassesModel::create($validated);
        return response()->json($class, 201);
    }

    /**
     * Update an existing class.
     */
    public function update(Request $request, $id)
    {
        $class = ClassesModel::findOrFail($id);
        $validated = $request->validate([
            'ClassName' => 'string',
            'Section' => 'string',
            'SY_ID' => 'exists:school_years,SY_ID',
            'Grade_Level' => 'in:7,8,9,10,11,12',
            'Track' => 'string',
            'classType' => 'nullable|string',
            'Teacher_ID' => 'exists:teachers,Teacher_ID',
        ]);

        $class->update($validated);
        return response()->json($class);
    }

    /**
     * Delete a class.
     */
    public function destroy($id)
    {
        ClassesModel::destroy($id);
        return response()->json(null, 204);
    }
}