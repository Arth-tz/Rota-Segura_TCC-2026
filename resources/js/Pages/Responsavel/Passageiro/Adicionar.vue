<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    UserIcon, PhoneIcon, MapPinIcon, HomeIcon,
    AcademicCapIcon, ChevronDownIcon, ChevronUpIcon,
    ArrowLeftIcon, CameraIcon,
} from '@heroicons/vue/24/outline'
import EnderecoSection from '@/Components/Responsavel/EnderecoSection.vue'

const enderecoVazio = () => ({
    logradouro: '', numero: '', complemento: '', bairro: '',
    cidade: '', estado: '', cep: '', latitude: '', longitude: '',
})

const form = useForm({
    nome:            '',
    cpf:             '',
    data_nascimento: '',
    telefone:        '',
    obs_medica:      '',
    foto:            null,

    embarque:  enderecoVazio(),

    residencia_mesmo_embarque: true,
    residencia: enderecoVazio(),

    desembarque_nome: '',
    desembarque: enderecoVazio(),

    foto_consentimento_lgpd: false,
})

// ─── FOTO ────────────────────────────────────────────────────────────────────
const fotoPreview = ref(null)
const fileInput   = ref(null)

function handleFoto(e) {
    const file = e.target.files[0]
    if (!file) return
    fotoPreview.value = URL.createObjectURL(file)
    form.foto = file
}

const showObsMedica = ref(false)

// ─── MÁSCARAS ────────────────────────────────────────────────────────────────
function maskCpf(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d+)$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    e.target.value = v
    form.cpf = v
}

