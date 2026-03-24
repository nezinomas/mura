<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            Global Discover
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 pb-24">
        <div class="space-y-8">
            @forelse ($quotes as $post)
                <x-quote-card :post="$post">
                    <x-slot name="actions">
                        <x-button as="a" href="{{ route('quotes.show', $post) }}" variant="text" class="mr-auto">Permalink</x-button>
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
    </div>
</x-app-layout>