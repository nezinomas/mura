<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <form method="POST" action="/compose" x-data="{ isPrivate: false }">
            @csrf

            <textarea
                name="content"
                placeholder="A beautifully quiet thought..."
                class="block w-full rounded-md shadow-sm font-mono border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >{{ old('content') }}</textarea>

            @error('content')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_private" x-model="isPrivate" class="hidden">

                    <button 
                        type="button" 
                        @click="isPrivate = !isPrivate"
                        class="btn btn-sm transition-colors duration-300"
                        :class="isPrivate ? 'btn-error' : 'btn-success'"
                    >
                        <span x-text="isPrivate ? 'Private' : 'Public'"></span>
                    </button>
                    
                    <span class="text-xs text-gray-500 italic">
                        <span x-show="!isPrivate">Everyone can see this.</span>
                        <span x-show="isPrivate">Only you can see this.</span>
                    </span>
                </div>

                <button type="submit" class="btn btn-primary">Save Thought</button>
            </div>
        </form>

    </div>
</x-app-layout>