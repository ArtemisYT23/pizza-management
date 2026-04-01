<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { PaginationMeta } from '@/types/api';

const props = defineProps<{
    meta: PaginationMeta;
    loading?: boolean;
}>();

const emit = defineEmits<{
    'update:page': [page: number];
}>();

const canPrev = computed(() => props.meta.current_page > 1);

const canNext = computed(
    () => props.meta.current_page < props.meta.last_page,
);

const rangeLabel = computed(() => {
    const { from, to, total } = props.meta;

    if (from === null || to === null || total === 0) {
        return 'Sin resultados';
    }

    return `${from}–${to} de ${total}`;
});

function goPrev() {
    if (canPrev.value && !props.loading) {
        emit('update:page', props.meta.current_page - 1);
    }
}

function goNext() {
    if (canNext.value && !props.loading) {
        emit('update:page', props.meta.current_page + 1);
    }
}
</script>

<template>
    <nav
        class="mt-8 flex flex-col gap-4 border-t border-border/60 pt-6 sm:flex-row sm:items-center sm:justify-between"
        aria-label="Paginación de la carta"
    >
        <p
            class="order-2 text-center text-sm text-muted-foreground sm:order-1 sm:text-left"
        >
            <span class="tabular-nums">{{ rangeLabel }}</span>
            <span class="hidden sm:inline">
                · Página
                <span class="tabular-nums font-medium text-foreground">{{
                    meta.current_page
                }}</span>
                de
                <span class="tabular-nums font-medium text-foreground">{{
                    meta.last_page
                }}</span>
            </span>
        </p>

        <div
            class="order-1 flex flex-wrap items-center justify-center gap-2 sm:order-2 sm:justify-end"
        >
            <Button
                variant="outline"
                size="sm"
                :disabled="!canPrev || loading"
                aria-label="Página anterior"
                @click="goPrev"
            >
                Anterior
            </Button>
            <Spinner
                v-if="loading"
                class="size-4 text-muted-foreground"
            />
            <span
                class="min-w-[5.5rem] text-center text-sm tabular-nums text-muted-foreground sm:hidden"
            >
                {{ meta.current_page }} / {{ meta.last_page }}
            </span>
            <Button
                variant="outline"
                size="sm"
                :disabled="!canNext || loading"
                aria-label="Página siguiente"
                @click="goNext"
            >
                Siguiente
            </Button>
        </div>
    </nav>
</template>
