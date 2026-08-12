@component('mail::message')
# Hi,

Someone just requested to change this account's email from **{{ $oldEmail }}** to **{{ $requestedEmail }}**.

Nothing has changed yet — this account still logs in with **{{ $oldEmail }}** until the new address is confirmed there.

**If you didn't request this**, your password may be compromised. Contact us immediately and change your password.

@component('mail::button', ['url' => $supportUrl])
Contact Support
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
