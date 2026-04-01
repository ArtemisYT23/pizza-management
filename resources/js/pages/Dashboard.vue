<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChefHat, LayoutGrid, Pizza, Receipt } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard, menu } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Panel" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Panel de gestión
            </h1>
            <p class="text-sm text-muted-foreground">
                Accesos rápidos a la carta pública y al backoffice (API).
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <Pizza class="size-5 text-muted-foreground" />
                        <CardTitle>Carta pública</CardTitle>
                    </div>
                    <CardDescription>
                        Listado de pizzas con ingredientes. Los clientes
                        registrados pueden pedir desde aquí.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button as-child>
                        <Link :href="menu()">Abrir carta</Link>
                    </Button>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <LayoutGrid class="size-5 text-muted-foreground" />
                        <CardTitle>Resumen</CardTitle>
                    </div>
                    <CardDescription>
                        Usa el menú lateral para administrar ingredientes,
                        pizzas y ver pedidos.
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <Button variant="outline" as-child>
                        <Link href="/admin/ingredients">Ingredientes</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/admin/pizzas">Pizzas</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/admin/orders">Pedidos</Link>
                    </Button>
                </CardContent>
            </Card>

            <Card class="md:col-span-2">
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <ChefHat class="size-5 text-muted-foreground" />
                        <CardTitle>Administración</CardTitle>
                    </div>
                    <CardDescription>
                        Todas las operaciones del dashboard consumen la API REST
                        del mismo dominio (sesión + CSRF).
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-2">
                        <Button as-child>
                            <Link href="/admin/ingredients">
                                CRUD ingredientes
                            </Link>
                        </Button>
                        <Button as-child>
                            <Link href="/admin/pizzas">CRUD pizzas</Link>
                        </Button>
                        <Button as-child>
                            <Link href="/admin/orders">
                                <Receipt class="mr-2 inline size-4" />
                                Listado de pedidos
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
