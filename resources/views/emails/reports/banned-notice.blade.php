@component('mail::message')
# Account Blocked Notice

Assalam o Alaikum {{ $memberName }},

Your account has been blocked due to safety policy violations.

Profile ID: **{{ $profileId }}**

If you think this happened by mistake, contact support on WhatsApp.

@component('mail::button', ['url' => $supportWhatsappUrl])
Contact Support on WhatsApp
@endcomponent

Regards,<br>
{{ config('app.name') }} Team
@endcomponent
