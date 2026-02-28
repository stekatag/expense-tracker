<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    links: PaginationLink[];
    total: number;
    from: number | null;
    to: number | null;
}>();
</script>

<template>
    <div
        class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <p class="text-sm text-muted-foreground">
            Показани {{ from ?? 0 }}-{{ to ?? 0 }} от {{ total }}
        </p>

        <div class="flex flex-wrap justify-end gap-2">
            <template
                v-for="(link, index) in links"
                :key="`${link.label}-${index}`"
            >
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors hover:bg-accent"
                    :class="{
                        'bg-accent font-semibold': link.active,
                    }"
                >
                    <ChevronsLeft v-if="index === 0" class="h-4 w-4" />
                    <ChevronsRight
                        v-else-if="index === links.length - 1"
                        class="h-4 w-4"
                    />
                    <span v-else v-html="link.label" />
                </Link>
                <span
                    v-else
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm opacity-50"
                >
                    <ChevronsLeft v-if="index === 0" class="h-4 w-4" />
                    <ChevronsRight
                        v-else-if="index === links.length - 1"
                        class="h-4 w-4"
                    />
                    <span v-else v-html="link.label" />
                </span>
            </template>
        </div>
    </div>
</template>
