<script setup>
import { computed, ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    status: { type: String },
})

const form = useForm({})
const pageVisible = ref(false)

onMounted(() => setTimeout(() => pageVisible.value = true, 80))

const linkSent = computed(() => props.status === 'verification-link-sent')

const submit = () => {
    form.post(route('verification.send'))
}
</script>

<template>
    <Head title="Confirme seu e-mail" />

    <div class="lg-root">

        <!-- ========== ASIDE ========== -->
        <aside class="lg-aside" :class="{ 'lg-aside--in': pageVisible }">
            <div class="lg-aside__blob lg-aside__blob--1"></div>
            <div class="lg-aside__blob lg-aside__blob--2"></div>
            <div class="lg-aside__dots"></div>

            <Link :href="route('home')" class="lg-aside__logo">
                <div class="lg-aside__logo-ring">
                    <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Logo Rota Segura" class="lg-aside__logo-img" />
                </div>
                <span class="lg-aside__logo-text">Rota Segura</span>
            </Link>

            <div class="lg-aside__body">
                <div class="lg-aside__tag">Confirmação de conta</div>
                <h2 class="lg-aside__title">Quase lá!</h2>
                <p class="lg-aside__sub">Enviamos um link para o seu e-mail. Clique nele para ativar sua conta.</p>
            </div>

            <p class="lg-aside__copy">© 2026 Rota Segura</p>
        </aside>

        <!-- ========== MAIN ========== -->
        <main class="lg-main" :class="{ 'lg-main--in': pageVisible }">
            <div class="lg-form-wrap">

                <!-- Logo mobile -->
                <div class="lg-mobile-logo">
                    <div class="lg-mobile-logo__ring">
                        <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Logo Rota-Segura" class="lg-mobile-logo__img" />
                    </div>
                    <span class="lg-mobile-logo__text">Rota Segura</span>
                </div>

                <!-- Ícone de envelope -->
                <div class="lg-envelope">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-envelope__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>

                <!-- Heading -->
                <div class="lg-heading">
                    <h1 class="lg-heading__title">Confirme seu e-mail</h1>
                    <p class="lg-heading__sub">
                        Enviamos um link de verificação para o seu e-mail.
                        Acesse sua caixa de entrada e clique no link para ativar sua conta.
                    </p>
                </div>

                <!-- Confirmação de reenvio -->
                <div v-if="linkSent" class="lg-status">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-status__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Novo link de verificação enviado com sucesso!
                </div>

                <!-- Dica -->
                <div class="lg-tip">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-tip__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg>
                    <span>Não encontrou o e-mail? Verifique sua pasta de <strong>spam</strong> ou solicite um novo link abaixo.</span>
                </div>

                <!-- Ações -->
                <div class="lg-actions">
                    <form @submit.prevent="submit">
                        <button type="submit" :disabled="form.processing" class="lg-submit">
                            <span v-if="!form.processing" class="lg-submit__inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="lg-submit__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                Reenviar link de verificação
                            </span>
                            <span v-else class="lg-submit__spinner">
                                <svg class="lg-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Enviando...
                            </span>
                        </button>
                    </form>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="lg-logout"
                    >
                        Sair da conta
                    </Link>
                </div>

            </div>
        </main>
    </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
* { font-family: 'Plus Jakarta Sans', sans-serif; }

.lg-root {
    min-height: 100vh; min-height: 100dvh;
    display: flex; background: #f8fafc;
}

/* ===== ASIDE ===== */
.lg-aside {
    display: none;
    width: 40%; min-width: 320px; max-width: 480px;
    flex-direction: column; justify-content: space-between;
    padding: 36px 40px;
    background: linear-gradient(160deg, #2563eb 0%, #1d4ed8 55%, #1e40af 100%);
    border-radius: 0 24px 24px 0;
    position: sticky; top: 0;
    height: 100vh; height: 100dvh;
    overflow: hidden;
    box-shadow: 12px 0 50px -8px rgba(37,99,235,0.3);
    opacity: 0; transform: translateX(-20px);
    transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
    flex-shrink: 0;
}
@media (min-width: 1024px) { .lg-aside { display: flex; } }
.lg-aside--in { opacity: 1; transform: translateX(0); }

.lg-aside__blob { position: absolute; border-radius: 50%; filter: blur(55px); pointer-events: none; }
.lg-aside__blob--1 {
    width: 340px; height: 340px;
    background: radial-gradient(circle, rgba(255,255,255,0.12), transparent 70%);
    top: -100px; left: -100px;
    animation: blobDrift 14s ease-in-out infinite alternate;
}
.lg-aside__blob--2 {
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(96,165,250,0.18), transparent 70%);
    bottom: 0; right: -50px;
    animation: blobDrift 18s ease-in-out infinite alternate-reverse;
}
@keyframes blobDrift {
    0% { transform: translate(0,0) scale(1); }
    100% { transform: translate(16px,24px) scale(1.08); }
}
.lg-aside__dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 26px 26px; pointer-events: none;
}

