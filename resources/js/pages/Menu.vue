<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import MenuPageShell from '@/components/MenuPageShell.vue';
import PizzaMenuLoader from '@/components/PizzaMenuLoader.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { apiFetch } from '@/lib/api';
import { home, login } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import type { ApiCollection, ApiResource, PizzaDto } from '@/types/api';
import { ApiError } from '@/types/api';

const page = usePage();
const pizzas = ref<PizzaDto[]>([]);
const loading = ref(true);
const errorMessage = ref<string | null>(null);
const orderingId = ref<string | null>(null);
const orderSuccess = ref<string | null>(null);

const isAuthenticated = () => !!page.props.auth?.user;

const cartaBreadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Carta',
        href: home(),
    },
];

async function loadPizzas() {
    loading.value = true;
    errorMessage.value = null;

    try {
        const res = await apiFetch<ApiCollection<PizzaDto>>('/pizzas');
        pizzas.value = res.data;
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'No se pudo cargar la carta.';
    } finally {
        loading.value = false;
    }
}

async function placeOrder(pizza: PizzaDto) {
    if (!isAuthenticated()) {
        return;
    }

    orderingId.value = pizza.id;
    orderSuccess.value = null;

    try {
        await apiFetch<ApiResource<unknown>>('/orders', {
            method: 'POST',
            body: { pizza_id: pizza.id },
        });
        orderSuccess.value = `Pedido registrado: ${pizza.name}. Revisa tu correo.`;
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError
                ? e.message
                : 'No se pudo completar el pedido.';
    } finally {
        orderingId.value = null;
    }
}

onMounted(() => {
    loadPizzas();
});
</script>

<template>
    <Head title="Carta de pizzas" />

    <MenuPageShell
        :authenticated="isAuthenticated()"
        :breadcrumbs="isAuthenticated() ? cartaBreadcrumbs : undefined"
    >
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">
                Nuestra carta
            </h1>
            <p class="mt-2 text-muted-foreground">
                Pizzas con ingredientes frescos. Los pedidos solo están
                disponibles para usuarios registrados.
            </p>
        </div>

        <p
            v-if="orderSuccess"
            class="mb-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-400"
        >
            {{ orderSuccess }}
        </p>
        <p
            v-if="errorMessage"
            class="mb-4 rounded-lg border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive"
        >
            {{ errorMessage }}
        </p>

        <PizzaMenuLoader v-if="loading" />

        <div
            v-else
            class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <Card
                v-for="pizza in pizzas"
                :key="pizza.id"
                class="flex h-full flex-col overflow-hidden shadow-sm transition-shadow duration-200 hover:shadow-md"
            >
                <CardHeader
                    class="shrink-0 space-y-3 border-b border-border/50 pb-4"
                >
                    <CardTitle
                        class="line-clamp-2 text-lg font-semibold leading-snug tracking-tight"
                    >
                        {{ pizza.name }}
                    </CardTitle>
                    <div class="min-h-[4.25rem]">
                        <CardDescription
                            v-if="pizza.description"
                            class="line-clamp-3 leading-relaxed"
                        >
                            {{ pizza.description }}
                        </CardDescription>
                    </div>
                </CardHeader>

                <CardContent
                    class="flex min-h-0 flex-1 flex-col gap-5 pt-5"
                >
                    <div class="shrink-0">
                        <p
                            class="mb-1.5 text-xs font-medium uppercase tracking-wide text-muted-foreground"
                        >
                            Precio
                        </p>
                        <p
                            class="text-2xl font-bold tabular-nums tracking-tight text-primary"
                        >
                            ${{ pizza.price }}
                        </p>
                    </div>

                    <div class="flex min-h-0 flex-1 flex-col">
                        <p
                            class="mb-2 shrink-0 text-xs font-medium uppercase tracking-wide text-muted-foreground"
                        >
                            Ingredientes
                        </p>
                        <ul
                            class="flex h-[7.5rem] flex-wrap content-start gap-1.5 overflow-y-auto overscroll-contain rounded-lg border border-border/50 bg-muted/30 p-2.5 dark:bg-muted/20"
                        >
                            <li
                                v-for="ing in pizza.ingredients"
                                :key="ing.id"
                                class="inline-flex max-w-full items-center rounded-md border border-border/60 bg-background/80 px-2.5 py-1 text-xs font-medium leading-none shadow-sm"
                            >
                                <span class="line-clamp-2 break-words">{{
                                    ing.name
                                }}</span>
                            </li>
                        </ul>
                    </div>
                </CardContent>

                <CardFooter
                    class="mt-auto shrink-0 flex-col gap-0 border-t border-border/50 bg-muted/20 pt-5 dark:bg-muted/10"
                >
                    <Button
                        v-if="isAuthenticated()"
                        class="w-full"
                        :disabled="orderingId === pizza.id"
                        @click="placeOrder(pizza)"
                    >
                        {{
                            orderingId === pizza.id
                                ? 'Enviando…'
                                : 'Pedir esta pizza'
                        }}
                    </Button>
                    <p
                        v-else
                        class="text-center text-sm leading-relaxed text-muted-foreground"
                    >
                        <Link
                            :href="login()"
                            class="font-medium text-primary underline-offset-4 hover:underline"
                        >
                            Inicia sesión
                        </Link>
                        para realizar un pedido.
                    </p>
                </CardFooter>
            </Card>
        </div>
    </MenuPageShell>
</template>
