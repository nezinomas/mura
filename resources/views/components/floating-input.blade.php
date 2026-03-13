@props(['id', 'name', 'type' => 'text', 'label', 'required' => false, 'autocomplete' => ''])

<div class="mb-6" x-data="{ hasError: {{ $errors->has($name) ? 'true' : 'false' }} }">
    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ old($name) }}" 
            {{ $required ? 'required' : '' }} 
            {{ $autocomplete ? 'autocomplete='.$autocomplete : '' }}
            class="peer input w-full h-auto pt-6 pb-2 focus:outline-none rounded-none transition-colors" 
            :class="hasError ? 'input-error focus:border-error' : 'input-bordered'"
            @input="hasError = false" 
            placeholder=" " 
        />
        <label 
            for="{{ $id }}" 
            class="absolute left-4 top-1.5 text-[10px] uppercase tracking-widest transition-all peer-placeholder-shown:top-4 peer-placeholder-shown:text-xs peer-focus:top-1.5 peer-focus:text-[10px] cursor-text"
            :class="hasError ? 'text-error' : 'text-base-content/50'">
            {{ $label }}
        </label>
    </div>

    @error($name)
        <div x-show="hasError" class="mt-1.5 text-sm text-error" x-transition>
            {{ $message }}
        </div>
    @enderror
</div>