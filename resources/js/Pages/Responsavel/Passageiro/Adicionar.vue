<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    UserIcon, PhoneIcon, MapPinIcon, HomeIcon,
    AcademicCapIcon, ChevronDownIcon, ChevronUpIcon,
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

    embarque:  enderecoVazio(),

    residencia_mesmo_embarque: true,
    residencia: enderecoVazio(),

    desembarque_nome: '',
    desembarque: enderecoVazio(),
})

const showObsMedica = ref(false)

// ─── MÁSCARAS ────────────────────────────────────────────────────────────────
function maskCpf(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d+)$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    form.cpf = v
}

function maskTel(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 10)     v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d+)$/, '($1) $2')
    form.telefone = v
}

// ─── SUBMIT ──────────────────────────────────────────────────────────────────
function submit() {
    form.transform(data => ({
        nome:            data.nome,
        cpf:             data.cpf.replace(/\D/g, ''),
        data_nascimento: data.data_nascimento,
        telefone:        data.telefone.replace(/\D/g, '') || null,
        obs_medica:      data.obs_medica || null,
        embarques:  [{ ...data.embarque }],
        residencia: data.residencia_mesmo_embarque
            ? [{ ...data.embarque }]
            : [{ ...data.residencia }],
        desembarques: data.desembarque.logradouro
            ? [{ ...data.desembarque, nome: data.desembarque_nome }]
            : [],
    })).post(route('responsavel.passageiros.adicionar.store'))
}

const ic = 'w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition'
</script>

<template>
    <Head title="Adicionar Passageiro" />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50">

        <!-- Topo -->
        <header class="sticky top-0 z-20 bg-white border-b border-slate-200 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('responsavel.dashboard')"
                    class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-base font-bold text-slate-900" style="font-family:'Sora',sans-serif;">
                        Novo passageiro
                    </h1>
                    <p class="text-xs text-slate-400">Preencha os dados e endereços</p>
                </div>
            </div>
        </header>

        <div class="max-w-2xl mx-auto px-4 py-6 pb-24 space-y-4">
            <form @submit.prevent="submit" class="space-y-4">

                <!-- ── 1. DADOS PESSOAIS ──────────────────────────────────── -->
                <section class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-blue-100 bg-blue-50/60">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                            <UserIcon class="w-4 h-4 text-white" />
                        </div>
                        <h2 class="text-sm font-semibold text-blue-900" style="font-family:'Sora',sans-serif;">
                            Dados pessoais
                        </h2>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Nome -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                Nome completo <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.nome" type="text" placeholder="Nome do passageiro"
                                required :class="[ic, form.errors.nome ? 'border-red-400 focus:ring-red-400' : '']" />
                            <p v-if="form.errors.nome" class="text-red-500 text-xs mt-1">{{ form.errors.nome }}</p>
                        </div>

                        <!-- CPF + Nascimento -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                    CPF <span class="text-red-400">*</span>
                                </label>
                                <input :value="form.cpf" type="text" placeholder="000.000.000-00"
                                    maxlength="14" required @input="maskCpf"
                                    :class="[ic, form.errors.cpf ? 'border-red-400 focus:ring-red-400' : '']" />
                                <p v-if="form.errors.cpf" class="text-red-500 text-xs mt-1">{{ form.errors.cpf }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                    Data de nascimento <span class="text-red-400">*</span>
                                </label>
                                <input v-model="form.data_nascimento" type="date" required
                                    :class="[ic, form.errors.data_nascimento ? 'border-red-400 focus:ring-red-400' : '']" />
                                <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                            </div>
                        </div>

                        <!-- Telefone -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                Telefone / WhatsApp
                                <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
                            </label>
                            <div class="relative">
                                <PhoneIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <input :value="form.telefone" type="text"
                                    placeholder="(00) 00000-0000" maxlength="15" @input="maskTel"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            </div>
                        </div>

                        <!-- Obs médica colapsável -->
                        <div>
                            <button type="button" @click="showObsMedica = !showObsMedica"
                                class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wide hover:text-slate-700 transition w-full text-left">
                                <span>Observações médicas</span>
                                <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
                                <ChevronUpIcon v-if="showObsMedica" class="w-3.5 h-3.5 ml-auto" />
                                <ChevronDownIcon v-else class="w-3.5 h-3.5 ml-auto" />
                            </button>
                            <textarea v-if="showObsMedica" v-model="form.obs_medica" rows="3"
                                placeholder="Alergias, medicamentos, condições relevantes..."
                                class="mt-2 w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">
                            </textarea>
                        </div>
                    </div>
                </section>

                <!-- ── 2. EMBARQUE ─────────────────────────────────────────── -->
                <section class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-blue-100 bg-blue-50/60">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                            <MapPinIcon class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-blue-900" style="font-family:'Sora',sans-serif;">
                                Local de embarque
                            </h2>
                            <p class="text-xs text-blue-400 mt-0.5">Onde o motorista busca o passageiro</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <EnderecoSection v-model="form.embarque" />

                        <!-- Checkbox residência -->
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
                                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Endereço de residência</span>
                                </div>
                                <EnderecoSection v-model="form.residencia" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ── 3. ESCOLA / DESTINO ────────────────────────────────── -->
                <section class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-blue-100 bg-blue-50/60">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                            <AcademicCapIcon class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-blue-900" style="font-family:'Sora',sans-serif;">
                                Escola / Destino
                            </h2>
                            <p class="text-xs text-blue-400 mt-0.5">Para onde o motorista leva o passageiro</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                                Nome do local
                                <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
                            </label>
                            <input v-model="form.desembarque_nome" type="text"
                                placeholder="Ex: Escola Municipal João XXIII" :class="ic" />
                        </div>

                        <EnderecoSection v-model="form.desembarque" />
                    </div>
                </section>

                <!-- Erro geral -->
                <div v-if="form.errors.geral" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ form.errors.geral }}
                </div>

                <!-- Ações -->
                <div class="flex gap-3 pt-1">
                    <Link :href="route('responsavel.dashboard')"
                        class="px-6 py-3 min-h-[44px] rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center">
                        Cancelar
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="flex-1 py-3 min-h-[44px] rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-bold transition shadow-lg shadow-blue-200"
                        style="font-family:'Sora',sans-serif;">
                        {{ form.processing ? 'Salvando...' : 'Cadastrar passageiro' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
