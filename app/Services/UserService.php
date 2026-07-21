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

                'name' => trim($data['first_name'].' '.$data['last_name']),

                'first_name' => $data['first_name'],

                'last_name' => $data['last_name'],

                'email' => $data['email'],

                'phone' => $data['phone'] ?? null,

                'photo' => $data['photo'] ?? null,

                'status' => $data['status'] ?? true,

                'password' => Hash::make($data['password']),

            ]);

            // Attribution des rôles
            $user->syncRoles($data['roles']);

            return $user->load(
                'roles',
                'permissions'
            );

        });
    }

    /**
     * Modifier un utilisateur.
     */
    public static function update(
        User $user,
        array $data
    ): User {

        return DB::transaction(function () use ($user, $data) {

            $user->update([

                'name' => trim($data['first_name'].' '.$data['last_name']),

                'first_name' => $data['first_name'],

                'last_name' => $data['last_name'],

                'email' => $data['email'],

                'phone' => $data['phone'] ?? null,

                'photo' => $data['photo'] ?? null,

                'status' => $data['status'] ?? true,

            ]);

            if (!empty($data['password'])) {

                $user->update([

                    'password' => Hash::make(
                        $data['password']
                    ),

                ]);

            }

            // Synchronisation des rôles
            if (isset($data['roles'])) {

                $user->syncRoles($data['roles']);

            }

            return $user->load(
                'roles',
                'permissions'
            );

        });
    }

    /**
     * Supprimer un utilisateur.
     */
    public static function delete(User $user): void
    {
        DB::transaction(function () use ($user) {

            $user->delete();

        });
    }
}