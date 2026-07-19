<x-app-layout>
    <x-slot name="header">
        <h1 class="mura-page-heading">
            {{ isset($quote) ? 'Edit Thought' : 'Compose Thought' }}
        </h1>
    </x-slot>

    <div class="flex justify-center my-12 px-4">
        <div class="card w-full max-w-3xl bg-base-100 shadow-xl border border-base-300 flex flex-col h-[75vh]">
            <div class="mura-card-body flex flex-col flex-1 h-full">
                @if(isset($quote))
                    <form method="POST" action="{{ route('quotes.update', $quote) }}" class="mura-compose-form" novalidate="">
                        @method('PUT')
                @else
                    <form method="POST" action="{{ route('quotes.store') }}" class="mura-compose-form" novalidate="">
                @endif
                    @csrf
                    @include('quotes.partials.form', ['buttonText' => isset($quote) ? 'Update' : 'Publish'])
                </form>

            </div>
        </div>
    </div>
</x-app-layout>