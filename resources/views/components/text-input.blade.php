@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-600 bg-white/90 text-black focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm placeholder:text-gray-500']) }}>
