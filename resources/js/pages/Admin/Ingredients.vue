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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { apiFetch } from '@/lib/api';
import { admin } from '@/routes';
import type { ApiCollection, ApiResource, IngredientDto } from '@/types/api';
import { ApiError } from '@/types/api';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: admin() },
            { title: 'Ingredientes', href: '/admin/ingredients' },
        ],
    },
});

const ingredients = ref<IngredientDto[]>([]);
const loading = ref(true);
const saving = ref(false);
const formName = ref('');
const editingId = ref<string | null>(null);
const editName = ref('');
const errorMessage = ref<string | null>(null);
const fieldErrors = ref<Record<string, string>>({});

async function load() {
    loading.value = true;
    errorMessage.value = null;

    try {
        const res = await apiFetch<ApiCollection<IngredientDto>>(
            '/ingredients',
        );
        ingredients.value = res.data.sort((a, b) =>
            a.name.localeCompare(b.name),
        );
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'Error al cargar ingredientes.';
    } finally {
        loading.value = false;
    }
}

function startEdit(ing: IngredientDto) {
    editingId.value = ing.id;
    editName.value = ing.name;
    fieldErrors.value = {};
}

function cancelEdit() {
    editingId.value = null;
    editName.value = '';
}

async function createIngredient() {
    saving.value = true;
    fieldErrors.value = {};
    errorMessage.value = null;

    try {
        await apiFetch<ApiResource<IngredientDto>>('/ingredients', {
            method: 'POST',
            body: { name: formName.value.trim() },
        });
        formName.value = '';
        await load();
    } catch (e) {
        if (e instanceof ApiError) {
            errorMessage.value = e.message;

            if (e.errors?.name?.[0]) {
                fieldErrors.value.name = e.errors.name[0];
            }
        }
    } finally {
        saving.value = false;
    }
}

async function updateIngredient(id: string) {
    saving.value = true;
    fieldErrors.value = {};
    errorMessage.value = null;

    try {
        await apiFetch<ApiResource<IngredientDto>>(`/ingredients/${id}`, {
            method: 'PUT',
            body: { name: editName.value.trim() },
        });
        cancelEdit();
        await load();
    } catch (e) {
        if (e instanceof ApiError) {
            errorMessage.value = e.message;

            if (e.errors?.name?.[0]) {
                fieldErrors.value.name = e.errors.name[0];
            }
        }
    } finally {
        saving.value = false;
    }
}

async function deleteIngredient(id: string) {
    if (!confirm('¿Eliminar este ingrediente?')) {
        return;
    }

    errorMessage.value = null;

    try {
        await apiFetch(`/ingredients/${id}`, { method: 'DELETE' });
        await load();
    } catch (e) {
        errorMessage.value =
            e instanceof ApiError ? e.message : 'No se pudo eliminar.';
    }
}

onMounted(load);
</script>

<template>
    <Head title="Ingredientes" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Ingredientes
                </h1>
                <p class="text-sm text-muted-foreground">
                    CRUD de ingredientes (API).
                </p>
            </div>
            <Button variant="outline" as-child>
                    <Link :href="admin()">Volver al panel</Link>
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
                <CardTitle>Nuevo ingrediente</CardTitle>
                <CardDescription>
                    El nombre debe ser único.
                </CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1 space-y-2">
                    <Label for="new-name">Nombre</Label>
                    <Input
                        id="new-name"
                        v-model="formName"
                        placeholder="Ej. Mozzarella"
                        @keyup.enter="createIngredient"
                    />
                    <p
                        v-if="fieldErrors.name && !editingId"
                        class="text-sm text-destructive"
                    >
                        {{ fieldErrors.name }}
                    </p>
                </div>
                <Button
                    :disabled="saving || !formName.trim()"
                    @click="createIngredient"
                >
                    Crear
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
                    class="divide-y divide-border"
                >
                    <li
                        v-for="ing in ingredients"
                        :key="ing.id"
                        class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <template v-if="editingId === ing.id">
                            <Input
                                v-model="editName"
                                class="max-w-md"
                                @keyup.enter="updateIngredient(ing.id)"
                            />
                            <div class="flex gap-2">
                                <Button
                                    size="sm"
                                    :disabled="saving"
                                    @click="updateIngredient(ing.id)"
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
                        </template>
                        <template v-else>
                            <span class="font-medium">{{ ing.name }}</span>
                            <div class="flex gap-2">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="startEdit(ing)"
                                >
                                    Editar
                                </Button>
                                <Button
                                    size="sm"
                                    variant="destructive"
                                    @click="deleteIngredient(ing.id)"
                                >
                                    Eliminar
                                </Button>
                            </div>
                        </template>
                    </li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
