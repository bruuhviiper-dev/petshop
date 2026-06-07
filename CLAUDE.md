# PetAgenda — guia para o Claude Code

Template **Laravel 12** de gestão para petshops (agenda, clientes/pets, financeiro,
comissões, fidelidade e link público de agendamento). Multi-tenant: cada usuário
admin é dono de um petshop e só enxerga os próprios dados.

## Stack
- PHP 8.2+ · Laravel 12 · Blade + **Alpine.js v3** (sem build de SPA) · **Tailwind CSS** (Vite)
- MySQL 8 (Laragon local) · Filas em `database` · Auth via Laravel Breeze
- Gráficos: Chart.js via CDN

## Comandos
```bash
npm run build          # OBRIGATÓRIO após mudar CSS/JS — gera public/build/manifest.json
                       # (sem ele: ViteManifestNotFoundException)
npm run dev            # HMR durante o desenvolvimento
php artisan migrate:fresh --seed --force   # recria o banco com dados demo
php artisan serve      # sobe em http://127.0.0.1:8000
php artisan test       # PHPUnit
./vendor/bin/pint      # formatação (PSR-12)
```
> Node v18 local emite warning de engine (projeto pede v20+), mas o `vite build` funciona.

## Login demo
`admin@demo.com` / `password` (petshop "Pet & Tosa da Ana", slug `pet-tosa-ana`).
Outros admins: `carlos@petshop2.com`, `renata@petshop3.com` (mesma senha).
O slug pode mudar se o petshop for renomeado nas Configurações — a home resolve o
slug do demo dinamicamente (ver `routes/web.php`, rota `home`).

## Arquitetura
- **Controllers** finos → **Services** (regra de negócio) → **Repositories** (consultas).
  Ex.: `AgendamentoService` (slots disponíveis, status) + `AgendamentoRepository`.
- **DTOs** tipados em `app/DTOs`. **Form Requests** em `app/Http/Requests`.
- **Events/Listeners + Jobs** para notificações WhatsApp (confirmação, lembretes 24h/1h,
  avaliação, retorno) — com retry, gravados em `notificacoes_log`.
- Agendamento público sem login: `AgendamentoPublicoController` + `routes` com prefixo
  `agendar/{slug}` (rate limit `agendamento-publico`, 10/min por IP, definido em `bootstrap/app.php`).

### Multi-tenancy (trait `App\Traits\BelongsToPetshop`)
Modelos com coluna `petshop_id` usam a trait, que:
1. aplica **global scope** filtrando pelo petshop do usuário logado;
2. preenche `petshop_id` automaticamente no `creating`.
Por isso **os controllers não setam `petshop_id` manualmente**. `Auth::user()->petshop`
existe só para **admins** (é `hasOne` por `user_id`); colaboradores têm `->colaborador`.

## Convenções de UI (importante)
- **Dark mode** é por classe `dark` no `<html>` (persistido em `localStorage.darkMode`,
  aplicado antes do render em `layouts/app.blade.php`). **Todo texto/borda/fundo precisa
  de variante `dark:`** — labels cinzas sem `dark:` somem no tema escuro.
  Use as classes utilitárias de `resources/css/app.css`:
  - `.form-label` · `.form-input` / `.form-select` / `.form-textarea` · `.form-hint` · `.form-error`
- **Cor da marca:** `--color-brand` vem de `$currentPetshop->primary_color`
  (compartilhado pelo middleware `SetPetshopContext`). Utilize `.bg-brand`, `.text-brand`,
  `.bg-brand-soft`, `.btn-brand`. Landing/auth usam o violeta fixo `.gradient-brand`.
- **CSP × Alpine (gotcha crítico):** o middleware `ContentSecurityPolicy` exige
  `'unsafe-eval'` no `script-src` para o Alpine v3 avaliar `x-data`/`@click`/`:class`.
  **Não remover.** Sem isso o JS quebra silenciosamente (só aparece no browser).
- **Toasts/flash:** componente `<x-flash />` (toast fixo no canto). Após uma ação via
  `fetch` que recarrega a página, grave `sessionStorage.setItem('flash', JSON.stringify({type,text}))`
  antes do reload, ou dispare `window.dispatchEvent(new CustomEvent('toast',{detail:{type,text}}))`.
- **Layout do app** = `layouts/app.blade.php` (sidebar própria). `layouts/navigation.blade.php`
  é resíduo do Breeze e **não** é usado pelo app.
- Para validar Alpine/JS em browser headless: Chrome/Edge instalados
  (`chrome --headless=new --dump-dom --virtual-time-budget=4000 URL`).

## Onde mexer
- Agenda interna (grade por colaborador, modal, recorrência, atribuição/status): `resources/views/agenda/index.blade.php` (+ partial `_card.blade.php`) + `AgendaController`.
- Agendamento público (Serviço → Horário → Dados): `resources/views/publico/agendar.blade.php` + `AgendamentoPublicoController`.
- **PDV / Produtos / Estoque**: `PdvController`, `ProdutoController`, `VendaService` (transação: baixa estoque + movimento + receita no Financeiro). Views em `resources/views/pdv/*` e `resources/views/produtos/*`. Models: `Produto`, `Venda`, `VendaItem`, `EstoqueMovimento`.
- **Carteirinha digital do pet (QR)**: `PetCarteirinhaController` + `resources/views/publico/carteirinha.blade.php` (pública por `pets.public_token`). QR via `simplesoftwareio/simple-qrcode` (SVG inline, sem imagick). Link/QR na ficha do pet (`pets/show`).
- Configurações (petshop, serviços, colaboradores, horários, fidelidade, integração):
  `ConfiguracaoController` + `resources/views/configuracoes/*`.
- Seeds demo: `database/seeders/DatabaseSeeder.php`.

## Notas de regra de negócio
- **Slug do petshop é estável**: renomear o petshop NÃO muda o slug (senão quebraria o link público
  e os QR Codes). O slug só muda se o usuário editar o campo "Link público" em Configurações → Petshop.
- **Petshop do usuário**: use `Auth::user()->currentPetshop()` / `currentPetshopId()` — resolve tanto
  admin (dono) quanto colaborador (vínculo em `colaboradores`). Não use `->petshop` direto.
- Documento de venda/posicionamento (Mercado Livre): `docs/VENDA-MERCADO-LIVRE.md`.

## Regras ao editar
- Após qualquer mudança em Blade que use classes Tailwind novas, rode `npm run build`.
- Nunca crie `<input>`/`<label>` sem variante `dark:` — prefira as classes `.form-*`.
- Mantenha controllers finos; coloque regra de negócio em Services.
- Páginas autenticadas usam `<x-app-layout>`; públicas montam o HTML próprio.
