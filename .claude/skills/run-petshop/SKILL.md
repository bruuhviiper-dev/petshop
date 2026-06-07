---
name: run-petshop
description: Subir, semear e buildar o PetAgenda (Laravel 12) localmente — banco MySQL Laragon, dados demo, credenciais de login e o build do Vite. Use quando pedirem para rodar o app, recriar o banco, ver o sistema funcionando ou testar o link público de agendamento.
---

# Rodar o PetAgenda localmente

Projeto Laravel 12 + Vite/Tailwind + Alpine. Banco MySQL do Laragon (`root`/`root`, base `petshop`, host `127.0.0.1`).

## Setup do zero
```bash
composer install
npm install            # Node v18 emite warning de engine, mas funciona
cp .env.example .env   # se ainda não existir
php artisan key:generate
php artisan migrate:fresh --seed --force   # cria 3 petshops + dados demo
npm run build          # gera public/build/manifest.json (sem ele: ViteManifestNotFoundException)
php artisan serve      # http://127.0.0.1:8000
```

## Sempre que mudar CSS/JS/Blade com classes novas
```bash
npm run build
```
Durante o desenvolvimento ativo, `npm run dev` dá HMR.

## Login demo
- Admin: `admin@demo.com` / `password` (petshop "Pet & Tosa da Ana")
- Outros: `carlos@petshop2.com`, `renata@petshop3.com` (mesma senha)

## Link público de agendamento
`/agendar/{slug}` — o slug do demo é resolvido dinamicamente na home. Para descobrir os slugs ativos:
```bash
php artisan tinker --execute="App\Models\Petshop::all(['slug','active'])->each(fn(\$p)=>print(\$p->slug.PHP_EOL));"
```

## Verificar JS/Alpine em browser headless
```bash
chrome --headless=new --dump-dom --virtual-time-budget=4000 http://127.0.0.1:8000/agenda
```
(curl não revela erros de Alpine; precisa de browser.)

## Testes e formatação
```bash
php artisan test
./vendor/bin/pint
```
