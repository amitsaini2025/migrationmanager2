<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('email_users.dashboard') }}" class="flex items-center">
                        @include('EmailUser.components.application-logo', ['class' => 'block h-8 w-auto fill-current text-gray-800'])
                        <span class="ml-2 text-lg font-semibold text-gray-800 hidden sm:block">Email Client</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('email_users.dashboard') }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('email_users.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('email_users.accounts.index') }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('email_users.accounts.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Email Accounts') }}
                    </a>
                    <a href="{{ route('email_users.signatures.index') }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('signatures.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Signatures') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition ease-in-out duration-150">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-2">
                                {{ substr(Auth::guard('email_users')->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block">{{ Auth::guard('email_users')->user()->name }}</span>
                        </div>

                        <div class="ms-2">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="{{ route('email_users.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Profile') }}
                        </a>

                        <!-- Authentication -->
                        <form method='POST' action='{{ route('email_users.logout') }}'>
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200 shadow-lg">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('email_users.dashboard') }}" 
               class="block px-3 py-2 rounded-lg text-base font-medium transition-colors {{ request()->routeIs('email_users.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('email_users.accounts.index') }}" 
               class="block px-3 py-2 rounded-lg text-base font-medium transition-colors {{ request()->routeIs('email_users.accounts.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                {{ __('Email Accounts') }}
            </a>
            <a href="{{ route('email_users.signatures.index') }}" 
               class="block px-3 py-2 rounded-lg text-base font-medium transition-colors {{ request()->routeIs('signatures.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                {{ __('Signatures') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
            <div class="px-4 py-3">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-lg font-semibold mr-3">
                        {{ substr(Auth::guard('email_users')->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::guard('email_users')->user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::guard('email_users')->user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <a href="{{ route('email_users.profile.edit') }}" 
                   class="block px-3 py-2 rounded-lg text-base font-medium transition-colors text-gray-600 hover:text-gray-900">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method='POST' action='{{ route('email_users.logout') }}'>
                    @csrf

                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-base font-medium transition-colors text-gray-600 hover:text-gray-900">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
