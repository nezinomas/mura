<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-200 font-serif">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-8 font-mono justify-center">Join mura.</h2>

                <form method="POST" action="{{ route('register') }}" novalidate="">
                    @csrf

                    <x-floating-input id="name" name="name" label="Name" autocomplete="name" required="true" />
                    
                    <x-floating-input id="email" name="email" type="email" label="Email" autocomplete="username" required="true" />

                    <x-floating-input id="password" name="password" type="password" label="Password" autocomplete="new-password" required="true" />

                    <x-floating-input id="password_confirmation" name="password_confirmation" type="password" label="Confirm Password" autocomplete="new-password" required="true" />

                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-neutral w-full font-mono rounded-none uppercase tracking-widest">Register</button>
                    </div>

                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="link link-hover text-xs font-mono uppercase tracking-wider opacity-70">Already registered?</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>