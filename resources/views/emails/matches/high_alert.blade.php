@component('mail::message')
# High Match Alert

Hello,

**Great news!** We found a profile that matches deeply with your preferences.

### **{{ $matchName }}** has a **{{ $matchPercentage }}%** compatibility score with you!

Don't wait—check out their profile now.

@component('mail::button', ['url' => $profileUrl])
View Profile
@endcomponent

Best wishes,<br>
{{ config('app.name') }}
@endcomponent