.lg-aside__logo { display: flex; align-items: center; gap: 10px; text-decoration: none; position: relative; z-index: 2; transition: opacity 0.2s; }
.lg-aside__logo:hover { opacity: 0.82; }
.lg-aside__logo-ring {
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25);
    display: flex; align-items: center; justify-content: center;
}
.lg-aside__logo-img { width: 28px; height: 28px; object-fit: contain; }
.lg-aside__logo-text { color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 1.05rem; }

.lg-aside__body { position: relative; z-index: 2; }
.lg-aside__tag {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    color: #bfdbfe; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; padding: 4px 12px; border-radius: 999px; margin-bottom: 18px;
}
.lg-aside__title {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.5rem, 2.2vw, 2rem);
    font-weight: 900; color: #fff; line-height: 1.2;
    margin-bottom: 14px; letter-spacing: -0.02em;
}
.lg-aside__sub { color: rgba(255,255,255,0.62); font-size: 0.875rem; line-height: 1.7; }

.lg-aside__copy { position: relative; z-index: 2; color: rgba(255,255,255,0.3); font-size: 0.75rem; }

/* ===== MAIN ===== */
.lg-main {
    flex: 1; min-width: 0;
    display: flex; align-items: center; justify-content: center;
    padding: 32px 20px 48px;
    opacity: 0; transform: translateY(16px);
    transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1) 0.08s, transform 0.8s cubic-bezier(0.16,1,0.3,1) 0.08s;
}
@media (min-width: 480px) { .lg-main { padding: 40px 32px 56px; } }
@media (min-width: 1024px) { .lg-main { padding: 48px 64px; } }
.lg-main--in { opacity: 1; transform: translateY(0); }

.lg-form-wrap { width: 100%; max-width: 480px; }

.lg-mobile-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 32px; }
@media (min-width: 1024px) { .lg-mobile-logo { display: none; } }
.lg-mobile-logo__ring {
    width: 36px; height: 36px; border-radius: 50%;
    background: #eff6ff; border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.lg-mobile-logo__img { width: 22px; height: 22px; object-fit: contain; }
.lg-mobile-logo__text { font-family: 'Nunito', sans-serif; font-weight: 800; color: #0f172a; font-size: 1rem; }

/* Envelope ilustrativo */
.lg-envelope {
    width: 64px; height: 64px; border-radius: 18px;
    background: #eff6ff; border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 24px;
}
.lg-envelope__icon { width: 30px; height: 30px; color: #2563eb; }

.lg-heading { margin-bottom: 20px; }
.lg-heading__title {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.8rem, 5vw, 2.4rem);
    font-weight: 900; color: #0f172a; letter-spacing: -0.03em; margin-bottom: 8px;
}
.lg-heading__sub { color: #64748b; font-size: 0.9rem; line-height: 1.7; }

/* Status: link enviado */
.lg-status {
    display: flex; align-items: center; gap: 10px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 12px; padding: 12px 16px;
    color: #15803d; font-size: 0.85rem; font-weight: 500;
    margin-bottom: 20px;
}
.lg-status__icon { width: 16px; height: 16px; flex-shrink: 0; }

/* Dica spam */
.lg-tip {
    display: flex; align-items: flex-start; gap: 10px;
    background: #fefce8; border: 1px solid #fef08a;
    border-radius: 12px; padding: 12px 16px;
    color: #713f12; font-size: 0.82rem; line-height: 1.6;
    margin-bottom: 28px;
}
.lg-tip__icon { width: 16px; height: 16px; color: #ca8a04; flex-shrink: 0; margin-top: 1px; }
.lg-tip strong { font-weight: 700; }

/* Ações */
.lg-actions { display: flex; flex-direction: column; gap: 12px; }

.lg-submit {
    width: 100%; padding: 14px 24px;
    background: #2563eb; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.95rem;
    border: none; border-radius: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    min-height: 50px;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease, background 0.2s ease;
}
@media (hover: hover) {
    .lg-submit:hover:not(:disabled) { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.45); }
}
.lg-submit:active:not(:disabled) { transform: scale(0.98); }
.lg-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.lg-submit__inner { display: flex; align-items: center; gap: 8px; }
.lg-submit__icon { width: 16px; height: 16px; }
.lg-submit__spinner { display: flex; align-items: center; gap: 10px; }
.lg-spin { width: 18px; height: 18px; animation: spin 0.8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

.lg-logout {
    width: 100%; padding: 13px 24px;
    background: transparent; color: #64748b;
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; font-size: 0.9rem;
    border: 1.5px solid #e2e8f0; border-radius: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; min-height: 48px;
    transition: border-color 0.2s, color 0.2s, background 0.2s;
}
.lg-logout:hover { border-color: #cbd5e1; color: #475569; background: #f8fafc; }
</style>
