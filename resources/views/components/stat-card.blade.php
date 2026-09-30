@props(['color' => 'blue', 'icon' => '', 'value' => 0, 'label' => '', 'href' => null])

@php
$colorClasses = match($color) {
    'blue' => 'border-l-blue-500 text-blue-600',
    'green' => 'border-l-green-500 text-green-600',
    'purple' => 'border-l-purple-500 text-purple-600',
    'red' => 'border-l-red-500 text-red-600',
    'amber', 'yellow' => 'border-l-amber-500 text-amber-600',
    'cyan' => 'border-l-cyan-500 text-cyan-600',
    'orange' => 'border-l-orange-500 text-orange-600',
    default => 'border-l-gray-500 text-gray-600',
};

$iconBgClasses = match($color) {
    'blue' => 'bg-blue-50',
    'green' => 'bg-green-50',
    'purple' => 'bg-purple-50',
    'red' => 'bg-red-50',
    'amber', 'yellow' => 'bg-amber-50',
    'cyan' => 'bg-cyan-50',
    'orange' => 'bg-orange-50',
    default => 'bg-gray-50',
};
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 {{ $colorClasses }} p-5 transition-transform hover:-translate-y-1 duration-200">
    @if($href)
        <a href="{{ $href }}" class="block">
    @endif
    
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">{{ $label }}</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ $value }}</h3>
        </div>
        <div class="p-3 rounded-full {{ $iconBgClasses }}">
            @if($icon)
                {!! $icon !!}
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @endif
        </div>
    </div>

    @if($href)
        </a>
    @endif
</div>
