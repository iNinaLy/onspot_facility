<nav x-data="{ open: false }" class="bg-white bg-opacity-90 backdrop-blur-md fixed w-full z-10 transition duration-300 shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-4">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('supervisor.dashboard') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')" 
                        class="text-gray-900 hover:bg-purple-500 hover:text-white transition px-4 py-2 rounded-lg {{ request()->routeIs('supervisor.dashboard') ? 'shadow-lg' : '' }}">
                        {{ __('Home') }}
                    </x-nav-link>

                    <x-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" 
                        class="text-gray-900 hover:bg-purple-500 hover:text-white transition px-4 py-2 rounded-lg {{ request()->routeIs('supervisor.cleaners') ? 'shadow-lg' : '' }}">
                        {{ __('Cleaners') }}
                    </x-nav-link>

                    <x-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')"
                        class="text-gray-900 hover:bg-purple-500 hover:text-white transition px-4 py-2 rounded-lg {{ request()->routeIs('supervisor.complaints.index') ? 'shadow-lg' : '' }}">
                        {{ __('Complaints') }}
                    </x-nav-link>

                    <x-nav-link :href="route('supervisor.history')" 
                        class="text-gray-900 hover:bg-purple-500 hover:text-white transition px-4 py-2 rounded-lg {{ request()->routeIs('supervisor.history') ? 'shadow-lg' : '' }}">
                        {{ __('History') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (User Info) -->
            <div class="hidden sm:flex sm:items-center sm:ml-auto space-x-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-900 bg-transparent hover:bg-purple-500 hover:text-white transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-gray-900">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();" class="text-gray-900">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Menu for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')" class="text-gray-900 dark:text-gray-100">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" class="text-gray-900 dark:text-gray-100">
                {{ __('Cleaners') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')" class="text-gray-900 dark:text-gray-100">
                {{ __('Complaints') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('supervisor.history')" :active="request()->routeIs('supervisor.history')" class="text-gray-900 dark:text-gray-100">
                {{ __('History') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-900">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-gray-900">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
@media (prefers-color-scheme: dark) {
    .dark\:text-gray-100 {
        --tw-text-opacity: 1;
        color: rgb(18, 18, 18);
    }
}
</style>
