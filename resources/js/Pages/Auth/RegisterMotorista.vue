<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { CameraIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline'

const form = useForm({
    nome: '',
    email: '',
    password: '',
    password_confirmation: '',
    cpf: '',
    telefone: '',
    data_nascimento: '',
    cnh_numero: '',
    cnh_categoria: '',
    cnh_validade: '',
    foto: null,
})

// ─── FOTO DE PERFIL ──────────────────────────────────────────────────────────
const fotoPreview = ref(null)
const fileInput   = ref(null)

function handleFoto(e) {
    const file = e.target.files[0]
    if (!file) return
    fotoPreview.value = URL.createObjectURL(file)
    form.foto = file
}

// ─── MÁSCARAS ────────────────────────────────────────────────────────────────
function maskCpf(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    e.target.value = v
    form.cpf = v
}

function maskTelefone(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 10)     v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d+)$/, '($1) $2')
    e.target.value = v
    form.telefone = v
}

function maskDateBr(e, field) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 8)
    if (v.length > 4)      v = v.replace(/^(\d{2})(\d{2})(\d{0,4}).*$/, '$1/$2/$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,2}).*$/, '$1/$2')
    e.target.value = v
    form[field] = v
}

function parseDateBrToIso(value) {
    const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/)
    if (!match) return value
    const [, dd, mm, yyyy] = match
    return `${yyyy}-${mm}-${dd}`
}

// ─── TOGGLE SENHAS ───────────────────────────────────────────────────────────
const showPassword        = ref(false)
const showPasswordConfirm = ref(false)

