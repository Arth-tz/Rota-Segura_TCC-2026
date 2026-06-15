<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { onClickOutside } from '@vueuse/core';

const modalRef    = ref(null)
const modalAberta = ref(false)
const heroVisible = ref(false)
const scrollY     = ref(0)

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    painelUrl: String,
});

const abrirModal = () => {
    modalAberta.value = true
    document.body.style.overflow = 'hidden'
}
const fecharModal = () => {
    modalAberta.value = false
    document.body.style.overflow = ''
}

onClickOutside(modalRef, fecharModal)

const handleScroll = () => {
    scrollY.value = window.scrollY
    document.querySelectorAll('.reveal').forEach(el => {
        if (el.getBoundingClientRect().top < window.innerHeight * 0.9)
            el.classList.add('revealed')
    })
}

onMounted(() => {
    setTimeout(() => heroVisible.value = true, 80)
    window.addEventListener('scroll', handleScroll, { passive: true })
    handleScroll()
})
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
    <div class="rs-root">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- NAV -->
        <nav class="rs-nav" :class="{ 'rs-nav--scrolled': scrollY > 30 }">
            <div class="rs-nav__inner">
                <Link href="/rota-segura/public/" class="rs-nav__logo">
                    <div class="rs-nav__logo-ring">
                        <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Logo Rota Segura" class="rs-nav__logo-img" />
                    </div>
                    <span class="rs-nav__logo-text">Rota Segura</span>
                </Link>
                <div v-if="canLogin" class="rs-nav__actions">
                    <template v-if="$page.props.auth.user">
                        <Link :href="painelUrl || route('home')" class="rs-btn rs-btn--white rs-btn--sm">Entrar no Painel</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="rs-btn rs-btn--ghost rs-btn--sm">Entrar</Link>
                        <button @click="abrirModal" class="rs-btn rs-btn--white rs-btn--sm">Cadastrar</button>
                    </template>
                </div>
            </div>
        </nav>

        <!-- HERO -->
        <section class="rs-hero">
            <div class="rs-hero__bg">
                <div class="rs-shape rs-shape--1"></div>
                <div class="rs-shape rs-shape--2"></div>
                <div class="rs-shape rs-shape--3"></div>
                <div class="rs-dots"></div>
            </div>

            <div class="rs-container rs-hero__grid">
                <!-- Texto -->
                <div class="rs-hero__text" :class="{ 'rs-hero__text--in': heroVisible }">
                    <div class="rs-badge">
                        <span class="rs-badge__dot"></span>
                        Transporte Escolar Seguro
                    </div>
                    <h1 class="rs-hero__title">
                        Seu filho <span class="rs-hero__highlight">mais seguro</span> a caminho da escola
                    </h1>
                    <p class="rs-hero__subtitle">
                        Encontre motoristas de confiança e acompanhe a rota em <strong>tempo real</strong>. Segurança e tranquilidade para toda a família.
                    </p>
                    <div class="rs-hero__actions">
                        <button @click="abrirModal" class="rs-btn rs-btn--primary rs-btn--lg">
                            Começar Agora
                        </button>
                        <Link href="#como-funciona-secao" class="rs-btn rs-btn--outline rs-btn--lg">Como funciona</Link>
                    </div>
                    <div class="rs-trust">
                        <div class="rs-trust__sep" aria-hidden="true">·</div>
                        <div class="rs-trust__item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="rs-trust__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            GPS em tempo real
                        </div>
                        <div class="rs-trust__sep" aria-hidden="true">·</div>
                        <div class="rs-trust__item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="rs-trust__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                            Notificações instantâneas
                        </div>
                    </div>
                </div>

                <!-- Imagem -->
                <div class="rs-hero__media" :class="{ 'rs-hero__media--in': heroVisible }">
                    <div class="rs-img-card">
                        <div class="rs-img-card__bg"></div>
                        <img src="/rota-segura/public/images/mapa_tempo_real.png" alt="Mapa com acompanhamento em tempo real" class="rs-img-card__img" />
                        <div class="rs-float rs-float--bottom-left">
                            <span class="rs-float__pulse"></span>
                            <span>Localização em tempo real!</span>
                        </div>
                        <div class="rs-float rs-float--top-right">
                            <svg xmlns="http://www.w3.org/2000/svg" class="rs-float__check" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Luísa chegou à escola</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DIFERENCIAIS -->
        <section class="rs-features">
            <div class="rs-container">
                <div class="rs-section-header reveal" style="--d:0s">
                    <div class="rs-section-tag">Por que escolher a Rota Segura?</div>
                    <h2 class="rs-section-title">Tudo que sua família precisa</h2>
                    <p class="rs-section-sub">Uma plataforma pensada para dar tranquilidade aos pais e segurança às crianças.</p>
                </div>
                <div class="rs-features__grid">
                    <div class="rs-feature-card reveal" style="--d:0.05s">
                        <div class="rs-feature-card__icon rs-feature-card__icon--blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <h3 class="rs-feature-card__title">Rastreamento ao vivo</h3>
                        <p class="rs-feature-card__desc">Veja em tempo real onde está o veículo do seu filho. Mapa atualizado a cada segundo.</p>
                    </div>
                    <div class="rs-feature-card reveal" style="--d:0.1s">
                        <div class="rs-feature-card__icon rs-feature-card__icon--green">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </div>
                        <h3 class="rs-feature-card__title">Motoristas verificados</h3>
                        <p class="rs-feature-card__desc">Todos os motoristas passam por verificação de documentos, antecedentes e habilitação.</p>
                    </div>
                    <div class="rs-feature-card reveal" style="--d:0.15s">
                        <div class="rs-feature-card__icon rs-feature-card__icon--orange">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        </div>
                        <h3 class="rs-feature-card__title">Alertas de embarque</h3>
                        <p class="rs-feature-card__desc">Receba uma notificação quando seu filho entra ou sai da van. Sempre informado.</p>
                    </div>
                   
                </div>
            </div>
        </section>

        <!-- COMO FUNCIONA -->
        <section id="como-funciona-secao" class="rs-how">
            <div class="rs-container">
                <div class="rs-section-header reveal" style="--d:0s">
                    <div class="rs-section-tag">Simples assim</div>
                    <h2 class="rs-section-title">Como funciona?</h2>
                    <p class="rs-section-sub">Em poucos passos, seu filho terá um transporte seguro e você terá total controle.</p>
                </div>
                <div class="rs-steps">
                    <div class="rs-step reveal" style="--d:0.08s">
                        <div class="rs-step__left">
                            <div class="rs-step__num">1</div>
                            <div class="rs-step__connector"></div>
                        </div>
                        <div class="rs-step__body">
                            <h3 class="rs-step__title">Cadastre seu filho</h3>
                            <p class="rs-step__desc">Informe os dados do aluno, escola, turno e endereço de embarque.</p>
                        </div>
                    </div>
                    <div class="rs-step reveal" style="--d:0.2s">
                        <div class="rs-step__left">
                            <div class="rs-step__num">2</div>
                            <div class="rs-step__connector"></div>
                        </div>
                        <div class="rs-step__body">
                            <h3 class="rs-step__title">Encontre uma van</h3>
                            <p class="rs-step__desc">Filtre por região, turno e disponibilidade. Veja avaliações e encontre o preço ideal.</p>
                        </div>
                    </div>
                    <div class="rs-step reveal" style="--d:0.32s">
                        <div class="rs-step__left">
                            <div class="rs-step__num">3</div>
                        </div>
                        <div class="rs-step__body">
                            <h3 class="rs-step__title">Acompanhe em tempo real</h3>
                            <p class="rs-step__desc">Monitore a rota ao vivo e receba confirmação de embarque e desembarque.</p>
                        </div>
                    </div>
                </div>
                <div class="rs-how__cta reveal" style="--d:0.44s">
                    <button @click="abrirModal" class="rs-btn rs-btn--primary rs-btn--lg">Criar conta gratuita</button>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="rs-footer">
            <div class="rs-container">
                <div class="rs-footer__top">
                    <div class="rs-footer__brand">
                        <Link href="/" class="rs-footer__logo">
                            <img src="/images/Logo_rota-segura_branco.png" alt="Logo Rota-Segura" class="rs-footer__logo-img" />
                            <span>Rota Segura</span>
                        </Link>
                        <p class="rs-footer__tagline">Conectando famílias a motoristas de confiança para um transporte escolar mais seguro.</p>
                        
                    </div>
                </div>
                <div class="rs-footer__bottom">
                    <p>© 2026 Rota Segura. Todos os direitos reservados.</p>
                    <p class="rs-footer__heart">Desenvolvido com carinho - para famílias brasileiras</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- MODAL -->
    <Teleport to="body">
        <Transition name="modal-overlay-fade">
            <div v-show="modalAberta" class="rs-modal-overlay" @click.self="fecharModal">
                <Transition name="modal-card-slide">
                    <div v-show="modalAberta" ref="modalRef" class="rs-modal">
                        <button @click="fecharModal" class="rs-modal__close" aria-label="Fechar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="rs-modal__close-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div class="rs-modal__logo">
                            <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" class="rs-modal__logo-img" alt="" />
                        </div>
                        <h1 class="rs-modal__title">Criar conta</h1>
                        <p class="rs-modal__sub">Como você quer se cadastrar?</p>
                        <div class="rs-modal__grid">
                            <Link :href="route('register.responsavel')" class="rs-modal__option">
                                <div class="rs-modal__option-mark">R</div>
                                <h3 class="rs-modal__option-title">Responsável</h3>
                                <p class="rs-modal__option-desc">Quero encontrar uma van para meu filho</p>
                            </Link>
                            <Link :href="route('register.motorista')" class="rs-modal__option">
                                <div class="rs-modal__option-mark">M</div>
                                <h3 class="rs-modal__option-title">Motorista</h3>
                                <p class="rs-modal__option-desc">Quero oferecer transporte escolar</p>
                            </Link>
                        </div>
                        <p class="rs-modal__footer">
                            Já tem conta?
                            <Link :href="route('login')" class="rs-modal__login-link">Entrar</Link>
                        </p>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* === RESET & BASE === */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
