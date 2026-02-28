import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/ExpenseController';
import { dashboard } from '@/routes';

export interface Category {
    id: number;
    name: string;
}

export interface Expense {
    id: number;
    amount: string;
    description: string;
    date: string;
    category: Category;
}

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type PaginatedResponse<TItem> = {
    data: TItem[];
    current_page: number;
    last_page: number;
    links: PaginationLink[];
    total: number;
    per_page: number;
    from: number | null;
    to: number | null;
};

export type DashboardPageProps = {
    expenses: PaginatedResponse<Expense>;
    categories: Category[];
    filters: {
        category_id: number | null;
        from: string | null;
        to: string | null;
    };
    stats: {
        total_amount: number;
        average_amount: number;
        count: number;
    };
};

type FlashPageProps = {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

const getTodayYmd = (): string => {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

export const useExpenseDashboardState = (props: DashboardPageProps) => {
    const page = usePage<FlashPageProps>();

    const form = useForm({
        amount: '',
        description: '',
        category_id: '',
        date: '',
    });

    const filterForm = useForm({
        category_id: props.filters.category_id
            ? String(props.filters.category_id)
            : '',
        from: props.filters.from ?? '',
        to: props.filters.to ?? '',
    });

    const editForm = useForm({
        amount: '',
        description: '',
        category_id: '',
        date: '',
    });

    const editingExpenseId = ref<number | null>(null);
    const isCreateDialogOpen = ref(false);
    const isEditDialogOpen = ref(false);
    const isDeleteDialogOpen = ref(false);
    const deletingExpense = ref<Expense | null>(null);

    const filterDateRange = computed({
        get: () => ({
            from: filterForm.from || null,
            to: filterForm.to || null,
        }),
        set: (value: { from: string | null; to: string | null }) => {
            filterForm.from = value.from ?? '';
            filterForm.to = value.to ?? '';
        },
    });

    const flashToastNonce = ref(0);
    const isClearingFilters = ref(false);
    const hasActiveFiltersInUrl = computed(() => {
        const queryString = page.url.split('?')[1] ?? '';
        const searchParams = new URLSearchParams(queryString);

        return (
            searchParams.has('category_id') ||
            searchParams.has('from') ||
            searchParams.has('to')
        );
    });

    watch(
        () => page.props.flash,
        (flash) => {
            flashToastNonce.value += 1;

            if (flash?.success) {
                toast.success(flash.success, {
                    id: `success-${flashToastNonce.value}`,
                });
            }

            if (flash?.error) {
                toast.error(flash.error, {
                    id: `error-${flashToastNonce.value}`,
                });
            }
        },
        { immediate: true },
    );

    const submitExpense = (): void => {
        form.submit(store(), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                isCreateDialogOpen.value = false;
            },
        });
    };

    const applyFilters = (): void => {
        const query: Record<string, string> = {};

        if (filterForm.category_id) {
            query.category_id = filterForm.category_id;
        }

        if (filterForm.from) {
            query.from = filterForm.from;
        }

        if (filterForm.to) {
            query.to = filterForm.to;
        }

        router.get(
            dashboard({ query }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    watch(
        () => [filterForm.category_id, filterForm.from, filterForm.to],
        () => {
            if (isClearingFilters.value) {
                return;
            }

            applyFilters();
        },
    );

    const clearFilters = (): void => {
        isClearingFilters.value = true;
        filterForm.category_id = '';
        filterForm.from = '';
        filterForm.to = '';
        applyFilters();
        isClearingFilters.value = false;
    };

    watch(isCreateDialogOpen, (isOpen) => {
        if (isOpen && !form.date) {
            form.date = getTodayYmd();
        }
    });

    const beginEdit = (expense: Expense): void => {
        editingExpenseId.value = expense.id;
        editForm.amount = expense.amount;
        editForm.description = expense.description;
        editForm.category_id = String(expense.category.id);
        editForm.date = expense.date;
        isEditDialogOpen.value = true;
    };

    const cancelEdit = (): void => {
        isEditDialogOpen.value = false;
        editingExpenseId.value = null;
        editForm.reset();
    };

    const submitEdit = (): void => {
        if (editingExpenseId.value === null) {
            return;
        }

        editForm.submit(update(editingExpenseId.value), {
            preserveScroll: true,
            onSuccess: () => {
                cancelEdit();
            },
        });
    };

    const openDeleteDialog = (expense: Expense): void => {
        deletingExpense.value = expense;
        isDeleteDialogOpen.value = true;
    };

    const confirmDelete = (): void => {
        if (!deletingExpense.value) {
            return;
        }

        const deleteForm = useForm({});
        deleteForm.submit(destroy(deletingExpense.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                deletingExpense.value = null;
            },
        });
    };

    const formatDate = (date: string): string => {
        return new Date(date).toLocaleDateString('bg-BG');
    };

    const formatCurrency = (amount: string): string => {
        return new Intl.NumberFormat('bg-BG', {
            style: 'currency',
            currency: 'EUR',
        }).format(Number.parseFloat(amount));
    };

    const formatNumberCurrency = (amount: number): string => {
        return new Intl.NumberFormat('bg-BG', {
            style: 'currency',
            currency: 'EUR',
        }).format(amount);
    };

    return {
        form,
        filterForm,
        editForm,
        editingExpenseId,
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
    };
};
