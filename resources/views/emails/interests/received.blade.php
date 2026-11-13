@component('mail::message')
# New Interest Received

Hello,

**{{ $senderName }}** has expressed interest in your profile!

Review their profile and decide if you'd like to connect.

@component('mail::button', ['url' => $profileUrl])
View Profile
@endcomponent

Good luck,<br>
{{ config('app.name') }}
@endcomponent
