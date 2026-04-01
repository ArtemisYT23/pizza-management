<script setup lang="ts">
/**
 * Loader centrado: arco giratorio alrededor del emoji de pizza.
 */
</script>

<template>
    <div
        class="flex min-h-[220px] flex-col items-center justify-center gap-4 py-12"
        role="status"
        aria-live="polite"
        aria-busy="true"
    >
        <div class="relative size-32 shrink-0">
            <svg
                class="absolute inset-0 size-full -rotate-90 text-orange-500 animate-spin dark:text-orange-400"
                style="animation-duration: 1.1s"
                viewBox="0 0 100 100"
                aria-hidden="true"
            >
                <circle
                    cx="50"
                    cy="50"
                    r="40"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="5"
                    stroke-linecap="round"
                    stroke-dasharray="63 189"
                    class="opacity-90"
                />
            </svg>
            <div
                class="pointer-events-none absolute inset-0 flex items-center justify-center"
            >
                <span class="text-5xl select-none drop-shadow-sm">🍕</span>
            </div>
        </div>
        <span class="sr-only">Cargando la carta de pizzas…</span>
    </div>
</template>
