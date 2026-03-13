<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'

const form = useForm({
    first_name: '', last_name: '', email: '',
    password: '', password_confirmation: '', cpf: '', phone: '',
})

const pageVisible = ref(false)
onMounted(() => setTimeout(() => pageVisible.value = true, 80))

const firstNameError = computed(() => {
    if (!form.first_name) return ''
    return form.first_name.length < 2 ? 'Nome deve ter pelo menos 2 caracteres' : ''
})
const lastNameError = computed(() => {
    if (!form.last_name) return ''
    return form.last_name.length < 2 ? 'Sobrenome deve ter pelo menos 2 caracteres' : ''
})
const emailError = computed(() => {
    if (!form.email) return ''
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email) ? '' : 'E-mail inválido'
})
const cpfError = computed(() => {
    if (!form.cpf) return ''
    return form.cpf.replace(/\D/g, '').length === 11 ? '' : 'CPF deve ter 11 números'
})
const phoneError = computed(() => {
    if (!form.phone) return ''
    return form.phone.replace(/\D/g, '').length >= 10 ? '' : 'Telefone inválido'
})
const passwordError = computed(() => {
    if (!form.password) return ''
    return form.password.length >= 6 ? '' : 'Senha deve ter no mínimo 6 caracteres'
})
const passwordConfirmationError = computed(() => {
    if (!form.password_confirmation) return ''
    return form.password_confirmation === form.password ? '' : 'As senhas não coincidem'
})

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

function PhoneMask(event) {
    let v = event.target.value.replace(/\D/g, '').substring(0, 11)
    if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d+)$/, '($1) $2')
    form.phone = v
}
function CPFMask(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    form.cpf = v
}

