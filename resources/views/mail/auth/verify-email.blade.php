@component('mail::message')
# Hi {{ $name }},

Thanks for signing up. Please confirm your email address to activate your account.

@component('mail::button', ['url' => $url])
Verify Email Address
@endcomponent

This link expires in 60 minutes. If you didn't create an account, no action is needed.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
