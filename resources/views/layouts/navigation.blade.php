<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
     <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('catalogo') : route('paginaprincipal') }}" class="flex items-center gap-4">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <span class="text-gray-800 tracking-wide uppercase" style="font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 36px;">Aventa</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <x-nav-link :href="route('misprendas')" :active="request()->routeIs('dashboard')">
                            {{ (auth()->user()->rol === 'admin' || auth()->user()->rol === 'empleado') ? __('Gestionar prendas') : __('Mis prendas') }}
                        </x-nav-link>
                    @endauth
                </div>

                @auth
                    @if(auth()->user()->rol === 'usuario')
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('intercambios')" :active="request()->routeIs('dashboard')">
                                {{ __('Intercambios') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('formsubirprenda')" :active="request()->routeIs('dashboard')">
                                {{ __('Subir') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'empleado')
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('gestionarusuarios')" :active="request()->routeIs('dashboard')">
                                {{ __('Gestionar usuarios') }}
                            </x-nav-link>
                        </div>
                    @endif
                    @if (auth()->check() && auth()->user()->esAdmin())
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('mantenimiento')" :active="request()->routeIs('dashboard')">
                                {{ __('Mantenimiento') }}
                            </x-nav-link>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
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
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2">Entrar</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2">Registrarse</a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
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
        
        @auth
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('misprendas')" :active="request()->routeIs('dashboard')">
                    {{ (auth()->user()->rol === 'admin' || auth()->user()->rol === 'empleado') ? __('Gestionar prendas') : __('Mis prendas') }}
                </x-responsive-nav-link>
            </div>

            @if(auth()->user()->rol === 'usuario')
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('intercambios')" :active="request()->routeIs('dashboard')">
                        {{ __('Mis intercambios') }}
                    </x-responsive-nav-link>
                </div>
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('formsubirprenda')" :active="request()->routeIs('dashboard')">
                        {{ __('Subir') }}
                    </x-responsive-nav-link>
                </div>
            @endif

            @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'empleado')
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('gestionarusuarios')" :active="request()->routeIs('dashboard')">
                        {{ __('Gestionar usuarios') }}
                    </x-responsive-nav-link>
                </div>
            @endif
        @endauth

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Cerrar sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Entrar') }}
                    </x-responsive-nav-link>

                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">
                            {{ __('Registrarse') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>