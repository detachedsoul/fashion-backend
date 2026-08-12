@component('mail::message')
# Hi,

Your admin account email has been changed successfully, from **{{ $oldEmail }}** to **{{ $newEmail }}**. This address is now confirmed and active on the account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
