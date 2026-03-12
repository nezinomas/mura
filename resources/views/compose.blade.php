<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <form method="POST" action="/compose">
            @csrf

            <textarea
                name="content"
                placeholder="A beautifully quiet thought..."
                class="block w-full rounded-md shadow-sm font-mono"
            >{{ old('content') }}</textarea>

            @error('content')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

            <div class="mt-4 flex justify-end">
                <button type="submit" class="btn btn-primary">Save Thought</button>
            </div>
        </form>

    </div>
</x-app-layout>