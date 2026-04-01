<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { home, login, register } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const page = usePage();

defineProps<{
    authenticated: boolean;
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppLayout v-if="authenticated" :breadcrumbs="breadcrumbs ?? []">
        <div
            class="mx-auto flex w-full max-w-6xl flex-col gap-6 p-4 md:p-6"
        >
            <slot />
        </div>
    </AppLayout>

    <div
        v-else
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
                    {{ page.props.name }}
                </Link>
                <nav class="flex flex-wrap items-center gap-2">
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="home()">Inicio</Link>
                    </Button>
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="login()">Iniciar sesión</Link>
                    </Button>
                    <Button size="sm" as-child>
                        <Link :href="register()">Registrarse</Link>
                    </Button>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
            <slot />
        </main>
    </div>
</template>
