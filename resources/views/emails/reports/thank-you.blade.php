@component('mail::message')
# Report Received - Thank You

Assalam o Alaikum {{ $reporterName }},

Thank you for helping us keep the community safe. We reviewed your report and have taken action on the reported account.

Reported Profile ID: **{{ $targetDisplayId }}**

@if($targetProfileUrl)
@component('mail::button', ['url' => $targetProfileUrl])
View Reported Profile
@endcomponent
@endif

JazakAllah khair,<br>
{{ config('app.name') }} Team
@endcomponent
