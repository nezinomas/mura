<x-app-layout>
    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h2 class="mura-card-title mb-6">mura.</h2>

                <div class="mb-6 text-sm text-center text-base-content/70 leading-relaxed">
                    Enter your email to reset your password.
                </div>
                
                @if (session('status'))
                    <div class="mura-alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" novalidate="">
                    @csrf

                    <x-floating-input id="email" name="email" type="email" label="Email" required="true" />

                    <div class="form-control mt-4">
                        <x-button type="submit" class="w-full">
                            Email Reset Link
                        </x-button>
                    </div>

                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="link link-hover opacity-70">
                            Back to login
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>