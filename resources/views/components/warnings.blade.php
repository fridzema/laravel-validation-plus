@if($warnings->any())
    <div {{ $attributes->merge(['class' => 'alert alert-warning']) }}>
        <ul>
            @foreach($warnings->all() as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
    </div>
@endif
