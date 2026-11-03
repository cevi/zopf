{{-- Beschriftung wie Form::label (laravelcollective/html). --}}
@props(['for'])
<label for="{{ $for }}" {{ $attributes }}>{{ $slot }}</label>
