<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { nextTick, onMounted, ref } from 'vue';
import MenuPageShell from '@/components/MenuPageShell.vue';
import MenuPagination from '@/components/MenuPagination.vue';
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
import type {
    ApiPaginatedCollection,
    ApiResource,
    PaginationMeta,
    PizzaDto,
} from '@/types/api';
import { ApiError } from '@/types/api';

const PER_PAGE = 12;

const page = usePage();
const pizzas = ref<PizzaDto[]>([]);
const meta = ref<PaginationMeta | null>(null);
const initialLoading = ref(true);
const pageLoading = ref(false);
const errorMessage = ref<string | null>(null);
const orderingId = ref<string | null>(null);
const orderSuccess = ref<string | null>(null);

/** IDs de pizza cuya imagen remota falló al cargar (mostramos placeholder). */
const pizzaImageFailedIds = ref<Set<string>>(new Set());

const isAuthenticated = () => !!page.props.auth?.user;

function onPizzaImageError(pizzaId: string) {
    const next = new Set(pizzaImageFailedIds.value);
    next.add(pizzaId);
    pizzaImageFailedIds.value = next;
}

function showPizzaImage(pizza: PizzaDto) {
    return (
        Boolean(pizza.image_url) && !pizzaImageFailedIds.value.has(pizza.id)
    );
}

const cartaBreadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Carta',
        href: home(),
    },
];

const cartaSectionRef = ref<HTMLElement | null>(null);
let hasLoadedOnce = false;

async function loadPizzas(
    pageNumber = 1,
    options: { scroll?: boolean } = {},
) {
    const firstPaint = !hasLoadedOnce;

    if (firstPaint) {
        initialLoading.value = true;
    } else {
        pageLoading.value = true;
    }

    errorMessage.value = null;

    try {
        const qs = new URLSearchParams({
            page: String(pageNumber),
            per_page: String(PER_PAGE),
        });
        const res = await apiFetch<ApiPaginatedCollection<PizzaDto>>(
            `/pizzas?${qs.toString()}`,
        );
        pizzas.value = res.data;
        meta.value = res.meta;

        if (options.scroll && hasLoadedOnce) {
            await nextTick();
            cartaSectionRef.value?.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'No se pudo cargar la carta.';
    } finally {
        initialLoading.value = false;
        pageLoading.value = false;
        hasLoadedOnce = true;
    }
}

function onPizzaPageChange(pageNumber: number) {
    void loadPizzas(pageNumber, { scroll: true });
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
    void loadPizzas(1);
});
</script>

<template>
    <Head title="Carta de pizzas" />

    <MenuPageShell
        :authenticated="isAuthenticated()"
        :breadcrumbs="isAuthenticated() ? cartaBreadcrumbs : undefined"
    >
        <section
            ref="cartaSectionRef"
            class="scroll-mt-6"
            aria-label="Carta de pizzas"
        >
            <div class="mb-6 sm:mb-8">
                <h1
                    class="text-2xl font-bold tracking-tight sm:text-3xl"
                >
                    Nuestra carta
                </h1>
                <p
                    class="mt-2 max-w-2xl text-sm text-muted-foreground sm:text-base"
                >
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

            <PizzaMenuLoader v-if="initialLoading" />

            <template v-else>
                <p
                    v-if="pizzas.length === 0"
                    class="rounded-lg border border-dashed border-border/80 bg-muted/20 px-4 py-10 text-center text-sm text-muted-foreground"
                >
                    No hay pizzas en la carta por ahora.
                </p>

                <div
                    v-else
                    class="relative"
                >
                    <div
                        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                        :class="{
                            'pointer-events-none opacity-60': pageLoading,
                        }"
                        :aria-busy="pageLoading"
                    >
                        <Card
                            v-for="pizza in pizzas"
                            :key="pizza.id"
                            class="flex flex-col gap-0 overflow-hidden p-0 shadow-sm transition-shadow duration-200 hover:shadow-md"
                        >
                            <!-- Altura fija: misma caja visual en todas las cards -->
                            <div
                                class="relative h-36 w-full shrink-0 overflow-hidden bg-muted"
                            >
                                <img
                                    v-if="showPizzaImage(pizza)"
                                    :src="pizza.image_url!"
                                    :alt="`Fotografía de ${pizza.name}`"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                    fetchpriority="low"
                                    sizes="(max-width: 639px) 100vw, (max-width: 1023px) 50vw, (max-width: 1279px) 33vw, 25vw"
                                    @error="onPizzaImageError(pizza.id)"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-orange-100/90 to-amber-50/90 dark:from-orange-950/50 dark:to-amber-950/30"
                                    aria-hidden="true"
                                >
                                    <span
                                        class="text-4xl opacity-70 drop-shadow-sm"
                                    >🍕</span>
                                    <span
                                        class="px-3 text-center text-xs font-medium text-muted-foreground"
                                    >
                                        Sin imagen
                                    </span>
                                </div>
                            </div>

                            <CardHeader
                                class="shrink-0 space-y-2 border-b border-border/50 pt-4 pb-3"
                            >
                                <CardTitle
                                    class="line-clamp-2 text-base font-semibold leading-snug tracking-tight"
                                >
                                    {{ pizza.name }}
                                </CardTitle>
                                <div class="min-h-[2.625rem]">
                                    <CardDescription
                                        v-if="pizza.description"
                                        class="line-clamp-2 text-sm leading-snug"
                                    >
                                        {{ pizza.description }}
                                    </CardDescription>
                                </div>
                            </CardHeader>

                            <CardContent
                                class="flex flex-col gap-4 pt-4"
                            >
                                <div class="shrink-0">
                                    <p
                                        class="mb-1 text-xs font-medium uppercase tracking-wide text-muted-foreground"
                                    >
                                        Precio
                                    </p>
                                    <p
                                        class="text-xl font-bold tabular-nums tracking-tight text-primary"
                                    >
                                        ${{ pizza.price }}
                                    </p>
                                </div>

                                <div class="mb-3 flex flex-col">
                                    <p
                                        class="mb-1.5 shrink-0 text-xs font-medium uppercase tracking-wide text-muted-foreground"
                                    >
                                        Ingredientes
                                    </p>
                                    <ul
                                        class="flex h-24 flex-wrap content-start gap-1.5 overflow-y-auto overscroll-contain rounded-lg border border-border/50 bg-muted/30 p-2 dark:bg-muted/20"
                                    >
                                        <li
                                            v-for="ing in pizza.ingredients"
                                            :key="ing.id"
                                            class="inline-flex max-w-full items-center rounded-md border border-border/60 bg-background/80 px-2.5 py-1 text-xs font-medium leading-none shadow-sm"
                                        >
                                            <span
                                                class="line-clamp-2 break-words"
                                            >{{ ing.name }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </CardContent>

                            <CardFooter
                                class="shrink-0 flex-col gap-0 border-t border-border/50 bg-muted/20 pt-4 pb-4 dark:bg-muted/10"
                            >
                                <Button
                                    v-if="isAuthenticated()"
                                    class="w-full"
                                    size="sm"
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
                                    class="text-center text-xs leading-relaxed text-muted-foreground"
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
                </div>

                <MenuPagination
                    v-if="meta && meta.last_page > 1"
                    :meta="meta"
                    :loading="pageLoading"
                    @update:page="onPizzaPageChange"
                />
            </template>
        </section>
    </MenuPageShell>
</template>