const submit = () => {
    form.post(route('register.responsavel.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Cadastro de Responsável" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <div class="reg-root">

        <!-- ========== ASIDE (esquerdo, apenas desktop) ========== -->
        <aside class="reg-aside" :class="{ 'reg-aside--in': pageVisible }">
            <div class="reg-aside__blob reg-aside__blob--1"></div>
            <div class="reg-aside__blob reg-aside__blob--2"></div>
            <div class="reg-aside__dots"></div>

            <Link href="http://localhost/rota-segura/public/" class="reg-aside__logo">
                <div class="reg-aside__logo-ring">
                    <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Logo Rota Segura" class="reg-aside__logo-img" />
                </div>
                <span class="reg-aside__logo-text">Rota Segura</span>
            </Link>

            <div class="reg-aside__body">
                <div class="reg-aside__tag">Para responsáveis</div>
                <h2 class="reg-aside__title">Segurança e tranquilidade para toda a família</h2>
                <p class="reg-aside__sub">Monitore o trajeto do seu filho em tempo real, com notificações instantâneas de embarque e desembarque.</p>
                <div class="reg-aside__perks">
                    <div class="reg-aside__perk">
                        <div class="reg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <span>Acompanhamento em tempo real</span>
                    </div>
                    <div class="reg-aside__perk">
                        <div class="reg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        </div>
                        <span>Notificações de embarque e desembarque</span>
                    </div>
                    <div class="reg-aside__perk">
                        <div class="reg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </div>
                        <span>Motoristas verificados e avaliados</span>
                    </div>
                    <div class="reg-aside__perk">
                        <div class="reg-aside__perk-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reg-perk-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                        <span>Gerencie múltiplos filhos facilmente</span>
                    </div>
                </div>
            </div>

            <p class="reg-aside__copy">© 2026 Rota Segura</p>
        </aside>

        <!-- ========== MAIN (formulário) ========== -->
        <main class="reg-main" :class="{ 'reg-main--in': pageVisible }">
            <div class="reg-form-wrap">

                <!-- Voltar -->
                <Link href="http://localhost/rota-segura/public/" class="reg-back">
                    <svg xmlns="http://www.w3.org/2000/svg" class="reg-back__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Voltar ao início
                </Link>

                <!-- Logo mobile -->
                <div class="reg-mobile-logo">
                    <div class="reg-mobile-logo__ring">
                        <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Logo Rota-Segura" class="reg-mobile-logo__img" />
                    </div>
                    <span class="reg-mobile-logo__text">Rota Segura</span>
                </div>

                <!-- Heading -->
                <div class="reg-heading">
                    <h1 class="reg-heading__title">Criar conta</h1>
                    <p class="reg-heading__sub">Cadastre-se como responsável e encontre uma van para seu filho.</p>
                </div>

                <!-- FORM -->
                <form @submit.prevent="submit" class="reg-form">

                    <!-- Nome + Sobrenome -->
                    <div class="reg-row">
                        <div class="reg-field">
                            <label class="reg-label">Nome</label>
                            <input v-model="form.first_name" type="text" placeholder="Arthur"
                                class="reg-input" :class="{ 'reg-input--error': form.errors.first_name || firstNameError }" />
                            <p v-if="form.errors.first_name || firstNameError" class="reg-error">{{ form.errors.first_name || firstNameError }}</p>
                        </div>
                        <div class="reg-field">
                            <label class="reg-label">Sobrenome</label>
                            <input v-model="form.last_name" type="text" placeholder="Trentin"
                                class="reg-input" :class="{ 'reg-input--error': form.errors.last_name || lastNameError }" />
                            <p v-if="form.errors.last_name || lastNameError" class="reg-error">{{ form.errors.last_name || lastNameError }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="reg-field">
                        <label class="reg-label">E-mail</label>
                        <div class="reg-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="reg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            <input v-model="form.email" type="email" placeholder="responsavel@email.com"
                                class="reg-input reg-input--icon" :class="{ 'reg-input--error': form.errors.email || emailError }" />
                        </div>
                        <p v-if="form.errors.email || emailError" class="reg-error">{{ form.errors.email || emailError }}</p>
                    </div>

                    <!-- CPF + Telefone -->
                    <div class="reg-row">
                        <div class="reg-field">
                            <label class="reg-label">CPF</label>
                            <input v-model="form.cpf" type="text" placeholder="000.000.000-00"
                                @input="CPFMask" maxlength="14"
                                class="reg-input" :class="{ 'reg-input--error': form.errors.cpf || cpfError }" />
                            <p v-if="form.errors.cpf || cpfError" class="reg-error">{{ form.errors.cpf || cpfError }}</p>
                        </div>
                        <div class="reg-field">
                            <label class="reg-label">Telefone</label>
                            <div class="reg-input-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" class="reg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                <input v-model="form.phone" type="tel" placeholder="(00) 00000-0000"
                                    @input="PhoneMask"
                                    class="reg-input reg-input--icon" :class="{ 'reg-input--error': form.errors.phone || phoneError }" />
                            </div>
                            <p v-if="form.errors.phone || phoneError" class="reg-error">{{ form.errors.phone || phoneError }}</p>
                        </div>
                    </div>

                    <!-- Senha -->
                    <div class="reg-field">
                        <label class="reg-label">Senha</label>
                        <div class="reg-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="reg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                placeholder="Mínimo 6 caracteres"
                                class="reg-input reg-input--icon reg-input--padright"
                                :class="{ 'reg-input--error': form.errors.password || passwordError }" />
                            <button type="button" class="reg-eye" @click="showPassword = !showPassword" tabindex="-1" :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'">
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="reg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="reg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password || passwordError" class="reg-error">{{ form.errors.password || passwordError }}</p>
                    </div>

                    <!-- Confirmar senha -->
                    <div class="reg-field">
                        <label class="reg-label">Confirmar senha</label>
                        <div class="reg-input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="reg-input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            <input v-model="form.password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'"
                                placeholder="Repita a senha"
                                class="reg-input reg-input--icon reg-input--padright"
                                :class="{ 'reg-input--error': form.errors.password_confirmation || passwordConfirmationError }" />
                            <button type="button" class="reg-eye" @click="showPasswordConfirmation = !showPasswordConfirmation" tabindex="-1" :aria-label="showPasswordConfirmation ? 'Ocultar senha' : 'Mostrar senha'">
                                <svg v-if="!showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="reg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="reg-eye__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation || passwordConfirmationError" class="reg-error">{{ form.errors.password_confirmation || passwordConfirmationError }}</p>
                    </div>

                    <!-- Erro geral -->
                    <div v-if="form.errors.geral" class="reg-error-box">
                        <svg xmlns="http://www.w3.org/2000/svg" class="reg-error-box__icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        {{ form.errors.geral }}
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="form.processing" class="reg-submit" :class="{ 'reg-submit--loading': form.processing }">
                        <span v-if="!form.processing" class="reg-submit__inner">
                            Criar conta
                            <svg xmlns="http://www.w3.org/2000/svg" class="reg-submit__arrow" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </span>
                        <span v-else class="reg-submit__spinner">
                            <svg class="reg-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Criando conta...
                        </span>
                    </button>
                </form>

                <p class="reg-login-hint">
                    Já tem conta?
                    <Link :href="route('login')" class="reg-login-link">Entrar</Link>
                </p>
            </div>
        </main>
    </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
* { font-family: 'Plus Jakarta Sans', sans-serif; }

/* === ROOT === */
.reg-root {
    min-height: 100vh;
    min-height: 100dvh; /* dynamic viewport for mobile browsers */
    display: flex;
    background: #f8fafc;
}

/* === ASIDE (hidden below lg) === */
.reg-aside {
    display: none;
    width: 40%;
    min-width: 320px;
    max-width: 480px;
    flex-direction: column;
    justify-content: space-between;
    padding: 36px 40px;
    background: linear-gradient(160deg, #2563eb 0%, #1d4ed8 55%, #1e40af 100%);
    border-radius: 0 24px 24px 0;
    position: relative; overflow: hidden;
    box-shadow: 12px 0 50px -8px rgba(37,99,235,0.3);
    opacity: 0; transform: translateX(-20px);
    transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
    flex-shrink: 0;
}
@media (min-width: 1024px) { .reg-aside { display: flex; } }
.reg-aside--in { opacity: 1; transform: translateX(0); }

/* Blobs */
.reg-aside__blob { position: absolute; border-radius: 50%; filter: blur(55px); pointer-events: none; }
.reg-aside__blob--1 {
    width: 340px; height: 340px;
    background: radial-gradient(circle, rgba(255,255,255,0.12), transparent 70%);
    top: -100px; left: -100px;
    animation: blobDrift 14s ease-in-out infinite alternate;
}
.reg-aside__blob--2 {
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(96,165,250,0.18), transparent 70%);
    bottom: 0; right: -50px;
    animation: blobDrift 18s ease-in-out infinite alternate-reverse;
}
@keyframes blobDrift {
    0% { transform: translate(0,0) scale(1); }
    100% { transform: translate(16px,24px) scale(1.08); }
}
.reg-aside__dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 26px 26px; pointer-events: none;
}

/* Logo */
.reg-aside__logo { display: flex; align-items: center; gap: 10px; text-decoration: none; position: relative; z-index: 2; transition: opacity 0.2s; }
.reg-aside__logo:hover { opacity: 0.82; }
.reg-aside__logo-ring {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25);
    display: flex; align-items: center; justify-content: center;
}
.reg-aside__logo-img { width: 22px; height: 22px; object-fit: contain; }
.reg-aside__logo-text { color: #fff; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 1.05rem; }

/* Body */
.reg-aside__body { position: relative; z-index: 2; }
.reg-aside__tag {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    color: #bfdbfe; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; padding: 4px 12px; border-radius: 999px; margin-bottom: 18px;
}
.reg-aside__title {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.4rem, 2vw, 1.9rem);
    font-weight: 900; color: #fff; line-height: 1.2;
    margin-bottom: 14px; letter-spacing: -0.02em;
}
.reg-aside__sub { color: rgba(255,255,255,0.62); font-size: 0.875rem; line-height: 1.7; margin-bottom: 28px; }

/* Perks */
.reg-aside__perks { display: flex; flex-direction: column; gap: 12px; }
.reg-aside__perk {
    display: flex; align-items: center; gap: 10px;
    color: rgba(255,255,255,0.85); font-size: 0.85rem; font-weight: 500;
}
.reg-aside__perk-icon {
    width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
    background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center; color: #fff;
    transition: background 0.25s, transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
}
@media (hover: hover) {
    .reg-aside__perk:hover .reg-aside__perk-icon { background: rgba(255,255,255,0.24); transform: scale(1.1); }
}
.reg-perk-svg { width: 14px; height: 14px; }

.reg-aside__copy { position: relative; z-index: 2; color: rgba(255,255,255,0.3); font-size: 0.75rem; }

/* === MAIN === */
.reg-main {
    flex: 1; min-width: 0;
    display: flex; align-items: flex-start; justify-content: center;
    padding: 28px 16px 48px;
    overflow-y: auto;
    opacity: 0; transform: translateY(16px);
    transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1) 0.08s, transform 0.8s cubic-bezier(0.16,1,0.3,1) 0.08s;
}
@media (min-width: 480px) { .reg-main { padding: 36px 24px 56px; align-items: center; } }
@media (min-width: 1024px) { .reg-main { padding: 48px 40px; } }
.reg-main--in { opacity: 1; transform: translateY(0); }

.reg-form-wrap { width: 100%; max-width: 480px; }

/* Voltar */
.reg-back {
    display: inline-flex; align-items: center; gap: 6px;
    color: #94a3b8; font-size: 0.8rem; font-weight: 500;
    text-decoration: none; margin-bottom: 24px;
    transition: color 0.2s ease, transform 0.2s ease;
}
.reg-back:hover { color: #475569; transform: translateX(-3px); }
.reg-back__icon { width: 14px; height: 14px; flex-shrink: 0; }

/* Logo mobile */
.reg-mobile-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
@media (min-width: 1024px) { .reg-mobile-logo { display: none; } }
.reg-mobile-logo__ring {
    width: 36px; height: 36px; border-radius: 50%;
    background: #eff6ff; border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.reg-mobile-logo__img { width: 22px; height: 22px; object-fit: contain; }
.reg-mobile-logo__text { font-family: 'Nunito', sans-serif; font-weight: 800; color: #0f172a; font-size: 1rem; }

/* Heading */
.reg-heading { margin-bottom: 24px; }
@media (min-width: 640px) { .reg-heading { margin-bottom: 28px; } }
.reg-heading__title {
    font-family: 'Nunito', sans-serif;
    font-size: clamp(1.6rem, 5vw, 2.2rem);
    font-weight: 900; color: #0f172a; letter-spacing: -0.025em; margin-bottom: 5px;
}
.reg-heading__sub { color: #64748b; font-size: 0.875rem; line-height: 1.6; }

/* Form */
.reg-form { display: flex; flex-direction: column; gap: 14px; }
@media (min-width: 480px) { .reg-form { gap: 16px; } }

.reg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (min-width: 480px) { .reg-row { gap: 14px; } }
/* On very small screens, stack vertically */
@media (max-width: 380px) { .reg-row { grid-template-columns: 1fr; } }

.reg-field { display: flex; flex-direction: column; gap: 4px; }

.reg-label { font-size: 0.68rem; font-weight: 700; color: #475569; letter-spacing: 0.08em; text-transform: uppercase; }
@media (min-width: 480px) { .reg-label { font-size: 0.72rem; } }

/* Input wrapper */
.reg-input-wrap { position: relative; }
.reg-input-icon {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px; color: #94a3b8; pointer-events: none; flex-shrink: 0;
}

.reg-eye {
    position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px;
    /* larger tap target on mobile */
    min-width: 36px; min-height: 36px;
    display: flex; align-items: center; justify-content: center;
    transition: color 0.2s;
}
.reg-eye:hover { color: #475569; }
.reg-eye__icon { width: 15px; height: 15px; }

/* Input */
.reg-input {
    width: 100%; padding: 11px 13px;
    border-radius: 11px; border: 1.5px solid #e2e8f0;
    background: #fff; color: #0f172a;
    font-size: 0.875rem; font-family: 'Plus Jakarta Sans', sans-serif;
    outline: none; -webkit-appearance: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
@media (min-width: 480px) { .reg-input { padding: 11px 14px; border-radius: 12px; } }
.reg-input::placeholder { color: #94a3b8; }
.reg-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fafcff; }
.reg-input--icon { padding-left: 38px; }
.reg-input--padright { padding-right: 44px; }
.reg-input--error { border-color: #f87171 !important; box-shadow: 0 0 0 3px rgba(248,113,113,0.1) !important; }

/* Error text */
.reg-error { color: #ef4444; font-size: 0.72rem; font-weight: 500; margin-top: 2px; }

/* Error box */
.reg-error-box {
    display: flex; align-items: center; gap: 8px;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: 10px; padding: 10px 14px;
    color: #dc2626; font-size: 0.82rem; font-weight: 500;
}
.reg-error-box__icon { width: 16px; height: 16px; flex-shrink: 0; }

/* Submit */
.reg-submit {
    width: 100%; padding: 13px 24px; margin-top: 4px;
    background: #2563eb; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.95rem;
    border: none; border-radius: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    /* Minimum touch target */
    min-height: 48px;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease, background 0.2s ease;
}
@media (hover: hover) {
    .reg-submit:hover:not(:disabled) { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.45); }
}
.reg-submit:active:not(:disabled) { transform: scale(0.98); }
.reg-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.reg-submit__inner { display: flex; align-items: center; gap: 8px; }
.reg-submit__arrow { width: 15px; height: 15px; transition: transform 0.25s ease; }
@media (hover: hover) { .reg-submit:hover .reg-submit__arrow { transform: translateX(3px); } }

.reg-submit__spinner { display: flex; align-items: center; gap: 10px; }
.reg-spin { width: 18px; height: 18px; animation: spin 0.8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* Login hint */
.reg-login-hint { text-align: center; margin-top: 18px; font-size: 0.875rem; color: #64748b; }
.reg-login-link { color: #2563eb; font-weight: 700; text-decoration: none; margin-left: 4px; }
.reg-login-link:hover { text-decoration: underline; }
</style>