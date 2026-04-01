<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { apiFetch } from '@/lib/api';
import { dashboard } from '@/routes';
import type { ApiCollection, ApiResource, IngredientDto, PizzaDto } from '@/types/api';
import { ApiError } from '@/types/api';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Pizzas', href: '/admin/pizzas' },
        ],
    },
});

const pizzas = ref<PizzaDto[]>([]);
const ingredients = ref<IngredientDto[]>([]);
const loading = ref(true);
const saving = ref(false);
const errorMessage = ref<string | null>(null);

const form = ref({
    name: '',
    description: '',
    price: '',
    ingredient_ids: [] as string[],
});

const editingId = ref<string | null>(null);
const editForm = ref({
    name: '',
    description: '',
    price: '',
    ingredient_ids: [] as string[],
});

const sortedIngredients = computed(() =>
    [...ingredients.value].sort((a, b) => a.name.localeCompare(b.name)),
);

function toggleIngredient(
    list: string[],
    id: string,
    checked: boolean,
) {
    const i = list.indexOf(id);

    if (checked && i === -1) {
        list.push(id);
    } else if (!checked && i !== -1) {
        list.splice(i, 1);
    }
}

function ingredientChecked(list: string[], id: string) {
    return list.includes(id);
}

function loadEditFromPizza(p: PizzaDto) {
    editingId.value = p.id;
    editForm.value = {
        name: p.name,
        description: p.description ?? '',
        price: String(p.price),
        ingredient_ids: p.ingredients.map((i) => i.id),
    };
}

function cancelEdit() {
    editingId.value = null;
}

async function loadAll() {
    loading.value = true;
    errorMessage.value = null;

    try {
        const [pRes, iRes] = await Promise.all([
            apiFetch<ApiCollection<PizzaDto>>('/pizzas'),
            apiFetch<ApiCollection<IngredientDto>>('/ingredients'),
        ]);
        pizzas.value = pRes.data.sort((a, b) => a.name.localeCompare(b.name));
        ingredients.value = iRes.data;
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'Error al cargar datos.';
    } finally {
        loading.value = false;
    }
}

async function createPizza() {
    saving.value = true;
    errorMessage.value = null;

    try {
        await apiFetch<ApiResource<PizzaDto>>('/pizzas', {
            method: 'POST',
            body: {
                name: form.value.name.trim(),
                description: form.value.description.trim() || null,
                price: Number(form.value.price),
                ingredient_ids: [...form.value.ingredient_ids],
            },
        });
        form.value = {
            name: '',
            description: '',
            price: '',
            ingredient_ids: [],
        };
        await loadAll();
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'No se pudo crear la pizza.';
    } finally {
        saving.value = false;
    }
}

async function updatePizza(id: string) {
    saving.value = true;
    errorMessage.value = null;

    try {
        await apiFetch<ApiResource<PizzaDto>>(`/pizzas/${id}`, {
            method: 'PUT',
            body: {
                name: editForm.value.name.trim(),
                description: editForm.value.description.trim() || null,
                price: Number(editForm.value.price),
                ingredient_ids: [...editForm.value.ingredient_ids],
            },
        });
        cancelEdit();
        await loadAll();
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'No se pudo actualizar.';
    } finally {
        saving.value = false;
    }
}

async function deletePizza(id: string) {
    if (!confirm('¿Eliminar esta pizza?')) {
        return;
    }

    errorMessage.value = null;

    try {
        await apiFetch(`/pizzas/${id}`, { method: 'DELETE' });

        if (editingId.value === id) {
            cancelEdit();
        }

        await loadAll();
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'No se pudo eliminar.';
    }
}

onMounted(loadAll);
</script>

