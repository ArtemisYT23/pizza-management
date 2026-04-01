<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { apiFetch } from '@/lib/api';
import { home } from '@/routes';
import type { ApiCollection, OrderDto } from '@/types/api';
import { ApiError } from '@/types/api';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Carta', href: home() },
            { title: 'Mis pedidos', href: '/my-orders' },
        ],
    },
});

const orders = ref<OrderDto[]>([]);
const loading = ref(true);
const errorMessage = ref<string | null>(null);

async function load() {
    loading.value = true;
    errorMessage.value = null;

    try {
        const res = await apiFetch<ApiCollection<OrderDto>>('/orders');
        orders.value = res.data;
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'Error al cargar pedidos.';
    } finally {
        loading.value = false;
    }
}

function formatDate(iso: string) {
    try {
        return new Date(iso).toLocaleString('es');
    } catch {
        return iso;
    }
}

onMounted(load);
</script>

<template>
    <Head title="Mis pedidos" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Mis pedidos
                </h1>
                <p class="text-sm text-muted-foreground">
                    Pedidos asociados a tu cuenta.
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="home()">Volver a la carta</Link>
                </Button>
                <Button variant="secondary" @click="load">
                    Actualizar
                </Button>
            </div>
        </div>

        <p
            v-if="errorMessage"
            class="rounded-md border border-destructive/30 bg-destructive/10 px-3 py-2 text-sm text-destructive"
        >
            {{ errorMessage }}
        </p>

        <Card>
            <CardHeader>
                <CardTitle>Tus pedidos</CardTitle>
                <CardDescription>
                    Pizza y fecha de cada pedido.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <p
                    v-if="loading"
                    class="text-muted-foreground"
                >
                    Cargando…
                </p>
                <div
                    v-else-if="orders.length === 0"
                    class="text-muted-foreground"
                >
                    Aún no has realizado pedidos.
                </div>
                <div
                    v-else
                    class="overflow-x-auto"
                >
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-border">
                                <th class="pb-2 pr-4 font-medium">
                                    Fecha
                                </th>
                                <th class="pb-2 pr-4 font-medium">
                                    Pizza
                                </th>
                                <th class="pb-2 font-medium">
                                    Precio
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="o in orders"
                                :key="o.id"
                                class="border-b border-border/60"
                            >
                                <td class="py-3 pr-4 align-top text-muted-foreground">
                                    {{ formatDate(o.ordered_at) }}
                                </td>
                                <td class="py-3 pr-4 align-top">
                                    {{ o.pizza?.name ?? '—' }}
                                </td>
                                <td class="py-3 align-top">
                                    ${{ o.pizza?.price ?? '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
