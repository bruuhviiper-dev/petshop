<?php

namespace Tests\Feature;

use App\Events\AgendamentoConcluido;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\Pet;
use App\Models\Petshop;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AgendamentoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Petshop $petshop;
    private Servico $servico;
    private Cliente $cliente;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->petshop = Petshop::create([
            'user_id' => $this->user->id, 'name' => 'Test', 'slug' => 'test',
            'phone' => '11999999999', 'address' => 'Rua, 1', 'active' => true,
        ]);
        $this->servico = Servico::create(['petshop_id' => $this->petshop->id, 'name' => 'Banho', 'duration_minutes' => 60, 'price' => 50]);
        $this->cliente = Cliente::create(['petshop_id' => $this->petshop->id, 'name' => 'João', 'phone' => '11999990001']);
        $this->pet = Pet::create(['cliente_id' => $this->cliente->id, 'name' => 'Rex', 'species' => 'Cachorro']);
    }

    public function test_criacao_agendamento_autenticado(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('agenda.store'), [
            'cliente_id'   => $this->cliente->id,
            'pet_id'       => $this->pet->id,
            'servico_id'   => $this->servico->id,
            'scheduled_at' => Carbon::tomorrow()->setHour(9)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('agendamentos', ['pet_id' => $this->pet->id]);
    }

    public function test_mudanca_status_concluido_dispara_evento(): void
    {
        Event::fake([AgendamentoConcluido::class]);
        $this->actingAs($this->user);

        $agendamento = Agendamento::create([
            'petshop_id' => $this->petshop->id, 'cliente_id' => $this->cliente->id,
            'pet_id' => $this->pet->id, 'servico_id' => $this->servico->id,
            'scheduled_at' => Carbon::tomorrow()->setHour(10),
            'status' => 'confirmado', 'valor' => 50,
        ]);

        $this->patchJson(route('agenda.updateStatus', $agendamento), ['status' => 'concluido']);

        Event::assertDispatched(AgendamentoConcluido::class);
    }

    public function test_usuario_nao_acessa_agendamento_de_outro_petshop(): void
    {
        $outroUser = User::factory()->create(['role' => 'admin']);
        $outroPetshop = Petshop::create([
            'user_id' => $outroUser->id, 'name' => 'Outro', 'slug' => 'outro',
            'phone' => '11888880000', 'address' => 'Rua B, 2', 'active' => true,
        ]);
        $outroCliente = Cliente::create(['petshop_id' => $outroPetshop->id, 'name' => 'Maria', 'phone' => '11888880001']);
        $outroPet = Pet::create(['cliente_id' => $outroCliente->id, 'name' => 'Luna', 'species' => 'Gato']);
        $outroServico = Servico::create(['petshop_id' => $outroPetshop->id, 'name' => 'Tosa', 'duration_minutes' => 60, 'price' => 70]);

        $agOutro = Agendamento::withoutGlobalScopes()->create([
            'petshop_id' => $outroPetshop->id, 'cliente_id' => $outroCliente->id,
            'pet_id' => $outroPet->id, 'servico_id' => $outroServico->id,
            'scheduled_at' => Carbon::tomorrow()->setHour(11),
            'status' => 'pendente', 'valor' => 70,
        ]);

        $this->actingAs($this->user);
        $response = $this->patchJson(route('agenda.updateStatus', $agOutro->id), ['status' => 'concluido']);
        $response->assertStatus(404);
    }
}
