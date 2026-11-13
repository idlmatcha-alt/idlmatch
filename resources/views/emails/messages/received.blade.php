@component('mail::message')
# New Message Received

Hello,

**{{ $senderName }}** has sent you a new message on Ideal Marriage Center.

@component('mail::button', ['url' => $threadUrl])
View Message
@endcomponent

Keep the conversation going!<br>
{{ config('app.name') }}
@endcomponent
