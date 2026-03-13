<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-200">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-8 justify-center">mura.</h2>
                
                @if (session('status'))
                    <div class="alert alert-success mb-6 text-sm rounded-none border-l-4 border-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-floating-input id="email" name="email" type="email" label="Email" autocomplete="username" required="true" />

                    <x-floating-input id="password" name="password" type="password" label="Password" autocomplete="current-password" required="true" />

                    <div class="flex items-center justify-between mb-8 mt-2">
                        <label class="label cursor-pointer p-0">
                            <input type="checkbox" name="remember" class="checkbox checkbox-sm rounded-none mr-2" />
                            <span class="label-text text-xs uppercase tracking-widest text-base-content/70">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="link link-hover text-xs uppercase tracking-widest opacity-70" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-neutral w-full rounded-none uppercase tracking-widest">Log in</button>
                    </div>

                    <div class="text-center mt-6">
                        <a href="{{ route('register') }}" class="link link-hover text-xs uppercase tracking-wider opacity-70">Need an account?</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>