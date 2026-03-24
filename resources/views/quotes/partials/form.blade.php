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
            <input type="hidden" name="is_private" value="0">
            <input type="checkbox" id="is_private" name="is_private" value="1" class="hidden" x-model="isPrivate">

            <x-button type="button" @click="isPrivate = !isPrivate" class="!px-4 !min-h-8 !h-8" x-bind:class="isPrivate ? '!bg-slate-200 !border-slate-300' : ''">
                <span x-text="isPrivate ? 'Private' : 'Public'">Public</span>
            </x-button>

            <span class="text-xs text-base-content/60 italic">
                <span x-show="!isPrivate">Everyone can see this.</span>
                <span x-show="isPrivate" style="display: none;">Only you can see this.</span>
            </span>
        </div>

        <x-button type="submit" class="w-full sm:w-auto px-8">
            {{ $buttonText ?? 'Save Thought' }}
        </x-button>
        
    </div>
</div>