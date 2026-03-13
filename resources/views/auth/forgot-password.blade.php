<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-200 font-serif">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-6 font-mono justify-center">mura.</h2>

                <div class="mb-6 text-sm text-base-content/70 font-serif leading-relaxed">
                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
                </div>
                
                @if (session('status'))
                    <div class="alert alert-success mb-6 text-sm rounded-none border-l-4 border-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <x-floating-input id="email" name="email" type="email" label="Email" required="true" />

                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-neutral w-full font-mono rounded-none uppercase tracking-widest">
                            Email Reset Link
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="link link-hover text-xs font-mono uppercase tracking-wider opacity-70">Back to login</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>