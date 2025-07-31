<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
    <header class="mb-4">
        <h2 class="h5 text-dark">Profile Information</h2>
        <p class="text-muted small">
            Update your account's profile information and email address.
        </p>
    </header>

    <form @submit.prevent="form.patch(route('profile.update'))" class="mt-4">
        <div class="mb-3">
            <InputLabel for="name" value="Name" />

            <TextInput
                id="name"
                type="text"
                class="form-control"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
            />

            <InputError class="text-danger mt-1" :message="form.errors.name" />
        </div>

        <div class="mb-3">
            <InputLabel for="email" value="Email" />

            <TextInput
                id="email"
                type="email"
                class="form-control"
                v-model="form.email"
                required
                autocomplete="username"
            />

            <InputError class="text-danger mt-1" :message="form.errors.email" />
        </div>

        <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mb-3">
            <p class="small text-dark">
                Your email address is unverified.
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="btn btn-link p-0 text-decoration-underline"
                >
                    Click here to re-send the verification email.
                </Link>
            </p>

            <div
                v-show="status === 'verification-link-sent'"
                class="text-success small fw-medium mt-2"
            >
                A new verification link has been sent to your email address.
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <PrimaryButton :disabled="form.processing" class="btn btn-primary">
                Save
            </PrimaryButton>

            <Transition
                enter-active-class="fade"
                enter-from-class="opacity-0"
                leave-active-class="fade"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="small text-muted"
                >
                    Saved.
                </p>
            </Transition>
        </div>
    </form>
</section>

</template>
