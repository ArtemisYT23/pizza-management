<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChefHat, LayoutGrid, Receipt } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import adminPages from '@/routes/admin';
import { admin } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Administración',
                href: admin(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Administración" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Panel de administración
            </h1>
            <p class="text-sm text-muted-foreground">
                Gestión de ingredientes, pizzas y todos los pedidos (API).
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <LayoutGrid class="size-5 text-muted-foreground" />
                        <CardTitle>Resumen</CardTitle>
                    </div>
                    <CardDescription>
                        Usa el menú lateral o los accesos de abajo para el
                        backoffice.
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="adminPages.ingredients()">
                            Ingredientes
                        </Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="adminPages.pizzas()">Pizzas</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="adminPages.orders()">Pedidos</Link>
                    </Button>
                </CardContent>
            </Card>

            <Card class="md:col-span-2">
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <ChefHat class="size-5 text-muted-foreground" />
                        <CardTitle>CRUD y pedidos</CardTitle>
                    </div>
                    <CardDescription>
                        Operaciones vía API REST del mismo dominio (sesión +
                        CSRF).
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-2">
                        <Button as-child>
                            <Link :href="adminPages.ingredients()">
                                CRUD ingredientes
                            </Link>
                        </Button>
                        <Button as-child>
                            <Link :href="adminPages.pizzas()">CRUD pizzas</Link>
                        </Button>
                        <Button as-child>
                            <Link :href="adminPages.orders()">
                                <Receipt class="mr-2 inline size-4" />
                                Todos los pedidos
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
