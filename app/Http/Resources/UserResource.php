<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

// PERMITE AL ADMIN VER LOS USUARIOS PERO CON CIERTA SEGURIDAD, NO VERÁ LA 
// CONTRASEÑA (CAMPO SENSIBLE).
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'cognoms' => $this->cognoms,
            'correu' => $this->correu,
            'rol' => $this->rols?->nom,
            'rol_id' => $this->rol_id,
        ];
    }
}
