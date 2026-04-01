<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
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
import { dashboard, home, login, register } from '@/routes';
import type { ApiCollection, ApiResource, PizzaDto } from '@/types/api';
import { ApiError } from '@/types/api';

const page = usePage();
const pizzas = ref<PizzaDto[]>([]);
const loading = ref(true);
const errorMessage = ref<string | null>(null);
const orderingId = ref<string | null>(null);
const orderSuccess = ref<string | null>(null);

const isAuthenticated = () => !!page.props.auth?.user;

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

    <div
        class="min-h-screen bg-background text-foreground"
    >
        <header
            class="border-b border-border bg-card/80 backdrop-blur-sm"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4"
            >
                <Link
                    :href="home()"
                    class="text-lg font-semibold tracking-tight"
                >
                    {{ $page.props.name }}
                </Link>
                <nav class="flex flex-wrap items-center gap-2">
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="home()">Inicio</Link>
                    </Button>
                    <template v-if="isAuthenticated()">
                        <Button size="sm" as-child>
                            <Link :href="dashboard()">Panel</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="login()">Iniciar sesión</Link>
                        </Button>
                        <Button size="sm" as-child>
                            <Link :href="register()">Registrarse</Link>
                        </Button>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
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

            <div
                v-if="loading"
                class="text-muted-foreground"
            >
                Cargando pizzas…
            </div>

            <div
                v-else
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Card
                    v-for="pizza in pizzas"
                    :key="pizza.id"
                >
                    <CardHeader>
                        <CardTitle>{{ pizza.name }}</CardTitle>
                        <CardDescription v-if="pizza.description">
                            {{ pizza.description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <p class="text-2xl font-semibold text-primary">
                            ${{ pizza.price }}
                        </p>
                        <div>
                            <p class="mb-2 text-xs font-medium uppercase text-muted-foreground">
                                Ingredientes
                            </p>
                            <ul class="flex flex-wrap gap-1.5">
                                <li
                                    v-for="ing in pizza.ingredients"
                                    :key="ing.id"
                                    class="rounded-md bg-muted px-2 py-0.5 text-xs"
                                >
                                    {{ ing.name }}
                                </li>
                            </ul>
                        </div>
                        <Button
                            v-if="isAuthenticated()"
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
                            class="text-sm text-muted-foreground"
                        >
                            <Link
                                :href="login()"
                                class="font-medium text-primary underline-offset-4 hover:underline"
                            >
                                Inicia sesión
                            </Link>
                            para realizar un pedido.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </main>
    </div>
</template>
