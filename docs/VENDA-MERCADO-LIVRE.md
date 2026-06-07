# PetAgenda — Guia de Venda no Mercado Livre (Premium)

Documento estratégico para vender o PetAgenda como **sistema premium de código-fonte
(licença vitalícia, sem mensalidade)** no Mercado Livre. Baseado em pesquisa dos
sistemas mais vendidos do nicho (jun/2026).

---

## 1. Posicionamento — o ângulo que vende

O nicho se divide em dois grupos:

| Modelo | Quem | Preço típico | Dor do cliente |
|---|---|---|---|
| **SaaS (mensalidade)** | SimplesVet, Vetus, Fourpet, Appet.tosa, 4Pets, VetSoft | **R$ 39,90 a R$ 229,90/mês** + implantação (~R$ 299) | Paga para sempre; refém da plataforma |
| **Código-fonte / vitalício** (Mercado Livre) | Anúncios "Código Fonte Sistema Pet", "Software Pet Shop sem mensalidade" | **Pagamento único** (uso vitalício, licença "99 anos", com CNPJ do cliente) | Sistemas antigos (VB/Delphi), feios, offline |

**Nossa proposta de valor (a frase-âncora do anúncio):**

> "Pare de pagar R$ 150–230 **todo mês**. Tenha o sistema completo do seu petshop com
> **pagamento único**, código-fonte 100% seu, visual moderno (dark mode), agenda
> inteligente e WhatsApp — instale em **domínios ilimitados**."

O PetAgenda ganha porque é **código-fonte vitalício** (como os do ML) **com cara de SaaS
moderno** (como os caros). É o melhor dos dois mundos.

---

## 2. Como titular o anúncio

O Mercado Livre dá ~60 caracteres de peso de busca no título. Os títulos campeões do
nicho seguem o padrão **[Tipo] + [Segmento] + [Diferencial forte] + [Versão]**.
Exemplos reais observados: *"Sistema Petshop E Clinica Veterinária Com App E Atendimento V1.0"*,
*"Código Fonte Sistema Pet"*, *"Software Para Pet Shop"*.

**Fórmula recomendada:**
`Sistema Petshop Completo + Agenda + WhatsApp + Código Fonte Laravel`

**5 títulos prontos para testar (A/B):**
1. `Sistema Pet Shop Completo Banho Tosa Agenda Whatsapp + Código Fonte`
2. `Sistema Petshop e Clínica Veterinária Agendamento Online Sem Mensalidade`
3. `Software Pet Shop Premium Laravel Agenda Financeiro Whatsapp Vitalício`
4. `Sistema Gestão Pet Shop Banho e Tosa Agendamento Online + App Web`
5. `Sistema Pet Shop Profissional Código Fonte Multi-loja Sem Mensalidade`

**Palavras-chave que o nicho mais busca** (use no título/descrição/atributos):
`pet shop`, `petshop`, `banho e tosa`, `clínica veterinária`, `agendamento online`,
`whatsapp`, `sem mensalidade`, `código fonte`, `vitalício`, `gestão`, `agenda`,
`comanda/ordem de serviço`, `financeiro`, `estoque`, `PDV`.

---

## 3. O que os concorrentes oferecem (matriz de features)

Legenda: ✅ já temos · 🟡 parcial · ❌ falta (oportunidade)

