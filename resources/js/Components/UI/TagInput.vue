<script setup>
import { ref } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'Digite e pressione Enter' },
})
const emit = defineEmits(['update:modelValue'])

const input = ref('')

function add() {
    const val = input.value.trim()
    if (val && !props.modelValue.includes(val)) {
        emit('update:modelValue', [...props.modelValue, val])
    }
    input.value = ''
}

function remove(item) {
    emit('update:modelValue', props.modelValue.filter(v => v !== item))
}

function onKeydown(e) {
    if (e.key === 'Enter') { e.preventDefault(); add() }
    if (e.key === 'Backspace' && !input.value && props.modelValue.length) {
        emit('update:modelValue', props.modelValue.slice(0, -1))
    }
}
</script>

<template>
    <div class="min-h-[42px] w-full rounded-xl border border-slate-200 focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-100 bg-white px-3 py-2 flex flex-wrap gap-1.5 transition cursor-text"
        @click="$refs.inp.focus()">
        <span v-for="tag in modelValue" :key="tag"
            class="inline-flex items-center gap-1 rounded-lg bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-1">
            {{ tag }}
            <button type="button" @click.stop="remove(tag)" class="hover:text-amber-600 transition">
                <XMarkIcon class="w-3 h-3" />
            </button>
        </span>
        <input ref="inp" v-model="input" @keydown="onKeydown" @blur="add"
            :placeholder="modelValue.length ? '' : placeholder"
            class="flex-1 min-w-[120px] outline-none text-sm text-slate-900 bg-transparent placeholder:text-slate-400" />
    </div>
</template>
