<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { CheckCircleIcon, ExclamationTriangleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const page    = usePage()
const visible = ref(false)
const current = ref({ type: '', text: '' })

const config = {
    sucesso: { icon: CheckCircleIcon,        bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-800', icon: 'text-emerald-500' },
    aviso:   { icon: ExclamationTriangleIcon, bg: 'bg-amber-50',   border: 'border-amber-200',   text: 'text-amber-800',   icon: 'text-amber-500'   },
    erro:    { icon: XCircleIcon,             bg: 'bg-red-50',     border: 'border-red-200',     text: 'text-red-800',     icon: 'text-red-500'     },
}

watch(() => page.props.flash, (flash) => {
    if (!flash) return
    const type = flash.sucesso ? 'sucesso' : flash.aviso ? 'aviso' : flash.erro ? 'erro' : null
    if (!type) return
    current.value = { type, text: flash[type] }
    visible.value = true
    setTimeout(() => { visible.value = false }, 4000)
}, { immediate: true, deep: true })

const cfg = computed(() => config[current.value.type] ?? config.sucesso)
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2">
        <div v-if="visible"
            class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm mx-auto px-4">
            <div class="flex items-start gap-3 rounded-2xl border px-4 py-3 shadow-lg"
                :class="[cfg.bg, cfg.border]">
                <component :is="cfg.icon" class="w-5 h-5 shrink-0 mt-0.5" :class="cfg.icon" />
                <p class="flex-1 text-sm font-medium" :class="cfg.text">{{ current.text }}</p>
                <button @click="visible = false" class="shrink-0 opacity-60 hover:opacity-100 transition">
                    <XMarkIcon class="w-4 h-4" :class="cfg.text" />
                </button>
            </div>
        </div>
    </Transition>
</template>
