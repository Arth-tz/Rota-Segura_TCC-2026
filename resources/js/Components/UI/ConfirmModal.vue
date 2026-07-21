<script setup>
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    show:         { type: Boolean, default: false },
    eyebrow:      { type: String, default: '' },
    title:        { type: String, required: true },
    message:      { type: String, default: '' },
    confirmLabel: { type: String, default: 'Confirmar' },
    cancelLabel:  { type: String, default: 'Cancelar' },
    variant:      { type: String, default: 'danger' }, // 'danger' | 'success'
    processing:   { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'close'])

const VARIANTS = {
    danger:  { button: 'bg-red-600 hover:bg-red-700' },
    success: { button: 'bg-emerald-600 hover:bg-emerald-700' },
}
</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="show"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="emit('close')">
                <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div>
                            <p v-if="eyebrow" class="text-xs text-slate-400 uppercase tracking-widest">{{ eyebrow }}</p>
                            <h3 class="font-bold text-slate-900 mt-0.5">
                                {{ title }}
                            </h3>
                        </div>
                        <button @click="emit('close')"
                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition shrink-0">
                            <XMarkIcon class="w-4 h-4 text-slate-500" />
                        </button>
                    </div>

                    <div v-if="message" class="px-5 py-4">
                        <p class="text-sm text-slate-600">{{ message }}</p>
                    </div>

                    <div class="px-5 pb-5 pt-1 flex gap-3">
                        <button @click="emit('close')"
                            class="flex-1 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            {{ cancelLabel }}
                        </button>
                        <button @click="emit('confirm')" :disabled="processing"
                            class="flex-1 py-3 rounded-xl text-white text-sm font-bold transition shadow-sm disabled:opacity-60"
                            :class="VARIANTS[variant].button">
                            {{ processing ? 'Aguarde…' : confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
