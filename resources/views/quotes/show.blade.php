<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            Thought
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 pb-12">
        <div class="space-y-8">
            @php($isMine = $quote->isMine())
            
            <x-quote-card :post="$quote" :isMine="$isMine">
                <x-slot name="actions">
                    <x-button as="a" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" variant="text" class="mr-auto">Back</x-button>
                </x-slot>
            </x-quote-card>
        </div>
    </div>
</x-app-layout>