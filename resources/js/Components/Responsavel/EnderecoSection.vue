<script setup>
import { ref } from 'vue'
import { MapPinIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: { type: Object, required: true },
})
const emit = defineEmits(['update:modelValue'])

const sugestoes  = ref([])
const loading    = ref(false)
const cepLoading = ref(false)
const cepErro    = ref('')
let timer = null

function set(key, val) {
    emit('update:modelValue', { ...props.modelValue, [key]: val })
}

// ── Autocomplete via Geoapify (proxy backend) ─────────────────────────────────
function onBuscar(e) {
    const q = e.target.value
    if (!q || q.length < 3) { sugestoes.value = []; return }
    loading.value = true
    clearTimeout(timer)
    timer = setTimeout(async () => {
        try {
            const r = await fetch(route('places.autocomplete', { q }))
            sugestoes.value = await r.json()
        } catch { sugestoes.value = [] }
        finally  { loading.value = false }
    }, 350)
}

function selecionar(item) {
    emit('update:modelValue', {
        ...props.modelValue,
        logradouro: item.logradouro || '',
        numero:     item.numero     || '',
        bairro:     item.bairro     || '',
        cidade:     item.cidade     || '',
        estado:     item.estado     || '',
        cep:        item.cep        || '',
        latitude:   item.latitude   || '',
        longitude:  item.longitude  || '',
    })
    sugestoes.value = []
}

function fechar() {
    setTimeout(() => { sugestoes.value = [] }, 200)
}

// ── CEP via ViaCEP ────────────────────────────────────────────────────────────
async function onCep(e) {
    const raw = e.target.value.replace(/\D/g, '')
    let v = raw.slice(0, 8)
    if (v.length > 5) v = v.replace(/^(\d{5})(\d+)$/, '$1-$2')
    set('cep', v)
    cepErro.value = ''
    if (raw.length !== 8) return
    cepLoading.value = true
    try {
        const res  = await fetch(`https://viacep.com.br/ws/${raw}/json/`)
        const data = await res.json()
        if (data.erro) { cepErro.value = 'CEP não encontrado.'; return }
        emit('update:modelValue', {
            ...props.modelValue,
            cep:        v,
            logradouro: data.logradouro || props.modelValue.logradouro || '',
            bairro:     data.bairro     || props.modelValue.bairro     || '',
            cidade:     data.localidade || props.modelValue.cidade     || '',
            estado:     data.uf         || props.modelValue.estado     || '',
        })
    } catch { cepErro.value = 'Erro ao buscar CEP.' }
    finally  { cepLoading.value = false }
}

const ic = 'min-w-0 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition w-full'
</script>

<template>
    <div class="space-y-2">
        <!-- Autocomplete -->
        <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-400 pointer-events-none z-10" />
            <input
                type="text"
                placeholder="Buscar por nome, endereço ou CEP…"
                class="w-full pl-10 pr-10 py-3 rounded-xl border border-blue-200 bg-blue-50 text-sm placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                @input="onBuscar"
                @blur="fechar"
            />
            <div v-if="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </div>
        </div>

        <!-- Dropdown sugestões -->
        <ul v-if="sugestoes.length"
            class="rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden max-h-56 overflow-y-auto">
            <li v-for="s in sugestoes" :key="s.place_id"
                @mousedown="selecionar(s)"
                class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-800 cursor-pointer border-b border-blue-50 last:border-0 flex items-start gap-2 transition">
                <MapPinIcon class="w-3.5 h-3.5 text-blue-400 shrink-0 mt-0.5" />
                <span class="line-clamp-2">{{ s.description }}</span>
            </li>
        </ul>

        <!-- Campos manuais -->
        <div class="grid grid-cols-[1fr_5rem] gap-2">
            <input :value="modelValue.logradouro" @input="set('logradouro', $event.target.value)"
                placeholder="Logradouro" :class="ic" />
            <input :value="modelValue.numero" @input="set('numero', $event.target.value)"
                placeholder="Nº" :class="ic" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <input :value="modelValue.complemento" @input="set('complemento', $event.target.value)"
                placeholder="Complemento" :class="ic" />
            <input :value="modelValue.bairro" @input="set('bairro', $event.target.value)"
                placeholder="Bairro" :class="ic" />
        </div>
        <div class="grid grid-cols-[1fr_3rem_6rem] gap-2">
            <input :value="modelValue.cidade" @input="set('cidade', $event.target.value)"
                placeholder="Cidade" :class="ic" />
            <input :value="modelValue.estado" @input="set('estado', $event.target.value.toUpperCase())"
                placeholder="UF" maxlength="2" :class="ic + ' uppercase text-center px-1'" />
            <input :value="modelValue.cep" @input="onCep"
                placeholder="CEP" inputmode="numeric" :class="ic" />
        </div>
        <p v-if="cepErro" class="text-xs text-red-500">{{ cepErro }}</p>
    </div>
</template>
