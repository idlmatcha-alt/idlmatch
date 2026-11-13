<x-mail::message>
    # Security Alert: New Login Detected

    Hello {{ $user->name }},

    We detected a login to your **Ideal Marriage Center** account from a new location or device.

    **IP Address:** {{ $ipAddress }}
    **Time:** {{ now()->toDayDateTimeString() }}

    If this was you, you can safely ignore this email.

    If you did not authorize this login, please **change your password immediately** to secure your account.

    <x-mail::button :url="route('password.request')">
        Reset Password
    </x-mail::button>

    Thanks,<br>
    The {{ config('app.name') }} Team
</x-mail::message>