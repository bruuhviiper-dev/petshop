<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Horario;
use App\Models\Petshop;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendamentoPublicoTest extends TestCase
{
    use RefreshDatabase;

    private Petshop $petshop;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['role' => 'admin']);
        $this->petshop = Petshop::create([
            'user_id' => $user->id, 'name' => 'Test Pet', 'slug' => 'test-pet',
            'phone' => '11999999999', 'address' => 'Rua Teste, 1', 'active' => true,
        ]);

        Horario::create([
            'petshop_id' => $this->petshop->id, 'weekday' => Carbon::tomorrow()->dayOfWeek,
            'open' => '08:00', 'close' => '18:00', 'closed' => false,
        ]);
    }

    public function test_link_publico_carrega_com_slug_valido(): void
    {
        $response = $this->get("/agendar/{$this->petshop->slug}");
        $response->assertStatus(200);
        $response->assertSee($this->petshop->name);
    }

    public function test_slug_invalido_retorna_404(): void
    {
        $response = $this->get('/agendar/nao-existe-123');
        $response->assertStatus(404);
    }

    public function test_criacao_agendamento_cliente_novo(): void
    {
        $servico = Servico::create([
            'petshop_id' => $this->petshop->id, 'name' => 'Banho', 'duration_minutes' => 60, 'price' => 50,
        ]);

        $amanha = Carbon::tomorrow()->format('Y-m-d');
        $response = $this->postJson("/agendar/{$this->petshop->slug}", [
            'dono_nome'     => 'João da Silva',
            'dono_telefone' => '11988880000',
            'pet_nome'      => 'Toby',
            'pet_especie'   => 'Cachorro',
            'pet_raca'      => 'Poodle',
            'servico_id'    => $servico->id,
            'data'          => $amanha,
            'horario'       => '09:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('clientes', ['phone' => '11988880000']);
        $this->assertDatabaseHas('agendamentos', ['servico_id' => $servico->id]);
    }

    public function test_slots_ocupados_nao_aparecem(): void
    {
        $servico = Servico::create([
            'petshop_id' => $this->petshop->id, 'name' => 'Banho', 'duration_minutes' => 60, 'price' => 50,
        ]);

        $amanha = Carbon::tomorrow();
        $user = User::factory()->create();
        $petshop = $this->petshop;

        $cliente = \App\Models\Cliente::create(['petshop_id' => $petshop->id, 'name' => 'Teste', 'phone' => '11111111111']);
        $pet = \App\Models\Pet::create(['cliente_id' => $cliente->id, 'name' => 'Rex', 'species' => 'Cachorro']);

        Agendamento::create([
            'petshop_id' => $petshop->id, 'cliente_id' => $cliente->id, 'pet_id' => $pet->id,
            'servico_id' => $servico->id, 'scheduled_at' => $amanha->setHour(9)->setMinute(0),
            'status' => 'confirmado', 'valor' => 50,
        ]);

        $response = $this->getJson("/agendar/{$petshop->slug}/horarios?data={$amanha->format('Y-m-d')}&servico_id={$servico->id}");
        $slots = $response->json('slots');
        $this->assertNotContains('09:00', $slots);
    }
}
