# Changelog

## [2.0.0-premium] - 2026-06-04

### Added

- Dark mode completo com toggle persistido em localStorage
- Skeleton loaders e empty states em todas as listas
- Services, Repositories e DTOs tipados (PHP 8.2 readonly classes)
- Rate limiting nas rotas públicas (10 req/min por IP)
- Honeypot no formulário público de agendamento
- CSP básico via middleware
- Docker Compose com PHP 8.3, MySQL 8, Redis e Nginx
- Makefile com comandos de desenvolvimento (`install`, `seed`, `test`, `queue`, `dev`)
- GitHub Actions CI com MySQL e testes paralelos
- Postman collection para API pública
- `aria-current="page"` nos links da sidebar ativos
- `role="dialog"` e `aria-modal="true"` nos modais
- Responsividade para telas 320px (overflow-x-auto, grid-cols-1 em mobile)

### Changed

- AgendaController, ClienteController, PetController, DashboardController e AgendamentoPublicoController refatorados com injeção de dependência
- Grid de métricas do dashboard: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`
- Componente `<x-card>` agora suporta dark mode

## [1.0.0] - 2026-06-04

### Added

- Sistema completo de agenda e gestão para petshops
- Link público de agendamento em 3 etapas com Alpine.js
- Jobs de WhatsApp com retry (confirmação 24h e 1h antes)
- Dashboard com métricas e gráficos Chart.js
- Programa de fidelidade configurável
- Relatório financeiro com exportação CSV
- Controle de comissões de colaboradores
- Registro de vacinas e ficha de pets
- Configuração de horários de funcionamento por dia da semana
- Autenticação com Laravel Breeze
- Políticas de autorização (Policies) por petshop
