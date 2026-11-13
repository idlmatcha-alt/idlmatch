<x-mail::message>
    # New Message Received

    You have received a new private message on **Ideal Marriage Center** from **{{ $senderProfile->first_name }}**.

    Log in to your account to read and reply to the message.

    <x-mail::button :url="route('dashboard')">
        View Messages
    </x-mail::button>

    Thanks,<br>
    The {{ config('app.name') }} Team
</x-mail::message>