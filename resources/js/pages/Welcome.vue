<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
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
        <header
            class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6"
        >
            <div class="text-lg font-semibold">Expense Tracker</div>

            <nav class="flex items-center gap-3">
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

        <main
            class="mx-auto grid w-full max-w-6xl gap-10 px-6 py-12 md:grid-cols-2 md:items-center"
        >
            <section class="space-y-6">
                <p
                    class="inline-flex rounded-full border px-3 py-1 text-xs font-medium tracking-wide"
                >
                    Лични финанси
                </p>

                <h1 class="text-4xl leading-tight font-bold md:text-5xl">
                    Следи разходите си бързо, ясно и на едно място.
                </h1>

                <p class="max-w-xl text-muted-foreground">
                    Добавяй ежедневни разходи, категоризирай ги и виждай
                    историята си в реално време. Идеален старт за личен бюджет и
                    контрол на месечните разходи.
                </p>

                <div class="flex flex-wrap gap-3">
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
                            Започни сега
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
            </section>

            <section class="rounded-xl border bg-card p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">Как работи</h2>
                <ul class="space-y-3 text-sm text-muted-foreground">
                    <li>1. Влизаш в профила си.</li>
                    <li>
                        2. Добавяш разход: сума, описание, категория и дата.
                    </li>
                    <li>3. Виждаш последните разходи в табличен изглед.</li>
                    <li>4. Изтриваш записите, които не ти трябват.</li>
                </ul>
            </section>
        </main>
    </div>
</template>