* { font-family: 'Plus Jakarta Sans', sans-serif; }

.rs-root { background: #f8fafc; color: #0f172a; overflow-x: hidden; }

.rs-container {
    max-width: 1120px;
    margin: 0 auto;
    padding: 0 20px;
}
@media (min-width: 640px) { .rs-container { padding: 0 28px; } }
@media (min-width: 1024px) { .rs-container { padding: 0 40px; } }

/* === NAV === */
.rs-nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 50;
    background: #2563eb;
    transition: box-shadow 0.3s ease, background 0.3s ease;
}
.rs-nav--scrolled { background: #1d4ed8; box-shadow: 0 4px 24px rgba(37,99,235,0.35); }
.rs-nav__inner {
    max-width: 1120px; margin: 0 auto;
    padding: 10px 20px;
    display: flex; align-items: center; justify-content: space-between;
}
@media (min-width: 640px) { .rs-nav__inner { padding: 10px 28px; } }
@media (min-width: 1024px) { .rs-nav__inner { padding: 10px 40px; } }

.rs-nav__logo { display: flex; align-items: center; gap: 10px; text-decoration: none; transition: opacity 0.2s; }
.rs-nav__logo:hover { opacity: 0.85; }
.rs-nav__logo-ring {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;
    transition: background 0.25s; flex-shrink: 0;
}
.rs-nav__logo:hover .rs-nav__logo-ring { background: rgba(255,255,255,0.25); }
.rs-nav__logo-img { width: 32px; height: 32px; object-fit: contain; }
.rs-nav__logo-text { color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 1.05rem; }
.rs-nav__actions { display: flex; align-items: center; gap: 8px; }

/* === BUTTONS === */
.rs-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600;
    border-radius: 12px; cursor: pointer; text-decoration: none; border: none;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease, background 0.2s ease;
    white-space: nowrap; line-height: 1;
}
.rs-btn:hover { transform: translateY(-2px); }
.rs-btn:active { transform: translateY(0) scale(0.98); }

