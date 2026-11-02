{{-- Auswahlliste wie Form::select (laravelcollective/html): old() vor selected vor Model-Attribut. --}}
@props(['name', 'options' => [], 'selected' => null, 'model' => null, 'placeholder' => null])
@php
    $current = \App\View\FormValue::resolve(\App\View\FormValue::key($name), $selected, $model);
    $isSelected = fn ($value) => is_array($current) || $current instanceof \Illuminate\Support\Collection
        ? collect($current)->map(fn ($v) => (string) $v)->contains((string) $value)
        : ! is_null($current) && (string) $value === (string) $current;
@endphp
<select name="{{ $name }}" {{ $attributes }}>
    @if (! is_null($placeholder))
        <option value="" @selected(is_null($current) || $current === '')>{{ $placeholder }}</option>
    @endif
    @foreach ($options as $value => $label)
        @if (is_iterable($label))
            <optgroup label="{{ $value }}">
                @foreach ($label as $groupValue => $groupLabel)
                    <option value="{{ $groupValue }}" @selected($isSelected($groupValue))>{{ $groupLabel }}</option>
                @endforeach
            </optgroup>
        @else
            <option value="{{ $value }}" @selected($isSelected($value))>{{ $label }}</option>
        @endif
    @endforeach
</select>
