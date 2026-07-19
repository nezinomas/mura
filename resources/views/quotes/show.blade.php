<x-app-layout>
    <x-slot name="header">
        <h1 class="mura-page-heading">
            Thought
        </h1>
    </x-slot>

    <div class="mura-page">
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