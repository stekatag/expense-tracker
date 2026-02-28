<script setup lang="ts">
import { onClickOutside } from '@vueuse/core';
import { CalendarDays, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
        position?: 'top' | 'bottom';
    }>(),
    {
        placeholder: 'Избери дата',
        position: 'bottom',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const isOpen = ref(false);
const rootRef = ref<HTMLElement | null>(null);

const today = new Date();
const initialDate = props.modelValue ? new Date(props.modelValue) : today;

const visibleMonth = ref(initialDate.getMonth());
const visibleYear = ref(initialDate.getFullYear());

const selectedDate = computed(() => {
    if (!props.modelValue) {
        return null;
    }

    return new Date(props.modelValue);
});

const monthLabel = computed(() => {
    const date = new Date(visibleYear.value, visibleMonth.value, 1);

    return date.toLocaleDateString('bg-BG', {
        month: 'long',
        year: 'numeric',
    });
});

const weekdayLabels = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд'];

const visibleDays = computed(() => {
    const firstDayOfMonth = new Date(visibleYear.value, visibleMonth.value, 1);
    const lastDayOfMonth = new Date(
        visibleYear.value,
        visibleMonth.value + 1,
        0,
    );

    const firstWeekday = (firstDayOfMonth.getDay() + 6) % 7;
    const daysInMonth = lastDayOfMonth.getDate();

    const days: Array<{ value: Date; isCurrentMonth: boolean }> = [];

    for (let index = firstWeekday; index > 0; index--) {
        days.push({
            value: new Date(visibleYear.value, visibleMonth.value, 1 - index),
            isCurrentMonth: false,
        });
    }

    for (let day = 1; day <= daysInMonth; day++) {
        days.push({
            value: new Date(visibleYear.value, visibleMonth.value, day),
            isCurrentMonth: true,
        });
    }

    const remainder = days.length % 7;
    if (remainder > 0) {
        const trailingDays = 7 - remainder;

        for (let day = 1; day <= trailingDays; day++) {
            days.push({
                value: new Date(visibleYear.value, visibleMonth.value + 1, day),
                isCurrentMonth: false,
            });
        }
    }

    return days;
});

const buttonLabel = computed(() => {
    if (!selectedDate.value) {
        return props.placeholder;
    }

    return selectedDate.value.toLocaleDateString('bg-BG');
});

const panelPositionClass = computed(() => {
    if (props.position === 'top') {
        return 'bottom-full mb-2';
    }

    return 'top-full mt-2';
});

const isSameDay = (firstDate: Date | null, secondDate: Date): boolean => {
    if (!firstDate) {
        return false;
    }

    return (
        firstDate.getFullYear() === secondDate.getFullYear() &&
        firstDate.getMonth() === secondDate.getMonth() &&
        firstDate.getDate() === secondDate.getDate()
    );
};

const toYmd = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const selectDate = (date: Date): void => {
    emit('update:modelValue', toYmd(date));
    isOpen.value = false;
};

const showPreviousMonth = (): void => {
    if (visibleMonth.value === 0) {
        visibleMonth.value = 11;
        visibleYear.value -= 1;

        return;
    }

    visibleMonth.value -= 1;
};

const showNextMonth = (): void => {
    if (visibleMonth.value === 11) {
        visibleMonth.value = 0;
        visibleYear.value += 1;

        return;
    }

    visibleMonth.value += 1;
};

watch(
    () => props.modelValue,
    (value) => {
        if (!value) {
            return;
        }

        const date = new Date(value);
        visibleMonth.value = date.getMonth();
        visibleYear.value = date.getFullYear();
    },
);

onClickOutside(rootRef, () => {
    isOpen.value = false;
});
</script>

<template>
    <div ref="rootRef" class="relative">
        <button
            type="button"
            class="flex w-full items-center justify-between rounded-md border bg-background px-3 py-2 text-sm"
            @click="isOpen = !isOpen"
        >
            <span>{{ buttonLabel }}</span>
            <CalendarDays class="h-4 w-4 text-muted-foreground" />
        </button>

        <div
            v-if="isOpen"
            :class="[
                'absolute z-50 w-80 rounded-xl border bg-card p-4 shadow-lg',
                panelPositionClass,
            ]"
        >
            <div class="mb-4 flex items-center justify-between">
                <button
                    type="button"
                    class="rounded-md border p-1 hover:bg-accent"
                    @click="showPreviousMonth"
                >
                    <ChevronLeft class="h-4 w-4" />
                </button>

                <p class="text-sm font-semibold capitalize">{{ monthLabel }}</p>

                <button
                    type="button"
                    class="rounded-md border p-1 hover:bg-accent"
                    @click="showNextMonth"
                >
                    <ChevronRight class="h-4 w-4" />
                </button>
            </div>

            <div class="mb-2 grid grid-cols-7 gap-1">
                <span
                    v-for="weekday in weekdayLabels"
                    :key="weekday"
                    class="py-1 text-center text-xs font-medium text-muted-foreground"
                >
                    {{ weekday }}
                </span>
            </div>

            <div class="grid grid-cols-7 gap-1">
                <button
                    v-for="day in visibleDays"
                    :key="toYmd(day.value)"
                    type="button"
                    class="rounded-md py-1.5 text-center text-sm hover:bg-accent"
                    :class="{
                        'opacity-50': !day.isCurrentMonth,
                        'bg-primary text-primary-foreground hover:bg-primary':
                            isSameDay(selectedDate, day.value),
                    }"
                    @click="selectDate(day.value)"
                >
                    {{ day.value.getDate() }}
                </button>
            </div>
        </div>
    </div>
</template>
