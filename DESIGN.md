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
