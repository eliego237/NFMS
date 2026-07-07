<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Liste des étudiants.
     */
    public function index()
    {
        return response()->json(

            Student::with([
                'latestEnrollment.training',
            ])
            ->latest()
            ->paginate(10)

        );
    }

    /**
     * Créer un étudiant.
     */
    public function store(StoreStudentRequest $request)
    {
        // Génération automatique du matricule
        $nextNumber = (Student::max('id') ?? 0) + 1;

        $matricule = sprintf(
            'NF%s%05d',
            now()->year,
            $nextNumber
        );

        $student = Student::create([

            'matricule' => $matricule,

            'first_name' => $request->first_name,

            'last_name' => $request->last_name,

            'gender' => $request->gender,

            'birth_date' => $request->birth_date,

            'phone' => $request->phone,

            'email' => $request->email,

            'address' => $request->address,

            'emergency_contact' => $request->emergency_contact,

            'photo' => $request->photo,

            'status' => $request->boolean('status', true),

        ]);

        return response()->json([

            'message' => 'Étudiant créé avec succès.',

            'student' => $student,

        ], 201);
    }

    /**
     * Afficher un étudiant.
     */
    public function show(Student $student)
    {
        return response()->json(

            $student->load([
                'enrollments.training',
                'enrollments.payments',
            ])

        );
    }

    /**
     * Modifier un étudiant.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return response()->json([

            'message' => 'Étudiant modifié avec succès.',

            'student' => $student->fresh(),

        ]);
    }

    /**
     * Supprimer un étudiant.
     */
    public function destroy(Student $student)
    {
        // Empêcher la suppression si l'étudiant possède des inscriptions
        if ($student->enrollments()->exists()) {

            return response()->json([

                'message' => 'Impossible de supprimer un étudiant ayant des inscriptions.'

            ], 422);

        }

        $student->delete();

        return response()->json([

            'message' => 'Étudiant supprimé avec succès.'

        ]);
    }
}