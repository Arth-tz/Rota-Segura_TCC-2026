<script setup>
defineProps({
    passageiros: { type: Array, default: () => [] },
})
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 xl:grid-cols-[1fr_0.85fr]">
            <div class="rounded-[2rem] bg-gradient-to-br from-sky-700 via-blue-600 to-indigo-600 p-6 text-white shadow-2xl shadow-slate-900/10">
                <div class="space-y-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-100/80">Buscar vans</p>
                    <h3 class="text-3xl font-semibold">Encontre a van ideal</h3>
                    <p class="max-w-xl text-sm leading-6 text-sky-100/85">Use filtros por turno, dia e região para achar opções com transporte escolar seguro e recomendado para sua família.</p>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-3xl bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-sky-100/80">Por turno</p>
                        <p class="mt-2 text-sm text-sky-100/85">Manhã, tarde e noite para encaixar no melhor horário.</p>
                    </div>
                    <div class="rounded-3xl bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-sky-100/80">Por dia</p>
                        <p class="mt-2 text-sm text-sky-100/85">Escolha o dia da semana que funciona melhor para o seu trajeto.</p>
                    </div>
                </div>
            </div>
            <div class="grid gap-4">
                <div class="rounded-[1.75rem] bg-white/90 p-6 shadow-xl shadow-slate-900/5">
                    <p class="text-sm uppercase tracking-[0.24em] text-sky-600">Próximo passo</p>
                    <h4 class="mt-3 text-xl font-semibold text-slate-900">Ainda em desenvolvimento</h4>
                    <p class="mt-2 text-sm text-slate-600">Em breve você poderá filtrar e comparar vans disponíveis diretamente do seu dashboard.</p>
                </div>
                <div class="rounded-[1.75rem] bg-white/90 p-6 shadow-xl shadow-slate-900/5">
                    <p class="text-sm uppercase tracking-[0.24em] text-sky-600">Dica de uso</p>
                    <p class="mt-3 text-sm text-slate-600">Quando tiver passageiros cadastrados, volte aqui para iniciar a busca e solicitar a melhor opção para o trajeto.</p>
                </div>
            </div>
        </div>

        <!-- Placeholder marketplace -->
        <div class="rounded-[2rem] border border-dashed border-sky-200/50 bg-white/95 p-10 text-center shadow-xl">
            <p class="text-4xl mb-3">⚡</p>
            <h3 class="text-2xl font-semibold text-slate-900">Marketplace em breve</h3>
            <p class="mt-3 text-sm text-slate-500">Estamos preparando uma experiência de busca de vans inteira dentro do painel.</p>
            <p class="mt-2 text-sm text-slate-500">Assim que estiver pronto, você poderá comparar motoristas, preços e disponibilidade sem sair do dashboard.</p>
        </div>
    </div>
</template>