{{-- Eingabefeld wie Form::text/number/email/password/time/hidden/file (laravelcollective/html): old() vor value vor Model-Attribut. --}}
@props(['name', 'type' => 'text', 'value' => null, 'model' => null])
@php
    $key = \App\View\FormValue::key($name);
    $current = in_array($type, ['password', 'file']) ? null : \App\View\FormValue::resolve($key, $value, $model, $type);
@endphp
<input type="{{ $type }}" name="{{ $name }}" @if (! is_null($current)) value="{{ $current }}" @endif {{ $attributes }}>
