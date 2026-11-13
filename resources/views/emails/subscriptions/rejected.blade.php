<x-mail::message>
# Subscription Rejected

Hello {{ $subscription->user->name }},

We regret to inform you that your subscription request for **{{ $subscription->plan->name }}** has been rejected.

**Reason:**
{{ $reason }}

Please verify your payment details and try again. If you believe this is an error, please contact support.

<x-mail::button :url="route('packages.index')">
View Plans
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
