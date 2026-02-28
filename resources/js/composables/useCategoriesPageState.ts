import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import {
    destroy,
    store,
    update,
} from '@/actions/App/Http/Controllers/CategoryController';

export type CategoryItem = {
    id: number;
    name: string;
    expenses_count?: number;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type CategoriesPageProps = {
    categories: {
        data: CategoryItem[];
        current_page: number;
        last_page: number;
        links: PaginationLink[];
        total: number;
        per_page: number;
        from: number | null;
        to: number | null;
    };
};

type FlashPageProps = {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

export const useCategoriesPageState = () => {
    const page = usePage<FlashPageProps>();

    const createForm = useForm({
        name: '',
    });

    const editForm = useForm({
        name: '',
    });

    const isCreateDialogOpen = ref(false);
    const isEditDialogOpen = ref(false);
    const isDeleteDialogOpen = ref(false);
    const editingCategoryId = ref<number | null>(null);
    const selectedCategory = ref<CategoryItem | null>(null);
    const flashToastNonce = ref(0);

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

    const submitCreate = (): void => {
        createForm.submit(store(), {
            preserveScroll: true,
            onSuccess: () => {
                createForm.reset();
                isCreateDialogOpen.value = false;
            },
        });
    };

    const beginEdit = (category: CategoryItem): void => {
        editingCategoryId.value = category.id;
        editForm.name = category.name;
        isEditDialogOpen.value = true;
    };

    const cancelEdit = (): void => {
        isEditDialogOpen.value = false;
        editingCategoryId.value = null;
        editForm.reset();
    };

    const submitEdit = (): void => {
        if (editingCategoryId.value === null) {
            return;
        }

        editForm.submit(update(editingCategoryId.value), {
            preserveScroll: true,
            onSuccess: () => {
                cancelEdit();
            },
        });
    };

    const openDeleteDialog = (category: CategoryItem): void => {
        selectedCategory.value = category;
        isDeleteDialogOpen.value = true;
    };

    const confirmDelete = (): void => {
        if (!selectedCategory.value) {
            return;
        }

        const deleteForm = useForm({});
        deleteForm.submit(destroy(selectedCategory.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                selectedCategory.value = null;
            },
        });
    };

    return {
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
    };
};
