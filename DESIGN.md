---
name: Rota Segura
description: Sistema de apoio ao transporte escolar particular — conecta responsáveis e motoristas de van
colors:
  primary-blue: "#2563eb"
  primary-blue-deep: "#1d4ed8"
  primary-amber: "#f59e0b"
  primary-amber-deep: "#d97706"
  ink: "#0f172a"
  body-text: "#475569"
  neutral-bg: "#f8fafc"
  neutral-border: "#e2e8f0"
  surface: "#ffffff"
  blue-tint-bg: "#eff6ff"
  blue-tint-border: "#bfdbfe"
  amber-tint-bg: "#fffbeb"
  amber-tint-border: "#fde68a"
  success: "#16a34a"
  danger: "#dc2626"
typography:
  display:
    fontFamily: "Sora, sans-serif"
    fontSize: "clamp(1.5rem, 4vw, 2.4rem)"
    fontWeight: 800
    lineHeight: 1.15
    letterSpacing: "normal"
  brand-display:
    fontFamily: "Plus Jakarta Sans, sans-serif"
    fontSize: "clamp(1.75rem, 5vw, 3rem)"
    fontWeight: 900
    lineHeight: 1.15
    letterSpacing: "normal"
  body:
    fontFamily: "Sora, sans-serif"
    fontSize: "0.9rem"
    fontWeight: 400
    lineHeight: 1.7
  label:
    fontFamily: "Sora, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    letterSpacing: "0.05em"
  micro:
    fontFamily: "Sora, sans-serif"
    fontSize: "10px"
    fontWeight: 600
    letterSpacing: "0.02em"
rounded:
  sm: "8px"
  md: "12px"
  lg: "16px"
  full: "9999px"
spacing:
  sm: "8px"
  md: "16px"
  lg: "24px"
components:
  button-primary:
    backgroundColor: "{colors.primary-blue}"
    textColor: "#ffffff"
    rounded: "{rounded.sm}"
    padding: "10px 20px"
  button-primary-hover:
    backgroundColor: "{colors.primary-blue-deep}"
  button-primary-motorista:
    backgroundColor: "{colors.primary-amber}"
    textColor: "#ffffff"
    rounded: "{rounded.sm}"
    padding: "10px 20px"
  button-primary-motorista-hover:
    backgroundColor: "{colors.primary-amber-deep}"
  card-hero:
    backgroundColor: "{colors.primary-blue}"
    textColor: "#ffffff"
    rounded: "{rounded.lg}"
    padding: "24px"
  card-surface:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.lg}"
    padding: "20px"
  badge-status:
    backgroundColor: "{colors.amber-tint-bg}"
    textColor: "{colors.primary-amber-deep}"
    rounded: "{rounded.full}"
    padding: "4px 12px"
---

# Design System: Rota Segura — "O Painel de Confiança"

## 1. Overview

**Creative North Star: "O Painel de Confiança"**

Rota Segura é onde um pai ou mãe decide se pode confiar o filho a um motorista que nunca viu pessoalmente. Todo o sistema existe pra tornar essa decisão mais tranquila: cartões sólidos com cantos generosamente arredondados, gradientes suaves de uma única cor de marca por portal, e uma paleta que nunca é neutra-fria nem lúdica-infantil. É um painel — não uma vitrine de marketing, não um brinquedo — onde cada portal (Responsável em azul, Motorista em âmbar, Admin em azul/slate) fala a mesma língua visual através de componentes compartilhados: o card hero com gradiente e estatísticas, o chip de ícone abrindo cada seção de formulário, o estado vazio com borda tracejada e call-to-action.

Este sistema rejeita explicitamente dois extremos: o visual **frio e corporativo** de um app bancário ou ERP genérico (sem calor humano, tudo cinza e retas), e o visual **infantilizado** que trataria crianças como o público visual em vez do responsável adulto que toma a decisão. A confiança se constrói por clareza e consistência, não por decoração.

