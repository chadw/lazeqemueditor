<header x-data class="bg-neutral border-b border-base-content/10 px-4 md:px-6 py-3.5 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <label for="mobile-drawer" class="btn btn-ghost btn-square lg:hidden mr-2" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </label>
        <button @click="$store.sidebar.toggleCollapse()"
                class="btn btn-ghost btn-square mr-2 hidden lg:inline-flex"
                x-show="$store.sidebar.collapsed"
                x-cloak
                aria-label="Expand sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-lg font-semibold lg:hidden">
            LazEQEmu Editor
        </h1>
        <h1 class="hidden lg:block text-lg font-semibold">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    <div class="flex items-center gap-4">
        {{-- <input
            type="text"
            placeholder="Search items, NPCs, spells..."
            class="input input-sm w-64"
        > --}}
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-sm btn-ghost">
                {{ auth()->user()->name ?? 'GM' }}
            </label>
                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-48">
                    <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left">Logout</button>
                        </form>
                    </li>
                </ul>
        </div>
    </div>
</header>
