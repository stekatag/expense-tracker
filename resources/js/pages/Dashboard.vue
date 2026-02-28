<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { FilterX, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import AppPagination from '@/components/AppPagination.vue';
import DatePicker from '@/components/DatePicker.vue';
import DateRangePicker from '@/components/DateRangePicker.vue';
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
import { useExpenseDashboardState } from '@/composables/useExpenseDashboardState';
import type { DashboardPageProps } from '@/composables/useExpenseDashboardState';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<DashboardPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Табло',
        href: dashboard(),
    },
];

const {
    form,
    filterForm,
    editForm,
    isCreateDialogOpen,
    isEditDialogOpen,
    isDeleteDialogOpen,
    deletingExpense,
    filterDateRange,
    hasActiveFiltersInUrl,
    submitExpense,
    clearFilters,
    beginEdit,
    cancelEdit,
    submitEdit,
    openDeleteDialog,
    confirmDelete,
    formatDate,
    formatCurrency,
    formatNumberCurrency,
} = useExpenseDashboardState(props);
</script>

<template>
    <Head title="Табло" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="grid gap-4 sm:grid-cols-3">
                <section class="rounded-xl border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Общо разходи</p>
                    <p class="mt-1 text-xl font-bold">
                        {{ formatNumberCurrency(props.stats.total_amount) }}
                    </p>
                </section>
                <section class="rounded-xl border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Брой записи</p>
                    <p class="mt-1 text-xl font-bold">
                        {{ props.stats.count }}
                    </p>
                </section>
                <section class="rounded-xl border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Среден разход</p>
                    <p class="mt-1 text-xl font-bold">
                        {{ formatNumberCurrency(props.stats.average_amount) }}
                    </p>
                </section>
            </div>

            <section class="rounded-xl border bg-card p-4">
                <h2 class="mb-3 text-lg font-semibold">Филтри</h2>

                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium"
                            for="filter_category"
                            >Категория</label
                        >
                        <select
                            id="filter_category"
                            v-model="filterForm.category_id"
                            class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                        >
                            <option value="">Всички</option>
                            <option
                                v-for="category in props.categories"
                                :key="category.id"
                                :value="String(category.id)"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium"
                            >Период</label
                        >
                        <div class="flex items-center gap-2">
                            <DateRangePicker v-model="filterDateRange" />
                            <Button
                                v-if="hasActiveFiltersInUrl"
                                type="button"
                                variant="outline"
                                size="icon"
                                title="Изчисти филтрите"
                                aria-label="Изчисти филтрите"
                                @click="clearFilters"
                            >
                                <FilterX class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border bg-card p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Последни разходи</h2>

                    <Dialog v-model:open="isCreateDialogOpen">
                        <DialogTrigger as-child>
                            <Button type="button" class="gap-2">
                                <Plus class="h-4 w-4" />
                                Добави разход
                            </Button>
                        </DialogTrigger>

                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Добави разход</DialogTitle>
                                <DialogDescription>
                                    Попълни информацията за новия разход.
                                </DialogDescription>
                            </DialogHeader>

                            <form
                                class="space-y-4"
                                @submit.prevent="submitExpense"
                            >
                                <div>
                                    <label
                                        for="amount"
                                        class="mb-1 block text-sm font-medium"
                                        >Сума (€)</label
                                    >
                                    <input
                                        id="amount"
                                        v-model="form.amount"
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.amount"
                                        class="mt-1"
                                    />
                                </div>

                                <div>
                                    <label
                                        for="description"
                                        class="mb-1 block text-sm font-medium"
                                        >Описание</label
                                    >
                                    <input
                                        id="description"
                                        v-model="form.description"
                                        type="text"
                                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.description"
                                        class="mt-1"
                                    />
                                </div>

                                <div>
                                    <label
                                        for="category_id"
                                        class="mb-1 block text-sm font-medium"
                                        >Категория</label
                                    >
                                    <select
                                        id="category_id"
                                        v-model="form.category_id"
                                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="" disabled>
                                            Избери категория...
                                        </option>
                                        <option
                                            v-for="category in props.categories"
                                            :key="category.id"
                                            :value="String(category.id)"
                                        >
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.category_id"
                                        class="mt-1"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium"
                                        >Дата</label
                                    >
                                    <DatePicker
                                        v-model="form.date"
                                        placeholder="Избери дата"
                                        position="top"
                                    />
                                    <InputError
                                        :message="form.errors.date"
                                        class="mt-1"
                                    />
                                </div>

                                <DialogFooter>
                                    <Button
                                        type="submit"
                                        :disabled="form.processing"
                                        >Запази</Button
                                    >
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <Dialog v-model:open="isEditDialogOpen">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Редактирай разход</DialogTitle>
                            <DialogDescription>
                                Обнови информацията за избрания разход.
                            </DialogDescription>
                        </DialogHeader>

                        <form class="space-y-4" @submit.prevent="submitEdit">
                            <div>
                                <label class="mb-1 block text-sm font-medium"
                                    >Сума (€)</label
                                >
                                <input
                                    v-model="editForm.amount"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                    required
                                />
                                <InputError
                                    :message="editForm.errors.amount"
                                    class="mt-1"
                                />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium"
                                    >Категория</label
                                >
                                <select
                                    v-model="editForm.category_id"
                                    class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="" disabled>
                                        Избери категория...
                                    </option>
                                    <option
                                        v-for="category in props.categories"
                                        :key="category.id"
                                        :value="String(category.id)"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="editForm.errors.category_id"
                                    class="mt-1"
                                />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium"
                                    >Описание</label
                                >
                                <input
                                    v-model="editForm.description"
                                    type="text"
                                    class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                                    required
                                />
                                <InputError
                                    :message="editForm.errors.description"
                                    class="mt-1"
                                />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium"
                                    >Дата</label
                                >
                                <DatePicker
                                    v-model="editForm.date"
                                    placeholder="Избери дата"
                                    position="top"
                                />
                                <InputError
                                    :message="editForm.errors.date"
                                    class="mt-1"
                                />
                            </div>

                            <DialogFooter>
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="cancelEdit"
                                    >Отказ</Button
                                >
                                <Button
                                    type="submit"
                                    :disabled="editForm.processing"
                                    >Запази промените</Button
                                >
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="isDeleteDialogOpen">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Потвърди изтриване</DialogTitle>
                            <DialogDescription>
                                Сигурни ли сте, че искате да изтриете разхода
                                <span
                                    v-if="deletingExpense"
                                    class="font-semibold"
                                    >„{{ deletingExpense.description }}“</span
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
                    v-if="props.expenses.data.length === 0"
                    class="rounded-md border border-dashed p-6 text-sm text-muted-foreground"
                >
                    Все още нямаш добавени разходи.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b text-muted-foreground">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">
                                    Дата
                                </th>
                                <th class="px-3 py-2 text-left font-medium">
                                    Описание
                                </th>
                                <th class="px-3 py-2 text-left font-medium">
                                    Категория
                                </th>
                                <th class="px-3 py-2 text-right font-medium">
                                    Сума
                                </th>
                                <th class="px-3 py-2 text-right font-medium">
                                    Действие
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="expense in props.expenses.data"
                                :key="expense.id"
                                class="border-b last:border-b-0"
                            >
                                <td class="px-3 py-3">
                                    {{ formatDate(expense.date) }}
                                </td>
                                <td class="px-3 py-3 font-medium">
                                    {{ expense.description }}
                                </td>
                                <td class="px-3 py-3">
                                    {{ expense.category.name }}
                                </td>
                                <td class="px-3 py-3 text-right font-semibold">
                                    {{ formatCurrency(expense.amount) }}
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="icon"
                                            title="Редактирай"
                                            @click="beginEdit(expense)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="icon"
                                            title="Изтрий"
                                            @click="openDeleteDialog(expense)"
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
                    :links="props.expenses.links"
                    :total="props.expenses.total"
                    :from="props.expenses.from"
                    :to="props.expenses.to"
                />
            </section>
        </div>
    </AppLayout>
</template>
