<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            Correspondence
        </div>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        
        @if (session('success'))
            <div class="mb-8 p-6 bg-slate-50 border border-slate-200 text-center font-mono text-sm text-base-content/70">
                {{ session('success') }}
            </div>
        @endif

        <div class="prose max-w-none leading-relaxed text-base-content mb-8">
            <p class="italic opacity-70">
                Whether you have uncovered a broken mechanism, possess a suggestion for the space, or simply wish to leave a note for the publisher, your words are welcome here.
            </p>
        </div>

        <form action="{{ route('correspondence.store') }}" method="POST" class="space-y-8" novalidate="">
            @csrf

            <x-floating-input 
                id="email"
                name="email"
                type="email"
                label="Return Address (Optional)"
                :value="auth()->check() ? auth()->user()->email : old('email')"
                :uppercase="false" 
            />

            <div>
                <textarea 
                    name="message" 
                    id="message" 
                    rows="8" 
                    class="w-full textarea textarea-bordered bg-base-100 focus:outline-none focus:border-base-content/50 focus:ring-0 rounded-none p-4 text-sm text-base-content transition-colors resize-y leading-relaxed"
                    placeholder="Write your observation here..."
                    required
                >{{ old('message') }}</textarea>

                @error('message')
                    <div class="mt-2 font-mono text-sm text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end pt-4">
                <x-button 
                    type="submit" 
                    variant="text" 
                    class="font-mono text-sm uppercase tracking-widest pb-1 border-b border-transparent hover:border-base-content px-0"
                >
                    Seal & Send
                </x-button>
            </div>
        </form>

    </div>
</x-app-layout>