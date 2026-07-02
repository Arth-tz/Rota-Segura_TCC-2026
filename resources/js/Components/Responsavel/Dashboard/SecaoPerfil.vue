<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { UserCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    usuario: { type: Object, default: null },
})

function formatarTelefone(tel) {
    if (!tel) return '—'
    const n = tel.replace(/\D/g, '')
    if (n.length === 11) return n.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    if (n.length === 10) return n.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3')
    return tel
}

const showLogoutModal = ref(false)
</script>

<template>
    <div class="max-w-lg space-y-4">

        <!-- Card dados -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Cabeçalho -->
            <div class="flex items-center gap-4 px-5 py-4 border-b border-blue-100 bg-blue-50/60">
                <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-lg">{{ usuario?.nome?.charAt(0)?.toUpperCase() ?? '?' }}</span>
                </div>
                <div>
                    <p class="font-bold text-slate-900" style="font-family:'Sora',sans-serif;">{{ usuario?.nome ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ usuario?.email ?? '—' }}</p>
                </div>
                <span class="ml-auto text-xs px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200 font-semibold shrink-0">
                    Responsável
                </span>
            </div>

            <!-- Dados -->
            <div class="divide-y divide-slate-100">
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-slate-500">Nome</span>
                    <span class="text-sm font-medium text-slate-900">{{ usuario?.nome ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-slate-500">E-mail</span>
                    <span class="text-sm font-medium text-slate-900">{{ usuario?.email ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-slate-500">Telefone</span>
                    <span class="text-sm font-medium text-slate-900">{{ formatarTelefone(usuario?.telefone) }}</span>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="grid grid-cols-2 gap-3">
            <Link
                :href="route('responsavel.perfil.edit')"
                class="flex items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-sm"
                style="font-family:'Sora',sans-serif;"
            >
                Editar perfil
            </Link>
            <button
                @click="showLogoutModal = true"
                class="flex items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition"
            >
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
                                <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1" style="font-family:'Sora',sans-serif;">Deseja sair?</h3>
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
.pop-enter-active { transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
.pop-leave-active { transition: all 0.15s ease-in; }
.pop-enter-from { opacity: 0; transform: scale(0.92) translateY(16px); }
.pop-leave-to { opacity: 0; transform: scale(0.96); }
</style>
