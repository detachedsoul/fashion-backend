@component('mail::message')
# Confirm your new admin email address

Click below to confirm this address as your admin account's new email. Nothing changes until you do.

@component('mail::button', ['url' => $url])
Confirm Email Address
@endcomponent

This link expires in 60 minutes. If you didn't request this, just ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
