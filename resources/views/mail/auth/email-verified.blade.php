@component('mail::message')
# Hi {{ $name }},

Your email address has been verified. Your account is fully active now.

@component('mail::button', ['url' => $shopUrl])
Start Shopping
@endcomponent

Thanks for confirming — welcome aboard.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
