<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Créer un utilisateur.
     */
    public static function store(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([

                'name' => $data['name'],

                'email' => $data['email'],

                'password' => Hash::make($data['password']),

                'first_name' => $data['first_name'] ?? null,

                'last_name' => $data['last_name'] ?? null,

                'phone' => $data['phone'] ?? null,

                'photo' => $data['photo'] ?? null,

                'status' => $data['status'] ?? true,

            ]);

            $user->assignRole(
                $data['role']
            );

            return $user->fresh('roles');

        });
    }

    /**
     * Modifier un utilisateur.
     */
    public static function update(
        User $user,
        array $data
    ): User {

        return DB::transaction(function () use (
            $user,
            $data
        ) {

            $user->update([

                'name' => $data['name'],

                'email' => $data['email'],

                'first_name' => $data['first_name'] ?? null,

                'last_name' => $data['last_name'] ?? null,

                'phone' => $data['phone'] ?? null,

                'photo' => $data['photo'] ?? null,

                'status' => $data['status'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Mise à jour du mot de passe
            |--------------------------------------------------------------------------
            */

            if (!empty($data['password'])) {

                $user->update([

                    'password' => Hash::make(
                        $data['password']
                    ),

                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Mise à jour du rôle
            |--------------------------------------------------------------------------
            */

            $user->syncRoles([
                $data['role'],
            ]);

            return $user->fresh('roles');

        });
    }

    /**
     * Supprimer un utilisateur.
     */
    public static function delete(User $user): void
    {
        $user->delete();
    }
}