<?php

namespace App\Http\Controllers;

use App\Models\SubjectModel;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Fetch all subjects with their relationships.
     */
    public function index()
    {
        $subjects = SubjectModel::with(['teacher', 'grades'])->get();
        return response()->json($subjects);
    }

    /**
     * Fetch a single subject by ID.
     */
    public function show($id)
    {
        $subject = SubjectModel::with(['teacher', 'grades'])->find($id);
        return response()->json($subject);
    }

    /**
     * Create a new subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'SubjectName' => 'required|string|max:255',
            'SubjectCode' => 'required|integer|min:1',
            'Track' => 'required|string|max:255',
            'Teacher_ID' => 'required|exists:teachers,Teacher_ID',
        ]);

        $subject = SubjectModel::create($validated);
        return response()->json($subject, 201);
    }

    /**
     * Update an existing subject.
     */
    public function update(Request $request, $id)
    {
        $subject = SubjectModel::findOrFail($id);
        $validated = $request->validate([
            'SubjectName' => 'string|max:255',
            'SubjectCode' => 'integer|min:1',
            'Track' => 'string|max:255',
            'Teacher_ID' => 'exists:teachers,Teacher_ID',
        ]);

        $subject->update($validated);
        return response()->json($subject);
    }

    /**
     * Delete a subject.
     */
    public function destroy($id)
    {
        SubjectModel::destroy($id);
        return response()->json(null, 204);
    }
}