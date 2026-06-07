<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\Comissao;
use App\Models\FidelidadeConfig;
use App\Models\Financeiro;
use App\Models\Horario;
use App\Models\Pet;
use App\Models\Petshop;
use App\Models\Produto;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /** Raças brasileiras populares de cachorro */
    private array $racasCachorro = [
        'Labrador', 'Golden Retriever', 'Poodle', 'Shih Tzu', 'Yorkshire Terrier',
        'Maltês', 'Lhasa Apso', 'Bulldog Francês', 'Beagle', 'Dachshund',
        'Pinscher', 'Buldogue Inglês', 'Chow Chow', 'Husky Siberiano', 'SRD',
    ];

    /** Raças brasileiras populares de gato */
    private array $racasGato = [
        'Siamês', 'Persa', 'Maine Coon', 'Ragdoll', 'Bengal',
        'SRD', 'Angorá', 'Sphynx', 'British Shorthair', 'Abissínio',
    ];

    /** Nomes de pets populares no Brasil */
    private array $nomesPets = [
        'Rex', 'Mel', 'Thor', 'Luna', 'Bob', 'Nina', 'Max', 'Bella',
        'Pipoca', 'Bolinha', 'Totó', 'Lilica', 'Simba', 'Fifi', 'Chico',
        'Bidu', 'Fred', 'Lola', 'Zeus', 'Branquinha', 'Pretinho', 'Duque',
        'Princesa', 'Pingo', 'Mimi', 'Bola', 'Caramelo', 'Amendoim',
    ];

    public function run(): void
    {
        // =====================================================================
        // PETSHOP 1 — DEMO (mantido com credenciais originais)
        // =====================================================================
        $admin1 = User::create([
            'name'     => 'Ana Paula Ferreira',
            'email'    => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '11999990000',
        ]);

        $petshop1 = Petshop::create([
            'user_id'       => $admin1->id,
            'name'          => 'Pet & Tosa da Ana',
            'slug'          => 'pet-tosa-ana',
            'primary_color' => '#8B5CF6',
            'phone'         => '11988880000',
            'address'       => 'Rua das Flores, 123 - São Paulo, SP',
            'active'        => true,
        ]);

        // =====================================================================
        // PETSHOP 2
        // =====================================================================
        $admin2 = User::create([
            'name'     => 'Carlos Eduardo Souza',
            'email'    => 'carlos@petshop2.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '21988887777',
        ]);

        $petshop2 = Petshop::create([
            'user_id'       => $admin2->id,
            'name'          => 'Patas & Amor Rio',
            'slug'          => 'patas-amor-rio',
            'primary_color' => '#EC4899',
            'phone'         => '21977770000',
            'address'       => 'Av. Atlântica, 500 - Rio de Janeiro, RJ',
            'active'        => true,
        ]);

        // =====================================================================
        // PETSHOP 3
        // =====================================================================
        $admin3 = User::create([
            'name'     => 'Renata Oliveira Costa',
            'email'    => 'renata@petshop3.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '31966665555',
        ]);

        $petshop3 = Petshop::create([
            'user_id'       => $admin3->id,
            'name'          => 'PetBH Premium',
            'slug'          => 'petbh-premium',
            'primary_color' => '#10B981',
            'phone'         => '31955554444',
            'address'       => 'Rua da Bahia, 300 - Belo Horizonte, MG',
            'active'        => true,
        ]);

        // =====================================================================
        // CONFIGURAR HORÁRIOS E SERVIÇOS PARA CADA PETSHOP
        // =====================================================================
        foreach ([$petshop1, $petshop2, $petshop3] as $petshop) {
            $this->criarHorarios($petshop->id);
            $servicos = $this->criarServicos($petshop->id);
            $colaboradores = $this->criarColaboradores($petshop->id);
            $this->criarProdutos($petshop->id);

            FidelidadeConfig::create([
                'petshop_id'               => $petshop->id,
                'atendimentos_para_premio' => 10,
                'desconto_percentual'      => 10,
            ]);

            // 15 clientes com 2-3 pets cada
            $clientes = $this->criarClientes($petshop->id, 15);
            $pets     = $this->criarPets($clientes, $petshop->id);

            // 6 meses de histórico (todos concluídos)
            $this->criarHistorico($petshop->id, $pets, $servicos, $colaboradores);
        }

        // =====================================================================
        // 20 AGENDAMENTOS FUTUROS NO PETSHOP DEMO (próximos 7 dias, status variados)
        // =====================================================================
        $servicos1      = Servico::where('petshop_id', $petshop1->id)->get()->all();
        $colaboradores1 = Colaborador::where('petshop_id', $petshop1->id)->get()->all();
        $pets1          = Pet::whereHas('cliente', fn ($q) => $q->where('petshop_id', $petshop1->id))->get()->all();
        $statuses       = ['pendente', 'confirmado', 'confirmado', 'em_andamento', 'cancelado'];

        for ($i = 0; $i < 20; $i++) {
            $daysAhead   = rand(0, 7);
            $hour        = rand(8, 17);
            $minute      = (rand(0, 1)) * 30;
            $scheduledAt = Carbon::now()->addDays($daysAhead)->setHour($hour)->setMinute($minute)->setSecond(0);
            $pet         = $pets1[array_rand($pets1)];
            $servico     = $servicos1[array_rand($servicos1)];
            $colaborador = $colaboradores1[array_rand($colaboradores1)];

            Agendamento::create([
                'petshop_id'     => $petshop1->id,
                'cliente_id'     => $pet->cliente_id,
                'pet_id'         => $pet->id,
                'colaborador_id' => $colaborador->id,
                'servico_id'     => $servico->id,
                'scheduled_at'   => $scheduledAt,
                'status'         => $statuses[array_rand($statuses)],
                'valor'          => $servico->price,
            ]);
        }
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    private function criarHorarios(int $petshopId): void
    {
        $horarios = [
            ['weekday' => 0, 'open' => null,    'close' => null,    'closed' => true],
            ['weekday' => 1, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 2, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 3, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 4, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 5, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 6, 'open' => '08:00', 'close' => '16:00', 'closed' => false],
        ];
        foreach ($horarios as $h) {
            Horario::create(array_merge($h, ['petshop_id' => $petshopId]));
        }
    }

    /** @return Servico[] */
    private function criarServicos(int $petshopId): array
    {
        $servicos = [
            ['name' => 'Banho P',      'duration_minutes' => 60,  'price' => 45.00],
            ['name' => 'Banho M',      'duration_minutes' => 60,  'price' => 60.00],
            ['name' => 'Banho G',      'duration_minutes' => 90,  'price' => 80.00],
            ['name' => 'Tosa',         'duration_minutes' => 90,  'price' => 70.00],
            ['name' => 'Banho + Tosa', 'duration_minutes' => 120, 'price' => 120.00],
        ];
        $models = [];
        foreach ($servicos as $s) {
            $models[] = Servico::create(array_merge($s, ['petshop_id' => $petshopId]));
        }
        return $models;
    }

    private function criarProdutos(int $petshopId): void
    {
        $produtos = [
            ['name' => 'Ração Premium Cães 15kg',     'category' => 'Ração',      'price' => 189.90, 'cost' => 120.00, 'stock_quantity' => 30],
            ['name' => 'Ração Gatos Castrados 10kg',  'category' => 'Ração',      'price' => 159.90, 'cost' => 98.00,  'stock_quantity' => 25],
            ['name' => 'Shampoo Neutro 500ml',        'category' => 'Higiene',    'price' => 29.90,  'cost' => 12.00,  'stock_quantity' => 40],
            ['name' => 'Coleira Antipulgas',          'category' => 'Acessórios', 'price' => 79.90,  'cost' => 35.00,  'stock_quantity' => 15],
            ['name' => 'Brinquedo Mordedor',          'category' => 'Brinquedo',  'price' => 24.90,  'cost' => 9.00,   'stock_quantity' => 3],
            ['name' => 'Petisco Bifinho 500g',        'category' => 'Petisco',    'price' => 19.90,  'cost' => 8.00,   'stock_quantity' => 50],
        ];
        foreach ($produtos as $p) {
            Produto::create(array_merge($p, ['petshop_id' => $petshopId, 'min_stock' => 5, 'unit' => 'un', 'active' => true]));
        }
    }

    /** @return Colaborador[] */
    private function criarColaboradores(int $petshopId): array
    {
        $colaboradoresData = [
            ['name' => 'Marcos Vinicius Silva',  'email' => "marcos.{$petshopId}@demo.com",  'cargo' => 'Tosador',   'comissao' => 40],
            ['name' => 'Júlia Cristina Ferreira','email' => "julia.{$petshopId}@demo.com",   'cargo' => 'Banhista',  'comissao' => 35],
            ['name' => 'Rodrigo Santos Alves',   'email' => "rodrigo.{$petshopId}@demo.com", 'cargo' => 'Auxiliar',  'comissao' => 30],
        ];

        $models = [];
        foreach ($colaboradoresData as $i => $c) {
            $user = User::create([
                'name'     => $c['name'],
                'email'    => $c['email'],
                'password' => Hash::make('password'),
                'role'     => 'colaborador',
                'phone'    => '1190000' . $petshopId . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);
            $models[] = Colaborador::create([
                'user_id'             => $user->id,
                'petshop_id'          => $petshopId,
                'cargo'               => $c['cargo'],
                'comissao_percentual' => $c['comissao'],
            ]);
        }
        return $models;
    }

    /**
     * Cria clientes com dados pt_BR realistas.
     *
     * @return Cliente[]
     */
    private function criarClientes(int $petshopId, int $quantidade): array
    {
        $nomes = [
            'Ana Carolina', 'Beatriz', 'Camila', 'Daniela', 'Eduarda',
            'Fernanda', 'Gabriela', 'Helena', 'Isabela', 'Juliana',
            'Karen', 'Larissa', 'Mariana', 'Natalia', 'Patricia',
            'Rafaela', 'Sabrina', 'Tatiana', 'Viviane', 'Yasmin',
        ];
        $sobrenomes = [
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues',
            'Ferreira', 'Alves', 'Pereira', 'Lima', 'Gomes',
            'Costa', 'Ribeiro', 'Martins', 'Carvalho', 'Almeida',
        ];
        $ddds = ['11', '21', '31', '41', '51', '61', '71', '81', '91'];

        $clientes = [];
        for ($i = 0; $i < $quantidade; $i++) {
            $nome      = $nomes[$i % count($nomes)] . ' ' . $sobrenomes[($i + $petshopId) % count($sobrenomes)];
            $ddd       = $ddds[$i % count($ddds)];
            $phone     = $ddd . '9' . str_pad(($petshopId * 1000 + $i + 10000), 8, '0', STR_PAD_LEFT);
            $clientes[] = Cliente::create([
                'petshop_id' => $petshopId,
                'name'       => $nome,
                'phone'      => $phone,
                'email'      => strtolower(str_replace(' ', '.', $nome)) . ".{$petshopId}{$i}@email.com.br",
                'birthdate'  => Carbon::now()->subYears(rand(25, 55))->subDays(rand(0, 365))->toDateString(),
            ]);
        }
        return $clientes;
    }

    /**
     * Cria 2-3 pets por cliente com raças brasileiras populares.
     *
     * @param  Cliente[]  $clientes
     * @return Pet[]
     */
    private function criarPets(array $clientes, int $petshopId): array
    {
        $pets       = [];
        $petCounter = 0;

        foreach ($clientes as $cliente) {
            $qtd = rand(1, 3);
            for ($j = 0; $j < $qtd; $j++) {
                $species = rand(0, 4) === 0 ? 'Gato' : 'Cachorro';
                $racas   = $species === 'Gato' ? $this->racasGato : $this->racasCachorro;
                $nome    = $this->nomesPets[$petCounter % count($this->nomesPets)];
                $petCounter++;

                $pets[] = Pet::create([
                    'cliente_id'   => $cliente->id,
                    'name'         => $nome,
                    'species'      => $species,
                    'breed'        => $racas[array_rand($racas)],
                    'weight'       => round(rand(20, 350) / 10, 1),
                    'retorno_dias' => 30,
                    'photo'        => "https://placedog.net/200/200?id={$petCounter}",
                ]);
            }
        }
        return $pets;
    }

    /**
     * Gera 6 meses de agendamentos históricos (todos concluídos) com financeiro e comissões.
     *
     * @param  Servico[]     $servicos
     * @param  Colaborador[] $colaboradores
     * @param  Pet[]         $pets
     */
    private function criarHistorico(int $petshopId, array $pets, array $servicos, array $colaboradores): void
    {
        // Gerar ~150 agendamentos ao longo de 180 dias
        $totalAgendamentos = count($pets) * 3;

        for ($i = 0; $i < $totalAgendamentos; $i++) {
            $daysAgo     = rand(1, 180);
            $hour        = rand(8, 17);
            $minute      = (rand(0, 1)) * 30;
            $scheduledAt = Carbon::now()->subDays($daysAgo)->setHour($hour)->setMinute($minute)->setSecond(0);

            $pet         = $pets[array_rand($pets)];
            $servico     = $servicos[array_rand($servicos)];
            $colaborador = $colaboradores[array_rand($colaboradores)];

            $agendamento = Agendamento::create([
                'petshop_id'     => $petshopId,
                'cliente_id'     => $pet->cliente_id,
                'pet_id'         => $pet->id,
                'colaborador_id' => $colaborador->id,
                'servico_id'     => $servico->id,
                'scheduled_at'   => $scheduledAt,
                'status'         => 'concluido',
                'valor'          => $servico->price,
            ]);

            // Lançamento financeiro
            Financeiro::create([
                'petshop_id'     => $petshopId,
                'agendamento_id' => $agendamento->id,
                'type'           => 'receita',
                'amount'         => $servico->price,
                'description'    => "Agendamento #{$agendamento->id} — {$servico->name}",
                'paid_at'        => $scheduledAt,
            ]);

            // Comissão do colaborador
            $comissaoValor = round(($servico->price * $colaborador->comissao_percentual) / 100, 2);
            Comissao::create([
                'colaborador_id' => $colaborador->id,
                'agendamento_id' => $agendamento->id,
                'amount'         => $comissaoValor,
                'paid'           => rand(0, 3) > 0, // 75% já pago
                'paid_at'        => rand(0, 3) > 0 ? $scheduledAt->copy()->addDays(rand(1, 5)) : null,
            ]);
        }
    }
}
