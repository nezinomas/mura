@php 
    $isPrivateState = old('is_private', $quote->is_private ?? false) ? 'true' : 'false'; 
@endphp

<div x-data="{ isPrivate: {{ $isPrivateState }} }" class="mura-compose-form">
    
    <textarea
        name="content"
        placeholder="A beautifully quiet thought..."
        class="textarea w-full rounded-none text-sm mb-0 flex-1 resize-none border border-base-300 focus:border-base-content/40 focus:outline-none focus:ring-0 transition-colors duration-300 bg-transparent"
    >{{ old('content', $quote->content ?? '') }}</textarea>

    @error('content')
        <p class="text-error text-sm mt-2">{{ $message }}</p>
    @enderror

    {{-- Markdown formatting help --}}
    <div x-data="{ showFormatting: false }" class="mt-3">
        <button
            type="button"
            @click="showFormatting = !showFormatting"
            :aria-expanded="showFormatting"
            aria-controls="formatting-help"
            class="inline-flex items-center gap-1.5 text-xs text-base-content/50 hover:text-base-content/80 transition-colors focus:outline-none"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 transition-transform duration-200" :class="showFormatting && 'rotate-90'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span>Formatting</span>
        </button>

        <div
            id="formatting-help"
            x-show="showFormatting"
            x-collapse
            style="display: none;"
            class="mt-3 border border-base-300 bg-slate-50/50 px-4 py-3 sm:px-5 sm:py-4"
        >
            <div class="grid grid-cols-[1.4fr_1fr] gap-x-6 gap-y-2.5 text-xs sm:text-sm">
                <span class="text-base-content/40 uppercase tracking-widest text-xs">You type</span>
                <span class="text-base-content/40 uppercase tracking-widest text-xs">You get</span>

                <span class="text-base-content/70">**bold**</span>
                <span class="text-base-content/60"><strong class="text-base-content/90">bold</strong></span>

                <span class="text-base-content/70">*italic* <span class="text-base-content/30">or</span> _italic_</span>
                <span class="text-base-content/60"><em>italic</em></span>

                <span class="text-base-content/70">***bold italic***</span>
                <span class="text-base-content/60"><strong><em class="text-base-content/90">bold italic</em></strong></span>

                <span class="text-base-content/70">~~strikethrough~~</span>
                <span class="text-base-content/60"><del>strikethrough</del></span>

                <span class="text-base-content/70">[label](https://…)</span>
                <span class="text-base-content/60"><span class="underline underline-offset-2">label</span></span>

                <span class="text-base-content/70">&lt;https://…&gt;</span>
                <span class="text-base-content/60"><span class="underline underline-offset-2">https://…</span></span>
            </div>
        </div>
    </div>

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