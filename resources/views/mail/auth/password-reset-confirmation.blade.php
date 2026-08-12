@component('mail::message')
# Hi {{ $name }},

Your password was just reset successfully. You've been logged out of all other devices as a precaution — you'll need to sign back in with your new password anywhere else you were logged in.

**If you didn't do this**, your account may be compromised. Contact us immediately using the button below.

@component('mail::button', ['url' => $supportUrl])
Contact Support
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