Hoje o sistema está fragmentado entre quatro implementações independentes (Breeze padrão, CSS artesanal do funil público, utility classes puras no cadastro, três temas de portal sem componentes compartilhados) — este documento descreve o sistema **de referência único** que consolida o que já existe, não uma nova identidade.

**O funil público é um registro à parte, por decisão, não por drift.** `Welcome.vue`, `Login.vue` e `ForgotPassword.vue` usam CSS artesanal próprio (classes `rs-*`/`lg-*`) com valores de cor, raio e gradiente que divergem da paleta de produto acima — isso é esperado: essas três telas são a "vitrine" (registro *brand*, onde o design É o produto), enquanto o resto do sistema é *product* (o design serve o produto). Os tokens deste frontmatter documentam o lado *product* (dashboards dos 3 portais); o funil público mantém seu próprio vocabulário visual até que uma unificação deliberada seja decidida (`/impeccable shape` + `/impeccable adapt`, não uma substituição mecânica de valores).

**Key Characteristics:**
- Um gradiente de cor sólida por contexto (azul para Responsável/Admin, âmbar para Motorista) — nunca gradiente multi-hue ou arco-íris.
- Cantos generosamente arredondados (16px em cards principais, 12px em blocos internos) — nunca esquadrejado.
- Sombra reservada para o que realmente precisa se destacar (hero cards, modais), não espalhada uniformemente.
- Um padrão de estado vazio (borda tracejada + ícone + CTA) repetido fielmente — é o melhor exemplo do sistema funcionando.

## 2. Colors

A paleta é dupla por design: **azul** carrega o portal do Responsável e o Admin (a voz institucional/organizadora), **âmbar** carrega o portal do Motorista (a voz operacional/no-terreno). As duas nunca se misturam dentro de um mesmo portal.

### Primary
- **Azul Confiança** (`#2563eb`): cor primária do Responsável e Admin — CTAs, links, cards hero com gradiente, ícones de destaque. Hover em `#1d4ed8`.
- **Âmbar Operacional** (`#f59e0b`): cor primária do Motorista — mesmo papel que o azul, mas no portal do motorista. Hover em `#d97706`.

### Neutral
- **Tinta** (`#0f172a`): texto de títulos e alto contraste.
- **Corpo** (`#475569`): texto de parágrafo e legendas — nunca usar tons mais claros que este para texto de leitura corrida sobre fundo branco ou tintado.
- **Fundo de Página** (`#f8fafc`): fundo padrão de todas as telas autenticadas.
- **Borda Neutra** (`#e2e8f0`): divisores e bordas de card em repouso.
- **Superfície** (`#ffffff`): fundo de cards e painéis.

### Tints (fundos tingidos, usados para estado/contexto)
- **Azul Claro** (`#eff6ff` fundo / `#bfdbfe` borda): destaque informativo leve, badges "verificado".
- **Âmbar Claro** (`#fffbeb` fundo / `#fde68a` borda): estados pendentes/de atenção.

### Semantic
- **Sucesso** (`#16a34a`): confirmações, status ativo/aprovado.
- **Perigo** (`#dc2626`): erros, ações destrutivas.

### Named Rules
**A Regra da Cor Única por Portal.** Cada portal usa exatamente uma cor de marca (azul ou âmbar) como protagonista. Misturar as duas dentro do mesmo portal quebra a orientação visual de "em qual perfil eu estou".

**A Regra do Texto Nunca Claro Demais.** Texto de corpo (`#475569`) ou mais escuro sobre qualquer fundo claro ou tintado — nunca um slate mais claro que isso para texto legível. Estados de `hover:` que tingem o fundo (ex: `hover:bg-blue-50`) devem sempre recolorir o texto junto (ex: `hover:text-blue-800`), nunca deixar o texto no tom de repouso sobre o novo fundo.

## 3. Typography

**Display Font:** Sora (com fallback `sans-serif`) — telas autenticadas (dashboards, formulários) nos três portais.
**Brand Display Font:** Plus Jakarta Sans (com fallback `sans-serif`) — reservada à Welcome.vue e ao funil de autenticação (Login/ForgotPassword), o "rosto público" do produto.
**Label/Secondary Font:** Nunito — usada pontualmente na wordmark do logo.

