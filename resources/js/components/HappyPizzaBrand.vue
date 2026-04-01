<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        /** Compacto: sidebar / cabecera. Prominente: login y registro. */
        variant?: 'compact' | 'prominent';
        /** Texto claro para fondos oscuros (p. ej. panel lateral auth). */
        lightOnDark?: boolean;
    }>(),
    { variant: 'compact', lightOnDark: false },
);

const emojiBoxClass = computed(() => {
    const base =
        props.variant === 'prominent'
            ? 'flex aspect-square size-10 shrink-0 items-center justify-center rounded-lg text-2xl leading-none shadow-md'
            : 'flex aspect-square size-8 shrink-0 items-center justify-center rounded-md text-lg leading-none shadow-sm';
    const gradient =
        'bg-gradient-to-br from-orange-500 to-amber-600 dark:from-orange-600 dark:to-amber-700';
    const ring = props.lightOnDark
        ? 'ring-1 ring-white/25'
        : 'ring-1 ring-orange-400/30 dark:ring-orange-500/20';

    return `${base} ${gradient} ${ring}`;
});

const textMargin = computed(() =>
    props.variant === 'prominent' ? 'ml-2' : 'ml-1',
);

const textClass = computed(() => {
    if (props.lightOnDark) {
        return props.variant === 'prominent'
            ? 'truncate bg-gradient-to-r from-white to-amber-100 bg-clip-text text-xl font-bold tracking-tight text-transparent'
            : 'truncate bg-gradient-to-r from-white to-amber-100 bg-clip-text text-base font-bold tracking-tight text-transparent';
    }

    return props.variant === 'prominent'
        ? 'truncate bg-gradient-to-r from-orange-600 to-amber-500 bg-clip-text text-xl font-bold tracking-tight text-transparent dark:from-orange-400 dark:to-amber-300'
        : 'truncate bg-gradient-to-r from-orange-600 to-amber-500 bg-clip-text text-base font-bold tracking-tight text-transparent dark:from-orange-400 dark:to-amber-300';
});
</script>

<template>
    <div class="flex min-w-0 items-center">
        <div :class="emojiBoxClass" aria-hidden="true">
            <span class="translate-y-px drop-shadow-sm">🍕</span>
        </div>
        <div :class="['grid min-w-0 flex-1 text-left', textMargin]">
            <span :class="textClass">Happy Pizza</span>
        </div>
    </div>
</template>
