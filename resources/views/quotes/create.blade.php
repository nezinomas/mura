<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            {{ isset($quote) ? 'Edit Thought' : 'Compose Thought' }}
        </div>
    </x-slot>

    <div class="flex justify-center my-12 px-4">
        <div class="card w-full max-w-3xl bg-base-100 shadow-xl border border-base-300 flex flex-col h-[75vh]">
            <div class="card-body p-8 flex flex-col flex-1 h-full">
                @if(isset($quote))
                    <form method="POST" action="{{ route('quotes.update', $quote) }}" class="flex flex-col flex-1 h-full w-full">
                        @method('PUT')
                @else
                    <form method="POST" action="{{ route('quotes.store') }}" class="flex flex-col flex-1 h-full w-full">
                @endif
                    @csrf
                    @include('quotes.partials.form', ['buttonText' => isset($quote) ? 'Update' : 'Publish'])
                </form>

            </div>
        </div>
    </div>
</x-app-layout>