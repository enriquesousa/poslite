<div>
    <style></style>


    <div class="row layout-top-spacing">
        
        {{-- Columna de la Izquierda --}}
        <div class="col-sm-12 col-md-8">
            {{-- DETALLES --}}
            @include('livewire.pos.partials.detail')
        </div>

        {{-- Columna de la Derecha --}}
        <div class="col-sm-12 col-md-4">
            {{-- TOTAL --}}
            @include('livewire.pos.partials.total')

            {{-- DENOMINACIONES --}}
            @include('livewire.pos.partials.coins')
        </div> 

    </div>

</div>

<script>
    
</script>
