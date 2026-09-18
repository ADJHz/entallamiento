@php($isElemento = $actor instanceof \App\Models\Elemento)

<x-layouts::access :actor="$actor">
    <x-access-status
        :isElemento="$isElemento"
        :name="$isElemento ? $actor->nombre : $actor->name"
    />
</x-layouts::access>
