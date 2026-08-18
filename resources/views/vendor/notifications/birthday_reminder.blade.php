@component('mail::message')
# Birthday Reminder 🎂

Today is **{{ $contact->name }}'**s birthday!

@if($contact->relationship)
**Relationship:** {{ $contact->relationship }}
@endif

@if($contact->birthday)
**Born:** {{ \Carbon\Carbon::parse($contact->birthday)->format('M j, Y') }}
@endif

@component('mail::button', ['url' => url('/contacts')])
View Contacts
@endcomponent

Wishing them a wonderful day! 🎉<br>
{{ config('app.name') }}
@endcomponent