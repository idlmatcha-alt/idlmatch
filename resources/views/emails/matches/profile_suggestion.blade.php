@component('mail::message')
# {{ $campaign === 'follow_up' ? 'More Profiles For You' : 'A New Profile Suggestion' }}

Assalam o Alaikum {{ $recipientName }},

@if($matchPercentage !== null)
We found a profile with a **{{ $matchPercentage }}%** compatibility score for you.
@else
Here is a profile suggestion you may like.
@endif

**Name:** {{ $displayName }}  
**Profile ID:** {{ $displayId }}  
@if(!empty($age))
**Age:** {{ $age }}  
@endif
@if(!empty($profession))
**Profession:** {{ $profession }}  
@endif
@if(!empty($location))
**Location:** {{ $location }}  
@endif

@component('mail::button', ['url' => $profileUrl])
View Profile
@endcomponent

Regards,  
{{ config('app.name') }}
@endcomponent
