@component('mail::message')
# Hi {{ $name }},

Your admin password was just reset successfully. You've been logged out of all other devices as a precaution.

**If you didn't do this**, contact us immediately using the button below - an admin account is a high-value target.

@component('mail::button', ['url' => $supportUrl])
Contact Support
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
