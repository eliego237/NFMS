<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'first_name' => $this->first_name,

            'last_name' => $this->last_name,

            'email' => $this->email,

            'phone' => $this->phone,

            'photo' => $this->photo,

            'status' => (bool) $this->status,

            // pratique pour l'affichage dans le frontend
            'status_label' => $this->status ? 'Actif' : 'Inactif',

            'last_login_at' => $this->last_login_at,

            // retourne uniquement les noms des rôles
            'roles' => $this->roles->pluck('name')->values(),

            // uniquement si les permissions sont chargées
            'permissions' => $this->whenLoaded(
                'permissions',
                fn () => $this->permissions->pluck('name')->values()
            ),

        ];
    }
}