<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'nom'     => $this->nom,
            'cognoms' => $this->cognoms,
            'correu'  => $this->correu,
            'rol'     => $this->rols?->nom,
        ];
    }
}
