<?php

namespace Tests\Feature;

use App\Events\AgendamentoConcluido;
use App\Listeners\IncrementarFidelidade;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\FidelidadeCliente;
use App\Models\FidelidadeConfig;
use App\Models\NotificacaoLog;
use App\Models\Pet;
use App\Models\Petshop;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FidelidadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_premio_registrado_ao_atingir_meta(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $petshop = Petshop::create([
            'user_id' => $user->id, 'name' => 'Petshop', 'slug' => 'ps',
            'phone' => '11999990000', 'address' => 'Rua, 1', 'active' => true,
        ]);
        $config = FidelidadeConfig::create([
            'petshop_id' => $petshop->id, 'atendimentos_para_premio' => 3, 'desconto_percentual' => 10,
        ]);
        $cliente = Cliente::create(['petshop_id' => $petshop->id, 'name' => 'Ana', 'phone' => '11900000001']);
        $pet = Pet::create(['cliente_id' => $cliente->id, 'name' => 'Bob', 'species' => 'Cachorro']);
        $servico = Servico::create(['petshop_id' => $petshop->id, 'name' => 'Banho', 'duration_minutes' => 60, 'price' => 50]);

        $fidelidade = FidelidadeCliente::create([
            'petshop_id' => $petshop->id, 'cliente_id' => $cliente->id, 'total_atendimentos' => 2,
        ]);

        $agendamento = Agendamento::withoutGlobalScopes()->create([
            'petshop_id' => $petshop->id, 'cliente_id' => $cliente->id, 'pet_id' => $pet->id,
            'servico_id' => $servico->id, 'scheduled_at' => now(), 'status' => 'concluido', 'valor' => 50,
        ]);

        $listener = new IncrementarFidelidade();
        $listener->handle(new AgendamentoConcluido($agendamento));

        $fidelidade->refresh();
        $this->assertEquals(3, $fidelidade->total_atendimentos);

        $this->assertDatabaseHas('notificacoes_log', [
            'agendamento_id' => $agendamento->id,
            'type' => 'fidelidade',
        ]);
    }
}
