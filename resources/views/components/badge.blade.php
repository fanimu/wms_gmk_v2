@props(['type' => 'default', 'size' => 'sm'])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-full';

$sizeClasses = match($size) {
    'xs' => 'px-2 py-0.5 text-xs',
    'sm' => 'px-2.5 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-1.5 text-base',
    default => 'px-2.5 py-0.5 text-xs',
};

$typeClasses = match($type) {
    'success' => 'bg-green-100 text-green-800 border border-green-200',
    'danger' => 'bg-red-100 text-red-800 border border-red-200',
    'warning' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    'info' => 'bg-blue-100 text-blue-800 border border-blue-200',
    'primary' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
    default => 'bg-gray-100 text-gray-800 border border-gray-200',
};
@endphp

<span {{ $attributes->merge(['class' => $baseClasses . ' ' . $sizeClasses . ' ' . $typeClasses]) }}>
    {{ $slot }}
</span>
