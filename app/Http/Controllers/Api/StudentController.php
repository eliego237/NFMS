<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Afficher la liste des étudiants.
     */
    public function index()
    {
        $students = Student::orderBy('created_at', 'desc')->paginate(10);

        return response()->json($students);
    }

    /**
     * Enregistrer un nouvel étudiant.
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
     * Afficher un étudiant.
     */
    public function show(Student $student)
    {
        return response()->json($student);
    }

    /**
     * Modifier un étudiant.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return response()->json([
            'message' => 'Étudiant modifié avec succès.',
            'student' => $student,
        ]);
    }

    /**
     * Supprimer un étudiant.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'message' => 'Étudiant supprimé avec succès.',
        ]);
    }
}