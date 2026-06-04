<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Comissao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComissaoController extends Controller
{
    public function index()
    {
        $colaboradores = Colaborador::with([
            'user',
            'comissoes' => fn($q) => $q->with('agendamento.servico'),
        ])
        ->get()
        ->map(function ($c) {
            $c->total_pendente = $c->comissoes->where('paid', false)->sum('amount');
            $c->total_pago = $c->comissoes->where('paid', true)->sum('amount');
            return $c;
        });

        return view('comissoes.index', compact('colaboradores'));
    }

    public function pagar(Comissao $comissao)
    {
        $comissao->update([
            'paid'    => true,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Comissão marcada como paga!');
    }
}