| Funcionalidade | Concorrentes que têm | PetAgenda hoje |
|---|---|---|
| Agenda visual por profissional | todos | ✅ (grade por colaborador) |
| Agendamento online pelo tutor (link público) | Appet, 4Pets, Fourpet | ✅ (link público white-label) |
| **Agendamento recorrente** (banho semanal/quinzenal) | SimplesVet, 4Pets | ✅ **(implementado agora)** |
| Lembretes/confirmação por WhatsApp | todos | ✅ (jobs com retry) |
| Ficha do pet (vacinas, peso, alergias, foto) | todos | ✅ |
| Fotos antes/depois do banho | SimplesVet (checklist) | 🟡 (campos existem, falta UI) |
| Financeiro (receitas/despesas/lucro) | todos | ✅ |
| Comissão por colaborador | Vetus, Fourpet | ✅ |
| Programa de fidelidade | 4Pets ("medalhas QR") | ✅ (config por atendimentos) |
| Dashboard com gráficos | todos | ✅ (Chart.js) |
| Multi-loja / multi-tenant | Vetus, Fourpet (rede) | ✅ (escala de petshop a rede) |
| **PDV + venda de produtos + estoque** | TODOS do ML, Fourpet, Nex | ❌ **(maior gap)** |
| **Emissão de NF-e / NFC-e** | Fourpet, Vetus, sistemas ML | ❌ |
| **Pesquisa de satisfação (NPS) pós-atendimento** | SimplesVet | 🟡 (job citado, falta tela) |
| **Hotel / creche / day-care** | Fourpet, 4Pets | ❌ |
| Pagamento online no agendamento (PIX) | 4Pets, Simples Agenda | ❌ |
| App nativo / portal do tutor | 4Pets, Appet | 🟡 (link público; falta portal logado) |
| Recorrência de cobrança (assinatura do tutor) | Vindi/4Pets | ❌ |

---

## 4. Roadmap para "premium" (priorizado por impacto × esforço)

Para justificar o **ticket premium** e bater os concorrentes, esta é a ordem recomendada:

### Já entregue nesta versão
- ✅ **Agendamento recorrente** (semanal/quinzenal/mensal) — paridade com SimplesVet/4Pets.
- ✅ Coluna **"A definir"** + atribuição de colaborador e mudança de status no card (kanban da regra de negócio).
- ✅ Gestão completa de **colaboradores** (criar/editar/excluir + usuário de login).
- ✅ Dark mode consistente, toasts, UX de agendamento sem travar.

### Alto impacto / esforço médio (faça primeiro)
1. **Módulo PDV + Produtos + Estoque** — é o item nº 1 que todo sistema de ML vende.
   Tabelas `produtos`, `estoque_movimentos`, `vendas`, `venda_itens`; tela de venda
   balcão integrada ao Financeiro e à comanda do agendamento. **Sem isso, perde para
   os básicos do ML.**
2. **Fotos antes/depois + checklist de entrada/saída** no atendimento (campos já existem
   em `agendamentos.photo_before/after`). Diferencial visual forte para fotos no anúncio.
3. **NPS / avaliação pós-atendimento** com link público (1–5 ⭐) + média no dashboard.

### Diferenciação (médio prazo)
4. **Hotel / creche** (check-in/check-out, diárias, mapa de baias).
5. **Pagamento online no agendamento (PIX)** via gateway (Mercado Pago/Asaas).
6. **Portal do tutor** (login do cliente: histórico, próximos agendamentos, carteirinha).
7. **NF-e/NFC-e** (integração com emissor — Focus NFe/PlugNotas).

---

## 5. Diferencial inovador (o que quase ninguém tem)

Para o anúncio dizer "vai além", estas ideias são viáveis sobre a base atual:

1. **Carteirinha digital do pet com QR Code** — página pública por pet (vacinas, alergias,
   contato de emergência). O tutor salva no celular; recepção escaneia o QR no check-in.
   (4Pets tem "medalhas QR" só para fidelidade — a nossa seria a ficha clínica completa.)
2. **Agenda recorrente inteligente** (já implementada) + sugestão automática do próximo
   banho com base no `retorno_dias` do pet → reduz no-show e fideliza.
3. **Link público white-label por loja** (cor + logo do petshop) — já temos; vender como
   "mini-site de agendamento" incluso, sem custo de plataforma.
4. **Briefing do dia por WhatsApp** para o dono (resumo: nº de banhos, faturamento previsto,
   pets com retorno atrasado) — usa a infra de jobs já existente.

> Escolha **1 diferencial** como "headline de inovação" do anúncio. Recomendado:
> **Carteirinha digital do pet com QR Code** (alto apelo visual, fácil de demonstrar).

---

## 6. Estratégia de preço (ticket razoável)

O cliente compara com **R$ 150–230/mês de SaaS** (≈ R$ 1.800–2.760/ano). Logo, um
pagamento único é fácil de justificar.

