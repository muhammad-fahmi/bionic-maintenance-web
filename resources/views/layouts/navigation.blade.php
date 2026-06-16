<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if(Auth::check())
                        @if(Auth::user()->isReporter())
                            <x-nav-link :href="route('reporter.work-orders.index')" :active="request()->routeIs('reporter.work-orders.*')">
                                {{ __('Laporan') }}
                            </x-nav-link>
                        @elseif(Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.work-orders.index')" :active="request()->routeIs('admin.work-orders.*')">
                                {{ __('Laporan') }}
                            </x-nav-link>
                        @elseif(Auth::user()->isReceiver())
                            <x-nav-link :href="route('receiver.work-orders.index')" :active="request()->routeIs('receiver.work-orders.*')">
                                {{ __('Laporan') }}
                            </x-nav-link>
                        @elseif(Auth::user()->isExecutor())
                            <x-nav-link :href="route('executor.work-orders.index')" :active="request()->routeIs('executor.work-orders.*')">
                                {{ __('Laporan') }}
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Theme Switcher -->
                <div class="flex items-center me-4 bg-gray-100 dark:bg-gray-900 rounded-full p-1 border border-gray-200 dark:border-gray-700">
                    <button @click="setTheme('light')" :class="theme === 'light' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-500' : 'text-gray-500 dark:text-gray-400'" class="p-1.5 rounded-full hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Light Mode">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </button>
                    <button @click="setTheme('system')" :class="theme === 'system' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-500' : 'text-gray-500 dark:text-gray-400'" class="p-1.5 rounded-full hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="System Theme">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="setTheme('dark')" :class="theme === 'dark' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-500' : 'text-gray-500 dark:text-gray-400'" class="p-1.5 rounded-full hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Dark Mode">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::check())
                @if(Auth::user()->isReporter())
                    <x-responsive-nav-link :href="route('reporter.work-orders.index')" :active="request()->routeIs('reporter.work-orders.*')">
                        {{ __('Laporan') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.work-orders.index')" :active="request()->routeIs('admin.work-orders.*')">
                        {{ __('Laporan') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->isReceiver())
                    <x-responsive-nav-link :href="route('receiver.work-orders.index')" :active="request()->routeIs('receiver.work-orders.*')">
                        {{ __('Laporan') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->isExecutor())
                    <x-responsive-nav-link :href="route('executor.work-orders.index')" :active="request()->routeIs('executor.work-orders.*')">
                        {{ __('Laporan') }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
