@php 
    $isPrivateState = old('is_private', $quote->is_private ?? false) ? 'true' : 'false'; 
@endphp

<div x-data="{ isPrivate: {{ $isPrivateState }} }" class="flex flex-col flex-1 h-full w-full">
    
    <textarea
        name="content"
        placeholder="A beautifully quiet thought..."
        class="textarea w-full rounded-none text-base mb-0 flex-1 resize-none border border-base-300 focus:border-base-content/40 focus:outline-none focus:ring-0 transition-colors duration-300 bg-transparent"
    >{{ old('content', $quote->content ?? '') }}</textarea>

    @error('content')
        <p class="text-error text-sm mt-2">{{ $message }}</p>
    @enderror

    <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        
        <div class="flex items-center space-x-3">
            <input type="checkbox" name="is_private" x-model="isPrivate" value="1" class="hidden">

            <button 
                type="button" 
                @click="isPrivate = !isPrivate"
                class="btn btn-sm rounded-none transition-colors duration-300 border border-base-300 font-normal"
                :class="isPrivate ? 'bg-base-200' : 'bg-transparent hover:bg-base-100'"
            >
                <span x-text="isPrivate ? 'Private' : 'Public'"></span>
            </button>

            <span class="text-xs text-base-content/60 italic">
                <span x-show="!isPrivate">Everyone can see this.</span>
                <span x-show="isPrivate">Only you can see this.</span>
            </span>
        </div>

        <button type="submit" class="btn rounded-none font-normal border border-slate-200 bg-slate-50 hover:bg-slate-100 w-full sm:w-auto px-8 transition-colors duration-300">
            {{ $buttonText ?? 'Save Thought' }}
        </button>
        
    </div>
</div>