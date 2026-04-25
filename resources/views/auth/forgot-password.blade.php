<x-app-layout>
    <div class="flex flex-col items-center justify-center mt-12 pb-24">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-6 justify-center">mura.</h2>

                <div class="mb-6 text-sm text-center text-base-content/70 leading-relaxed">
                    Enter your email to reset your password.
                </div>
                
                @if (session('status'))
                    <div class="alert alert-success mb-6 text-sm rounded-none border-l-4 border-success">
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