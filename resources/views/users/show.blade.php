<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-center gap-3 w-full text-center">
            <span>{{ $user->display_name }}</span>
            
            <a href="{{ route('users.feed', $user) }}" 
               class="text-slate-400 hover:text-slate-700 transition-colors" 
               title="Subscribe via RSS"
               target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 11a9 9 0 0 1 9 9"></path>
                    <path d="M4 4a16 16 0 0 1 16 16"></path>
                    <circle cx="5" cy="19" r="1"></circle>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 pb-24">
        <div class="space-y-8">
            @forelse ($quotes as $post)
                <x-quote-card :post="$post">
                    <x-slot name="actions">
                        <x-button as="a" href="{{ route('quotes.show', $post) }}" variant="text" class="mr-auto">Permalink</x-button>

                        @auth
                            @if(auth()->id() !== $user->id)
                                <livewire:grab-button :quote="$post" :key="'grab-'.$post->id" />
                            @endif
                        @endauth
                    </x-slot>
                </x-quote-card>
            @empty
                <div class="flex flex-col items-center justify-center py-24 border border-dashed border-base-300 bg-slate-50/30">
                    <p class="text-base-content/50 italic">
                        The world is quiet. No thoughts have been shared yet.
                    </p>
                </div>
            @endforelse
        </div>

        @if($quotes->hasPages())
            <div class="mt-8 mb-24">
                {{ $quotes->links('layouts.pagination') }}
            </div>
        @endif
    </div>
</x-app-layout>