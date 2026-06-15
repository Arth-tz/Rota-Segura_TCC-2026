<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    usuario: { type: Object, default: null },
})

// Função para formatar o telefone
const formatarTelefone = (tel) => {
    if (!tel) return '—'
    
    const n = tel.replace(/\D/g, '')

    if (n.length === 11) {
        return n.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    } else if (n.length === 10) {
        return n.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3')
    }
    
    return tel
}
const showLogoutModal = ref(false)

const openModal = () => (showLogoutModal.value = true)
const closeModal = () => (showLogoutModal.value = false)
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-[1fr_0.85fr]">
        <div class="rounded-[2rem] bg-gradient-to-br from-sky-700 via-blue-600 to-indigo-600 p-6 text-white shadow-2xl shadow-slate-900/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-100/80">Seu perfil</p>
                    <h2 class="mt-2 text-3xl font-semibold" style="font-family:'Sora',sans-serif;">Olá, {{ usuario?.nome ?? 'Responsável' }}</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-sky-100/85">Este é o espaço onde você gerencia suas informações, acessa opções rápidas e mantém seus dados atualizados.</p>
                </div>
                <div class="rounded-3xl bg-white/10 px-4 py-3 text-sm text-sky-100">
                    <p class="font-semibold">Responsável ativo</p>
                    <p class="mt-1 text-sky-100/80">Acesso ao painel de passageiros</p>
                </div>
            </div>
        </div>

        <div class="grid gap-4">
            <div class="rounded-[2rem] bg-white/95 p-6 shadow-xl shadow-slate-900/5">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-blue-700 text-2xl font-bold">
                        {{ usuario?.nome?.charAt(0)?.toUpperCase() ?? '?' }}
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-slate-900" style="font-family:'Sora',sans-serif;">{{ usuario?.nome ?? '-' }}</p>
                        <p class="text-sm text-slate-500">{{ usuario?.email ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-sm text-slate-500">Nome</span>
                        <span class="text-sm font-semibold text-slate-900">{{ usuario?.nome ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-sm text-slate-500">E-mail</span>
                        <span class="text-sm font-semibold text-slate-900">{{ usuario?.email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-sm text-slate-500">Telefone</span>
                        <span class="text-sm font-semibold text-slate-900">{{ formatarTelefone(usuario?.telefone) ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-sm text-slate-500">Perfil</span>
                        <span class="text-xs rounded-full bg-blue-50 px-3 py-1 font-semibold text-blue-600 border border-blue-100">Responsável</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white/95 p-6 shadow-xl shadow-slate-900/5">
                <p class="text-sm uppercase tracking-[0.24em] text-sky-600">Ações rápidas</p>
                <div class="mt-4 grid gap-3">
                    <Link
                        :href="route('profile.edit')"
                        class="w-full inline-flex items-center justify-center rounded-full bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/10 hover:bg-blue-700 transition"
                        style="font-family:'Sora',sans-serif;"
                    >
                        Editar perfil
                    </Link>
                    <button
                        @click="openModal"
                        class="w-full rounded-full border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition"
                    >
                        Sair da conta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="showLogoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                <Transition name="pop">
                    <div v-if="showLogoutModal" class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100">
                        <div class="flex flex-col items-center text-center">
                            <!-- Ícone de Alerta -->
                            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                            </div>
                            
                            <h3 class="text-xl font-bold text-slate-900 mb-2" style="font-family:'Sora',sans-serif;">Deseja sair?</h3>
                            <p class="text-slate-500 text-sm mb-6">Você precisará fazer login novamente para acessar suas informações.</p>
                            
                            <div class="flex flex-col w-full gap-2">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-xl transition"
                                >
                                    Sim, sair agora
                                </Link>
                                <button 
                                    @click="closeModal"
                                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold py-3 rounded-xl transition"
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
/* Animação de Fade para o Fundo */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Animação de "Pop" para o Card */
.pop-enter-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.pop-leave-active {
    transition: all 0.2s ease-in;
}
.pop-enter-from {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
}
.pop-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>