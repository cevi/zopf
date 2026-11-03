{{-- Textfeld wie Form::textarea (laravelcollective/html): old() vor value vor Model-Attribut. --}}
@props(['name', 'value' => null, 'model' => null])
<textarea name="{{ $name }}" {{ $attributes->merge(['cols' => 50, 'rows' => 10]) }}>{{ \App\View\FormValue::resolve(\App\View\FormValue::key($name), $value, $model) }}</textarea>
