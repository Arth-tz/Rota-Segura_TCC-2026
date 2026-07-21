---
target: todo o front-end (resources/js, 58 arquivos)
total_score: 22
p0_count: 2
p1_count: 3
timestamp: 2026-07-21T02-27-39Z
slug: resources-js-todo-o-frontend
---
Method: dual-agent (A: a0778deee99c41568 · B: a6cc06a228c6b9352)

**Coverage note**: no dev server was reachable on :5173, :8000, or the Laragon virtual host, so this critique is 100% source-code-based — no browser rendering, no live contrast measurement, no visual overlay. Both assessments flag this explicitly; contrast-ratio estimates below are computed from Tailwind's published hex values, not a real browser.

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | Solid overall (`form.processing` states, `FlashMessage.vue` toasts) but `Admin/Dashboard.vue` approve/reject actions fire with no loading/disabled state |
| 2 | Match System / Real World | 3 | Correct pt-BR + CPF/phone/CEP masks throughout, undercut by fully untranslated English `ResetPassword.vue`/`ConfirmPassword.vue`/`VerifyEmail.vue` |
| 3 | User Control and Freedom | 2 | Native `confirm()` used for consequential actions (cancel request, end vínculo) alongside custom modals for equivalent actions — no undo window |
| 4 | Consistency and Standards | 1 | Four unreconciled visual systems live at once (see Anti-Patterns Verdict) — the standout failure |
| 5 | Error Prevention | 3 | Strong input masking, van placa locked post-creation; docked for the confirm()/modal split |
| 6 | Recognition Rather Than Recall | 3 | Edit forms pre-fill correctly, status badges keep context visible |
| 7 | Flexibility and Efficiency | 2 | No keyboard shortcuts, no bulk actions (Admin approves one motorista/van at a time), no saved searches |
| 8 | Aesthetic and Minimalist Design | 2 | Individual cards are clean, but decoration (3 blur blobs + dot-grid + pulsing badge on the landing hero alone) outweighs information density |
| 9 | Error Recovery | 2 | Inline field errors are good; `SecaoTrajetos.vue`'s GPS-send catch block is empty — silent failure on the app's core safety feature |
| 10 | Help and Documentation | 1 | No help center, no onboarding walkthrough, no FAQ |
| **Total** | | **22/40** | **Acceptable — significant improvements needed before users are happy** |

## Anti-Patterns Verdict

**Yes — this reads as AI-generated, and the tells compound.**

**LLM assessment**: The strongest evidence isn't any single component but the *repetition*: the same gradient-hero-card composition (radial overlay, greeting eyebrow, big stat numbers, CTA row) is recolored and reused across at least 8 sections — `SecaoInicio.vue` (both portals), `SecaoSolicitacoes.vue`, `SecaoTrajetos.vue`, `SecaoPassageiros.vue`, `SecaoAcompanhar.vue`, `Marketplace.vue`, `Admin/Dashboard.vue`. Same for the icon-chip-eyebrow-title card header (~15+ copies across every form), the numbered step wizard (`Welcome.vue`, `Passageiro/create.vue`, `enderecos.vue`), and the blob/dot-grid decorative background (`Welcome.vue`, `Login.vue`, `ForgotPassword.vue`, byte-for-byte similar CSS). A distinctive tell on top of the visual repetition: verbatim-style ASCII section-divider comments (`<!-- ── SEÇÃO 1: ... ── -->`) recur across unrelated files, suggesting the frontend was scaffolded file-by-file via prompting rather than designed once as a system — which is also the root cause of the consistency failures below. For a product about trusting strangers with children, the visual language never breaks from generic-SaaS-dashboard blue/amber into anything that reads as warm, local, or safety-specific.

