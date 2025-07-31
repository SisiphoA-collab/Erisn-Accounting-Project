<script setup>
import { Head, Link } from '@inertiajs/vue3';
import img from '../../assets/bg-image.jpg';
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>


<template>
    <Head title="Home">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div :style="`background-image: url(${img});`"
        class="d-flex flex-column align-items-center justify-content-center min-vh-100 text-white p-4"
        style="background-size: cover; background-position: center; background-color: #1b1b18;">

        <header class="position-absolute top-0 w-100 mt-4">
            <nav class="d-flex justify-content-end gap-3 px-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="btn btn-outline-light fw-bold">
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="btn btn-outline-info fw-bold">
                        Log in
                    </Link>
                    <Link :href="route('register')" class="btn btn-outline-light fw-bold">
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <div class="container mt-5">
            <main class="p-5 rounded">
                <div class="text-center bg-dark bg-opacity-25 glass-card">
                    <h1 class="display-1 fw-bold">
                        <span class="d-block text">Welcome to</span>
                        <span class="d-block text">Erisn Accounting</span>
                        <span class="d-block text">System</span>
                    </h1>
                </div>
            </main>
        </div>
    </div>
</template>
