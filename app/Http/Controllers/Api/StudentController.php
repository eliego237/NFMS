<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Routing\Controllers\HasMiddleware;

class StudentController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        // Temporairement désactivé pendant le développement React
        return [];
    }

    /**
     * Liste des étudiants.
     */
    public function index()
    {
        $students = Student::with([
            'latestEnrollment.trainings',
        ])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Liste des étudiants récupérée avec succès.',
            'data' => $students,
        ]);
    }

    /**
     * Créer un étudiant.
     */
    public function store(StoreStudentRequest $request)
    {
        $student = StudentService::store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Étudiant créé avec succès.',
            'data' => $student,
        ], 201);
    }

    /**
     * Afficher un étudiant.
     */
    public function show(Student $student)
    {
        $student->load([
            'enrollments.trainings',
            'enrollments.payments',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant récupéré avec succès.',
            'data' => $student,
        ]);
    }

    /**
     * Modifier un étudiant.
     */
    public function update(
        UpdateStudentRequest $request,
        Student $student
    ) {
        $student = StudentService::update(
            $student,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Étudiant modifié avec succès.',
            'data' => $student,
        ]);
    }

    /**
     * Supprimer un étudiant.
     */
    public function destroy(Student $student)
    {
        if ($student->enrollments()->exists()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Impossible de supprimer un étudiant ayant des inscriptions.',
            ], 422);
        }

        StudentService::delete($student);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant supprimé avec succès.',
        ]);
    }
}