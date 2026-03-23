<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'btn w-full rounded-none tracking-widest text-[.8rem] uppercase mt-2'
]) }}>
    {{ $slot }}
</button>