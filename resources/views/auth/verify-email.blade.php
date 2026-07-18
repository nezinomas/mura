<x-app-layout>
    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h2 class="mura-card-title mb-6">mura.</h2>

                <div class="mura-card-note">
                    Please verify your email address by clicking the link we just sent you.
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mura-alert-success">
                        A new verification link has been sent.
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <form method="POST" action="{{ route('verification.send') }}" novalidate="">
                        @csrf
                        <x-button type="submit">
                            Resend Email
                        </x-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" novalidate="">
                        @csrf
                        <x-button type="submit" variant="text">
                            Log Out
                        </x-button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>