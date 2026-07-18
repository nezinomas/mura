<x-app-layout>
    <x-slot name="header">
        <div class="mura-page-heading">
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
                    <div class="mura-empty">
                        <p class="mura-empty-text font-mono">
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