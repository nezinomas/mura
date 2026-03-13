<div class="bg-base-100 border-b border-base-300 px-4 sm:px-8 py-3 flex items-center justify-between">
    
    <div class="flex items-center gap-8">
        
        <a href="{{ route('dashboard') }}" class="text-2xl font-bold hover:opacity-70 transition-opacity">
            mura.
        </a>
        
        <div class="hidden sm:flex items-center gap-6 mt-1">
            <a href="{{ route('dashboard') }}" class="text-typewriter hover:underline {{ request()->routeIs('dashboard') ? 'underline' : 'opacity-70' }}">
                Feed
            </a>
            <a href="#" class="text-typewriter hover:underline opacity-70">
                Global Discover
            </a>
        </div>
        
    </div>

    <div class="relative" x-data="{ open: false }">
        
        <button @click="open = !open" @click.outside="open = false" class="flex items-center space-x-2 text-typewriter focus:outline-none hover:opacity-70">
            <span>{{ Auth::user()->name }}</span>
            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
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
             class="absolute right-0 mt-4 w-48 bg-base-100 border border-base-300 shadow-xl z-50"
             style="display: none;">
            
            <div class="py-1 flex flex-col">
                <a href="{{ route('profile.edit') }}" class="px-4 py-3 text-typewriter hover:bg-base-200 w-full text-left">
                    Profile
                </a>
                
                <a href="#" class="px-4 py-3 text-typewriter hover:bg-base-200 w-full text-left">
                    Change Password
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="m-0 border-t border-base-300">
                    @csrf
                    <button type="submit" class="px-4 py-3 text-typewriter text-error hover:bg-base-200 w-full text-left">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>