// ─── SUBMIT ──────────────────────────────────────────────────────────────────
function submit() {
    form.data_nascimento = parseDateBrToIso(form.data_nascimento)
    form.cnh_validade    = parseDateBrToIso(form.cnh_validade)
    form.post(route('register.motorista.store'), {
        forceFormData: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

const inputClass = computed(() => (err) =>
    `w-full rounded-xl border px-4 py-3 text-sm bg-white text-slate-900 placeholder-slate-400 outline-none transition focus:ring-2 focus:border-transparent ${
        err ? 'border-red-300 bg-red-50 focus:ring-red-200' : 'border-slate-200 focus:border-amber-600 focus:ring-amber-100'
    }`
)
</script>

<template>
    <Head title="Cadastro de Motorista" />

    <div class="min-h-screen bg-slate-50 flex">

        <!-- ── ASIDE (âmbar) ─────────────────────────────────────────────── -->
        <aside class="hidden lg:flex lg:w-5/12 xl:w-[42%] flex-col justify-between p-10 xl:p-12 sticky top-0 h-screen overflow-y-auto z-10
                       bg-gradient-to-br from-amber-700 via-amber-700 to-amber-800
                       shadow-[12px_0_40px_-12px_rgba(0,0,0,0.25)]">

            <Link :href="route('home')" class="inline-flex items-center">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-9 w-auto">
            </Link>

            <div>
                <p class="text-xs font-semibold text-amber-200 tracking-widest uppercase mb-3">Para motoristas</p>
                <h2 class="text-3xl font-extrabold text-white leading-tight mb-4">
                    Conecte sua van<br>a famílias da sua cidade
                </h2>
                <p class="text-amber-100 text-sm leading-relaxed mb-8">
                    Cadastre-se como motorista verificado e comece a receber solicitações de responsáveis que buscam transporte escolar seguro.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Clientes verificados</p>
                            <p class="text-xs text-amber-200 mt-0.5">Responsáveis com cadastro confirmado e perfil completo.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Renda previsível</p>
                            <p class="text-xs text-amber-200 mt-0.5">Mensalidades fixas com passageiros vinculados à sua van.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Gestão no celular</p>
                            <p class="text-xs text-amber-200 mt-0.5">Rotas, passageiros e trajetos em tempo real pelo painel.</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-amber-300 text-xs">© 2026 Rota Segura</p>
        </aside>

        <!-- ── FORMULÁRIO ─────────────────────────────────────────────────── -->
        <main class="flex-1 flex items-start justify-center px-5 py-10 overflow-y-auto">
            <div class="w-full max-w-xl">

                <!-- Banner mobile (substitui o aside) -->
                <div class="lg:hidden rounded-2xl bg-gradient-to-br from-amber-700 to-amber-800 px-5 py-5 mb-6 text-white">
                    <Link :href="route('home')" class="inline-flex items-center gap-2 mb-4 hover:opacity-80 transition">
                        <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-7 w-7">
                        <span class="font-bold text-sm">Rota Segura</span>
                    </Link>
                    <p class="text-xs font-semibold text-amber-200 tracking-widest uppercase mb-1.5">Para motoristas</p>
                    <p class="text-lg font-extrabold leading-snug">Conecte sua van a famílias verificadas</p>
                    <p class="text-sm text-amber-100 mt-1.5">Gerencie passageiros, rotas e receba mensalidades de forma previsível.</p>
                </div>

                <Link :href="route('home')"
                    class="hidden lg:inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 transition mb-6">
                    <ArrowLeftIcon class="w-4 h-4" />
                    Voltar ao início
                </Link>

                <!-- Cabeçalho -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-900">Cadastro de motorista</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Já tem conta?
                        <Link :href="route('login')" class="text-amber-700 font-semibold hover:text-amber-800">Entrar</Link>
                    </p>
                </div>

                <!-- ── FOTO DE PERFIL ─────────────────────────────────── -->
                <div class="flex items-center gap-5 mb-8 pb-8 border-b border-slate-100">
                    <button type="button" @click="fileInput.click()"
                        class="relative group shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 rounded-full">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-amber-100 border-2 border-amber-200 flex items-center justify-center">
                            <img v-if="fotoPreview" :src="fotoPreview" class="w-full h-full object-cover" alt="Foto de perfil" />
                            <span v-else class="text-2xl font-bold text-amber-600">{{ form.nome?.charAt(0)?.toUpperCase() || 'M' }}</span>
                        </div>
                        <div class="absolute inset-0 rounded-full bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <CameraIcon class="w-6 h-6 text-white" />
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-amber-700 rounded-full border-2 border-white flex items-center justify-center">
                            <CameraIcon class="w-3.5 h-3.5 text-white" />
                        </div>
                    </button>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Foto de perfil</p>
                        <p class="text-xs text-slate-500 mt-0.5">Responsáveis veem sua foto ao solicitar uma vaga. Aumenta a confiança.</p>
                        <button type="button" @click="fileInput.click()"
                            class="mt-1.5 text-xs text-amber-700 font-semibold hover:text-amber-800 transition">
                            {{ fotoPreview ? 'Alterar foto' : 'Adicionar foto' }}
                        </button>
                        <p v-if="form.errors.foto" class="text-xs text-red-500 mt-1">{{ form.errors.foto }}</p>
                    </div>
                    <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleFoto" />
                </div>

                <form @submit.prevent="submit" autocomplete="off" class="space-y-6">

                    <!-- ── DADOS PESSOAIS ─────────────────────────────────── -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-semibold text-slate-800 mb-3">Dados pessoais</legend>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome completo</label>
                            <input v-model="form.nome" type="text" placeholder="Como aparecerá no seu perfil" required autofocus
                                :class="inputClass(form.errors.nome)" />
                            <p v-if="form.errors.nome" class="text-red-500 text-xs mt-1">{{ form.errors.nome }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">E-mail</label>
                            <input v-model="form.email" type="email" placeholder="voce@email.com" required
                                :class="inputClass(form.errors.email)" />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">CPF</label>
                                <input :value="form.cpf" @input="maskCpf" type="text" placeholder="000.000.000-00" maxlength="14" required
                                    :class="inputClass(form.errors.cpf)" />
                                <p v-if="form.errors.cpf" class="text-red-500 text-xs mt-1">{{ form.errors.cpf }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefone</label>
                                <input :value="form.telefone" @input="maskTelefone" type="text" placeholder="(00) 00000-0000" required
                                    :class="inputClass(form.errors.telefone)" />
                                <p v-if="form.errors.telefone" class="text-red-500 text-xs mt-1">{{ form.errors.telefone }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Data de nascimento</label>
                            <input :value="form.data_nascimento" @input="maskDateBr($event, 'data_nascimento')"
                                type="text" inputmode="numeric" maxlength="10" placeholder="DD/MM/AAAA" required
                                :class="inputClass(form.errors.data_nascimento)" />
                            <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                        </div>
                    </fieldset>

                    <!-- ── CNH ───────────────────────────────────────────── -->
                    <fieldset class="space-y-4 pt-2">
                        <legend class="text-sm font-semibold text-slate-800 mb-3">Habilitação (CNH)</legend>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Número da CNH</label>
                                <input v-model="form.cnh_numero" type="text" maxlength="11" placeholder="00000000000" required
                                    :class="inputClass(form.errors.cnh_numero)" />
                                <p v-if="form.errors.cnh_numero" class="text-red-500 text-xs mt-1">{{ form.errors.cnh_numero }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoria</label>
                                <select v-model="form.cnh_categoria" required
                                    :class="inputClass(form.errors.cnh_categoria)">
                                    <option value="" disabled>—</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                </select>
                                <p v-if="form.errors.cnh_categoria" class="text-red-500 text-xs mt-1">{{ form.errors.cnh_categoria }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Validade da CNH</label>
                            <input :value="form.cnh_validade" @input="maskDateBr($event, 'cnh_validade')"
                                type="text" inputmode="numeric" maxlength="10" placeholder="DD/MM/AAAA" required
                                :class="inputClass(form.errors.cnh_validade)" />
                            <p v-if="form.errors.cnh_validade" class="text-red-500 text-xs mt-1">{{ form.errors.cnh_validade }}</p>
                        </div>
                    </fieldset>

                    <!-- ── SENHA ──────────────────────────────────────────── -->
                    <fieldset class="space-y-4 pt-2">
                        <legend class="text-sm font-semibold text-slate-800 mb-3">Senha de acesso</legend>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Senha</label>
                                <div class="relative">
                                    <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                                        :class="inputClass(form.errors.password) + ' pr-10'" />
                                    <button type="button" tabindex="-1" @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                        <svg v-if="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar</label>
                                <div class="relative">
                                    <input v-model="form.password_confirmation" :type="showPasswordConfirm ? 'text' : 'password'" required
                                        :class="inputClass(null) + ' pr-10'" />
                                    <button type="button" tabindex="-1" @click="showPasswordConfirm = !showPasswordConfirm"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                        <svg v-if="showPasswordConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Erro geral -->
                    <div v-if="form.errors.geral"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ form.errors.geral }}
                    </div>

                    <!-- CTA -->
                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-xl bg-amber-700 hover:bg-amber-800 disabled:opacity-60 disabled:cursor-not-allowed
                               text-white font-semibold py-3.5 text-sm transition shadow-sm shadow-amber-900/20">
                        {{ form.processing ? 'Criando conta…' : 'Criar conta e cadastrar van →' }}
                    </button>

                </form>
            </div>
        </main>
    </div>
</template>
