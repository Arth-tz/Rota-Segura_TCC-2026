<script setup>
import { PlusIcon, TrashIcon, MapPinIcon } from '@heroicons/vue/24/outline'
import TagInput from '@/Components/UI/TagInput.vue'

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue'])

function adicionarCidade() {
    emit('update:modelValue', [...props.modelValue, { cidade: '', bairros: [] }])
}

function removerCidade(index) {
    emit('update:modelValue', props.modelValue.filter((_, i) => i !== index))
}

function atualizarCidade(index, valor) {
    const novo = props.modelValue.map((r, i) => (i === index ? { ...r, cidade: valor } : r))
    emit('update:modelValue', novo)
}

function atualizarBairros(index, valor) {
    const novo = props.modelValue.map((r, i) => (i === index ? { ...r, bairros: valor } : r))
    emit('update:modelValue', novo)
}
</script>

<template>
    <div class="space-y-3">
        <div v-for="(regiao, i) in modelValue" :key="i"
            class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 space-y-2.5">
            <div class="flex items-center gap-2">
                <MapPinIcon class="w-4 h-4 text-amber-600 shrink-0" />
                <input :value="regiao.cidade" @input="atualizarCidade(i, $event.target.value)"
                    type="text" maxlength="100" placeholder="Nome da cidade — ex: Canoas"
                    class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition" />
                <button type="button" @click="removerCidade(i)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition shrink-0"
                    title="Remover cidade">
                    <TrashIcon class="w-4 h-4" />
                </button>
            </div>
            <div class="pl-6">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">
                    Bairros atendidos em {{ regiao.cidade || 'nessa cidade' }}
                </label>
                <TagInput :model-value="regiao.bairros" @update:model-value="(v) => atualizarBairros(i, v)"
                    placeholder="Ex: Igara · pressione Enter para adicionar" />
            </div>
        </div>

        <button type="button" @click="adicionarCidade"
            class="w-full flex items-center justify-center gap-1.5 rounded-xl border border-dashed border-amber-300 py-2.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 transition">
            <PlusIcon class="w-4 h-4" /> Adicionar cidade
        </button>
    </div>
</template>
