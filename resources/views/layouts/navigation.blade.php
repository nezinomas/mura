<div class="bg-base-100 border-b border-base-300 px-4 sm:px-8 py-3 flex items-center justify-between">
    
    <div class="flex items-baseline">
        
        <a href="{{ route('home') }}" class="text-2xl font-bold hover:opacity-70 transition-opacity mr-6 sm:mr-8">
            mura.
        </a>
        
        @auth
        <div class="hidden sm:flex items-baseline gap-4 mr-4 text-sm">
            <a href="{{ route('quotes.create') }}" class="hover:underline font-medium {{ request()->routeIs('quotes.create') ? 'underline' : 'opacity-100' }}">
                Compose
            </a>
            <a href="{{ route('dashboard') }}" class="hover:underline font-medium {{ request()->routeIs('dashboard') ? 'underline' : 'opacity-70' }}">
                Feed
            </a>
        </div>
        @endauth

        <form 
            action="{{ route('search.index') }}" 
            method="GET" 
            class="relative flex items-center"
            x-data="{ query: '' }" 
            x-init="query = $refs.searchInput.value"
        >
            <input 
                x-ref="searchInput"
                x-model="query"
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Search" 
                class="bg-transparent border border-transparent focus:border-base-300 rounded pl-2 pr-8 py-1 m-0 w-48 sm:w-64 md:w-80 text-sm font-medium focus:font-mono opacity-70 hover:opacity-100 focus:opacity-100 transition-colors focus:outline-none focus:ring-0 shadow-none cursor-text text-base-content placeholder-base-content focus:placeholder-base-content/30"
            >
            
            <button 
                type="button" 
                x-show="query.length > 0" 
                @click="query = ''; $refs.searchInput.focus()"
                style="display: none;"
                class="absolute right-2 top-1/2 -translate-y-1/2 opacity-30 hover:opacity-100 focus:outline-none transition-opacity text-base-content"
                aria-label="Clear search"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <button type="submit" class="hidden">Submit</button>
        </form>
    </div>

    <div class="flex items-center gap-4 sm:gap-6 text-sm">
        @auth
            <div class="relative" x-data="{ open: false }">
                
                <button @click="open = !open" @click.outside="open = false" class="flex items-center space-x-2 focus:outline-none hover:opacity-70 font-medium opacity-70 hover:opacity-100 transition-opacity">
                    <span>{{ Auth::user()->name }}</span>
                    <svg class="h-4 w-4 fill-current opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-4 w-48 bg-base-100 border border-base-300 shadow-sm z-50 font-mono"
                     style="display: none;">
                    
                    <div class="py-1 flex flex-col">
                        <a href="{{ route('profile.edit') }}" class="px-4 py-3 hover:bg-base-200 w-full text-left">
                            Profile
                        </a>
                        
                        <a href="{{ route('password.change') }}" class="px-4 py-3 hover:bg-base-200 w-full text-left">
                            Change Password
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="m-0 border-t border-base-300">
                            @csrf
                            <button type="submit" class="px-4 py-3 text-error hover:bg-base-200 w-full text-left">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-center gap-4 font-medium">
                <a href="{{ route('login') }}" class="hover:underline opacity-70 hover:opacity-100">Log in</a>
                <a href="{{ route('register') }}" class="hover:underline opacity-70 hover:opacity-100">Register</a>
            </div>
        @endauth
    </div>
</div>