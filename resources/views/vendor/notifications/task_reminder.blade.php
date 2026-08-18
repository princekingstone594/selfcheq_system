@component('mail::message')
# Task Deadline Reminder ⏰

Your task **{{ $task->title }}** is due today!

@if($task->deadline)
**Due:** {{ \Carbon\Carbon::parse($task->deadline)->format('M j, Y g:ia') }}
@endif

@if($task->description)
**Details:** {{ $task->description }}
@endif

@component('mail::button', ['url' => url('/tasks')])
View Tasks
@endcomponent

Stay focused and get it done! 💪<br>
{{ config('app.name') }}
@endcomponent