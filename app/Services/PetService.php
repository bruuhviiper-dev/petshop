<?php

namespace App\Services;

use App\DTOs\PetData;
use App\Models\Pet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Service responsável pela lógica de negócio de pets.
 */
class PetService
{
    /**
     * Cria um novo pet para um cliente, com foto opcional.
     */
    public function criar(PetData $data, int $clienteId, ?UploadedFile $foto): Pet
    {
        $photoPath = $foto ? $foto->store('pets', 'public') : null;

        return Pet::create([
            'cliente_id'   => $clienteId,
            'name'         => $data->name,
            'species'      => $data->species,
            'breed'        => $data->breed,
            'weight'       => $data->weight,
            'temperament'  => $data->temperament,
            'allergies'    => $data->allergies,
            'notes'        => $data->notes,
            'retorno_dias' => $data->retornoDias,
            'photo'        => $photoPath,
        ]);
    }

    /**
     * Atualiza os dados de um pet existente, com foto opcional.
     */
    public function atualizar(Pet $pet, PetData $data, ?UploadedFile $foto): Pet
    {
        $photoPath = $pet->photo;

        if ($foto) {
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            $photoPath = $foto->store('pets', 'public');
        }

        $pet->update([
            'name'         => $data->name,
            'species'      => $data->species,
            'breed'        => $data->breed,
            'weight'       => $data->weight,
            'temperament'  => $data->temperament,
            'allergies'    => $data->allergies,
            'notes'        => $data->notes,
            'retorno_dias' => $data->retornoDias,
            'photo'        => $photoPath,
        ]);

        return $pet->fresh();
    }
}
