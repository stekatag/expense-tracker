<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    ChartColumnIncreasing,
    Github,
    Tags,
    Wallet,
} from 'lucide-vue-next';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Expense Tracker" />

    <div class="min-h-screen bg-background text-foreground">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-linear-to-b from-muted/50 to-transparent"
        />

        <header
            class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6"
        >
            <div class="inline-flex items-center gap-2 text-lg font-semibold">
                <Wallet class="h-5 w-5" />
                <span>Expense Tracker</span>
            </div>

            <nav class="flex items-center gap-3">
                <a
                    href="https://github.com/stekatag/expense-tracker"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="GitHub репозитория"
                    aria-label="GitHub репозитория"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md border hover:bg-accent"
                >
                    <Github class="h-4 w-4" />
                </a>

                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-accent"
                >
                    Табло
                </Link>

                <template v-else>
                    <Link
                        :href="login()"
                        class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-accent"
                    >
                        Вход
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90"
                    >
                        Регистрация
                    </Link>
                </template>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-6xl space-y-10 px-6 py-12">
            <section class="mx-auto w-full max-w-4xl px-2 py-4 md:px-0 md:py-6">
                <div class="space-y-6 text-center">
                    <p
                        class="inline-flex rounded-full border px-3 py-1 text-xs font-medium tracking-wide"
                    >
                        Лични финанси
                    </p>

                    <h1
                        class="mx-auto max-w-3xl text-4xl leading-tight font-bold md:text-5xl"
                    >
                        Следи разходите си бързо, лесно и на едно място.
                    </h1>

                    <p class="mx-auto max-w-2xl text-muted-foreground">
                        Добавяй ежедневни разходи, категоризирай ги и виждай
                        историята си в реално време. Планирай по-добре бюджета
                        си, следи тенденциите и поддържай контрол върху
                        месечните разходи.
                    </p>

                    <div class="flex flex-wrap justify-center gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground hover:opacity-90"
                        >
                            Към таблото
                        </Link>

                        <template v-else>
                            <Link
                                :href="login()"
                                class="rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground hover:opacity-90"
                            >
                                <span class="inline-flex items-center gap-2">
                                    Започни сега
                                    <ArrowRight class="h-4 w-4" />
                                </span>
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-md border px-5 py-3 text-sm font-semibold hover:bg-accent"
                            >
                                Създай акаунт
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-semibold">Какво получаваш</h2>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border bg-card p-4">
                        <Wallet class="mb-2 h-4 w-4 text-muted-foreground" />
                        <p class="text-sm font-medium">Бързо добавяне</p>
                        <p class="text-xs text-muted-foreground">
                            Записваш разход за секунди.
                        </p>
                    </div>
                    <div class="rounded-lg border bg-card p-4">
                        <Tags class="mb-2 h-4 w-4 text-muted-foreground" />
                        <p class="text-sm font-medium">Категоризация</p>
                        <p class="text-xs text-muted-foreground">
                            Подреждаш бюджета по категории.
                        </p>
                    </div>
                    <div class="rounded-lg border bg-card p-4">
                        <ChartColumnIncreasing
                            class="mb-2 h-4 w-4 text-muted-foreground"
                        />
                        <p class="text-sm font-medium">Преглед</p>
                        <p class="text-xs text-muted-foreground">
                            Виждаш ясно как харчиш.
                        </p>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4 rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">Как работи</h2>
                    <ul class="space-y-3 text-sm text-muted-foreground">
                        <li>1. Влизаш в профила си или създаваш нов акаунт.</li>
                        <li>
                            2. Добавяш разход: сума, описание, категория и дата.
                        </li>
                        <li>
                            3. Филтрираш по категория и период, за да намериш
                            конкретни разходи.
                        </li>
                        <li>
                            4. Редактираш или изтриваш записи и следиш общи
                            суми.
                        </li>
                    </ul>
                </div>

                <div class="space-y-4 rounded-xl border bg-card p-6 shadow-sm">
                    <h2 class="text-lg font-semibold">За какво е подходящо?</h2>
                    <p class="text-sm text-muted-foreground">
                        Личен месечен бюджет, ежедневен контрол на разходите и
                        бърз поглед върху най-активните категории.
                    </p>
                    <div class="rounded-lg border bg-muted/40 p-4 text-sm">
                        <p class="font-medium">Примерен сценарий</p>
                        <p class="mt-1 text-muted-foreground">
                            Въвеждаш разходите през месеца, филтрираш по „Храна“
                            и „Транспорт“, и виждаш къде отива най-голяма част
                            от бюджета.
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