**Deterministic scan**: `detect.mjs` found 52 hits across 7 antipattern classes in `resources/js` (gray-on-color: 13, bounce-easing: 14, overused-font: 11, single-font: 6, flat-type-hierarchy: 5, dark-glow: 2, side-tab: 1). Verification against source narrowed this:
- **bounce-easing (14) — fully confirmed.** The identical `cubic-bezier(0.34, 1.56, 0.64, 1)` overshoot curve is copy-pasted across ≥8 files (`SecaoPerfil.vue`, `Admin/Dashboard.vue`, `Welcome.vue`, `Login.vue`, `ForgotPassword.vue`, `Marketplace.vue`) for both modal-pop transitions and button hovers — one un-componentized animation primitive spread everywhere.
- **dark-glow (2) — real but mislabeled.** Both hits are a light page (`#f8fafc`) with one deliberately dark decorative aside panel, and the glow color matches that panel's own gradient — not a mismatched neon-on-dark-mode artifact, more a legitimate (if generic) design choice worth noting rather than a hard violation.
- **side-tab (1) — real, but it's untouched Breeze scaffolding** (`ResponsiveNavLink.vue`, indigo/gray colors that don't match the app's brand anywhere else) — likely dead code the real `Sidebar.vue`/`BottomNav.vue` components have superseded.
- **gray-on-color (13) — the detector over-fired here.** At least 5 of 13 are false positives: the scanner cross-matched Tailwind classes from mutually-exclusive ternary branches or from a `hover:` pair that already recolors the text (e.g. `SecaoTrajetos.vue:271`'s flagged "text-slate-400 on bg-amber-500" never actually renders — the real pairs are amber-500/white and slate-100/slate-400). The remaining ~8 (`enderecos.vue`, `Disponibilidade/Criar.vue`+`Editar.vue`) are real instances of a *different, smaller* bug: hover states that tint the background but forget to recolor the text — computed contrast stays well above WCAG AA in every case checked (~8–10:1), so it's a polish nit, not an accessibility failure. Notably, the sibling component `EnderecoSection.vue` implements the identical hover pattern *correctly*, evidence of copy-drift between near-duplicate components rather than a systemic contrast problem.
- **overused-font / single-font / flat-type-hierarchy (22 combined) — confirmed, and worse than the raw scan suggests.** The app's configured Tailwind default font (Figtree) is dead — never loaded, zero matches anywhere. In its place three unrelated font choices compete: Plus Jakarta Sans (Welcome/Login/ForgotPassword, via a blanket `* {}` selector), Sora (the 8 authenticated dashboard/marketplace/passageiro shells, correctly loaded per-page), and Sora-referenced-but-never-loaded (6 Motorista driver sub-forms — `Disponibilidade/Criar.vue`, `Editar.vue`, `Van/Criar.vue`, `Editar.vue`, both `Perfil/Editar.vue` — these silently fall back to plain system sans-serif because the inline `style="font-family:'Sora'"` was carried over from a clone but the Google Fonts `<link>` wasn't). This is a broken reference bug hiding inside what the detector read as a stylistic pattern.

**Visual overlays**: not available this run — no dev server reachable, so no browser injection was attempted and no user-visible overlay exists. The findings above come entirely from source reading, not a rendered page.

## Overall Impression

The app has real domain craft in places (the embark/disembark tracking flow, the consistently-executed empty-state pattern, correct Brazilian input masking) that prove the team can build a system when they commit to one. But the frontend as a whole was clearly built as four unreconciled sub-projects rather than one product: untouched Breeze scaffolding, hand-rolled CSS on the public/auth funnel, Tailwind-utility Register pages, and three independently-themed portals sharing zero components. That fragmentation is the single biggest opportunity — nearly every other issue found (broken font references, inconsistent confirm-dialog patterns, the untranslated auth pages) is a symptom of never having centralized a design system, not an isolated bug.

## What's Working

1. **`SecaoTrajetos.vue`'s embark/disembark flow** is the one place the app modeled the real physical process instead of doing generic CRUD: stop ordering locks once a route goes live, each stop shows real-time confirm buttons with captured timestamps, and empty states explain *why* rather than just saying "nothing here."
2. **The empty-state pattern** (dashed border + icon badge + bold line + CTA) is a genuine, faithfully-repeated system across at least 8 sections in both portals — proof the team can build and follow a shared pattern.
3. **Brazilian input masking** (CPF/phone/date/CEP) is correctly and consistently hand-rolled across every form that needs it, without regex bugs — real attention to the target audience's actual needs.

## Priority Issues

