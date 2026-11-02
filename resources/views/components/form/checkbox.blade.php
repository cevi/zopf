{{-- Checkbox wie Form::checkbox (laravelcollective/html): nach dem Absenden zählt old(), sonst checked, sonst das Model-Attribut. --}}
@props(['name', 'value' => 1, 'checked' => null, 'model' => null])
@php
    $key = \App\View\FormValue::key($name);
    if (session()->hasOldInput()) {
        $state = old($key);
    } elseif (! is_null($checked)) {
        $state = $checked;
    } else {
        $state = data_get($model, $key);
    }
    $isChecked = is_iterable($state) ? collect($state)->contains($value) : (bool) $state;
@endphp
<input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked($isChecked) {{ $attributes }}>
