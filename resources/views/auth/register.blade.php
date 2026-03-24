<x-app-layout>
    <div class="flex flex-col items-center justify-center mt-12 pb-24">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-8 justify-center">Join mura.</h2>

                <form method="POST" action="{{ route('register') }}" novalidate="">
                    @csrf

                    <x-floating-input id="name" name="name" label="Name" autocomplete="name" required="true" />
                    
                    <x-floating-input id="email" name="email" type="email" label="Email" autocomplete="username" required="true" />

                    <x-floating-input id="password" name="password" type="password" label="Password" autocomplete="new-password" required="true" />

                    <x-floating-input id="password_confirmation" name="password_confirmation" type="password" label="Confirm Password" autocomplete="new-password" required="true" />

                    <div class="form-control mt-4">
                        <x-button type="submit" class="w-full">Register</x-button>
                    </div>

                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="link link-hover text-xs uppercase tracking-wider opacity-70">Already registered?</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>