<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    usuario:   { type: Object, default: null },
    motorista: { type: Object, default: null },
})

const form = useForm({})
function logout() { form.post(route('logout')) }
</script>

<template>
    <div class="max-w-lg space-y-4">

        <!-- Card dados -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-4 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                <div class="h-12 w-12 rounded-full bg-amber-500 flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-lg">{{ usuario?.nome?.charAt(0)?.toUpperCase() ?? 'M' }}</span>
                </div>
                <div>
                    <p class="font-bold text-slate-900" style="font-family:'Sora',sans-serif;">{{ usuario?.nome ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ usuario?.email ?? '—' }}</p>
                </div>
                <span class="ml-auto text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200 font-semibold shrink-0">
                    Motorista
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">Nome</span>
                    <span class="font-medium text-slate-900">{{ usuario?.nome ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">E-mail</span>
                    <span class="font-medium text-slate-900">{{ usuario?.email ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">CNH</span>
                    <span class="font-medium text-slate-900">{{ motorista?.cnh_numero ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">Categoria</span>
                    <span class="font-medium text-slate-900">{{ motorista?.cnh_categoria ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3 text-sm">
                    <span class="text-slate-500">Validade CNH</span>
                    <span class="font-medium text-slate-900">
                        {{ motorista?.cnh_validade ? new Date(motorista.cnh_validade + 'T00:00:00').toLocaleDateString('pt-BR') : '—' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="grid grid-cols-2 gap-3">
            <Link :href="route('motorista.perfil.edit')"
                class="flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-3 text-sm font-semibold text-white transition shadow-sm"
                style="font-family:'Sora',sans-serif;">
                Editar perfil
            </Link>
            <button @click="logout" :disabled="form.processing"
                class="flex items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition disabled:opacity-60">
                Sair da conta
            </button>
        </div>
    </div>
</template>
