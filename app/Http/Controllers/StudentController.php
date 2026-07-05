<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStudentRequest;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $students = Student::orderBy('created_at', 'desc')->paginate(10);

    return response()->json($students);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
{
    // Génération automatique du matricule
    $lastStudent = Student::latest('id')->first();

    if ($lastStudent) {
        $number = intval(substr($lastStudent->matricule, -5)) + 1;
    } else {
        $number = 1;
    }

    $matricule = 'NF' . date('Y') . str_pad($number, 5, '0', STR_PAD_LEFT);

    $student = Student::create([
        'matricule' => $matricule,
        ...$request->validated(),
    ]);

    return response()->json([
        'message' => 'Étudiant enregistré avec succès.',
        'student' => $student,
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
{
    return response()->json($student);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreStudentRequest $request, Student $student)
{
    $student->update($request->validated());

    return response()->json([
        'message' => 'Étudiant modifié avec succès.',
        'student' => $student,
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
{
    $student->delete();

    return response()->json([
        'message' => 'Étudiant supprimé avec succès.',
    ]);
}
}
