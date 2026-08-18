@component('mail::message')
# {{ $subject ?? 'SelfCheq Notification' }}

{{ $body ?? 'You have a new notification from SelfCheq.' }}

@component('mail::panel')
@if($notification->task ?? null)
    **Task:** {{ $notification->task->title }}
    @if($notification->task->deadline)
    **Due:** {{ \Carbon\Carbon::parse($notification->task->deadline)->format('M j, Y g:ia') }}
    @endif
    @if($notification->task->description)
    **Details:** {{ $notification->task->description }}
    @endif
@elseif($notification->contact ?? null)
    **Contact:** {{ $notification->contact->name }}
    @if($notification->contact->relationship)
    **Relationship:** {{ $notification->contact->relationship }}
    @endif
    @if($notification->contact->birthday)
    **Birthday:** {{ \Carbon\Carbon::parse($notification->contact->birthday)->format('M j, Y') }}
    @endif
@endif
@endcomponent

@component('mail::button', ['url' => url('/dashboard')])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent