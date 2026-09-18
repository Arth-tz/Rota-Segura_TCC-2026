<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import FlashMessage from '@/Components/UI/FlashMessage.vue'
import {
    ArrowLeftIcon,
    LockClosedIcon,
    DocumentTextIcon,
    IdentificationIcon,
    CheckCircleIcon,
    ArrowUpTrayIcon,
    ArrowTopRightOnSquareIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    motorista: { type: Object, required: true },
})

const formCnh = useForm({ arquivo: null })
const formCertidao = useForm({ arquivo: null })
const formCurso = useForm({ arquivo: null })


const inputCnh = ref(null)
const inputCertidao = ref(null)
const inputCurso = ref(null)


function enviarCnh() {
    if (!inputCnh.value?.files[0]) return
    formCnh.arquivo = inputCnh.value.files[0]
    formCnh.post(route('motorista.documentos.upload', 'cnh'), {
        forceFormData: true,
        onSuccess: () => { formCnh.reset(); if (inputCnh.value) inputCnh.value.value = '' },
    })
}

function enviarCertidao() {
    if (!inputCertidao.value?.files[0]) return
    formCertidao.arquivo = inputCertidao.value.files[0]
    formCertidao.post(route('motorista.documentos.upload', 'certidao'), {
        forceFormData: true,
        onSuccess: () => { formCertidao.reset(); if (inputCertidao.value) inputCertidao.value.value = '' },
    })
}

function enviarCurso() {
    if (!inputCurso.value?.files[0]) return
    formCurso.arquivo = inputCurso.value.files[0]
    formCurso.post(route('motorista.documentos.upload', 'curso_transporte'), {
        forceFormData: true,
        onSuccess: () => { formCurso.reset(); if (inputCurso.value) inputCurso.value.value = '' },
    })
}

