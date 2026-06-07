<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\FidelidadeConfig;
use App\Models\Horario;
use App\Models\Petshop;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ConfiguracaoController extends Controller
{
    private function currentPetshop(): Petshop
    {
        return Auth::user()->currentPetshop();
    }

    public function petshop()
    {
        return view('configuracoes.petshop', ['petshop' => $this->currentPetshop()]);
    }

    public function updatePetshop(Request $request)
    {
        $petshop = $this->currentPetshop();

        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string|max:500',
            'primary_color' => 'required|string|max:20',
            'logo'          => 'nullable|image|max:2048',
            // slug é opcional e estável: só muda se o usuário editar de propósito.
            'slug'          => ['nullable', 'string', 'max:255', Rule::unique('petshops', 'slug')->ignore($petshop->id)],
        ]);

        $data = $request->only(['name', 'phone', 'address', 'primary_color']);

        // NÃO regerar o slug a partir do nome — isso quebraria o link público já
        // compartilhado. Só atualizamos o slug quando o usuário informa um novo.
        if ($request->filled('slug')) {
            $data['slug'] = Str::slug($request->slug);
        } elseif (empty($petshop->slug)) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            if ($petshop->logo) Storage::disk('public')->delete($petshop->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $petshop->update($data);
        return back()->with('success', 'Petshop atualizado com sucesso!');
    }

    public function servicos()
    {
        $servicos = Servico::orderBy('name')->get();
        return view('configuracoes.servicos', compact('servicos'));
    }

    public function storeServico(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'price'            => 'required|numeric|min:0',
        ]);
        $data['active'] = true;
        Servico::create($data);
        return back()->with('success', 'Serviço criado!');
    }

    public function updateServico(Request $request, Servico $servico)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'price'            => 'required|numeric|min:0',
        ]);
        // Checkbox: marcado => ativo; ausente => inativo.
        $data['active'] = $request->boolean('active');
        $servico->update($data);
        return back()->with('success', 'Serviço atualizado!');
    }

    public function destroyServico(Servico $servico)
    {
        $servico->delete();
        return back()->with('success', 'Serviço removido.');
    }

    public function colaboradores()
    {
        $colaboradores = Colaborador::with('user')
            ->withCount('agendamentos')
            ->get();

        return view('configuracoes.colaboradores', compact('colaboradores'));
    }

    /**
     * Cria um colaborador: gera o usuário (role colaborador) e o vínculo com o petshop.
     */
    public function storeColaborador(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|max:255|unique:users,email',
            'phone'               => 'nullable|string|max:20',
            'cargo'               => 'required|string|max:100',
            'comissao_percentual' => 'required|numeric|min:0|max:100',
            'password'            => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'role'     => 'colaborador',
                'password' => Hash::make($data['password']),
            ]);

            // petshop_id é preenchido automaticamente pela trait BelongsToPetshop.
            Colaborador::create([
                'user_id'             => $user->id,
                'cargo'               => $data['cargo'],
                'comissao_percentual' => $data['comissao_percentual'],
            ]);
        });

        return back()->with('success', 'Colaborador adicionado com sucesso!');
    }

    /**
     * Atualiza os dados do colaborador e do usuário vinculado.
     */
    public function updateColaborador(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($colaborador->user_id)],
            'phone'               => 'nullable|string|max:20',
            'cargo'               => 'required|string|max:100',
            'comissao_percentual' => 'required|numeric|min:0|max:100',
            'password'            => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($data, $colaborador) {
            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $colaborador->user->update($userData);

            $colaborador->update([
                'cargo'               => $data['cargo'],
                'comissao_percentual' => $data['comissao_percentual'],
            ]);
        });

        return back()->with('success', 'Colaborador atualizado!');
    }

    /**
     * Remove o colaborador e o usuário vinculado.
     * Agendamentos passados mantêm-se (colaborador_id vira null via nullOnDelete).
     */
    public function destroyColaborador(Colaborador $colaborador)
    {
        DB::transaction(function () use ($colaborador) {
            $user = $colaborador->user;
            $colaborador->delete();
            // Não removemos admins; só usuários de colaborador.
            if ($user && $user->role === 'colaborador') {
                $user->delete();
            }
        });

        return back()->with('success', 'Colaborador removido.');
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
        $data = $request->validate([
            'atendimentos_para_premio' => 'required|integer|min:1',
            'desconto_percentual'      => 'required|numeric|min:0|max:100',
        ]);
        FidelidadeConfig::updateOrCreate(
            ['petshop_id' => $this->currentPetshop()->id],
            $data
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
