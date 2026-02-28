<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { index as categoriesIndex } from '@/actions/App/Http/Controllers/CategoryController';
import AppPagination from '@/components/AppPagination.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { useCategoriesPageState } from '@/composables/useCategoriesPageState';
import type { CategoriesPageProps } from '@/composables/useCategoriesPageState';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<CategoriesPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Табло',
        href: dashboard(),
    },
    {
        title: 'Категории',
        href: categoriesIndex(),
    },
];

const {
    createForm,
    editForm,
    isCreateDialogOpen,
    isEditDialogOpen,
    isDeleteDialogOpen,
    selectedCategory,
    submitCreate,
    beginEdit,
    cancelEdit,
    submitEdit,
    openDeleteDialog,
    confirmDelete,
} = useCategoriesPageState();
</script>

<template>
    <Head title="Категории" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <section class="rounded-xl border bg-card p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Категории</h2>

                    <Dialog v-model:open="isCreateDialogOpen">
                        <DialogTrigger as-child>
                            <Button type="button" class="gap-2">
                                <Plus class="h-4 w-4" />
                                Добави категория
                            </Button>
                        </DialogTrigger>

                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Добави категория</DialogTitle>
                                <DialogDescription>
                                    Въведи име на новата категория.
                                </DialogDescription>
                            </DialogHeader>

                            <form
                                class="space-y-4"
                                @submit.prevent="submitCreate"
                            >
                                <div>
                                    <label
                                        for="category_name"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        Име
                                    </label>
                                    <input
                                        id="category_name"
                                        v-model="createForm.name"
                                        type="text"
                                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                        required
                                    />
                                    <InputError
                                        :message="createForm.errors.name"
                                        class="mt-1"
                                    />
                                </div>

                                <DialogFooter>
                                    <Button
                                        type="submit"
                                        :disabled="createForm.processing"
                                    >
                                        Запази
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <Dialog v-model:open="isEditDialogOpen">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Редактирай категория</DialogTitle>
                            <DialogDescription>
                                Обнови името на избраната категория.
                            </DialogDescription>
                        </DialogHeader>

                        <form class="space-y-4" @submit.prevent="submitEdit">
                            <div>
                                <label class="mb-1 block text-sm font-medium">
                                    Име
                                </label>
                                <input
                                    v-model="editForm.name"
                                    type="text"
                                    class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                    required
                                />
                                <InputError
                                    :message="editForm.errors.name"
                                    class="mt-1"
                                />
                            </div>

                            <DialogFooter>
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="cancelEdit"
                                >
                                    Отказ
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="editForm.processing"
                                >
                                    Запази промените
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="isDeleteDialogOpen">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Потвърди изтриване</DialogTitle>
                            <DialogDescription>
                                Сигурни ли сте, че искате да изтриете
                                категорията
                                <span
                                    v-if="selectedCategory"
                                    class="font-semibold"
                                    >„{{ selectedCategory.name }}“</span
                                >?
                            </DialogDescription>
                        </DialogHeader>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="isDeleteDialogOpen = false"
                            >
                                Отказ
                            </Button>
                            <Button
                                type="button"
                                variant="destructive"
                                @click="confirmDelete"
                            >
                                Изтрий
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <div
                    v-if="props.categories.data.length === 0"
                    class="rounded-md border border-dashed p-6 text-sm text-muted-foreground"
                >
                    Все още няма добавени категории.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b text-muted-foreground">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">
                                    Категория
                                </th>
                                <th class="px-3 py-2 text-right font-medium">
                                    Разходи
                                </th>
                                <th class="px-3 py-2 text-right font-medium">
                                    Действие
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="category in props.categories.data"
                                :key="category.id"
                                class="border-b last:border-b-0"
                            >
                                <td class="px-3 py-3 font-medium">
                                    {{ category.name }}
                                </td>
                                <td class="px-3 py-3 text-right">
                                    {{ category.expenses_count ?? 0 }}
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="icon"
                                            title="Редактирай"
                                            @click="beginEdit(category)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="icon"
                                            title="Изтрий"
                                            @click="openDeleteDialog(category)"
                                        >
                                            <Trash2
                                                class="h-4 w-4 text-destructive"
                                            />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppPagination
                    :links="props.categories.links"
                    :total="props.categories.total"
                    :from="props.categories.from"
                    :to="props.categories.to"
                />
            </section>
        </div>
    </AppLayout>
</template>
