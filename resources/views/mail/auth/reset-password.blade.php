@component('mail::message')
# Hi {{ $name }},

We received a request to reset your password.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

This link expires in {{ $expiresMinutes }} minutes. If you didn't request a password reset, no action is needed.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
