<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    passos: { type: Array, required: true },
})

const emit = defineEmits(['concluir', 'irPara'])

const passoAtual = ref(0)
const visivel = ref(true)

const passo = computed(() => props.passos[passoAtual.value])
const total = computed(() => props.passos.length)
const ultimo = computed(() => passoAtual.value === total.value - 1)

function proximo() {
    if (ultimo.value) {
        concluir()
        return
    }
    passoAtual.value++
    const novoPasso = props.passos[passoAtual.value]
    if (novoPasso.secao) emit('irPara', novoPasso.secao)
}

function concluir() {
    visivel.value = false
    emit('concluir')
    router.post(route('tour.concluir'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <Teleport to="body">
        <Transition name="tour-fade">
            <div v-if="visivel" class="fixed inset-0 z-[9998] flex items-end sm:items-center justify-center pointer-events-none">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-slate-900/55 pointer-events-auto" @click="concluir" />

                <!-- Card -->
                <Transition name="tour-slide">
                    <div class="relative z-10 w-full max-w-sm mx-4 mb-20 sm:mb-0 pointer-events-auto">
                        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                            <!-- Barra de progresso -->
                            <div class="h-1 bg-slate-100">
                                <div
                                    class="h-full bg-blue-500 transition-all duration-500 ease-out"
                                    :style="{ width: `${((passoAtual + 1) / total) * 100}%` }"
                                />
                            </div>

                            <div class="p-6">
                                <p class="text-xs text-slate-400 font-medium mb-4">
                                    Passo {{ passoAtual + 1 }} de {{ total }}
                                </p>

                                <div class="flex items-start gap-3 mb-3">
                                    <div
                                        v-if="passo.icone"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                        :class="passo.iconeBg ?? 'bg-blue-50'"
                                    >
                                        <component
                                            :is="passo.icone"
                                            class="w-5 h-5"
                                            :class="passo.iconeColor ?? 'text-blue-600'"
                                        />
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900 leading-tight pt-1.5">
                                        {{ passo.titulo }}
                                    </h3>
                                </div>

                                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                                    {{ passo.corpo }}
                                </p>

                                <div class="flex items-center justify-between gap-3">
                                    <button
                                        @click="concluir"
                                        class="text-sm text-slate-400 hover:text-slate-600 transition-colors"
                                    >
                                        Pular tour
                                    </button>
                                    <button
                                        @click="proximo"
                                        class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors"
                                    >
                                        {{ ultimo ? 'Concluir' : 'Próximo' }}
                                        <ChevronRightIcon v-if="!ultimo" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.tour-fade-enter-active, .tour-fade-leave-active { transition: opacity 0.3s ease; }
.tour-fade-enter-from, .tour-fade-leave-to { opacity: 0; }

.tour-slide-enter-active {
    transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease;
}
.tour-slide-leave-active {
    transition: transform 0.2s ease, opacity 0.2s ease;
}
.tour-slide-enter-from { transform: translateY(20px); opacity: 0; }
.tour-slide-leave-to   { transform: translateY(8px);  opacity: 0; }
</style>
