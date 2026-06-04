# PetAgenda

Sistema completo de agenda e gestão para petshops e banho & tosa.

## Stack

- **Backend:** Laravel 12 + PHP 8.3
- **Banco de dados:** MySQL 8
- **Frontend:** Blade + Alpine.js + Tailwind CSS
- **Autenticação:** Laravel Breeze (stack Blade)
- **Filas:** Laravel Queue (driver: database)
- **Agendamentos:** Laravel Scheduler
- **Notificações:** WhatsApp via Z-API / Evolution API

## Funcionalidades

- Agenda visual por colaborador com grade horária
- Link público de agendamento (sem login) em 3 etapas
- Cadastro completo de clientes e pets com ficha histórica
- Registro de vacinas por pet
- Relatório financeiro com exportação CSV
- Controle de comissões por colaborador
- Programa de fidelidade configurável
- Notificações automáticas via WhatsApp (confirmação, lembretes, avaliação, retorno)
- Dashboard com métricas e gráficos
- Painel de configurações completo (horários, serviços, integração WhatsApp)

## Requisitos

- PHP 8.3+
- MySQL 8+
- Node.js 20+
- Composer 2+

## Instalação local

```bash
# 1. Clone o repositório
git clone https://github.com/bruuhviiper-dev/petshop.git
cd petshop

# 2. Instale as dependências PHP
composer install

# 3. Instale as dependências Node
npm install

# 4. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 5. Configure o banco de dados no .env
DB_DATABASE=petshop
DB_USERNAME=root
DB_PASSWORD=

# 6. Execute as migrations e seeders
php artisan migrate --seed

# 7. Crie o link simbólico de storage
php artisan storage:link

# 8. Compile os assets
npm run build

# 9. Inicie o servidor
php artisan serve
```

Acesse `http://localhost:8000` e faça login com:

- **Email:** admin@demo.com
- **Senha:** password

## Estrutura de pastas

```
app/
├── Events/          # AgendamentoCriado, AgendamentoConcluido, AgendamentoCancelado
├── Http/
│   ├── Controllers/ # Agenda, Clientes, Pets, Financeiro, Dashboard, Configuracoes...
│   ├── Middleware/  # EnsurePetshopSetup, SetPetshopContext
│   └── Requests/    # Form Requests validados
├── Jobs/            # Jobs de WhatsApp (confirmação, lembretes, avaliação, retorno)
├── Listeners/       # Financeiro, Fidelidade, Avaliação
├── Models/          # Todos os models com relacionamentos
├── Policies/        # AgendamentoPolicy, ClientePolicy, PetPolicy
└── Traits/          # BelongsToPetshop (global scope por petshop)

resources/views/
├── agenda/          # Grade horária com Alpine.js
├── clientes/        # CRUD + ficha completa
├── pets/            # Ficha com vacinas e histórico
├── financeiro/      # Relatório filtrado + exportar CSV
├── comissoes/       # Controle de comissões
├── configuracoes/   # Petshop, serviços, colaboradores, horários, fidelidade, integração
├── publico/         # Agendamento público em 3 etapas
├── dashboard.blade.php
└── layouts/app.blade.php  # Sidebar + topbar + CSS custom property --color-brand
```

## Instalação em produção (Hostinger VPS com Nginx)

```bash
# 1. Conecte via SSH e instale as dependências
sudo apt update && sudo apt install -y php8.3-fpm php8.3-mysql php8.3-curl php8.3-mbstring \
    php8.3-xml php8.3-zip php8.3-gd nodejs npm composer nginx mysql-server

# 2. Clone e configure
git clone https://github.com/bruuhviiper-dev/petshop.git /var/www/petshop
cd /var/www/petshop
composer install --no-dev --optimize-autoloader
npm ci && npm run build

cp .env.example .env
php artisan key:generate
# Edite .env com as configurações de produção

php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache

sudo chown -R www-data:www-data /var/www/petshop/storage /var/www/petshop/bootstrap/cache

# 3. Configure o Nginx
sudo nano /etc/nginx/sites-available/petshop
```

Configuração do Nginx:

```nginx
server {
    listen 80;
    server_name seudominio.com.br;
    root /var/www/petshop/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/petshop /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# 4. Configure o Supervisor para filas e scheduler
sudo apt install -y supervisor

# /etc/supervisor/conf.d/petshop-worker.conf
# [program:petshop-worker]
# command=php /var/www/petshop/artisan queue:work database --sleep=3 --tries=3
# user=www-data
# autostart=true
# autorestart=true

# Cron para o scheduler
echo "* * * * * www-data cd /var/www/petshop && php artisan schedule:run >> /dev/null 2>&1" \
    | sudo tee /etc/cron.d/petshop
```

## WhatsApp — Z-API / Evolution API

No painel de configurações (`/configuracoes/integracao`), preencha:

| Campo | Descrição |
|-------|-----------|
| WHATSAPP_API_URL | URL base da API (ex.: `https://api.z-api.io/instances/...`) |
| WHATSAPP_API_TOKEN | Token de autenticação |
| WHATSAPP_DEFAULT_INSTANCE | ID da instância conectada |

Os jobs disparam mensagens personalizadas nos seguintes momentos:
- **Confirmação** — imediatamente após criar o agendamento
- **Lembrete 24h** — diariamente às 08:00 para agendamentos do dia seguinte
- **Lembrete 1h** — a cada hora para agendamentos na próxima hora
- **Avaliação** — 2 horas após o status ser marcado como concluído
- **Retorno** — semanalmente (domingos às 09:00) para pets com retorno atrasado

## Credenciais demo

| Campo | Valor |
|-------|-------|
| Email | admin@demo.com |
| Senha | password |
| Email Marcos | marcos@demo.com |
| Email Julia | julia@demo.com |

## Licença

Uso e revenda permitidos. Domínios ilimitados. Sem royalties.
