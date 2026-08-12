@component('mail::message')
# Hi {{ $name }},

Your admin account email has been verified. You're all set.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
