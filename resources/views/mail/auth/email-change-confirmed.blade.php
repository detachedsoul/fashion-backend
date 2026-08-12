@component('mail::message')
# Hi,

Your account email has been changed successfully, from **{{ $oldEmail }}** to **{{ $newEmail }}**. This address is now confirmed and active on your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
