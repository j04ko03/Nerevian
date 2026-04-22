<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'nom'      => $this->nom,
            'cognoms'  => $this->cognoms,
            'correu'   => $this->correu,
            'telefon'  => $this->telefon,
            'rol_id'   => $this->rol_id,
            'rols'     => $this->whenLoaded('rols', fn() => [
                'id'  => $this->rols->id,
                'rol' => $this->rols->rol,
            ]),
            'actiu' => $this->whenLoaded('clients', fn() => (bool) $this->clients?->actiu, null),
        ];
    }
}