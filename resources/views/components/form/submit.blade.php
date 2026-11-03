{{-- Absende-Knopf wie Form::submit (laravelcollective/html). --}}
@props(['value'])
<input type="submit" value="{{ $value }}" {{ $attributes }}>