**[P0] Fragmented design system — four unreconciled visual languages coexist**
- **Why it matters**: Breeze-default Auth pages, an unused shared component library, hand-crafted `rs-*`/`lg-*` CSS on landing/login, Tailwind-utility Register pages, and three portal themes (amber/blue/slate) sharing zero components — every new contributor has to guess which pattern to copy, and the exact pages where first impressions form (auth, registration) look like different products. It's also the root cause behind the broken-Sora-reference bug and the confirm()/modal split below.
- **Fix**: Define brand tokens once in `tailwind.config.js` (colors, one font family — replacing every inline `style="font-family:..."`), build one shared component set (Button/Input/Card/Modal/EmptyState/StatusBadge — the app already has an empty-state pattern worth promoting to a real component), and migrate all three portals plus Auth onto it.
- **Suggested command**: `/impeccable shape` (define the system), then `/impeccable adapt` (roll it out)

**[P0] Marketing promises features that don't exist in the product**
- **Why it matters**: `Welcome.vue` headlines "Notificações instantâneas" and "Veja avaliações" (reviews) — neither has any implementation anywhere in the app (only 5-second polling + a session-scoped toast). For a trust-dependent child-safety marketplace, a worried parent discovering the marketing over-promised is a severe credibility hit at exactly the wrong moment.
- **Fix**: either scope the landing copy down to what's actually shipped, or prioritize real push notifications and a minimal ratings field before advertising them.
- **Suggested command**: `/impeccable audit` (content/feature parity check), then `/impeccable clarify`

**[P1] Broken font reference on 6 Motorista driver forms**
- **Why it matters**: `Disponibilidade/Criar.vue`, `Editar.vue`, `Van/Criar.vue`, `Editar.vue`, and both `Perfil/Editar.vue` pages apply `style="font-family:'Sora'"` to headings but never load the Sora font on those pages (no `<link>`, no shared layout) — headings silently render as plain system sans-serif, visibly inconsistent with the rest of the driver dashboard, most likely because these pages were cloned from `Dashboard.vue` without carrying over its font `<link>`.
- **Fix**: centralize font loading once in the root Blade layout instead of per-page `<link>` tags, and register the real font in `tailwind.config.js` so `font-sans` isn't silently dead (Figtree is configured but never loaded anywhere in the app today).
- **Suggested command**: `/impeccable typeset`

**[P1] Silent failure on safety-critical GPS transmission, plus a false-reassurance bug on the receiving end**
- **Why it matters**: `SecaoTrajetos.vue`'s `enviarPosicao()` has an empty catch block — if a driver's connection drops mid-route, nobody is told tracking stopped. It compounds with `SecaoAcompanhar.vue`, which updates the "última atualização" timestamp on every 5s poll regardless of whether new coordinates actually arrived — a parent can see a confidently fresh "updated just now" label while the van's position hasn't changed in 20 minutes. This is the exact feature parents rely on for peace of mind, failing invisibly on both ends.
- **Fix**: surface a "location not sending" state to the driver after repeated failures; on the parent side, only bump the timestamp on genuinely new coordinates and show a "stale position" warning past ~30s.
- **Suggested command**: `/impeccable harden`

**[P1] Two conflicting destructive-action patterns used interchangeably**
- **Why it matters**: native `confirm()` (`CardPassageiro.vue`, `SecaoSolicitacoes.vue`) and custom Teleport modals (`SecaoPassageiros.vue`, `Admin/Dashboard.vue`) are both used for functionally equivalent, consequential actions (cancel a request, end a vínculo, mark an absence) — users can't predict which interaction they'll get, and it breaks the app's own established modal language for no reason.
- **Fix**: replace every `confirm()` call with the existing modal pattern.
- **Suggested command**: `/impeccable polish`

**[P2] Untranslated, unstyled Breeze pages sit directly downstream of fully custom pages**
- **Why it matters**: `ResetPassword.vue`, `ConfirmPassword.vue`, and `VerifyEmail.vue` are stock English Breeze scaffolding (indigo focus rings, "Reset Password") reached immediately after the fully localized, custom-styled `ForgotPassword.vue`/`Login.vue` — a jarring language and visual break mid-auth-flow for a Brazilian audience.
- **Fix**: port these onto the same layout system as Login/ForgotPassword and translate the copy.
- **Suggested command**: `/impeccable adapt`

