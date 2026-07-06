<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger text-white']) }}>
    {{ $slot }}
</button>
