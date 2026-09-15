@component('mail::message')
# New Contact Form Submission

**Name:** {{ $contactMessage->name }}
**Email:** {{ $contactMessage->email }}
@if($contactMessage->organization)
**Organization:** {{ $contactMessage->organization }}
@endif
**Reason:** {{ ucfirst($contactMessage->reason) }}

**Message:**

{{ $contactMessage->message }}

@component('mail::button', ['url' => config('app.url') . '/admin'])
View in Admin Panel
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent