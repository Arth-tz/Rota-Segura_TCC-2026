<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
    ArrowRightOnRectangleIcon,
    TruckIcon,
    PencilSquareIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    XCircleIcon,
    IdentificationIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    usuario:   { type: Object, default: null },
    motorista: { type: Object, default: null },
    van:       { type: Object, default: null },
})

const statusAprovacaoLabel = computed(() => ({
    aprovado:  { text: 'Aprovada',   cls: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    pendente:  { text: 'Em análise', cls: 'bg-amber-100 text-amber-700 border-amber-200' },
    reprovado: { text: 'Reprovada',  cls: 'bg-red-100 text-red-700 border-red-200' },
}[props.van?.status_aprovacao ?? 'pendente'] ?? { text: 'Pendente', cls: 'bg-slate-100 text-slate-600 border-slate-200' }))

const showLogoutModal = ref(false)
</script>

<template>
    <div class="max-w-lg space-y-4">

        <!-- Card dados -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-4 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                <div class="h-12 w-12 rounded-full overflow-hidden bg-amber-700 flex items-center justify-center shrink-0">
                    <img v-if="usuario?.foto_url" :src="usuario.foto_url" class="w-full h-full object-cover" alt="" />
                    <span v-else class="text-white font-bold text-lg">{{ usuario?.nome?.charAt(0)?.toUpperCase() ?? 'M' }}</span>
                </div>
                <div>
                    <p class="font-bold text-slate-900">{{ usuario?.nome ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ usuario?.email ?? '—' }}</p>
                </div>
                <span class="ml-auto text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200 font-semibold shrink-0">
                    Motorista
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">Nome</span>
                    <span class="font-medium text-slate-900">{{ usuario?.nome ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">E-mail</span>
                    <span class="font-medium text-slate-900">{{ usuario?.email ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">CNH</span>
                    <span class="font-medium text-slate-900">{{ motorista?.cnh_numero ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">Categoria</span>
                    <span class="font-medium text-slate-900">{{ motorista?.cnh_categoria ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">Validade CNH</span>
                    <span class="font-medium text-slate-900">
                        {{ motorista?.cnh_validade ? new Date(motorista.cnh_validade + 'T00:00:00').toLocaleDateString('pt-BR') : '—' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Documentos Pessoais -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden"
            :class="(!motorista?.has_cnh_foto || !motorista?.has_certidao) ? 'border-amber-200' : 'border-slate-200'">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-slate-100 bg-slate-50/60">
                <IdentificationIcon class="w-4 h-4 text-amber-600" />
                <h3 class="text-sm font-semibold text-slate-800">Documentos Pessoais</h3>
                <span v-if="!motorista?.has_cnh_foto || !motorista?.has_certidao"
                    class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                    Pendente
                </span>
                <span v-else class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                    Enviados
                </span>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div class="grid grid-cols-2 gap-1.5 text-xs">
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <CheckCircleIcon v-if="motorista?.has_cnh_foto" class="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                        <XCircleIcon v-else class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                        <span>Foto da CNH</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <CheckCircleIcon v-if="motorista?.has_certidao" class="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                        <XCircleIcon v-else class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                        <span>Certidão criminal</span>
                    </div>
                </div>
                <Link :href="route('motorista.documentos.index')"
                    class="flex items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 px-3 py-2.5 text-xs font-semibold text-amber-800 transition w-full">
                    <DocumentTextIcon class="w-3.5 h-3.5" />
                    Gerenciar documentos pessoais
                </Link>
            </div>
        </div>

        <!-- Minha Van -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-slate-100 bg-slate-50/60">
                <TruckIcon class="w-4 h-4 text-amber-600" />
                <h3 class="text-sm font-semibold text-slate-800">Minha Van</h3>
            </div>

            <!-- Van cadastrada -->
            <div v-if="van" class="px-5 py-4 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-14 w-14 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                        <img v-if="van.foto_url" :src="van.foto_url" class="w-full h-full object-cover" alt="" />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <TruckIcon class="w-7 h-7 text-slate-400" />
                        </div>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 text-base">{{ van.placa }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">
                            {{ van.marca }} {{ van.modelo }} {{ van.ano_fabricacao }}
                        </p>
                        <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded-full border"
                            :class="statusAprovacaoLabel.cls">
                            {{ statusAprovacaoLabel.text }}
                        </span>
                    </div>
                </div>

                <!-- Alerta foto ausente -->
                <div v-if="!van.foto_url"
                    class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">
                    <ExclamationCircleIcon class="w-4 h-4 text-amber-500 shrink-0" />
                    <span>Adicione a <strong>foto frontal</strong> para aparecer no marketplace.</span>
                </div>

                <!-- Status docs -->
                <div class="grid grid-cols-2 gap-1.5 text-xs">
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <CheckCircleIcon v-if="van.documentacao_completa" class="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                        <XCircleIcon v-else class="w-3.5 h-3.5 text-slate-300 shrink-0" />
                        <span>Docs completos</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <CheckCircleIcon v-if="van.foto_url" class="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                        <XCircleIcon v-else class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                        <span>Foto frontal</span>
                    </div>
                </div>

                <!-- Botões de edição -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <Link :href="route('motorista.van.edit')"
                        class="flex items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 px-3 py-2.5 text-xs font-semibold text-amber-800 transition">
                        <PencilSquareIcon class="w-3.5 h-3.5" />
                        Editar dados e fotos
                    </Link>
                    <Link :href="route('motorista.van.documentos')"
                        class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 px-3 py-2.5 text-xs font-semibold text-slate-700 transition">
                        <DocumentTextIcon class="w-3.5 h-3.5" />
                        Documentos
                    </Link>
                </div>
            </div>

            <!-- Nenhuma van -->
            <div v-else class="px-5 py-5 flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center">
                    <TruckIcon class="w-6 h-6 text-slate-400" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Nenhuma van cadastrada</p>
                    <p class="text-xs text-slate-400 mt-0.5">Cadastre sua van para aparecer no marketplace.</p>
                </div>
                <Link :href="route('motorista.van.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-amber-700 hover:bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white transition shadow-sm">
                    <TruckIcon class="w-4 h-4" />
                    Cadastrar van
                </Link>
            </div>
        </div>

        <!-- Ações -->
        <div class="grid grid-cols-2 gap-3">
            <Link :href="route('motorista.perfil.edit')"
                class="flex items-center justify-center rounded-xl bg-amber-700 hover:bg-amber-800 px-4 py-3 text-sm font-semibold text-white transition shadow-sm">
                Editar perfil
            </Link>
            <button @click="showLogoutModal = true"
                class="flex items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                Sair da conta
            </button>
        </div>
    </div>

    <!-- Modal logout -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="showLogoutModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
                @click.self="showLogoutModal = false">
                <Transition name="pop">
                    <div v-if="showLogoutModal" class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-slate-100">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mb-4">
                                <ArrowRightOnRectangleIcon class="w-7 h-7 text-red-500" />
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Deseja sair?</h3>
                            <p class="text-sm text-slate-500 mb-6">Você precisará fazer login novamente para acessar o painel.</p>
                            <div class="flex flex-col w-full gap-2">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-xl transition text-sm"
                                >
                                    Sair agora
                                </Link>
                                <button
                                    @click="showLogoutModal = false"
                                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold py-3 rounded-xl transition text-sm"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.pop-enter-active { transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.pop-leave-active { transition: all 0.15s ease-in; }
.pop-enter-from { opacity: 0; transform: scale(0.92) translateY(16px); }
.pop-leave-to { opacity: 0; transform: scale(0.96); }
</style>
