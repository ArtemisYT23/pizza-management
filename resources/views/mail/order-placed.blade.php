<x-mail::message>
# Hola, {{ $order->user->name }}

Gracias por tu pedido. Aquí tienes el resumen:

**Pizza:** {{ $order->pizza->name }}  
**Precio:** ${{ $order->pizza->price }}

@if($order->pizza->description)
<x-mail::panel>
{{ $order->pizza->description }}
</x-mail::panel>
@endif

## Ingredientes
@foreach($order->pizza->ingredients as $ingredient)
- {{ $ingredient->name }}
@endforeach

**Fecha del pedido:** {{ $order->ordered_at->format('d/m/Y H:i') }}

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
