<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-[#0056b3] border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-[#004494] focus:bg-[#004494] active:bg-[#003366] focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition ease-in-out duration-300 shadow-xl shadow-blue-900/40 hover:shadow-blue-500/50']) }}>
    {{ $slot }}
</button>
