<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { SunIcon, MoonIcon, ClockIcon, MapPinIcon, AcademicCapIcon, UserGroupIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    motorista: { type: Object, required: true },
    van:       { type: Object, required: true },
})

const TURNOS  = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const DIAS    = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }
const ORDEM   = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom']

function diasOrdenados(dias) {
    return ORDEM.filter(d => dias.includes(d))
}

function formatarPreco(v) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

function vagasRestantes(d) {
    return Math.max(0, (d.capacidade_total ?? 0) - (d.vagas_ocupadas ?? 0))
}

function turnoIcon(t) {
    return t === 'manha' ? SunIcon : t === 'tarde' ? MoonIcon : ClockIcon
}

const urlBusca = computed(() =>
    route('responsavel.marketplace', { motorista: props.motorista.nome })
)
</script>

<template>
    <Head :title="`${van.nome_servico} — ${motorista.nome}`" />

    <div class="min-h-screen bg-slate-100">

        <!-- Header azul com dados principais -->
        <div class="bg-gradient-to-b from-blue-700 to-blue-800 text-white">
            <div class="max-w-2xl mx-auto px-4 py-8 flex items-center gap-5">
                <!-- Foto da van -->
                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-blue-900/60 border-2 border-white/20 shrink-0">
                    <img v-if="van.foto_url" :src="van.foto_url" class="w-full h-full object-cover" alt="" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <UserGroupIcon class="w-8 h-8 text-blue-300" />
                    </div>
                </div>

                <div class="min-w-0">
                    <p class="text-xs text-blue-300 font-medium uppercase tracking-wider mb-0.5">Serviço de transporte escolar</p>
                    <h1 class="text-2xl font-bold leading-tight truncate">{{ van.nome_servico }}</h1>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="w-6 h-6 rounded-full overflow-hidden bg-blue-600 border border-white/30 shrink-0">
                            <img v-if="motorista.foto_url" :src="motorista.foto_url" class="w-full h-full object-cover" alt="" />
                            <span v-else class="flex items-center justify-center w-full h-full text-xs font-bold text-white">
                                {{ motorista.nome?.charAt(0)?.toUpperCase() }}
                            </span>
                        </div>
                        <span class="text-sm text-blue-100">{{ motorista.nome }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disponibilidades -->
        <div class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                Vagas disponíveis
            </h2>

            <div v-if="van.disponibilidades.length === 0"
                class="rounded-2xl bg-white border border-slate-200 p-6 text-center text-slate-400 text-sm shadow-sm">
                Nenhuma disponibilidade ativa no momento.
            </div>

            <div v-for="d in van.disponibilidades" :key="d.id_disponibilidade"
                class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 space-y-3">

                <!-- Turno + nome -->
                <div class="flex items-center gap-2">
                    <component :is="turnoIcon(d.turno)"
                        class="w-5 h-5 shrink-0"
                        :class="d.turno === 'manha' ? 'text-amber-500' : d.turno === 'tarde' ? 'text-indigo-500' : 'text-slate-400'" />
                    <span class="font-semibold text-slate-800">
                        {{ d.nome || TURNOS[d.turno] }}
                    </span>
                    <span class="ml-auto text-xs font-medium px-2.5 py-0.5 rounded-full"
                        :class="d.turno === 'manha'
                            ? 'bg-amber-100 text-amber-700'
                            : d.turno === 'tarde'
                                ? 'bg-indigo-100 text-indigo-700'
                                : 'bg-slate-100 text-slate-600'">
                        {{ TURNOS[d.turno] }}
                    </span>
                </div>

                <!-- Dias da semana -->
                <div class="flex flex-wrap gap-1.5">
                    <span v-for="dia in diasOrdenados(d.dias)" :key="dia"
                        class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium">
                        {{ DIAS[dia] }}
                    </span>
                </div>

                <!-- Regiões e escolas -->
                <div v-if="d.regioes_atendidas?.length || d.escolas_atendidas?.length" class="space-y-1.5">
                    <div v-if="d.regioes_atendidas?.length" class="flex items-start gap-2 text-sm text-slate-600">
                        <MapPinIcon class="w-4 h-4 mt-0.5 text-slate-400 shrink-0" />
                        <span>{{ d.regioes_atendidas.join(', ') }}</span>
                    </div>
                    <div v-if="d.escolas_atendidas?.length" class="flex items-start gap-2 text-sm text-slate-600">
                        <AcademicCapIcon class="w-4 h-4 mt-0.5 text-slate-400 shrink-0" />
                        <span>{{ d.escolas_atendidas.join(', ') }}</span>
                    </div>
                </div>

                <!-- Preço e vagas -->
                <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                    <span class="text-xl font-bold text-slate-800">
                        {{ formatarPreco(d.preco_mensal) }}
                        <span class="text-sm font-normal text-slate-400">/mês</span>
                    </span>
                    <span class="text-sm font-medium"
                        :class="vagasRestantes(d) > 0 ? 'text-emerald-600' : 'text-red-500'">
                        {{ vagasRestantes(d) > 0
                            ? `${vagasRestantes(d)} vaga${vagasRestantes(d) !== 1 ? 's' : ''}`
                            : 'Sem vagas' }}
                    </span>
                </div>
            </div>

            <!-- CTA -->
            <div class="rounded-2xl bg-blue-600 p-5 text-white text-center space-y-3 shadow-md">
                <p class="text-sm text-blue-200">
                    Para solicitar uma vaga você precisa ter uma conta de responsável.
                </p>
                <a :href="urlBusca"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-blue-700 hover:bg-blue-50 transition shadow-sm">
                    Ver na busca de motoristas
                </a>
            </div>

            <!-- Voltar para o app -->
            <div class="text-center pb-6">
                <Link :href="route('home')" class="text-xs text-slate-400 hover:text-slate-600 transition">
                    Rota Segura — transporte escolar
                </Link>
            </div>
        </div>
    </div>
</template>
