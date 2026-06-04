<x-mail::message>
# {{ $subjectTitle }}

{!! $messageBody !!}

مع خالص التحيات،<br>
فريق {{ config('app.name', 'ZinToop') }}
</x-mail::message>
