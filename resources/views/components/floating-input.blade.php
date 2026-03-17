@props(['id', 'name', 'type' => 'text', 'label', 'required' => false, 'autocomplete' => '', 'value' => '', 'bag' => 'default'])

@php
    $initialValue = old($name, $value);
@endphp

<div class="mb-6" x-data="{ 
    hasError: {{ $errors->getBag($bag)->has($name) ? 'true' : 'false' }},
    hasValue: {{ $initialValue ? 'true' : 'false' }}
}">
    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ $initialValue }}" 
            {{ $required ? 'required' : '' }} 
            {{ $autocomplete ? 'autocomplete='.$autocomplete : '' }}
            class="peer input w-full h-auto pt-6 pb-2 focus:outline-none rounded-none transition-colors" 
            :class="hasError ? 'input-error focus:border-error' : 'input-bordered'"

            {{-- When the user types, remove the error AND update hasValue --}}
            @input="hasError = false; hasValue = $el.value.length > 0" 
            placeholder=" " 
        />

        <label for="{{ $id }}" 
            class="absolute left-4 uppercase tracking-widest transition-all duration-200 pointer-events-none"
            :class="[
                hasError ? 'text-error' : 'text-base-content/50',
                /* If it has a value, OR the peer is focused: Shrink it to the top */
                hasValue ? 'top-1 text-[10px]' : 'top-4 text-xs peer-focus:top-1 peer-focus:text-[10px]',
                /* If it has no value AND it's not focused: Keep it normal size (handled by default classes above + peer-focus fallback) */
                !hasValue ? 'peer-placeholder-shown:top-4 peer-placeholder-shown:text-xs' : ''
            ]">
            {{ $label }}
        </label>
    </div>

    @error($name, $bag)
        <div x-show="hasError" class="text-error mt-1 text-sm">
            {{ $message }}
        </div>
    @enderror
</div>