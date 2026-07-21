<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StudentController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:students.view',
                only: [
                    'index',
                    'show',
                ]
            ),

            new Middleware(
                'permission:students.create',
                only: [
                    'store',
                ]
            ),

            new Middleware(
                'permission:students.update',
                only: [
                    'update',
                ]
            ),

            new Middleware(
                'permission:students.delete',
                only: [
                    'destroy',
                ]
            ),

        ];
    }

    /**
     * Liste des étudiants.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des étudiants récupérée avec succès.',

            'data' => Student::with([
                    'latestEnrollment.training',
                ])
                ->latest()
                ->paginate(10),

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
        return response()->json([

            'success' => true,

            'message' => 'Étudiant récupéré avec succès.',

            'data' => $student->load([
                'enrollments.training',
                'enrollments.payments',
            ]),

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

                'message' => 'Impossible de supprimer un étudiant ayant des inscriptions.',

            ], 422);

        }

        StudentService::delete($student);

        return response()->json([

            'success' => true,

            'message' => 'Étudiant supprimé avec succès.',

        ]);
    }
}