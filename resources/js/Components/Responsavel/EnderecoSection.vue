<script setup>
import { ref } from 'vue'
import { MapPinIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: { type: Object, required: true },
})
const emit = defineEmits(['update:modelValue'])

const sugestoes = ref([])
const loading   = ref(false)
let timer = null

const estadosUF = {
    'Acre':'AC','Alagoas':'AL','Amapá':'AP','Amazonas':'AM','Bahia':'BA',
    'Ceará':'CE','Distrito Federal':'DF','Espírito Santo':'ES','Goiás':'GO',
    'Maranhão':'MA','Mato Grosso':'MT','Mato Grosso do Sul':'MS','Minas Gerais':'MG',
    'Pará':'PA','Paraíba':'PB','Paraná':'PR','Pernambuco':'PE','Piauí':'PI',
    'Rio de Janeiro':'RJ','Rio Grande do Norte':'RN','Rio Grande do Sul':'RS',
    'Rondônia':'RO','Roraima':'RR','Santa Catarina':'SC','São Paulo':'SP',
    'Sergipe':'SE','Tocantins':'TO',
}

function set(key, val) {
    emit('update:modelValue', { ...props.modelValue, [key]: val })
}

function onBuscar(e) {
    const q = e.target.value
    if (!q || q.length < 4) { sugestoes.value = []; return }
    loading.value = true
    clearTimeout(timer)
    timer = setTimeout(async () => {
        try {
            const r = await fetch(
                `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&addressdetails=1&limit=5&countrycodes=br`,
                { headers: { 'Accept-Language': 'pt-BR' } }
            )
            sugestoes.value = await r.json()
        } catch { sugestoes.value = [] }
        finally { loading.value = false }
    }, 500)
}

function selecionar(item) {
    const a = item.address || {}
    emit('update:modelValue', {
        ...props.modelValue,
        logradouro: a.road || a.pedestrian || a.footway || '',
        bairro:     a.suburb || a.neighbourhood || a.city_district || '',
        cidade:     a.city || a.town || a.village || '',
        estado:     a.state_code || estadosUF[a.state] || '',
        cep:        (a.postcode || '').replace('-', ''),
        latitude:   item.lat || '',
        longitude:  item.lon || '',
    })
    sugestoes.value = []
}

function fechar() {
    setTimeout(() => { sugestoes.value = [] }, 200)
}

function maskCep(e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 8)
    if (v.length > 5) v = v.replace(/^(\d{5})(\d+)$/, '$1-$2')
    set('cep', v)
}

const ic = 'px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition'
</script>

<template>
    <div class="space-y-2">
        <!-- Autocomplete -->
        <div class="relative">
            <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-400 pointer-events-none" />
                <input
                    type="text"
                    placeholder="Buscar endereço automaticamente..."
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
            <ul v-if="sugestoes.length" class="absolute z-30 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden max-h-56 overflow-y-auto">
                <li v-for="s in sugestoes" :key="s.place_id"
                    @mousedown="selecionar(s)"
                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-800 cursor-pointer border-b border-blue-50 last:border-0 flex items-start gap-2 transition">
                    <MapPinIcon class="w-3.5 h-3.5 text-blue-400 shrink-0 mt-0.5" />
                    <span class="line-clamp-2">{{ s.display_name }}</span>
                </li>
            </ul>
        </div>

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
        <div class="grid grid-cols-[1fr_3.5rem_1fr] gap-2">
            <input :value="modelValue.cidade" @input="set('cidade', $event.target.value)"
                placeholder="Cidade" :class="ic" />
            <input :value="modelValue.estado" @input="set('estado', $event.target.value.toUpperCase())"
                placeholder="UF" maxlength="2" :class="ic + ' uppercase text-center'" />
            <input :value="modelValue.cep" @input="maskCep"
                placeholder="CEP" :class="ic" />
        </div>
    </div>
</template>