| Pacote | O que inclui | Faixa sugerida |
|---|---|---|
| **Essencial** | Código-fonte + instalação em 1 domínio + 30 dias de suporte | R$ 297 – R$ 497 |
| **Profissional** (recomendado) | + personalização de marca + WhatsApp configurado + 90 dias suporte | R$ 697 – R$ 997 |
| **Revenda / Agência** | Licença para revender, domínios ilimitados, atualizações | R$ 1.497 – R$ 2.497 |

Gatilhos de conversão no anúncio: **"sem mensalidade"**, **"código-fonte é seu"**,
**"instalo para você"**, **"domínios ilimitados"**, **garantia de 7 dias**.
Upsell pós-venda: instalação, NF-e, customizações, hospedagem gerenciada.

---

## 7. Checklist de prontidão para vender

- [ ] Definir título (seção 2) e cadastrar atributos/keywords no ML.
- [ ] 6–8 prints em **alta** (landing, dashboard, agenda dark+light, link público no celular,
      ficha do pet, financeiro). O ML prioriza anúncios com muitas fotos boas.
- [ ] Vídeo de 60–90s mostrando: agendar → confirmar no WhatsApp → financeiro.
- [ ] Demo online no ar + credenciais (`admin@demo.com` / `password`) e link público.
- [ ] Documento de instalação (já há `README.md` + `docker-compose.yml` + `Makefile`).
- [ ] Termo de licença (uso/revenda) e política de suporte.
- [ ] Página de FAQ: "funciona offline?", "tem app?", "emite nota?", "quantas lojas?".

---

## 8. Descrição de anúncio (copy pronta — ajuste e cole)

```
🐶 SISTEMA COMPLETO PARA PET SHOP, BANHO & TOSA E CLÍNICA VETERINÁRIA
Pagamento ÚNICO • SEM mensalidade • Código-fonte 100% seu

✅ Agenda visual por profissional (arrastar, status colorido)
✅ Agendamento ONLINE pelo tutor (link com a cor e o logo da sua loja)
✅ Agendamento RECORRENTE (banho semanal/quinzenal automático)
✅ Lembretes e confirmação por WhatsApp
✅ Ficha completa do pet: vacinas, peso, alergias, foto
✅ Financeiro: receitas, despesas, lucro e comissões
✅ Programa de fidelidade + Dashboard com gráficos
✅ Multi-loja (de 1 unidade a redes) + Dark mode

🚀 Tecnologia atual (Laravel 12) — rápido, seguro e bonito.
💰 Economize milhares por ano: SaaS cobra R$ 150–230 TODO MÊS.
🔒 Você recebe o código-fonte e instala em domínios ILIMITADOS.

🎁 Instalação assistida + 90 dias de suporte (plano Profissional).
```

---

## Fontes (pesquisa de mercado, jun/2026)
- [Vetus — sistema para banho e tosa](https://vetus.com.br/universidade/sistema-para-banho-e-tosa-a-solucao-completa-para-o-seu-estabelecimento/)
- [SimplesVet — petshop, banho e tosa](https://simples.vet/petshop-banho-e-tosa/) · [Planos](https://simples.vet/precos/)
- [Fourpet — sistema para pet shop](https://www.fourpet.com.br/) · [Planos/implantação](https://www.fourpet.com.br/pet-shop/)
- [Appet.tosa](https://appettosa.com.br/)
- [4Pets.app — gestão](https://www.4pets.app/sistema-de-gestao)
- [Mercado Livre — Código Fonte Sistema Pet](https://lista.mercadolivre.com.br/codigo-fonte-sistema-pet) · [Software para Pet Shop](https://lista.mercadolivre.com.br/software-para-pet-shop)
- [Sistema Petshop e Clínica (anúncio ML)](https://www.mercadolivre.com.br/sistema-petshop-e-clinica-veterinaria-com-e-atendimento-v10/up/MLBU1971120174)
- [Vindi — recorrência para pet shop](https://blog.vindi.com.br/recorrencia-para-pet-shop/)
- [Simples Agenda — a partir de R$ 39,90/mês](https://www.simplesagenda.com.br/site/sistema-para-petshop)