const statusConfig = {
    aprovado:  { label: 'Aprovado',  classes: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    pendente:  { label: 'Pendente',  classes: 'bg-amber-50 text-amber-700 border-amber-200' },
    rejeitado: { label: 'Rejeitado', classes: 'bg-red-50 text-red-600 border-red-200' },
}
</script>

<template>
    <Head title="Documentos Pessoais" />
    <FlashMessage />

    <div class="min-h-screen bg-slate-50">

        <!-- Header -->
        <header class="bg-gradient-to-b from-amber-700 to-amber-800 px-4 py-4 shadow-md">
            <div class="max-w-2xl mx-auto flex items-center gap-3">
                <Link :href="route('motorista.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div>
                    <h1 class="text-white font-bold text-base leading-tight">Documentos Pessoais</h1>
                    <p class="text-amber-200 text-xs">CNH, certidão, SEST SENAT e RENACH</p>
                </div>
                <div class="ml-auto">
                    <span class="text-xs px-2.5 py-1 rounded-full border font-semibold"
                        :class="statusConfig[motorista.status_aprovacao]?.classes ?? 'bg-slate-50 text-slate-500 border-slate-200'">
                        {{ statusConfig[motorista.status_aprovacao]?.label ?? motorista.status_aprovacao }}
                    </span>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-5 pb-12">

            <!-- Aviso de privacidade -->
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                <LockClosedIcon class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                <p class="text-xs text-slate-500 leading-relaxed">
                    Seus documentos são enviados de forma segura e utilizados <strong class="text-slate-700">apenas para verificação pelo administrador</strong>.
                    Eles <strong class="text-slate-700">nunca são compartilhados</strong> com responsáveis ou terceiros.
                </p>
            </div>

            <!-- CNH -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden"
                :class="!motorista.cnh_foto_url ? 'ring-2 ring-amber-400' : ''">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-700 flex items-center justify-center shrink-0">
                        <IdentificationIcon class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-slate-800">Carteira Nacional de Habilitação (CNH)</h2>
                        <p class="text-xs text-slate-500">Foto da frente da CNH (JPG, PNG ou PDF, até 5 MB)</p>
                    </div>
                    <CheckCircleIcon v-if="motorista.cnh_foto_url" class="w-5 h-5 text-emerald-500 shrink-0" />
                    <ExclamationTriangleIcon v-else class="w-5 h-5 text-amber-500 shrink-0" />
                </div>

                <div class="px-5 py-4 space-y-3">
                    <!-- Metadados CNH -->
                    <div class="grid grid-cols-3 gap-3 text-xs">
                        <div>
                            <p class="text-slate-400 mb-0.5">Número</p>
                            <p class="font-medium text-slate-700">{{ motorista.cnh_numero ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 mb-0.5">Categoria</p>
                            <p class="font-medium text-slate-700">{{ motorista.cnh_categoria ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 mb-0.5">Validade</p>
                            <p class="font-medium text-slate-700">{{ motorista.cnh_validade ?? '—' }}</p>
                        </div>
                    </div>

                    <!-- Arquivo atual -->
                    <div v-if="motorista.cnh_foto_url"
                        class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5">
                        <DocumentTextIcon class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span class="text-xs text-emerald-700 flex-1">Arquivo enviado</span>
                        <a :href="motorista.cnh_foto_url" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:underline">
                            <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                            Visualizar
                        </a>
                    </div>

                    <!-- Upload -->
                    <form @submit.prevent="enviarCnh" class="flex gap-2">
                        <input ref="inputCnh" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="flex-1 text-xs text-slate-600 file:mr-3 file:text-xs file:font-semibold
                                   file:rounded-lg file:border-0 file:bg-amber-50 file:text-amber-700
                                   file:px-3 file:py-2 hover:file:bg-amber-100 file:transition
                                   border border-slate-200 rounded-xl px-2 py-2 bg-white" />
                        <button type="submit" :disabled="formCnh.processing"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800
                                   disabled:opacity-60 text-white text-xs font-semibold transition shrink-0">
                            <ArrowUpTrayIcon class="w-3.5 h-3.5" />
                            {{ formCnh.processing ? 'Enviando…' : (motorista.cnh_foto_url ? 'Substituir' : 'Enviar') }}
                        </button>
                    </form>
                    <p v-if="formCnh.errors.arquivo" class="text-red-500 text-xs">{{ formCnh.errors.arquivo }}</p>
                </div>
            </section>

            <!-- Certidão de Antecedentes -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <div class="w-8 h-8 rounded-xl bg-slate-600 flex items-center justify-center shrink-0">
                        <DocumentTextIcon class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-slate-800">Certidão de Antecedentes Criminais</h2>
                        <p class="text-xs text-slate-500">Recomendada — pode levar alguns dias para emitir (JPG, PNG ou PDF, até 5 MB)</p>
                    </div>
                    <CheckCircleIcon v-if="motorista.certidao_antecedentes_url" class="w-5 h-5 text-emerald-500 shrink-0" />
                </div>

                <div class="px-5 py-4 space-y-3">
                    <p class="text-xs text-slate-500">
                        <strong class="text-slate-700">Recomendada</strong> pelo CONTRAN para transporte escolar remunerado.
                        Disponível no site do Tribunal de Justiça do seu estado ou nos Correios — o processo pode levar alguns dias.
                    </p>

                    <!-- Arquivo atual -->
                    <div v-if="motorista.certidao_antecedentes_url"
                        class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5">
                        <DocumentTextIcon class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span class="text-xs text-emerald-700 flex-1">Arquivo enviado</span>
                        <a :href="motorista.certidao_antecedentes_url" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:underline">
                            <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                            Visualizar
                        </a>
                    </div>

                    <!-- Upload -->
                    <form @submit.prevent="enviarCertidao" class="flex gap-2">
                        <input ref="inputCertidao" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="flex-1 text-xs text-slate-600 file:mr-3 file:text-xs file:font-semibold
                                   file:rounded-lg file:border-0 file:bg-amber-50 file:text-amber-700
                                   file:px-3 file:py-2 hover:file:bg-amber-100 file:transition
                                   border border-slate-200 rounded-xl px-2 py-2 bg-white" />
                        <button type="submit" :disabled="formCertidao.processing"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800
                                   disabled:opacity-60 text-white text-xs font-semibold transition shrink-0">
                            <ArrowUpTrayIcon class="w-3.5 h-3.5" />
                            {{ formCertidao.processing ? 'Enviando…' : (motorista.certidao_antecedentes_url ? 'Substituir' : 'Enviar') }}
                        </button>
                    </form>
                    <p v-if="formCertidao.errors.arquivo" class="text-red-500 text-xs">{{ formCertidao.errors.arquivo }}</p>
                </div>
            </section>

            <!-- Certificado SEST SENAT -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <div class="w-8 h-8 rounded-xl bg-slate-600 flex items-center justify-center shrink-0">
                        <DocumentTextIcon class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-slate-800">Certificado SEST SENAT</h2>
                        <p class="text-xs text-slate-500">Recomendado — requer conclusão de curso presencial ou online (JPG, PNG ou PDF, até 5 MB)</p>
                    </div>
                    <CheckCircleIcon v-if="motorista.curso_transporte_url" class="w-5 h-5 text-emerald-500 shrink-0" />
                </div>

                <div class="px-5 py-4 space-y-3">
                    <p class="text-xs text-slate-500">
                        <strong class="text-slate-700">Recomendado</strong> pelo CTB Art. 138, IV para quem transporta escolares.
                        Faça o curso no portal <strong class="text-slate-700">sestnsenat.org.br</strong> → "Portal do Aluno" → "Histórico de Cursos".
                        Visível apenas pelo administrador; responsáveis veem somente o status (concluído/pendente).
                    </p>

                    <div v-if="motorista.curso_transporte_url"
                        class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5">
                        <DocumentTextIcon class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span class="text-xs text-emerald-700 flex-1">Arquivo enviado</span>
                        <a :href="motorista.curso_transporte_url" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:underline">
                            <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                            Visualizar
                        </a>
                    </div>

                    <form @submit.prevent="enviarCurso" class="flex gap-2">
                        <input ref="inputCurso" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="flex-1 text-xs text-slate-600 file:mr-3 file:text-xs file:font-semibold
                                   file:rounded-lg file:border-0 file:bg-amber-50 file:text-amber-700
                                   file:px-3 file:py-2 hover:file:bg-amber-100 file:transition
                                   border border-slate-200 rounded-xl px-2 py-2 bg-white" />
                        <button type="submit" :disabled="formCurso.processing"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800
                                   disabled:opacity-60 text-white text-xs font-semibold transition shrink-0">
                            <ArrowUpTrayIcon class="w-3.5 h-3.5" />
                            {{ formCurso.processing ? 'Enviando…' : (motorista.curso_transporte_url ? 'Substituir' : 'Enviar') }}
                        </button>
                    </form>
                    <p v-if="formCurso.errors.arquivo" class="text-red-500 text-xs">{{ formCurso.errors.arquivo }}</p>
                </div>
            </section>

        </main>
    </div>
</template>
