@component('mail::message')
# Hallo {{$action->user['username']}}, Du hast eine Zopfaktion erstellt.

Viel Erfolg bei deiner {{$action['name']}}. Solltest du noch Fragen haben, dann lies die interaktive Hilfe oder melde dich bei
<a href="mailto:{{config('mail.from.address')}}">uns.</a>

Vielen Dank und viel Spass,<br>
Das CeviTools-Team
@endcomponent
