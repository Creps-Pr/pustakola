@props(['name', 'class' => 'size-5'])
@php
    $berkas = resource_path('icons/'.$name.'.svg');
    $svg = is_file($berkas) ? file_get_contents($berkas) : '';
    $svg = preg_replace('/<svg\b/', '<svg class="'.e($class).' shrink-0" aria-hidden="true" focusable="false"', $svg, 1);
@endphp
{!! $svg !!}
