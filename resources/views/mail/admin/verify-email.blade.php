@component('mail::message')
# Hi {{ $name }},

An admin account was created for you. Please confirm your email address to activate it.

@component('mail::button', ['url' => $url])
Verify Email Address
@endcomponent

This link expires in 60 minutes.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
