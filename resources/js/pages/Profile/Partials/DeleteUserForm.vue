<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="mb-5">
    <header class="mb-4">
        <h2 class="h5 text-dark">Delete Account</h2>
        <p class="text-muted small">
            Once your account is deleted, all of its resources and data will be permanently deleted.
            Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <DangerButton @click="confirmUserDeletion" class="btn btn-danger">
        Delete Account
    </DangerButton>

    <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div class="p-4">
            <h2 class="h5 text-dark mb-2">
                Are you sure you want to delete your account?
            </h2>

            <p class="text-muted small mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted.
                Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mb-4">
                <InputLabel for="password" value="Password" class="visually-hidden" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="form-control w-75"
                    placeholder="Password"
                    @keyup.enter="deleteUser"
                />

                <InputError :message="form.errors.password" class="text-danger mt-1" />
            </div>

            <div class="d-flex justify-content-end">
                <SecondaryButton @click="closeModal" class="btn btn-secondary">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="btn btn-danger ms-2"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    Delete Account
                </DangerButton>
            </div>
        </div>
    </Modal>
</section>
</template>