**Character:** Sora é geométrica, arredondada nos detalhes e confiante sem ser fria — combina com a linguagem de cards sólidos do produto. Plus Jakarta Sans no funil público empresta um tom ligeiramente mais caloroso e humano para o primeiro contato do visitante, antes de entrar na "voz de produto" do Sora.

### Hierarchy
- **Display** (800, `clamp(1.5rem, 4vw, 2.4rem)`, 1.15): títulos de seção dentro do produto (dashboards, formulários).
- **Brand Display** (900, `clamp(1.75rem, 5vw, 3rem)`, 1.15): headline da Welcome.vue e telas de autenticação.
- **Título de Card** (700, 1.05–1.25rem): cabeçalho de card/seção de formulário.
- **Body** (400, 0.9rem, 1.7): texto corrido — manter ≤75ch de largura de linha.
- **Label** (600, 0.75rem, tracking 0.05em, uppercase pontual): rótulos de campo e eyebrows — usar com moderação, não em toda seção.
- **Micro** (600, 10px, tracking 0.02em): badges minúsculos, chips de dia, legendas de sidebar/bottom nav — o menor step da hierarquia, usar só onde `label` (12px) não cabe fisicamente.

### Named Rules
**A Regra da Fonte Única de Produto.** Toda tela autenticada (Motorista, Responsável, Admin) carrega Sora a partir de um único ponto central (layout raiz — `resources/views/app.blade.php`), nunca via `<link>` por página nem `style="font-family"` inline.

**A Regra da Fronteira Pública/Produto.** Plus Jakarta Sans fica restrita ao que um visitante não-autenticado vê (Welcome, Login, ForgotPassword). Ao entrar no dashboard, a voz muda para Sora — essa é a única fronteira de fonte permitida no sistema.

## 4. Elevation

Elevação é reservada, não uniforme: a maior parte dos cards de conteúdo (listas, blocos internos, inputs) fica praticamente plana (`shadow-sm` no máximo, muitas vezes nenhuma sombra, apoiada só em `border border-slate-200`). Sombra visível é reservada para o que precisa competir pela atenção — o card hero de topo de cada dashboard e modais/diálogos flutuantes.

### Shadow Vocabulary
- **Ambient (`shadow-sm`, `0 1px 2px rgba(15,23,42,0.05)`)**: cards de lista e blocos internos em repouso.
- **Destaque (`shadow-lg`, `0 10px 25px rgba(15,23,42,0.1)`)**: card hero no topo do dashboard, elementos de CTA primário.
- **Flutuante (`0 20px 60px rgba(15,23,42,0.25)` ou similar)**: modais e diálogos sobre backdrop.

### Named Rules
**A Regra da Sombra que Importa.** Se todo card tem a mesma sombra, nenhum se destaca. Sombra forte é reservada a no máximo 1–2 elementos por tela: o hero e, quando aberto, o modal.

## 5. Components

### Buttons
- **Shape:** cantos de 8px (`rounded-lg` no sentido Tailwind, chamado `sm` neste sistema).
- **Primary (Responsável/Admin):** fundo `#2563eb`, texto branco, padding `10px 20px`, hover `#1d4ed8` com sombra azul suave (`rgba(37,99,235,0.35)`).
- **Primary (Motorista):** mesmo formato, fundo `#f59e0b` → hover `#d97706`.
- **Secondary/Ghost:** fundo transparente ou branco translúcido, borda 1–2px na cor da marca do portal.
- **Estado de carregamento:** label muda para um verbo no gerúndio ("Salvando…") e o botão desabilita — padrão já bem estabelecido, manter.

### Cards / Containers
- **Hero card (topo de dashboard):** `rounded-2xl` (16px), gradiente sólido da cor do portal (`from-{cor}-500 to-{cor}-600`), texto branco, `shadow-lg`.
- **Card de conteúdo:** `rounded-2xl` (16px) ou `rounded-xl` (12px) para blocos menores, fundo branco, `border border-slate-200`, sombra mínima ou ausente.
- **Chip de ícone (cabeçalho de formulário):** quadrado `32×32px`, `rounded-xl`, fundo tintado na cor do contexto (ex: `bg-amber-100`), ícone Heroicons no centro — abre quase toda seção de formulário do produto.

