<script setup>
import { ref, onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    password: '',
})

const showPassword = ref(false)
const pageVisible  = ref(false)

onMounted(() => setTimeout(() => pageVisible.value = true, 80))

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Confirmar senha" />

    <div class="lg-root">

        <!-- ========== ASIDE ========== -->
        <aside class="lg-aside" :class="{ 'lg-aside--in': pageVisible }">
            <div class="lg-aside__blob lg-aside__blob--1"></div>
            <div class="lg-aside__blob lg-aside__blob--2"></div>
            <div class="lg-aside__dots"></div>

            <a href="/" class="lg-aside__logo">
                <div class="lg-aside__logo-ring">
                    <img src="/images/Logo_rota-segura_branco.png" alt="Logo Rota Segura" class="lg-aside__logo-img" />
                </div>
                <span class="lg-aside__logo-text">Rota Segura</span>
            </a>

            <div class="lg-aside__body">
                <div class="lg-aside__tag">Área segura</div>
                <h2 class="lg-aside__title">Confirme sua identidade</h2>
                <p class="lg-aside__sub">Por segurança, precisamos verificar sua senha antes de continuar.</p>
            </div>

            <p class="lg-aside__copy">© 2026 Rota Segura</p>
        </aside>

        <!-- ========== MAIN ========== -->
        <main class="lg-main" :class="{ 'lg-main--in': pageVisible }">
            <div class="lg-form-wrap">

                <!-- Logo mobile -->
                <div class="lg-mobile-logo">
                    <div class="lg-mobile-logo__ring">
                        <img src="/images/Logo_rota_segura-azul.png" alt="Rota Segura" class="lg-mobile-logo__img" />
                    </div>
                    <span class="lg-mobile-logo__text">Rota Segura</span>
                </div>

                <!-- Ícone de cadeado -->
                <div class="lg-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>

                <!-- Heading -->
                <div class="lg-heading">
                    <h1 class="lg-heading__title">Confirmar senha</h1>
                    <p class="lg-heading__sub">Esta é uma área segura. Digite sua senha para confirmar que é você.</p>
                </div>

                <!-- Erro -->
                <div v-if="form.errors.password" class="lg-error-box">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-error-box__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    {{ form.errors.password }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="lg-form">
                    <div class="lg-field">
                        <label class="lg-label">Sua senha</label>
                        <div class="lg-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="lg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Digite sua senha"
                                autocomplete="current-password"
                                autofocus
                                class="lg-input lg-input--icon lg-input--padright"
                                :class="{ 'lg-input--error': form.errors.password }"
                            />
                            <button type="button" class="lg-eye" @click="showPassword = !showPassword" tabindex="-1" :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'">
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="lg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="lg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" :disabled="form.processing" class="lg-submit">
                        <span v-if="!form.processing" class="lg-submit__inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="lg-submit__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            Confirmar e continuar
                        </span>
                        <span v-else class="lg-submit__spinner">
                            <svg class="lg-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Verificando...
                        </span>
                    </button>
                </form>

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

.lg-icon-wrap {
    width: 64px; height: 64px; border-radius: 18px;
    background: #eff6ff; border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 24px;
}
.lg-icon { width: 30px; height: 30px; color: #2563eb; }

.lg-heading { margin-bottom: 24px; }
.lg-heading__title {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.8rem, 5vw, 2.4rem);
    font-weight: 900; color: #0f172a; letter-spacing: -0.03em; margin-bottom: 6px;
}
.lg-heading__sub { color: #64748b; font-size: 0.9rem; line-height: 1.6; }

.lg-error-box {
    display: flex; align-items: center; gap: 8px;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: 10px; padding: 10px 14px;
    color: #dc2626; font-size: 0.82rem; font-weight: 500;
    margin-bottom: 18px;
}
.lg-error-box__icon { width: 16px; height: 16px; flex-shrink: 0; }

.lg-form { display: flex; flex-direction: column; gap: 18px; }
.lg-field { display: flex; flex-direction: column; gap: 5px; }
.lg-label { font-size: 0.72rem; font-weight: 700; color: #475569; letter-spacing: 0.08em; text-transform: uppercase; }

.lg-input-wrap { position: relative; }
.lg-input-icon {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    width: 16px; height: 16px; color: #94a3b8; pointer-events: none;
}
.lg-eye {
    position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px;
    min-width: 36px; min-height: 36px;
    display: flex; align-items: center; justify-content: center;
    transition: color 0.2s;
}
.lg-eye:hover { color: #475569; }
.lg-eye__icon { width: 16px; height: 16px; }

.lg-input {
    width: 100%; padding: 13px 14px;
    border-radius: 12px; border: 1.5px solid #e2e8f0;
    background: #fff; color: #0f172a;
    font-size: 0.9rem; font-family: 'Plus Jakarta Sans', sans-serif;
    outline: none; -webkit-appearance: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.lg-input::placeholder { color: #94a3b8; }
.lg-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fafcff; }
.lg-input--icon { padding-left: 40px; }
.lg-input--padright { padding-right: 44px; }
.lg-input--error { border-color: #f87171 !important; box-shadow: 0 0 0 3px rgba(248,113,113,0.1) !important; }

.lg-submit {
    width: 100%; padding: 14px 24px; margin-top: 4px;
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
</style>
