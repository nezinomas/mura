@props(['id', 'name', 'type' => 'text', 'label', 'required' => false, 'autocomplete' => '', 'value' => '', 'bag' => 'default'])

@php
    // Check if there is a Laravel validation error on the server
    $hasServerSideError = $errors->getBag($bag)->has($name);
@endphp

<div class="mb-6" x-data="{ hasError: {{ $hasServerSideError ? 'true' : 'false' }} }">
    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            {{ $required ? 'required' : '' }} 
            {{ $autocomplete ? 'autocomplete='.$autocomplete : '' }}
            class="text-sm peer input w-full h-auto pt-6 pb-2 focus:outline-none rounded-none transition-colors {{ $hasServerSideError ? 'input-error' : 'input-bordered' }}" 
            :class="hasError ? 'input-error focus:border-error' : 'input-bordered focus:border-base-content/50'"
            @input="hasError = false" 
            placeholder=" " 
        />
        
        <label for="{{ $id }}" 
            class="absolute left-4 uppercase tracking-widest transition-all duration-200 pointer-events-none
                   top-1 text-[10px] peer-focus:top-1 peer-focus:text-[10px]
                   peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm
                   {{ $hasServerSideError ? 'text-error' : 'text-base-content/50' }}"
            :class="hasError ? 'text-error' : 'text-base-content/50 peer-focus:text-base-content'">
            {{ $label }}
        </label>
    </div>

    @error($name, $bag)
        <div x-show="hasError" class="text-error mt-1 text-sm">
            {{ $message }}
        </div>
    @enderror
</div>