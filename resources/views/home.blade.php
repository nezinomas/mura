<x-app-layout>
    <x-slot name="header">
        <div class="mura-page-heading">
            Global Discover
        </div>
    </x-slot>

    <div class="mura-feed-page">
        <div class="space-y-8">
            @if($dailyQuote)
                <div class="relative mt-4">
                    <div class="absolute -top-3 left-6 z-10">
                        <span class="bg-slate-50 border border-slate-200 px-3 py-1 text-xs uppercase tracking-widest text-slate-500 rounded-full shadow-sm">
                            Thought of the Day
                        </span>
                    </div>
                    @php($isDailyMine = auth()->check() ? $dailyQuote->isMine() : false)
                    
                    <x-quote-card :post="$dailyQuote">
                        <x-slot name="actions">
                            <x-button as="a" href="{{ route('quotes.show', $dailyQuote) }}" variant="text" class="mr-auto">Permalink</x-button>
                            
                            @auth
                                @if(!$isDailyMine)
                                    <livewire:grab-button :quote="$dailyQuote" :key="'daily-grab-'.$dailyQuote->id" />
                                @endif
                            @endauth
                        </x-slot>
                    </x-quote-card>
                </div>
                
                @if($quotes->isNotEmpty())
                    <div class="flex items-center justify-center my-12">
                        <div class="mura-divider-line"></div>
                        <span class="px-4 text-slate-400 italic text-sm text-center">Discover<br>more</span>
                        <div class="mura-divider-line"></div>
                    </div>
                @endif
            @endif

            @forelse ($quotes as $post)
                @if($dailyQuote && $post->id === $dailyQuote->id)
                    @continue
                @endif
                
                <x-quote-card :post="$post">
                    <x-slot name="actions">
                        <x-button as="a" href="{{ route('quotes.show', $post) }}" variant="text" class="mr-auto">Permalink</x-button>
                        
                        @auth
                            <livewire:grab-button :quote="$post" :key="'grab-'.$post->id" />
                        @endauth
                    </x-slot>
                </x-quote-card>
            @empty
                @if(!$dailyQuote)
                    <div class="mura-empty">
                        <p class="mura-empty-text">
                            The world is quiet. No thoughts have been shared yet.
                        </p>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</x-app-layout>