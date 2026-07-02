<script setup>
import { Link } from '@inertiajs/vue3'
import { MagnifyingGlassIcon, ArrowRightIcon, FunnelIcon, ShieldCheckIcon, MapPinIcon } from '@heroicons/vue/24/outline'
</script>

<template>
    <div class="space-y-4">

        <!-- Hero -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 p-6 text-white shadow-lg">
            <div class="flex items-center gap-2 mb-1">
                <MagnifyingGlassIcon class="w-5 h-5 text-blue-300" />
                <p class="text-xs uppercase tracking-widest text-blue-300 font-medium">Marketplace</p>
            </div>
            <h3 class="text-2xl font-bold mt-1" style="font-family:'Sora',sans-serif;">Encontre a van ideal</h3>
            <p class="mt-2 text-sm text-blue-200 max-w-md">
                Busque por nome do motorista, bairro, escola, turno ou dias da semana. Resultados carregados por página — sem travamentos.
            </p>
            <Link :href="route('responsavel.marketplace')"
                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-blue-700 hover:bg-blue-50 transition shadow-sm"
                style="font-family:'Sora',sans-serif;">
                Abrir marketplace
                <ArrowRightIcon class="w-4 h-4" />
            </Link>
        </div>

        <!-- Destaques do que é possível fazer -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-start gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                    <FunnelIcon class="w-4 h-4 text-blue-600" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Filtros avançados</p>
                    <p class="text-xs text-slate-500 mt-0.5">Turno, bairro, escola, dias da semana e nome do motorista</p>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-start gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <ShieldCheckIcon class="w-4 h-4 text-emerald-600" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Somente verificados</p>
                    <p class="text-xs text-slate-500 mt-0.5">Apenas motoristas e vans aprovados pelo administrador</p>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-start gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <MapPinIcon class="w-4 h-4 text-amber-600" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Por região</p>
                    <p class="text-xs text-slate-500 mt-0.5">Filtre por bairros e escolas atendidos por cada trajeto</p>
                </div>
            </div>
        </div>

    </div>
</template>
