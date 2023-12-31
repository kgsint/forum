<script setup>
import HamburgerIcon from "@/Components/Icons/HamburgerIcon.vue"
import CloseIcon from "@/Components/Icons/CloseIcon.vue"
import SideNavigation from "@/Components/Admin/SideNavigation.vue"
import { ref } from 'vue'
import { Link, router } from "@inertiajs/vue3"

let showUserInfoDropdown = ref(false)
const showSideNavigation = ref(true)

window.addEventListener('resize', () => {
    // hide side navigation on screen size of below 1024px
    // always shows on larger view
    if(window.innerWidth < 1024) {
        showSideNavigation.value = false
    }else {
        showSideNavigation.value = true
    }
})

document.addEventListener('click', (e) => {
    // close on click away | click outside for user dropdown info
    if(! document.querySelector('#dropdown-info').contains(e.target) && showUserInfoDropdown.value) {
        showUserInfoDropdown.value = false
    }
})


// logout
const logout = () => {
    router.post(route('logout'))
}

</script>

<template>
    <!-- nav -->
    <nav class="fixed z-30 w-full bg-white border-b border-gray-200 shadow">
        <div class="px-3 py-3 lg:px-5 lg:pl-3 max-w-7xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button
                        @click="showSideNavigation = !showSideNavigation"
                        aria-expanded="true"
                        aria-controls="sidebar"
                        class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100"
                    >
                        <!-- hamburger menu icon -->
                        <HamburgerIcon />
                        <!-- close menu icon -->
                        <CloseIcon />
                    </button>
                    <Link
                        href="/admin"
                        class="flex ml-2 md:mr-24"
                    >
                        <span
                            class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap"
                            >Forummie Admin</span
                        >
                    </Link>
                    <!-- global search form -->
                    <!-- <form
                        action="#"
                        method="GET"
                        class="hidden lg:block lg:pl-3.5"
                    >
                        <label for="topbar-search" class="sr-only"
                            >Search</label
                        >
                        <div class="relative mt-1 lg:w-96">
                            <div
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                            >
                                <svg
                                    class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="topbar-search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5"
                                placeholder="Search"
                            />
                        </div>
                    </form> -->
                </div>
                <div id="dropdown-info" class="flex items-center">
                    <div class="hidden mr-3 -mb-1 sm:block">
                        <span></span>
                    </div>
                    <!-- toggle searchbar mobile -->
                    <!-- <button
                        type="button"
                        class="p-2 text-gray-500 rounded-lg lg:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400"
                    >
                        <span class="sr-only">Search</span>

                        <SearchIcon />
                    </button> -->

                    <div class="flex items-center ml-3">
                        <button
                            @click="showUserInfoDropdown = !showUserInfoDropdown"
                            type="button"
                            class="relative flex text-sm bg-gray-800 rounded-full focus:ring-2 focus:ring-blue-500"
                            aria-expanded="false"
                        >
                            <span class="sr-only">Open user menu</span>
                            <img
                                class="w-8 h-8 rounded-full object-cover"
                                :src="$page.props.auth.user.avatar"
                                alt="user photo"
                            />
                        </button>
                        <!-- nav bar user info dropdown -->
                        <div
                            v-show="showUserInfoDropdown"
                            class="absolute top-10 right-10 z-50 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow"
                        >
                            <div class="px-4 py-3" role="none">
                                <p
                                    class="text-sm text-gray-900"
                                    role="none"
                                >
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <p
                                    class="text-sm font-medium text-gray-900 truncate"
                                    role="none"
                                >
                                    {{ $page.props.auth.user.email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <Link
                                        :href="route('profile.edit')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem"
                                        >
                                        Settings
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        @click="logout"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full"
                                        role="menuitem"
                                        >
                                        Sign out
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- end nav -->

    <div class="flex pt-16 overflow-hidden bg-gray-50">
        <!-- sidebar -->
        <SideNavigation :showSideNavigation="showSideNavigation" />

        <div
            class="fixed inset-0 z-10 hidden"
        ></div>
        <!-- main content -->
        <div
            class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64"
        >
            <main>
                <div
                    class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5"
                >
                    <slot name="header" />
                </div>
                <div class="flex flex-col">
                    <slot />
                </div>

            </main>
        </div>
    </div>
</template>
