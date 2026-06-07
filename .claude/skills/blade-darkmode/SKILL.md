---
name: blade-darkmode
description: Criar formulários e telas Blade do PetAgenda com dark mode correto e cor da marca. Use ao adicionar/editar inputs, labels, cards, botões ou qualquer view autenticada — evita o bug de "labels cinzas que somem" no tema escuro.
---

# Dark mode e marca no PetAgenda

O dark mode é controlado pela classe `dark` no `<html>` (persistida em `localStorage.darkMode`).
**Qualquer cor sem variante `dark:` some no tema escuro.** Este é o erro mais comum no projeto.

## Use as classes utilitárias (resources/css/app.css)
Em vez de escrever Tailwind cru em cada input, use:

| Classe | Para |
|---|---|
| `.form-label` | `<label>` de campo |
| `.form-input` | `<input>` texto/number/date/datetime-local |
| `.form-select` | `<select>` |
| `.form-textarea` | `<textarea>` |
| `.form-hint` | texto de ajuda discreto |
| `.form-error` | mensagem de erro |

Exemplo:
```blade
<div>
    <label class="form-label">Nome *</label>
    <input type="text" name="name" class="form-input" required>
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>
```

## Regras
- Texto: sempre par claro/escuro, ex. `text-gray-800 dark:text-gray-100`,
  `text-gray-500 dark:text-gray-400`.
- Fundos de card/linha: `bg-gray-50 dark:bg-gray-700/40`. Bordas: `border-gray-100 dark:border-gray-700`.
- Botão secundário: `bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600`.
- Componentes prontos já são dark-aware: `<x-card>`, `<x-modal>`, `<x-flash>`,
  `<x-empty-state>`, `<x-input-label>`, `<x-text-input>`, `<x-confirm-delete>`.

## Cor da marca (cor do petshop)
Use `.bg-brand`, `.text-brand`, `.border-brand`, `.bg-brand-soft`, `.btn-brand` — derivam de
`--color-brand` (definida no layout a partir de `$currentPetshop->primary_color`). **Não** use
violeta fixo no app; o violeta (`.gradient-brand`) é só da landing/auth.

## Gotcha CSP × Alpine
O CSP exige `'unsafe-eval'` em `script-src` (já configurado em `ContentSecurityPolicy`).
Não remover — sem isso `x-data`/`@click`/`:class` param de funcionar silenciosamente.

## Depois de editar
Rode `npm run build` (classes novas precisam entrar no bundle do Tailwind).