.rs-btn--sm { padding: 8px 14px; font-size: 0.82rem; border-radius: 10px; }
@media (min-width: 400px) { .rs-btn--sm { padding: 8px 18px; font-size: 0.875rem; } }

.rs-btn--lg { padding: 13px 22px; font-size: 0.9rem; }
@media (min-width: 640px) { .rs-btn--lg { padding: 14px 28px; font-size: 0.95rem; } }

.rs-btn--primary { background: #2563eb; color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
.rs-btn--primary:hover { background: #1d4ed8; box-shadow: 0 8px 24px rgba(37,99,235,0.45); }
.rs-btn--ghost { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.25); }
.rs-btn--ghost:hover { background: rgba(255,255,255,0.25); }
.rs-btn--white { background: #fff; color: #2563eb; font-weight: 700; }
.rs-btn--white:hover { background: #eff6ff; box-shadow: 0 4px 14px rgba(37,99,235,0.2); }
.rs-btn--outline { background: transparent; color: #2563eb; border: 2px solid #bfdbfe; }
.rs-btn--outline:hover { background: #eff6ff; border-color: #93c5fd; }

.rs-btn__icon { width: 15px; height: 15px; flex-shrink: 0; transition: transform 0.25s ease; }
.rs-btn:hover .rs-btn__icon { transform: translateX(3px); }

/* === HERO === */
.rs-hero {
    position: relative; overflow: hidden;
    padding: 100px 0 72px;
    background: linear-gradient(160deg, #ffffff 0%, #eff6ff 55%, #dbeafe 100%);
}
@media (min-width: 768px) { .rs-hero { padding: 120px 0 88px; } }
@media (min-width: 1024px) { .rs-hero { padding: 128px 0 100px; } }

.rs-hero__bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }

.rs-shape { position: absolute; border-radius: 50%; filter: blur(55px); }
.rs-shape--1 {
    width: 320px; height: 320px;
    background: radial-gradient(circle, #bfdbfe, #dbeafe80);
    top: -80px; left: -80px; opacity: 0.65;
    animation: shapeFloat 14s ease-in-out infinite alternate;
}
.rs-shape--2 {
    width: 260px; height: 260px;
    background: radial-gradient(circle, #93c5fd60, #eff6ff80);
    top: 40px; right: -60px; opacity: 0.5;
    animation: shapeFloat 18s ease-in-out infinite alternate-reverse;
}
.rs-shape--3 {
    width: 220px; height: 220px;
    background: radial-gradient(circle, #bfdbfe50, transparent);
    bottom: -40px; left: 40%; opacity: 0.4;
    animation: shapeFloat 22s ease-in-out infinite alternate;
}
@media (min-width: 768px) {
    .rs-shape--1 { width: 480px; height: 480px; top: -100px; left: -120px; }
    .rs-shape--2 { width: 350px; height: 350px; }
    .rs-shape--3 { width: 280px; height: 280px; }
}
@keyframes shapeFloat {
    0% { transform: translate(0,0) scale(1); }
    100% { transform: translate(20px, 28px) scale(1.08); }
}
.rs-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, #93c5fd 1px, transparent 1px);
    background-size: 28px 28px; opacity: 0.15;
}
@media (min-width: 768px) { .rs-dots { background-size: 32px 32px; opacity: 0.18; } }

/* Hero grid */
.rs-hero__grid {
    display: grid; grid-template-columns: 1fr;
    gap: 40px; align-items: center; position: relative; z-index: 2;
}
@media (min-width: 900px) {
    .rs-hero__grid { grid-template-columns: 1fr 1fr; gap: 56px; }
}

/* On mobile: image comes first */
.rs-hero__media { order: -1; }
@media (min-width: 900px) { .rs-hero__media { order: 0; } }

/* Hero entrance animations */
.rs-hero__text {
    opacity: 0; transform: translateY(32px);
    transition: opacity 0.9s cubic-bezier(0.16,1,0.3,1), transform 0.9s cubic-bezier(0.16,1,0.3,1);
}
.rs-hero__text--in { opacity: 1; transform: translateY(0); }
.rs-hero__media {
    opacity: 0; transform: translateY(28px) scale(0.97);
    transition: opacity 1s cubic-bezier(0.16,1,0.3,1) 0.12s, transform 1s cubic-bezier(0.16,1,0.3,1) 0.12s;
}
.rs-hero__media--in { opacity: 1; transform: translateY(0) scale(1); }

/* Badge */
.rs-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8;
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
    padding: 5px 12px; border-radius: 999px; margin-bottom: 18px;
}
@media (min-width: 640px) { .rs-badge { font-size: 0.72rem; padding: 6px 14px; margin-bottom: 22px; } }
.rs-badge__dot {
    width: 7px; height: 7px; background: #2563eb; border-radius: 50%; flex-shrink: 0;
    animation: badgePulse 2.5s ease-in-out infinite;
}
@keyframes badgePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(37,99,235,0.4); }
    50% { box-shadow: 0 0 0 6px rgba(37,99,235,0); }
}

/* Hero title */
.rs-hero__title {
    font-family: 'Sora', sans-serif;
    font-size: clamp(1.9rem, 5.5vw, 3.4rem);
    font-weight: 900; line-height: 1.15; color: #0f172a;
    margin-bottom: 16px; letter-spacing: -0.025em;
}
@media (min-width: 640px) { .rs-hero__title { margin-bottom: 20px; } }

.rs-hero__highlight { color: #2563eb; position: relative; display: inline-block; font-family: 'Sora', sans-serif;}

@media (min-width: 640px) { .rs-hero__highlight::after { height: 4px; bottom: 2px; } }
@keyframes underlineGrow { to { transform: scaleX(1); } }

.rs-hero__subtitle {
    color: #475569; font-size: 0.95rem; line-height: 1.75; margin-bottom: 28px;
}
@media (min-width: 640px) { .rs-hero__subtitle { font-size: 1.05rem; max-width: 460px; margin-bottom: 32px; } }
.rs-hero__subtitle strong { color: #0f172a; font-weight: 700; }

.rs-hero__actions { display: flex; flex-wrap: wrap; gap: 10px; }
@media (min-width: 400px) { .rs-hero__actions { gap: 12px; } }

/* Trust strip */
.rs-trust {
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px 10px;
    margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;
}
@media (min-width: 640px) { .rs-trust { gap: 10px 14px; margin-top: 32px; padding-top: 24px; } }
.rs-trust__item { display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #475569; font-weight: 500; }
@media (min-width: 640px) { .rs-trust__item { font-size: 0.8rem; gap: 6px; } }
.rs-trust__icon { width: 14px; height: 14px; color: #2563eb; flex-shrink: 0; }
.rs-trust__sep { color: #cbd5e1; font-size: 1rem; }

/* Image card */
.rs-img-card { position: relative; max-width: 520px; margin: 0 auto; }
@media (min-width: 900px) { .rs-img-card { max-width: none; margin: 0; } }

.rs-img-card__bg {
    position: absolute; inset: -8px;
    background: linear-gradient(135deg, #bfdbfe, #dbeafe);
    border-radius: 22px; transform: rotate(2deg); z-index: 0;
    transition: transform 0.6s ease;
}
@media (min-width: 640px) { .rs-img-card__bg { inset: -10px; border-radius: 24px; transform: rotate(2.5deg); } }
.rs-img-card:hover .rs-img-card__bg { transform: rotate(1deg); }

.rs-img-card__img {
    position: relative; z-index: 1; width: 100%; height: auto;
    border-radius: 16px; box-shadow: 0 16px 48px rgba(15,23,42,0.14); display: block;
    transition: transform 0.5s ease, box-shadow 0.5s ease;
}
@media (min-width: 640px) { .rs-img-card__img { border-radius: 18px; } }
.rs-img-card:hover .rs-img-card__img { transform: translateY(-4px); box-shadow: 0 24px 64px rgba(15,23,42,0.18); }

/* Floating cards */
.rs-float {
    position: absolute; z-index: 2;
    display: flex; align-items: center; gap: 7px;
    background: #fff; border-radius: 10px; padding: 8px 12px;
    font-size: 11px; font-weight: 600; color: #0f172a;
    box-shadow: 0 6px 20px rgba(15,23,42,0.12); border: 1px solid #f1f5f9; white-space: nowrap;
}
@media (min-width: 480px) { .rs-float { padding: 10px 14px; font-size: 12px; border-radius: 12px; gap: 8px; } }
@media (min-width: 640px) { .rs-float { padding: 10px 16px; font-size: 13px; } }

.rs-float--bottom-left { bottom: -12px; left: -8px; animation: floatBob 5s ease-in-out infinite; }
.rs-float--top-right { top: -12px; right: -8px; animation: floatBob 4.5s ease-in-out infinite 1.2s; }
@media (min-width: 480px) {
    .rs-float--bottom-left { bottom: -14px; left: -12px; }
    .rs-float--top-right { top: -12px; right: -12px; }
}
@media (min-width: 640px) {
    .rs-float--bottom-left { bottom: -16px; left: -16px; }
    .rs-float--top-right { top: -14px; right: -14px; }
}
@keyframes floatBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

.rs-float__pulse {
    width: 8px; height: 8px; background: #22c55e; border-radius: 50%; flex-shrink: 0;
    animation: pulseRing 2.2s ease-in-out infinite;
}
@keyframes pulseRing {
    0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
    70% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
    100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
.rs-float__check { width: 15px; height: 15px; flex-shrink: 0; color: #16a34a; }

/* === SCROLL REVEAL === */
.reveal {
    opacity: 0; transform: translateY(28px);
    transition: opacity 0.75s cubic-bezier(0.16,1,0.3,1), transform 0.75s cubic-bezier(0.16,1,0.3,1);
    transition-delay: var(--d, 0s);
}
.reveal.revealed { opacity: 1; transform: translateY(0); }

/* === SECTION HEADER === */
.rs-section-header { text-align: center; margin-bottom: 40px; }
@media (min-width: 640px) { .rs-section-header { margin-bottom: 52px; } }
.rs-section-tag {
    display: inline-block; background: #eff6ff; color: #1d4ed8;
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
    padding: 4px 12px; border-radius: 999px; border: 1px solid #bfdbfe; margin-bottom: 12px;
}
@media (min-width: 640px) { .rs-section-tag { font-size: 0.72rem; padding: 5px 14px; margin-bottom: 14px; } }
.rs-section-title {
    font-family: 'Sora', sans-serif;
    font-size: clamp(1.5rem, 4vw, 2.4rem); font-weight: 900; color: #0f172a;
    margin-bottom: 12px; letter-spacing: -0.02em;
}
.rs-section-sub { color: #475569; font-size: 0.9rem; max-width: 440px; margin: 0 auto; line-height: 1.7; }
@media (min-width: 640px) { .rs-section-sub { font-size: 0.95rem; } }

/* === FEATURES === */
.rs-features { padding: 64px 0; background: #fff; }
@media (min-width: 640px) { .rs-features { padding: 80px 0; } }
@media (min-width: 1024px) { .rs-features { padding: 96px 0; } }

.rs-features__grid {
    display: grid; grid-template-columns: 1fr; gap: 14px;
}
@media (min-width: 480px) { .rs-features__grid { grid-template-columns: repeat(2, 1fr); gap: 16px; } }
@media (min-width: 900px) { .rs-features__grid { grid-template-columns: repeat(3, 1fr); gap: 20px; } }

.rs-feature-card {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 18px; padding: 22px;
    transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.35s ease, border-color 0.25s ease, background 0.25s ease;
}
@media (min-width: 640px) { .rs-feature-card { padding: 26px; border-radius: 20px; } }
@media (hover: hover) {
    .rs-feature-card:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(15,23,42,0.1); border-color: #bfdbfe; background: #fff; }
    .rs-feature-card:hover .rs-feature-card__icon { transform: scale(1.1) rotate(-4deg); }
}

.rs-feature-card__icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
    transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1);
}
@media (min-width: 640px) { .rs-feature-card__icon { width: 48px; height: 48px; border-radius: 14px; margin-bottom: 18px; } }
.rs-feature-card__icon svg { width: 20px; height: 20px; }
@media (min-width: 640px) { .rs-feature-card__icon svg { width: 22px; height: 22px; } }
.rs-feature-card__icon--blue { background: #dbeafe; color: #1d4ed8; }
.rs-feature-card__icon--green { background: #dcfce7; color: #16a34a; }
.rs-feature-card__icon--orange { background: #ffedd5; color: #ea580c; }
.rs-feature-card__icon--purple { background: #ede9fe; color: #7c3aed; }
.rs-feature-card__icon--teal { background: #ccfbf1; color: #0d9488; }
.rs-feature-card__icon--rose { background: #ffe4e6; color: #e11d48; }

.rs-feature-card__title { font-family: 'Nunito', sans-serif; font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.rs-feature-card__desc { font-size: 0.83rem; color: #475569; line-height: 1.65; }
@media (min-width: 640px) { .rs-feature-card__desc { font-size: 0.875rem; } }

/* === HOW IT WORKS === */
.rs-how { padding: 64px 0; background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%); }
@media (min-width: 640px) { .rs-how { padding: 80px 0; } }
@media (min-width: 1024px) { .rs-how { padding: 96px 0; } }

.rs-steps { display: flex; flex-direction: column; max-width: 560px; margin: 0 auto; }

.rs-step { display: flex; align-items: flex-start; gap: 16px; }
@media (min-width: 640px) { .rs-step { gap: 20px; } }

.rs-step__left { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }

.rs-step__num {
    width: 40px; height: 40px; background: #2563eb; color: #fff; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Nunito', sans-serif; font-weight: 900; font-size: 1rem; flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(37,99,235,0.35); position: relative; z-index: 1;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.3s ease;
}
@media (min-width: 640px) { .rs-step__num { width: 44px; height: 44px; font-size: 1.1rem; } }
@media (hover: hover) {
    .rs-step:hover .rs-step__num { transform: scale(1.12); box-shadow: 0 8px 24px rgba(37,99,235,0.45); }
}

.rs-step__connector {
    width: 2px; flex: 1; min-height: 40px;
    background: linear-gradient(180deg, #bfdbfe, #e0e7ff); margin-top: 4px;
}

.rs-step__body { padding: 8px 0 44px; }
@media (min-width: 640px) { .rs-step__body { padding: 10px 0 56px; } }

.rs-step__title { font-family: 'Nunito', sans-serif; font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
@media (min-width: 640px) { .rs-step__title { font-size: 1.05rem; } }
.rs-step__desc { font-size: 0.85rem; color: #475569; line-height: 1.65; }
@media (min-width: 640px) { .rs-step__desc { font-size: 0.9rem; } }

.rs-how__cta { text-align: center; margin-top: 4px; }

/* === FOOTER === */
.rs-footer { background: #1e3a8a; color: rgba(255,255,255,0.7); padding: 52px 0 0; }
@media (min-width: 640px) { .rs-footer { padding: 64px 0 0; } }

.rs-footer__top {
    display: grid; grid-template-columns: 1fr; gap: 36px;
    padding-bottom: 40px; border-bottom: 1px solid rgba(255,255,255,0.1);
}
@media (min-width: 560px) { .rs-footer__top { grid-template-columns: 1fr 1fr; gap: 32px; } }
@media (min-width: 900px) { .rs-footer__top { grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; } }

/* On 2-column layout, brand spans full width */
@media (min-width: 560px) and (max-width: 899px) {
    .rs-footer__brand { grid-column: 1 / -1; }
}

.rs-footer__logo { display: flex; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 14px; }
.rs-footer__logo span { color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 1.1rem; }
.rs-footer__logo-img { width: 36px; height: 36px; object-fit: contain; }
.rs-footer__tagline { font-size: 0.85rem; line-height: 1.65; margin-bottom: 22px; max-width: 300px; }
.rs-footer__socials { display: flex; gap: 10px; }

.rs-social-btn {
    width: 36px; height: 36px; background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12); border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.6); text-decoration: none; transition: all 0.25s ease;
}
@media (hover: hover) {
    .rs-social-btn:hover { background: rgba(255,255,255,0.18); color: #fff; transform: translateY(-2px); }
}
.rs-social-icon { width: 15px; height: 15px; }

.rs-footer__links-title { color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.88rem; margin-bottom: 14px; }
.rs-footer__links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.rs-footer__links li a, .rs-footer__links li button {
    color: rgba(255,255,255,0.55); text-decoration: none; font-size: 0.85rem;
    background: none; border: none; cursor: pointer; padding: 0; text-align: left;
    transition: color 0.2s ease;
}
.rs-footer__links li a:hover, .rs-footer__links li button:hover { color: #fff; }

.rs-footer__bottom {
    display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center;
    padding: 18px 0; font-size: 0.78rem; color: rgba(255,255,255,0.3);
}
@media (min-width: 640px) {
    .rs-footer__bottom { flex-direction: row; justify-content: space-between; text-align: left; }
}
.rs-footer__heart { color: rgba(255,255,255,0.3); }

/* === MODAL === */
.rs-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.6);
    display: flex; align-items: center; justify-content: center;
    z-index: 100;
    padding: 16px;
    backdrop-filter: blur(4px);
    /* permite scroll do overlay caso o modal seja maior que a tela */
    overflow-y: auto;
}

.rs-modal {
    background: #fff;
    width: 100%;
    max-width: 460px;
    position: relative;
    border-radius: 20px;
    padding: 28px 18px 28px;
    box-shadow: 0 24px 64px rgba(15,23,42,0.22);
    /* garante que o modal nunca ultrapasse a tela */
    max-height: calc(100dvh - 32px);
    overflow-y: auto;
    /* afasta do topo/fundo em telas muito pequenas */
    margin: auto;
}
@media (min-width: 400px) {
    .rs-modal { padding: 32px 24px 28px; border-radius: 22px; }
}
@media (min-width: 560px) {
    .rs-modal { padding: 44px 36px 36px; border-radius: 24px; }
}

.rs-modal__close {
    position: absolute; top: 14px; right: 14px; width: 32px; height: 32px;
    border-radius: 8px; background: #f1f5f9; border: none; color: #64748b;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    transition: all 0.2s ease;
}
.rs-modal__close:hover { background: #e2e8f0; color: #0f172a; }
.rs-modal__close-icon { width: 14px; height: 14px; }

.rs-modal__logo {
    width: 42px; height: 42px; background: #2563eb; border-radius: 13px;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
    box-shadow: 0 6px 18px rgba(37,99,235,0.35);
}
@media (min-width: 400px) { .rs-modal__logo { width: 48px; height: 48px; border-radius: 14px; margin-bottom: 16px; } }
.rs-modal__logo-img { width: 26px; height: 26px; object-fit: contain; }
@media (min-width: 400px) { .rs-modal__logo-img { width: 28px; height: 28px; } }

.rs-modal__title {
    font-family: 'Nunito', sans-serif; font-size: 1.35rem; font-weight: 900;
    color: #0f172a; text-align: center; margin-bottom: 4px;
}
@media (min-width: 400px) { .rs-modal__title { font-size: 1.5rem; margin-bottom: 6px; } }
@media (min-width: 560px) { .rs-modal__title { font-size: 1.65rem; } }

.rs-modal__sub {
    color: #475569; font-size: 0.82rem; text-align: center; margin-bottom: 18px;
}
@media (min-width: 400px) { .rs-modal__sub { font-size: 0.875rem; margin-bottom: 22px; } }

.rs-modal__grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media (min-width: 400px) { .rs-modal__grid { gap: 14px; } }

.rs-modal__option {
    display: block; background: #f8fafc; border: 2px solid #e2e8f0;
    border-radius: 14px; padding: 16px 10px; text-align: center; text-decoration: none;
    transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
@media (min-width: 400px) { .rs-modal__option { padding: 20px 14px; border-radius: 16px; } }
@media (min-width: 480px) { .rs-modal__option { padding: 22px 16px; border-radius: 18px; } }
@media (hover: hover) {
    .rs-modal__option:hover { border-color: #2563eb; background: #eff6ff; transform: translateY(-4px); box-shadow: 0 10px 28px rgba(37,99,235,0.15); }
}
.rs-modal__option:active { transform: scale(0.97); }

.rs-modal__option-mark {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
    font-weight: 800;
}
@media (min-width: 400px) { .rs-modal__option-mark { width: 48px; height: 48px; } }
.rs-modal__option-title { font-family: 'Nunito', sans-serif; font-weight: 800; color: #0f172a; font-size: 0.85rem; margin-bottom: 4px; }
@media (min-width: 400px) { .rs-modal__option-title { font-size: 0.9rem; } }
.rs-modal__option-desc { font-size: 0.7rem; color: #475569; line-height: 1.45; }
@media (min-width: 400px) { .rs-modal__option-desc { font-size: 0.75rem; } }

.rs-modal__footer { text-align: center; margin-top: 18px; font-size: 0.875rem; color: #475569; }
.rs-modal__login-link { color: #2563eb; font-weight: 700; text-decoration: none; margin-left: 4px; }
.rs-modal__login-link:hover { text-decoration: underline; }

/* === TRANSITIONS === */
.modal-overlay-fade-enter-active, .modal-overlay-fade-leave-active { transition: opacity 0.3s ease; }
.modal-overlay-fade-enter-from, .modal-overlay-fade-leave-to { opacity: 0; }

/* Mobile: slides up slightly */
.modal-card-slide-enter-active { transition: all 0.35s cubic-bezier(0.16,1,0.3,1); }
.modal-card-slide-leave-active { transition: all 0.2s ease-in; }
.modal-card-slide-enter-from { opacity: 0; transform: scale(0.93) translateY(16px); }
.modal-card-slide-leave-to { opacity: 0; transform: scale(0.95) translateY(8px); }
</style>
