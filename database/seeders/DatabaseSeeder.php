<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\FidelidadeConfig;
use App\Models\Horario;
use App\Models\Pet;
use App\Models\Petshop;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name'     => 'Ana Paula',
            'email'    => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '11999990000',
        ]);

        // Petshop
        $petshop = Petshop::create([
            'user_id'       => $admin->id,
            'name'          => 'Pet & Tosa da Ana',
            'slug'          => 'pet-tosa-ana',
            'primary_color' => '#8B5CF6',
            'phone'         => '11988880000',
            'address'       => 'Rua das Flores, 123 - São Paulo, SP',
            'active'        => true,
        ]);

        // Horários: seg-sex 8-18, sab 8-16, dom fechado
        $horarios = [
            ['weekday' => 0, 'open' => null,   'close' => null,    'closed' => true],
            ['weekday' => 1, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 2, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 3, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 4, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 5, 'open' => '08:00', 'close' => '18:00', 'closed' => false],
            ['weekday' => 6, 'open' => '08:00', 'close' => '16:00', 'closed' => false],
        ];
        foreach ($horarios as $h) {
            Horario::create(array_merge($h, ['petshop_id' => $petshop->id]));
        }

        // Colaboradores
        $userMarcos = User::create([
            'name'     => 'Marcos Silva',
            'email'    => 'marcos@demo.com',
            'password' => Hash::make('password'),
            'role'     => 'colaborador',
            'phone'    => '11977770001',
        ]);
        $marcos = Colaborador::create([
            'user_id'              => $userMarcos->id,
            'petshop_id'           => $petshop->id,
            'cargo'                => 'Tosador',
            'comissao_percentual'  => 40,
        ]);

        $userJulia = User::create([
            'name'     => 'Julia Ferreira',
            'email'    => 'julia@demo.com',
            'password' => Hash::make('password'),
            'role'     => 'colaborador',
            'phone'    => '11966660002',
        ]);
        $julia = Colaborador::create([
            'user_id'              => $userJulia->id,
            'petshop_id'           => $petshop->id,
            'cargo'                => 'Banhista',
            'comissao_percentual'  => 35,
        ]);

        // Serviços
        $servicos = [
            ['name' => 'Banho P',     'duration_minutes' => 60,  'price' => 45.00],
            ['name' => 'Banho M',     'duration_minutes' => 60,  'price' => 60.00],
            ['name' => 'Banho G',     'duration_minutes' => 90,  'price' => 80.00],
            ['name' => 'Tosa',        'duration_minutes' => 90,  'price' => 70.00],
            ['name' => 'Banho + Tosa','duration_minutes' => 120, 'price' => 120.00],
        ];
        $servicoModels = [];
        foreach ($servicos as $s) {
            $servicoModels[] = Servico::create(array_merge($s, ['petshop_id' => $petshop->id]));
        }

        // Fidelidade config
        FidelidadeConfig::create([
            'petshop_id'             => $petshop->id,
            'atendimentos_para_premio' => 10,
            'desconto_percentual'    => 10,
        ]);

        // Clientes com dados brasileiros fictícios
        $clientesData = [
            ['name' => 'Carla Mendes',     'phone' => '11912340001', 'email' => 'carla@email.com'],
            ['name' => 'Roberto Alves',    'phone' => '11912340002', 'email' => 'roberto@email.com'],
            ['name' => 'Fernanda Costa',   'phone' => '11912340003', 'email' => null],
            ['name' => 'Paulo Rodrigues',  'phone' => '11912340004', 'email' => 'paulo@email.com'],
            ['name' => 'Mariana Lima',     'phone' => '11912340005', 'email' => 'mariana@email.com'],
            ['name' => 'Lucas Pereira',    'phone' => '11912340006', 'email' => null],
            ['name' => 'Patrícia Santos',  'phone' => '11912340007', 'email' => 'patricia@email.com'],
            ['name' => 'Eduardo Oliveira', 'phone' => '11912340008', 'email' => null],
            ['name' => 'Juliana Souza',    'phone' => '11912340009', 'email' => 'juliana@email.com'],
            ['name' => 'André Barbosa',    'phone' => '11912340010', 'email' => 'andre@email.com'],
        ];
        $clientes = [];
        foreach ($clientesData as $c) {
            $clientes[] = Cliente::create(array_merge($c, ['petshop_id' => $petshop->id]));
        }

        // Pets distribuídos entre clientes
        $petsData = [
            ['cliente' => 0, 'name' => 'Rex',     'species' => 'Cachorro', 'breed' => 'Labrador',   'weight' => 28.5],
            ['cliente' => 0, 'name' => 'Mel',     'species' => 'Cachorro', 'breed' => 'Poodle',     'weight' => 5.2],
            ['cliente' => 1, 'name' => 'Thor',    'species' => 'Cachorro', 'breed' => 'Pitbull',    'weight' => 22.0],
            ['cliente' => 2, 'name' => 'Mimi',    'species' => 'Gato',     'breed' => 'Siamês',     'weight' => 3.8],
            ['cliente' => 3, 'name' => 'Bob',     'species' => 'Cachorro', 'breed' => 'Bulldog',    'weight' => 18.0],
            ['cliente' => 4, 'name' => 'Luna',    'species' => 'Cachorro', 'breed' => 'Shih Tzu',   'weight' => 4.5],
            ['cliente' => 4, 'name' => 'Simba',   'species' => 'Gato',     'breed' => 'Persa',      'weight' => 4.1],
            ['cliente' => 5, 'name' => 'Pipoca',  'species' => 'Cachorro', 'breed' => 'Lhasa Apso', 'weight' => 6.0],
            ['cliente' => 6, 'name' => 'Bolinha', 'species' => 'Cachorro', 'breed' => 'Yorkshire',  'weight' => 3.2],
            ['cliente' => 7, 'name' => 'Nina',    'species' => 'Cachorro', 'breed' => 'Dachshund',  'weight' => 7.5],
            ['cliente' => 7, 'name' => 'Max',     'species' => 'Cachorro', 'breed' => 'Golden',     'weight' => 30.0],
            ['cliente' => 8, 'name' => 'Fifi',    'species' => 'Gato',     'breed' => 'Maine Coon', 'weight' => 6.5],
            ['cliente' => 9, 'name' => 'Totó',    'species' => 'Cachorro', 'breed' => 'Poodle',     'weight' => 5.8],
            ['cliente' => 9, 'name' => 'Lilica',  'species' => 'Cachorro', 'breed' => 'Maltês',     'weight' => 3.0],
            ['cliente' => 1, 'name' => 'Chico',   'species' => 'Cachorro', 'breed' => 'SRD',        'weight' => 12.0],
        ];
        $pets = [];
        foreach ($petsData as $p) {
            $pets[] = Pet::create([
                'cliente_id'   => $clientes[$p['cliente']]->id,
                'name'         => $p['name'],
                'species'      => $p['species'],
                'breed'        => $p['breed'],
                'weight'       => $p['weight'],
                'retorno_dias' => 30,
            ]);
        }

        // 20 agendamentos nos próximos 7 dias
        $statuses   = ['pendente', 'confirmado', 'em_andamento', 'concluido', 'cancelado'];
        $colaboradores = [$marcos, $julia];
        $now        = Carbon::now();

        for ($i = 0; $i < 20; $i++) {
            $daysAhead   = rand(0, 7);
            $hour        = rand(8, 17);
            $minute      = rand(0, 1) * 30;
            $scheduledAt = $now->copy()->addDays($daysAhead)->setHour($hour)->setMinute($minute)->setSecond(0);
            $pet         = $pets[array_rand($pets)];
            $servico     = $servicoModels[array_rand($servicoModels)];
            $colaborador = $colaboradores[array_rand($colaboradores)];
            $status      = $statuses[array_rand($statuses)];

            Agendamento::create([
                'petshop_id'    => $petshop->id,
                'cliente_id'    => $pet->cliente_id,
                'pet_id'        => $pet->id,
                'colaborador_id'=> $colaborador->id,
                'servico_id'    => $servico->id,
                'scheduled_at'  => $scheduledAt,
                'status'        => $status,
                'valor'         => $servico->price,
            ]);
        }
    }
}
