<x-mail::message>
    # Hello!

    You have received a new Interest on **Ideal Marriage Center**.

    **From:** {{ $senderProfile->first_name }} {{ $senderProfile->last_name }}

    Click the button below to view their profile and respond.

    <x-mail::button :url="route('profiles.show', $senderProfile->display_id)">
        View Profile
    </x-mail::button>

    Thanks,<br>
    The {{ config('app.name') }} Team
</x-mail::message>