<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\FidelidadeConfig;
use App\Models\Horario;
use App\Models\Petshop;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfiguracaoController extends Controller
{
    private function currentPetshop(): Petshop
    {
        return Auth::user()->petshop;
    }

    public function petshop()
    {
        return view('configuracoes.petshop', ['petshop' => $this->currentPetshop()]);
    }

    public function updatePetshop(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string|max:500',
            'primary_color' => 'required|string|max:20',
            'logo'          => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'phone', 'address', 'primary_color']);
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('logo')) {
            $petshop = $this->currentPetshop();
            if ($petshop->logo) Storage::disk('public')->delete($petshop->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $this->currentPetshop()->update($data);
        return back()->with('success', 'Petshop atualizado com sucesso!');
    }

    public function servicos()
    {
        $servicos = Servico::orderBy('name')->get();
        return view('configuracoes.servicos', compact('servicos'));
    }

    public function storeServico(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'price'            => 'required|numeric|min:0',
        ]);
        Servico::create($request->validated());
        return back()->with('success', 'Serviço criado!');
    }

    public function updateServico(Request $request, Servico $servico)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'price'            => 'required|numeric|min:0',
            'active'           => 'boolean',
        ]);
        $servico->update($request->validated());
        return back()->with('success', 'Serviço atualizado!');
    }

    public function destroyServico(Servico $servico)
    {
        $servico->delete();
        return back()->with('success', 'Serviço removido.');
    }

    public function colaboradores()
    {
        $colaboradores = Colaborador::with('user')->get();
        return view('configuracoes.colaboradores', compact('colaboradores'));
    }

    public function horarios()
    {
        $petshop = $this->currentPetshop();
        $horarios = Horario::where('petshop_id', $petshop->id)
            ->orderBy('weekday')
            ->get()
            ->keyBy('weekday');
        return view('configuracoes.horarios', compact('horarios'));
    }

    public function updateHorarios(Request $request)
    {
        $petshop = $this->currentPetshop();
        foreach (range(0, 6) as $day) {
            Horario::updateOrCreate(
                ['petshop_id' => $petshop->id, 'weekday' => $day],
                [
                    'closed' => isset($request->horarios[$day]['closed']),
                    'open'   => $request->horarios[$day]['open'] ?? null,
                    'close'  => $request->horarios[$day]['close'] ?? null,
                ]
            );
        }
        return back()->with('success', 'Horários atualizados!');
    }

    public function fidelidade()
    {
        $config = FidelidadeConfig::firstOrNew(['petshop_id' => $this->currentPetshop()->id]);
        return view('configuracoes.fidelidade', compact('config'));
    }

    public function updateFidelidade(Request $request)
    {
        $request->validate([
            'atendimentos_para_premio' => 'required|integer|min:1',
            'desconto_percentual'      => 'required|numeric|min:0|max:100',
        ]);
        FidelidadeConfig::updateOrCreate(
            ['petshop_id' => $this->currentPetshop()->id],
            $request->validated()
        );
        return back()->with('success', 'Programa de fidelidade atualizado!');
    }

    public function integracao()
    {
        return view('configuracoes.integracao');
    }

    public function updateIntegracao(Request $request)
    {
        $request->validate([
            'WHATSAPP_API_URL'           => 'nullable|url',
            'WHATSAPP_API_TOKEN'         => 'nullable|string',
            'WHATSAPP_DEFAULT_INSTANCE'  => 'nullable|string',
        ]);

        $envFile = base_path('.env');
        $env = file_get_contents($envFile);

        foreach (['WHATSAPP_API_URL', 'WHATSAPP_API_TOKEN', 'WHATSAPP_DEFAULT_INSTANCE'] as $key) {
            $value = $request->get($key, '');
            $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
        }

        file_put_contents($envFile, $env);
        return back()->with('success', 'Integração salva!');
    }
}
