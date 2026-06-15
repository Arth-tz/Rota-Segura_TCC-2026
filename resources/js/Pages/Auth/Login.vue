<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const pageVisible = ref(false)
const showPassword = ref(false)
onMounted(() => setTimeout(() => pageVisible.value = true, 80))

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Entrar" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

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
                <div class="lg-aside__tag">Bem-vindo de volta</div>
                <h2 class="lg-aside__title">Tudo o que você precisa está aqui</h2>
                <p class="lg-aside__sub">Acesse sua conta e continue acompanhando o trajeto do seu filho com segurança e tranquilidade.</p>
                <div class="lg-aside__perks">
                    <div class="lg-aside__perk">
                        <div class="lg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="lg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <span>Acompanhamento em tempo real</span>
                    </div>
                    <div class="lg-aside__perk">
                        <div class="lg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="lg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        </div>
                        <span>Confirmação de embarque e desembarque</span>
                    </div>
                    <div class="lg-aside__perk">
                        <div class="lg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="lg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        </div>
                        <span>Vans com o preço ideal para você</span>
                    </div>
                </div>
            </div>

            <p class="lg-aside__copy">© 2026 Rota Segura</p>
        </aside>

        <!-- ========== MAIN ========== -->
        <main class="lg-main" :class="{ 'lg-main--in': pageVisible }">
            <div class="lg-form-wrap">

                <!-- Voltar -->
                <Link :href="route('home')" class="lg-back">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-back__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Voltar ao início
                </Link>

                <!-- Logo mobile -->
                <div class="lg-mobile-logo">
                    <div class="lg-mobile-logo__ring">
                        <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Logo Rota-Segura" class="lg-mobile-logo__img" />
                    </div>
                    <span class="lg-mobile-logo__text">Rota Segura</span>
                </div>

                <!-- Heading -->
                <div class="lg-heading">
                    <h1 class="lg-heading__title">Entrar</h1>
                    <p class="lg-heading__sub">Acesse sua conta e aproveite todas as funcionalidades.</p>
                </div>

                <!-- Status (ex: link de redefinição enviado) -->
                <div v-if="status" class="lg-status">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-status__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ status }}
                </div>

                <!-- FORM -->
                <form @submit.prevent="submit" class="lg-form">

                    <!-- Email -->
                    <div class="lg-field">
                        <label class="lg-label">E-mail</label>
                        <div class="lg-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="lg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="usuario@email.com"
                                autofocus
                                autocomplete="username"
                                class="lg-input lg-input--icon"
                                :class="{ 'lg-input--error': form.errors.email }"
                            />
                        </div>
                        <p v-if="form.errors.email" class="lg-error">{{ form.errors.email }}</p>
                    </div>

                    <!-- Senha -->
                    <div class="lg-field">
                        <div class="lg-label-row">
                            <label class="lg-label">Senha</label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="lg-forgot">
                                Esqueceu sua senha?
                            </Link>
                        </div>
                        <div class="lg-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="lg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Sua senha"
                                autocomplete="current-password"
                                class="lg-input lg-input--icon lg-input--padright"
                                :class="{ 'lg-input--error': form.errors.password }"
                            />
                            <button type="button" class="lg-eye" @click="showPassword = !showPassword" tabindex="-1" :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'">
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="lg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="lg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="lg-error">{{ form.errors.password }}</p>
                    </div>

                    <!-- Lembrar de mim -->
                    <div class="lg-remember">
                        <label class="lg-remember__label">
                            <div class="lg-checkbox-wrap">
                                <input id="remember" v-model="form.remember" type="checkbox" class="lg-checkbox" />
                                <div class="lg-checkbox__custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lg-checkbox__check" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                            </div>
                            <span>Lembrar de mim</span>
                        </label>
                    </div>

                    <!-- Erro geral -->
                    <div v-if="form.errors.geral" class="lg-error-box">
                        <svg xmlns="http://www.w3.org/2000/svg" class="lg-error-box__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        {{ form.errors.geral }}
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="form.processing" class="lg-submit">
                        <span v-if="!form.processing" class="lg-submit__inner">
                            Entrar
                            <svg xmlns="http://www.w3.org/2000/svg" class="lg-submit__arrow" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </span>
                        <span v-else class="lg-submit__spinner">
                            <svg class="lg-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Entrando...
                        </span>
                    </button>
                </form>

                <p class="lg-register-hint">
                    Ainda não tem conta?
                    <Link :href="route('home')" class="lg-register-link">Cadastre-se</Link>
                </p>

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
.lg-aside__sub { color: rgba(255,255,255,0.62); font-size: 0.875rem; line-height: 1.7; margin-bottom: 32px; }

.lg-aside__perks { display: flex; flex-direction: column; gap: 14px; }
.lg-aside__perk {
    display: flex; align-items: center; gap: 10px;
    color: rgba(255,255,255,0.85); font-size: 0.875rem; font-weight: 500;
}
.lg-aside__perk-icon {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
    background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center; color: #fff;
    transition: background 0.25s, transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
}
@media (hover: hover) {
    .lg-aside__perk:hover .lg-aside__perk-icon { background: rgba(255,255,255,0.24); transform: scale(1.1); }
}
.lg-perk-svg { width: 15px; height: 15px; }
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

