<x-app-layout>
    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h2 class="mura-card-title mb-8">mura.</h2>
                
                @if (session('status'))
                    <div class="mura-alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate="">
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
                        <x-button type="submit" class="w-full">Log in</x-button>
                    </div>

                    <div class="text-center mt-6">
                        <a href="{{ route('register') }}" class="link link-hover text-xs uppercase tracking-wider opacity-70">Need an account?</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>