### Badges / Status Pills
- **Shape:** `rounded-full`, padding `4px 12px`, texto `0.75rem` semibold.
- **Estados:** cor de fundo/texto tintada semanticamente (âmbar = pendente, azul = informativo, verde = aprovado/ativo, vermelho = rejeitado/erro) — nunca cor arbitrária desconectada do significado.

### Empty States
- **Estilo:** borda tracejada 2px na cor do contexto, ícone grande centralizado (tom claro da cor), uma frase em negrito, uma linha de apoio em cinza, um CTA — este é o padrão mais bem executado do sistema hoje; qualquer novo estado vazio deve segui-lo exatamente, sem reinventar.

### Inputs / Fields
- **Style:** borda `slate-200` em repouso, fundo branco, `rounded-lg`.
- **Focus:** borda muda para a cor da marca do portal.
- **Error:** borda e texto de apoio em vermelho (`#dc2626`), mensagem específica abaixo do campo.

### Modals
- **Style:** Teleport para o body, backdrop escuro semi-transparente, painel branco `rounded-2xl` com sombra flutuante forte.
- **Uso obrigatório para ações destrutivas** (cancelar solicitação, encerrar vínculo, marcar falta) — nunca `confirm()` nativo do navegador, que quebra a consistência visual e de acessibilidade do resto do sistema.

### Navigation
- **Sidebar (desktop) / BottomNav (mobile):** ícone + label sempre juntos (nunca ícone sozinho), item ativo destacado com fundo tintado na cor do portal — não usar borda lateral colorida (`border-l-4`) como indicador de ativo.

## 6. Do's and Don'ts

### Do:
- **Do** manter uma cor de marca sólida por portal (azul para Responsável/Admin, âmbar para Motorista) — nunca misturar as duas no mesmo contexto.
- **Do** carregar Sora uma única vez, centralizado no layout raiz — não via `<link>` por página nem `style="font-family"` inline.
- **Do** reservar sombra forte (`shadow-lg`+) para o hero card e modais; manter o resto quase plano.
- **Do** usar o padrão de estado vazio (borda tracejada + ícone + frase + CTA) para qualquer nova tela sem conteúdo.
- **Do** recolorir o texto junto com o fundo em todo estado de `hover:` tintado (ex: `hover:bg-blue-50 hover:text-blue-800` juntos, nunca só o primeiro).
- **Do** usar o modal padrão (Teleport, backdrop, painel `rounded-2xl`) para toda ação destrutiva ou de confirmação.

### Don't:
- **Don't** introduzir uma nova cor de marca, gradiente multi-hue ou paleta "arco-íris" — o sistema já tem sua identidade (azul/âmbar), não é hora de reinventar.
- **Don't** usar `border-left`/`border-right` colorido como indicador de item ativo em navegação (ex: o `border-l-4` herdado do Breeze em `ResponsiveNavLink.vue`) — usar fundo tintado.
- **Don't** usar `confirm()` nativo do navegador para ações destrutivas — sempre o modal do sistema.
- **Don't** deixar texto de corpo mais claro que `#475569` (slate-600) sobre fundo branco ou tintado — nem "para dar leveza".
- **Don't** deixar uma página nova sem herdar o layout raiz e a fonte Sora — todo formulário novo do motorista, responsável ou admin nasce dentro do mesmo shell.
- **Don't** usar cores lúdicas/saturadas fora da paleta azul/âmbar/slate definida aqui, nem ícones cartunescos — o produto é sério mesmo lidando com crianças.
- **Don't** usar curvas de easing com "bounce"/overshoot (`cubic-bezier(0.34, 1.56, 0.64, 1)`) — preferir saída exponencial suave (`ease-out-quart`/`quint`) nas transições que ainda não foram revisadas.
