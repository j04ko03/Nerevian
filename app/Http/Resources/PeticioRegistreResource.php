<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PeticioRegistreResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'nom_empresa'  => $this->nom_empresa,
            'contacte'     => $this->contacte,
            'correu'       => $this->correu,
            'telefon'      => $this->telefon,
            'missatge'     => $this->missatge,
            'estat'        => $this->estat,
            'data_creacio' => $this->data_creacio ? Carbon::parse($this->data_creacio)->diffForHumans() : null,
            'data_full'    => $this->data_creacio ? $this->data_creacio->format('d/m/Y H:i') : null,
        ];
    }
}
