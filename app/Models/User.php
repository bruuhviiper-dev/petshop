<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Petshop resolvido (admin = dono; colaborador = vínculo). Cacheado na instância. */
    private ?Petshop $resolvedPetshop = null;

    public function petshop()
    {
        return $this->hasOne(Petshop::class);
    }

    public function colaborador()
    {
        return $this->hasOne(Colaborador::class);
    }

    /**
     * Petshop do usuário, seja admin (dono) ou colaborador (vínculo na tabela colaboradores).
     * Usa withoutGlobalScopes() ao buscar o colaborador para evitar recursão com o
     * global scope de BelongsToPetshop (que também chama este método).
     */
    public function currentPetshop(): ?Petshop
    {
        if ($this->resolvedPetshop) {
            return $this->resolvedPetshop;
        }

        $petshop = $this->petshop
            ?: $this->colaborador()->withoutGlobalScopes()->first()?->petshop;

        return $this->resolvedPetshop = $petshop;
    }

    public function currentPetshopId(): ?int
    {
        return $this->currentPetshop()?->id;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
