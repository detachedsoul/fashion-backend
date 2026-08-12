@component('mail::message')
# Hi {{ $name }},

Your account password was just changed from your account settings. Your other logged-in devices remain signed in.

**If you didn't do this**, contact us immediately using the button below.

@component('mail::button', ['url' => $supportUrl])
Contact Support
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
