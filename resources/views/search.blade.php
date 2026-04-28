<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            Search
        </div>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">


        <div class="space-y-8">
            @forelse ($quotes as $quote)
                <x-quote-card :post="$quote" :isMine="auth()->check() ? $quote->isMine() : false">
                    <x-slot name="actions">
                        <x-button as="a" href="{{ route('quotes.show', $quote) }}" variant="text" class="mr-auto">Permalink</x-button>
                        
                        @auth
                            @if(!$quote->isMine())
                                <livewire:grab-button :quote="$quote" :key="'search-grab-'.$quote->id" />
                            @endif
                        @endauth
                    </x-slot>
                </x-quote-card>
            @empty
                @if($searchTerm)
                    <div class="flex flex-col items-center justify-center py-24 border border-dashed border-base-300 bg-slate-50/30">
                        <p class="text-base-content/50 italic font-mono">
                            No thoughts found matching your search.
                        </p>
                    </div>
                @endif
            @endforelse
        </div>

        <div class="mt-8">
            {{ $quotes->links() }}
        </div>

    </div>
</x-app-layout>