**[P2] Verification/trust UI is thinner than the product's core promise**
- **Why it matters**: the "Verificado" badge on marketplace cards is a static pill with no link to what was actually checked; CNH expiry dates render in plain text with no expired-state styling; `Van/Criar.vue` explicitly implies documents "can be sent later," meaning there's no evident document-review surface for Admin at all. For a product whose entire value proposition is "verified drivers," the actual verification UI doesn't back that claim up.
- **Fix**: add a red/warning state when CNH is expired; build a real document upload + review panel in Admin.
- **Suggested command**: `/impeccable harden`

## Persona Red Flags

**Jordan (Confused First-Timer parent)**: Signs up under time pressure via `Welcome.vue`'s registration modal, choosing "Responsável" vs "Motorista" from a single one-line description each — no clarification that "Responsável" means legal guardian. Lands on an empty dashboard with no onboarding checklist beyond a passive empty-state CTA. The "Fazer depois" skip link on address entry (`Passageiro/create.vue`, `enderecos.vue`) later collides with `Marketplace.vue`'s request-a-van flow, which blocks submission with a dense warning redirecting Jordan to fill in what was just marked optional — a confusing loop. If a request is rejected, there's no modeled UI state for it at all (`CardPassageiro.vue` only handles active/pending/fallback) — it simply vanishes with no explanation.

**Sam (Accessibility-dependent)**: The pickup-order reorder buttons in `SecaoTrajetos.vue` (up/down chevrons — the exact control sequencing child pickups) have no `aria-label`, so a screen reader announces an unnamed button. Status meaning is frequently carried only by a pulsing colored dot (`animate-pulse`), invisible to screen readers and easy to miss for low vision, even where text labels are also present. The confirm()/modal inconsistency above means Sam's screen reader gets a completely different announcement model action-to-action on the same page.

**Riley (Deliberate Stress Tester)**: Confirmed the `SecaoAcompanhar.vue` false-reassurance bug directly — the "last updated" timestamp advances on every poll whether or not the van's GPS actually reported new coordinates, so Riley watching this screen cannot tell "van is fine" from "tracking silently died 10 minutes ago." If a driver ends a vínculo mid-day, there's no evidence the parent-facing UI ever surfaces the `motivo_encerramento` collected from the driver — Riley, as the affected parent, would land on a generic "no van linked" state with zero context for an already-expected pickup.

## Minor Observations

- Hardcoded absolute/localhost paths will break outside the dev environment: `Motorista/Layout/Sidebar.vue`, `Responsavel/Layout/Sidebar.vue` (`href="http://localhost/rota-segura/public/"` literally).
- `Welcome.vue` defines `.rs-footer__socials`/`.rs-social-btn` CSS but renders no actual social links — dead CSS.
- `Responsavel/Dashboard.vue` injects an unscoped global `<style>` touching `html, body` background from inside a page component — fragile, can leak across route transitions.
- The `form.errors.geral` red error box is duplicated verbatim across at least 6 files — a clear candidate for a shared `<FormError>` component.
- Leaflet marker icons in `SecaoAcompanhar.vue` load from the `unpkg.com` CDN at runtime rather than being bundled — an external dependency and possible broken-icon failure mode in the app's core safety feature.
- Two apparently-duplicate "add passenger" flows exist side by side (`Passageiro/Adicionar.vue` single-page form vs. `Passageiro/create.vue` + `enderecos.vue` two-step wizard) with different fields and different styling — looks like an unfinished refactor rather than an intentional choice.

## Questions to Consider

- If "reviews" and "instant notifications" are headline value props on the landing page, was that cut under deadline pressure — and what's the plan before real parents rely on this for their kids?
- What is Admin actually verifying today beyond typed text fields — where's the CNH photo, CRLV, insurance document, or background-check evidence a parent should be able to trust exists?
- With four unreconciled visual systems already live, what happens when a fourth portal or feature gets built by copying whichever pattern the next contributor happens to land on?
- Has anyone watched the live-tracking screen long enough to notice the timestamp keeps advancing even when the van's position doesn't move?
