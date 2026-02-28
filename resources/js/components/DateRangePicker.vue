<script setup lang="ts">
import type { DateRange } from 'reka-ui';
import type { Ref } from 'vue';

import {
    DateFormatter,
    getLocalTimeZone,
    parseDate,
} from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

type DateRangeFilter = {
    from: string | null;
    to: string | null;
};

const props = withDefaults(
    defineProps<{
        modelValue: DateRangeFilter;
        placeholder?: string;
    }>(),
    {
        placeholder: 'Избери период',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: DateRangeFilter];
}>();

const value = ref<DateRange>({
    start: props.modelValue.from ? parseDate(props.modelValue.from) : undefined,
    end: props.modelValue.to ? parseDate(props.modelValue.to) : undefined,
}) as Ref<DateRange>;

watch(
    () => props.modelValue,
    (modelValue) => {
        value.value = {
            start: modelValue.from ? parseDate(modelValue.from) : undefined,
            end: modelValue.to ? parseDate(modelValue.to) : undefined,
        };
    },
    { deep: true },
);

watch(
    value,
    (range) => {
        emit('update:modelValue', {
            from: range.start ? range.start.toString() : null,
            to: range.end ? range.end.toString() : null,
        });
    },
    { deep: true },
);

const hasSelectedRange = computed(() => value.value.start || value.value.end);
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="
                    cn(
                        'w-70 justify-start text-left font-normal',
                        !hasSelectedRange && 'text-muted-foreground',
                    )
                "
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                <template v-if="value.start">
                    <template v-if="value.end">
                        {{ df.format(value.start.toDate(getLocalTimeZone())) }}
                        - {{ df.format(value.end.toDate(getLocalTimeZone())) }}
                    </template>

                    <template v-else>
                        {{ df.format(value.start.toDate(getLocalTimeZone())) }}
                    </template>
                </template>
                <template v-else> {{ placeholder }} </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <RangeCalendar
                v-model="value"
                initial-focus
                :number-of-months="2"
                @update:start-value="(startDate) => (value.start = startDate)"
            />
        </PopoverContent>
    </Popover>
</template>