function maskTel(e) {
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

// ─── SUBMIT ──────────────────────────────────────────────────────────────────
function submit() {
    form.transform(data => ({
        nome:            data.nome,
        cpf:             data.cpf.replace(/\D/g, ''),
        data_nascimento: parseDateBrToIso(data.data_nascimento),
        telefone:        data.telefone.replace(/\D/g, '') || null,
        obs_medica:      data.obs_medica || null,
        foto:            data.foto,
        foto_consentimento_lgpd: data.foto_consentimento_lgpd,
        embarques:  [{ ...data.embarque }],
        residencia: data.residencia_mesmo_embarque
            ? [{ ...data.embarque }]
            : [{ ...data.residencia }],
        desembarques: data.desembarque.logradouro
            ? [{ ...data.desembarque, nome: data.desembarque_nome }]
            : [],
    })).post(route('responsavel.passageiros.adicionar.store'), { forceFormData: true })
}

const inputClass = computed(() => (err) =>
    `w-full px-4 py-3 rounded-xl border text-sm bg-white text-slate-900 placeholder-slate-400 outline-none transition focus:ring-2 focus:border-transparent ${
        err ? 'border-red-300 bg-red-50 focus:ring-red-200' : 'border-slate-200 focus:border-blue-400 focus:ring-blue-100'
    }`
)
</script>

<template>
    <Head title="Adicionar passageiro" />

    <div class="min-h-screen bg-slate-50">

        <!-- ── TOPBAR ─────────────────────────────────────────────────────── -->
        <header class="sticky top-0 z-20 bg-white border-b border-slate-200 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('responsavel.dashboard')"
                    class="w-9 h-9 rounded-xl hover:bg-slate-100 transition flex items-center justify-center shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-slate-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Novo passageiro</h1>
                    <p class="text-xs text-slate-400">Preencha os dados e endereços</p>
                </div>
            </div>
        </header>

        <div class="max-w-2xl mx-auto px-4 py-6 pb-24">
            <form @submit.prevent="submit" class="space-y-4">

                <!-- ── DADOS PESSOAIS ─────────────────────────────────────── -->
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                            <UserIcon class="w-4 h-4 text-white" />
                        </div>
                        <h2 class="text-lg font-semibold text-slate-800">Dados pessoais</h2>
                    </div>

                    <div class="p-5 space-y-4">

                        <!-- Foto de perfil -->
                        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                            <button type="button" @click="fileInput.click()"
                                class="relative group shrink-0 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-blue-100 border-2 border-blue-200 flex items-center justify-center">
                                    <img v-if="fotoPreview" :src="fotoPreview" class="w-full h-full object-cover" alt="Foto" />
                                    <span v-else class="text-blue-400 font-bold text-lg">
                                        {{ form.nome?.charAt(0)?.toUpperCase() || '?' }}
                                    </span>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <CameraIcon class="w-5 h-5 text-white" />
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-600 rounded-full border-2 border-white flex items-center justify-center">
                                    <CameraIcon class="w-3 h-3 text-white" />
                                </div>
                            </button>
                            <div>
                                <p class="text-sm font-medium text-slate-700">Foto do passageiro</p>
                                <p class="text-xs text-slate-400 mt-0.5">Opcional — ajuda o motorista a identificar</p>
                                <button type="button" @click="fileInput.click()"
                                    class="mt-1 text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                                    {{ fotoPreview ? 'Alterar' : 'Adicionar foto' }}
                                </button>
                                <p v-if="form.errors.foto" class="text-xs text-red-500 mt-1">{{ form.errors.foto }}</p>
                            </div>
                            <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp"
                                class="hidden" @change="handleFoto" />
                        </div>

                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Nome completo <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.nome" type="text" placeholder="Nome do passageiro" required
                                :class="inputClass(form.errors.nome)" />
                            <p v-if="form.errors.nome" class="text-red-500 text-xs mt-1">{{ form.errors.nome }}</p>
                        </div>

                        <!-- CPF + Nascimento -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    CPF <span class="text-red-400">*</span>
                                </label>
                                <input :value="form.cpf" type="text" placeholder="000.000.000-00"
                                    maxlength="14" required @input="maskCpf"
                                    :class="inputClass(form.errors.cpf)" />
                                <p v-if="form.errors.cpf" class="text-red-500 text-xs mt-1">{{ form.errors.cpf }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Data de nascimento <span class="text-red-400">*</span>
                                </label>
                                <input :value="form.data_nascimento" type="text"
                                    inputmode="numeric" maxlength="10" placeholder="DD/MM/AAAA"
                                    required @input="maskDateBr($event, 'data_nascimento')"
                                    :class="inputClass(form.errors.data_nascimento)" />
                                <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                            </div>
                        </div>

                        <!-- Telefone -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Telefone / WhatsApp
                                <span class="text-slate-400 font-normal">(opcional)</span>
                            </label>
                            <div class="relative">
                                <PhoneIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input :value="form.telefone" type="text"
                                    placeholder="(00) 00000-0000" maxlength="15" @input="maskTel"
                                    :class="inputClass(null) + ' pl-10'" />
                            </div>
                        </div>

                        <!-- Obs médica colapsável -->
                        <div>
                            <button type="button" @click="showObsMedica = !showObsMedica"
                                class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-slate-900 transition w-full text-left">
                                <span>Observações médicas</span>
                                <span class="text-slate-400 font-normal">(opcional)</span>
                                <ChevronUpIcon v-if="showObsMedica" class="w-4 h-4 ml-auto text-slate-400" />
                                <ChevronDownIcon v-else class="w-4 h-4 ml-auto text-slate-400" />
                            </button>
                            <textarea v-if="showObsMedica" v-model="form.obs_medica" rows="3"
                                placeholder="Alergias, medicamentos, condições relevantes…"
                                :class="inputClass(null) + ' mt-2 resize-none'">
                            </textarea>
                        </div>

                    </div>
                </section>

                <!-- ── LOCAL DE EMBARQUE ───────────────────────────────────── -->
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                            <MapPinIcon class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">Local de embarque</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Onde o motorista busca o passageiro</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <EnderecoSection v-model="form.embarque" />

                        <div class="pt-3 border-t border-slate-100">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input v-model="form.residencia_mesmo_embarque" type="checkbox"
                                    class="w-4 h-4 mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer shrink-0" />
                                <div>
                                    <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">
                                        Residência neste mesmo endereço
                                    </span>
                                    <p class="text-xs text-slate-400 mt-0.5">O passageiro mora onde embarca</p>
                                </div>
                            </label>

                            <div v-if="!form.residencia_mesmo_embarque" class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                                <div class="flex items-center gap-2">
                                    <HomeIcon class="w-4 h-4 text-slate-400" />
                                    <span class="text-sm font-medium text-slate-700">Endereço de residência</span>
                                </div>
                                <EnderecoSection v-model="form.residencia" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ── ESCOLA / DESTINO ───────────────────────────────────── -->
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                            <AcademicCapIcon class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">Escola / Destino</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Para onde o motorista leva o passageiro</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Nome do local
                                <span class="text-slate-400 font-normal">(opcional)</span>
                            </label>
                            <input v-model="form.desembarque_nome" type="text"
                                placeholder="Ex: Escola Municipal João XXIII"
                                :class="inputClass(null)" />
                        </div>

                        <EnderecoSection v-model="form.desembarque" />
                    </div>
                </section>

                <!-- LGPD: consentimento para foto -->
                <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3.5">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" v-model="form.foto_consentimento_lgpd"
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer shrink-0" />
                        <span class="text-sm text-slate-700 leading-relaxed">
                            Autorizo o uso de foto do passageiro para identificação no embarque e desembarque,
                            conforme o <strong class="text-slate-800">Art. 14 da LGPD</strong>.
                            A foto será usada exclusivamente pelo motorista contratado para identificar o passageiro.
                        </span>
                    </label>
                    <p v-if="form.errors.foto_consentimento_lgpd" class="text-red-500 text-xs mt-1.5 pl-7">
                        {{ form.errors.foto_consentimento_lgpd }}
                    </p>
                </div>

                <!-- Erro geral -->
                <div v-if="form.errors.geral"
                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    {{ form.errors.geral }}
                </div>

                <!-- Ações -->
                <div class="flex gap-3 pt-1">
                    <Link :href="route('responsavel.dashboard')"
                        class="px-6 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center">
                        Cancelar
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="flex-1 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-bold transition shadow-sm shadow-blue-200">
                        {{ form.processing ? 'Salvando…' : 'Cadastrar passageiro' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
