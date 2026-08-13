@if (filled($errors))
    <x-filament::callout icon="{{ $icon }}" color="{{ $type }}">
        <x-slot name="heading">
            {{ $title }}
        </x-slot>

        <x-slot name="description">
            {{ $errors }}
        </x-slot>
    </x-filament::callout>
@endif
