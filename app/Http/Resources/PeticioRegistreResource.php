<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

// RESOURCES ES COMO UNA CAPA DE TRANSFORMACIÓN, SIMILAR A LOS DTO DE 
// JAVA (POR SI TE SUENA), ENTRE LOS MODELOS DE LA BBDD Y EL JSON QUE 
// RECIBE VUE. UN FILTRO SI QUIERES LLAMARLO.

// LO GUAPO DE RESOURCES ES QUE TAMBIÉN SI ALGO CAMBIA EN LA BBDD SOLO TENEMOS 
// QUE CAMBIAR AQUI EL CAMPO CORRESPONDIENTE :).

// ESTE RESOURCE SE ENCARGA DE QUE EL ADMIN PUEDA VER LA PETICIONES EN LA SCRENN 
// CORRESPONDIENTE DE FROMA LEGIBLE "PARA HUMANOS".
class PeticioRegistreResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom_empresa' => $this->nom_empresa,
            'contacte' => $this->contacte,
            'correu' => $this->correu,
            'telefon' => $this->telefon,
            'missatge' => $this->missatge,
            'estat' => $this->estat,
            // 
            'data_creacio' => $this->data_creacio ? Carbon::parse($this->data_creacio)->diffForHumans() : null,
            'data_full' => $this->data_creacio ? $this->data_creacio->format('d/m/Y H:i') : null,
        ];
    }
}