<template>
    <Head title="Pizzas" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Pizzas
                </h1>
                <p class="text-sm text-muted-foreground">
                    CRUD de pizzas e ingredientes asociados.
                </p>
            </div>
            <Button variant="outline" as-child>
                <Link :href="dashboard()">Volver al panel</Link>
            </Button>
        </div>

        <p
            v-if="errorMessage"
            class="rounded-md border border-destructive/30 bg-destructive/10 px-3 py-2 text-sm text-destructive"
        >
            {{ errorMessage }}
        </p>

        <Card>
            <CardHeader>
                <CardTitle>Nueva pizza</CardTitle>
                <CardDescription>
                    Marca los ingredientes que incluye.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="p-name">Nombre</Label>
                        <Input
                            id="p-name"
                            v-model="form.name"
                            placeholder="Margherita"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="p-price">Precio</Label>
                        <Input
                            id="p-price"
                            v-model="form.price"
                            type="number"
                            min="0.01"
                            step="0.01"
                            placeholder="9.99"
                        />
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="p-desc">Descripción</Label>
                    <Input
                        id="p-desc"
                        v-model="form.description"
                        placeholder="Opcional"
                    />
                </div>
                <div class="space-y-2">
                    <Label>Ingredientes</Label>
                    <div
                        class="max-h-40 overflow-y-auto rounded-md border border-border p-3"
                    >
                        <label
                            v-for="ing in sortedIngredients"
                            :key="ing.id"
                            class="flex cursor-pointer items-center gap-2 py-1 text-sm"
                        >
                            <input
                                type="checkbox"
                                class="size-4 rounded border-input"
                                :checked="ingredientChecked(form.ingredient_ids, ing.id)"
                                @change="
                                    toggleIngredient(
                                        form.ingredient_ids,
                                        ing.id,
                                        ($event.target as HTMLInputElement).checked,
                                    )
                                "
                            />
                            {{ ing.name }}
                        </label>
                    </div>
                </div>
                <Button
                    :disabled="
                        saving ||
                        !form.name.trim() ||
                        !form.price ||
                        Number(form.price) <= 0
                    "
                    @click="createPizza"
                >
                    Crear pizza
                </Button>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Listado</CardTitle>
            </CardHeader>
            <CardContent>
                <p
                    v-if="loading"
                    class="text-muted-foreground"
                >
                    Cargando…
                </p>
                <ul
                    v-else
                    class="space-y-6"
                >
                    <li
                        v-for="p in pizzas"
                        :key="p.id"
                        class="rounded-lg border border-border p-4"
                    >
                        <template v-if="editingId === p.id">
                            <div class="space-y-4">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Nombre</Label>
                                        <Input v-model="editForm.name" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Precio</Label>
                                        <Input
                                            v-model="editForm.price"
                                            type="number"
                                            min="0.01"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <Label>Descripción</Label>
                                    <Input v-model="editForm.description" />
                                </div>
                                <div class="space-y-2">
                                    <Label>Ingredientes</Label>
                                    <div
                                        class="max-h-36 overflow-y-auto rounded-md border p-2"
                                    >
                                        <label
                                            v-for="ing in sortedIngredients"
                                            :key="ing.id"
                                            class="flex items-center gap-2 py-1 text-sm"
                                        >
                                            <input
                                                type="checkbox"
                                                class="size-4 rounded border-input"
                                                :checked="
                                                    ingredientChecked(
                                                        editForm.ingredient_ids,
                                                        ing.id,
                                                    )
                                                "
                                                @change="
                                                    toggleIngredient(
                                                        editForm.ingredient_ids,
                                                        ing.id,
                                                        ($event.target as HTMLInputElement)
                                                            .checked,
                                                    )
                                                "
                                            />
                                            {{ ing.name }}
                                        </label>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Button
                                        size="sm"
                                        :disabled="saving"
                                        @click="updatePizza(p.id)"
                                    >
                                        Guardar
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        @click="cancelEdit"
                                    >
                                        Cancelar
                                    </Button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div>
                                    <h3 class="font-semibold">
                                        {{ p.name }}
                                    </h3>
                                    <p
                                        v-if="p.description"
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ p.description }}
                                    </p>
                                    <p class="mt-1 text-lg font-medium text-primary">
                                        ${{ p.price }}
                                    </p>
                                    <p class="mt-2 text-xs text-muted-foreground">
                                        {{
                                            p.ingredients
                                                .map((i) => i.name)
                                                .join(', ')
                                        }}
                                    </p>
                                </div>
                                <div class="flex shrink-0 gap-2">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        @click="loadEditFromPizza(p)"
                                    >
                                        Editar
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        @click="deletePizza(p.id)"
                                    >
                                        Eliminar
                                    </Button>
                                </div>
                            </div>
                        </template>
                    </li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
