@props(['id', 'name', 'type' => 'text', 'label', 'required' => false, 'autocomplete' => '', 'value' => '', 'bag' => 'default'])

<div class="mb-6" x-data="{ hasError: {{ $errors->getBag($bag)->has($name) ? 'true' : 'false' }} }">
    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            {{ $required ? 'required' : '' }} 
            {{ $autocomplete ? 'autocomplete='.$autocomplete : '' }}
            class="peer input w-full h-auto pt-6 pb-2 focus:outline-none rounded-none transition-colors" 
            :class="hasError ? 'input-error focus:border-error' : 'input-bordered'"
            @input="hasError = false" 
            placeholder=" " 
        />
        <label for="{{ $id }}" 
            class="absolute left-4 top-4 text-base-content/50 text-xs uppercase tracking-widest transition-all peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-focus:top-1 peer-focus:text-[10px] peer-focus:text-base-content"
            :class="hasError ? 'text-error peer-focus:text-error' : ''">
            {{ $label }}
        </label>
    </div>

    @error($name, $bag)
        <div x-show="hasError" class="text-error mt-1">
            {{ $message }}
        </div>
    @enderror
</div>