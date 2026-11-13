<x-mail::message>
# Subscription Approved

Dear {{ $subscription->user->profile->first_name ?? $subscription->user->name }},

Great news! Your subscription to the **{{ $subscription->plan->name }}** plan has been approved.

**Subscription Details:**
- Plan: {{ $subscription->plan->name }}
- Duration: {{ $subscription->plan->duration_days }} days
- Start Date: {{ $subscription->start_at->format('M d, Y') }}
- End Date: {{ $subscription->end_at->format('M d, Y') }}

Your subscription is now active. You can start enjoying all the benefits of your plan immediately.

<x-mail::button :url="route('dashboard')">
Visit Your Dashboard
</x-mail::button>

If you have any questions, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
