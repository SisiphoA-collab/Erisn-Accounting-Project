<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const collapsed = ref(true)

// Get the page data from Inertia
const { props } = usePage();

// Use the pageTitle from the props (or fallback to a default title)
const pageTitle = computed(() => props.pageTitle || 'Dashboard');

// Update the document title dynamically
document.title = pageTitle.value;

const getInitials = (fullName) => {
  const parts = fullName.trim().split(' ');
  const first = parts[0]?.[0] || '';
  const last = parts.length > 1 ? parts[parts.length - 1][0] : '';
  return (first + last).toUpperCase();
}

</script>

<template>
    <div id="app">
        <div class="p-0 user-select-none">
            <div class="">
                <!-- Sidebar -->
                <Sidebar :collapsed="collapsed" />
        
                <!-- navbar  -->
                <div class="flex-grow-1 py-2 bg-nav d-flex fixed-top"
                    :style="{ marginLeft: collapsed ? '65px' : '250px', transition: 'margin-left 0.3s' }">

                    <div class="d-flex text-center align-items-center text-white">
                        <button @click.prevent="collapsed = !collapsed" class="btn text-white d-flex align-items-center">
                            <i class="fas fa-bars"></i>
                            <span class="ms-2">{{ pageTitle }}</span>
                        </button>
                    </div>

                    <div class="col text-end text-w px-5">
                        <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                        <button
                                                type="button"
                                                class="inline-flex items-center space-x-3 rounded-md border border-transparent bg-white px-4 py-1 text-sm font-medium text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out focus:outline-none"
                                            >
                                                <!-- Avatar Circle -->
                                                <span
                                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-700 text-white text-xs font-semibold"
                                                >
                                                    {{ getInitials($page.props.auth.user.name) }}
                                                </span>

                                                <!-- Name and optional email -->
                                                <div class="flex flex-col text-left">
                                                    <span class="font-medium leading-tight">
                                                        {{$page.props.auth.user.name }}
                                                    </span>
                                                </div>
                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                         </button>
                                        </span>
                                    </template>

                                    <template #content>
                                    <ul class="w-60  divide-y divide-gray-200 text-sm text-gray-700 z-50">
                                        <!-- User Info -->
                                            <li>
                                                <DropdownLink
                                                        :href="route('profile.edit')" class="flex space-x-3">
                                                <span
                                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-700 text-white uppercase text-xs font-semibold"
                                                >
                                                    {{ getInitials($page.props.auth.user.name) }}
                                                </span>
                                                <div class="flex flex-col truncate">
                                                    <span class="text-black font-medium truncate">{{$page.props.auth.user.name }}</span>
                                                    <span class="text-gray-500 italic text-xs truncate">{{$page.props.auth.user.email }}</span>
                                                </div>
                                                </DropdownLink>
                                                
                                            </li>

                                            <!-- Settings -->
                                            <li>
                                                <DropdownLink
                                                            :href="route('settings')">
                                                <button
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-black transition"
                                                >
                                                    <i class="fa-solid fa-gear "></i>
                                                    <span>Settings</span>
                                                </button>
                                                </DropdownLink>
                                            </li>

                                            <!-- Logout -->
                                            <li>
                                                <DropdownLink 
                                                           :href="route('logout')"
                                                            method="post"
                                                            as="button"
                                                    >
                                                    <button
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-red-600 transition"
                                                >
                                                    <i class="fas fa-sign-out-alt"></i>
                                                    <span>Log out</span>
                                                </button>
                                                </DropdownLink>
                                            </li>
                                        </ul>
                                    </template>
                                </Dropdown>    
                            </div>
                    </div>
                </div>
                <!-- Main Content -->
                <main :style="{ marginLeft: collapsed ? '65px' : '250px', transition: 'margin-left 0.3s' }"
                    class="flex-grow-1 d-flex justify-content-center p-3 mt-16 pt-8">
                    <div class="w-100">
                       <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.sidebar-expanded {
    width: 250px;
    transition: width 0.3s;
}

.sidebar-collapsed {
    width: 65px;
    transition: width 0.3s;
}

.sidebar-collapsed span {
    display: none;
}
</style>