/* Como o login é curto, centralizar verticalmente fica ótimo */
.lg-form-wrap { width: 100%; max-width: 480px; }

/* Voltar */
.lg-back {
    display: inline-flex; align-items: center; gap: 6px;
    color: #94a3b8; font-size: 0.8rem; font-weight: 500;
    text-decoration: none; margin-bottom: 28px;
    transition: color 0.2s ease, transform 0.2s ease;
}
.lg-back:hover { color: #475569; transform: translateX(-3px); }
.lg-back__icon { width: 14px; height: 14px; flex-shrink: 0; }

/* Logo mobile */
.lg-mobile-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
@media (min-width: 1024px) { .lg-mobile-logo { display: none; } }
.lg-mobile-logo__ring {
    width: 36px; height: 36px; border-radius: 50%;
    background: #eff6ff; border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.lg-mobile-logo__img { width: 22px; height: 22px; object-fit: contain; }
.lg-mobile-logo__text { font-family: 'Nunito', sans-serif; font-weight: 800; color: #0f172a; font-size: 1rem; }

/* Heading */
.lg-heading { margin-bottom: 28px; }
.lg-heading__title {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.8rem, 5vw, 2.4rem);
    font-weight: 900; color: #0f172a; letter-spacing: -0.03em; margin-bottom: 6px;
}
.lg-heading__sub { color: #64748b; font-size: 0.9rem; line-height: 1.6; }

/* Status */
.lg-status {
    display: flex; align-items: center; gap: 10px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 12px; padding: 12px 16px;
    color: #15803d; font-size: 0.85rem; font-weight: 500;
    margin-bottom: 20px;
}
.lg-status__icon { width: 16px; height: 16px; flex-shrink: 0; }

/* Form */
.lg-form { display: flex; flex-direction: column; gap: 18px; }

.lg-field { display: flex; flex-direction: column; gap: 5px; }

.lg-label { font-size: 0.72rem; font-weight: 700; color: #475569; letter-spacing: 0.08em; text-transform: uppercase; }

.lg-label-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1px; }
.lg-forgot { font-size: 0.78rem; color: #2563eb; font-weight: 600; text-decoration: none; transition: color 0.2s; }
.lg-forgot:hover { color: #1d4ed8; text-decoration: underline; }

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

.lg-error { color: #ef4444; font-size: 0.75rem; font-weight: 500; }

/* Lembrar de mim — checkbox customizado */
.lg-remember { display: flex; align-items: center; }
.lg-remember__label {
    display: flex; align-items: center; gap: 10px;
    cursor: pointer; user-select: none;
    color: #475569; font-size: 0.875rem; font-weight: 500;
}
.lg-checkbox-wrap { position: relative; display: flex; align-items: center; }
.lg-checkbox { position: absolute; opacity: 0; width: 0; height: 0; }
.lg-checkbox__custom {
    width: 18px; height: 18px; border-radius: 5px;
    border: 2px solid #cbd5e1; background: #fff;
    display: flex; align-items: center; justify-content: center;
    transition: border-color 0.2s, background 0.2s;
    flex-shrink: 0;
}
.lg-checkbox:checked ~ .lg-checkbox__custom {
    background: #2563eb; border-color: #2563eb;
}
.lg-checkbox__check {
    width: 10px; height: 10px; color: #fff;
    opacity: 0; transform: scale(0.6);
    transition: opacity 0.15s, transform 0.15s cubic-bezier(0.34,1.56,0.64,1);
}
.lg-checkbox:checked ~ .lg-checkbox__custom .lg-checkbox__check {
    opacity: 1; transform: scale(1);
}
.lg-remember__label:hover .lg-checkbox__custom { border-color: #2563eb; }

/* Error box */
.lg-error-box {
    display: flex; align-items: center; gap: 8px;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: 10px; padding: 10px 14px;
    color: #dc2626; font-size: 0.82rem; font-weight: 500;
}
.lg-error-box__icon { width: 16px; height: 16px; flex-shrink: 0; }

/* Submit */
.lg-submit {
    width: 100%; padding: 14px 24px; margin-top: 4px;
    background: #2563eb; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.95rem;
    border: none; border-radius: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    min-height: 50px;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease, background 0.2s ease;
}
@media (hover: hover) {
    .lg-submit:hover:not(:disabled) { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.45); }
}
.lg-submit:active:not(:disabled) { transform: scale(0.98); }
.lg-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.lg-submit__inner { display: flex; align-items: center; gap: 8px; }
.lg-submit__arrow { width: 15px; height: 15px; transition: transform 0.25s ease; }
@media (hover: hover) { .lg-submit:hover .lg-submit__arrow { transform: translateX(3px); } }

.lg-submit__spinner { display: flex; align-items: center; gap: 10px; }
.lg-spin { width: 18px; height: 18px; animation: spin 0.8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* Register hint */
.lg-register-hint { text-align: center; margin-top: 22px; font-size: 0.875rem; color: #64748b; }
.lg-register-link { color: #2563eb; font-weight: 700; text-decoration: none; margin-left: 4px; }
.lg-register-link:hover { text-decoration: underline; }
</style>
