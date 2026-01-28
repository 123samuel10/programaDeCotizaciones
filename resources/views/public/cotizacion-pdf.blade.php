<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        .header { border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 16px; }
        .brand { font-size: 16px; font-weight: 800; }
        .muted { color:#555; }
        .box { border:1px solid #ddd; border-radius:10px; padding:12px; margin-bottom: 12px; }
        .grid { width:100%; }
        .grid td { vertical-align: top; }
        .right { text-align:right; }
        table.items { width:100%; border-collapse: collapse; margin-top: 10px; }
        table.items th { background:#f3f4f6; padding:8px; text-align:left; border:1px solid #e5e7eb; font-size: 11px; }
        table.items td { padding:8px; border:1px solid #e5e7eb; }
        .badge { display:inline-block; padding:3px 8px; border-radius:999px; font-size:10px; font-weight:700; background:#f3f4f6; }
        .totalBox { background:#eef2ff; border:1px solid #c7d2fe; border-radius:12px; padding:14px; }
        .totalBig { font-size: 18px; font-weight: 900; }
        ul { margin: 6px 0 0 16px; padding:0; }
        li { margin: 2px 0; }
        .footer { margin-top: 18px; font-size: 10px; color:#666; border-top:1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

@php
  $c = $cotizacion;
  $estado = $c->estado ?? 'pendiente';
@endphp

<div class="header">
    <table class="grid">
        <tr>
            <td>
                <div class="brand">Heral Enterprises</div>
                <div class="muted">Documento: Cotización #{{ $c->id }}</div>
                <div class="muted">Fecha: {{ optional($c->created_at)->format('d/m/Y H:i') }}</div>
            </td>
            <td class="right">
                <span class="badge">Estado: {{ strtoupper($estado) }}</span>
            </td>
        </tr>
    </table>
</div>

<div class="box">
    <table class="grid">
        <tr>
            <td>
                <div><b>Cliente:</b> {{ $c->usuario->name ?? '—' }}</div>
                <div class="muted"><b>Correo:</b> {{ $c->usuario->email ?? '—' }}</div>
            </td>
            <td class="right">
                <div class="muted">Total</div>
                <div class="totalBig">${{ number_format((float)$c->total_venta, 2, ',', '.') }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="box">
    <b>Detalle de productos</b>

    <table class="items">
        <thead>
        <tr>
            <th style="width:38%;">Producto</th>
            <th style="width:12%;">Cant.</th>
            <th style="width:16%;" class="right">Base</th>
            <th style="width:18%;" class="right">Adiciones</th>
            <th style="width:16%;" class="right">Total</th>
        </tr>
        </thead>

        <tbody>
        @foreach($c->items as $it)
            @php
                $p = $it->producto;
                $baseLinea = (float)$it->precio_base_venta * (int)$it->cantidad;
                $adicLinea = (float)$it->opciones->sum('subtotal_venta');
                $totalLinea = $baseLinea + $adicLinea;
            @endphp

            <tr>
                <td>
                    <b>{{ $p->nombre_producto ?? 'Producto' }}</b><br>
                    <span class="muted">{{ $p->marca ?? '—' }} · {{ $p->modelo ?? '—' }}</span>

                    @if($it->opciones->count())
                        <div class="muted" style="margin-top:6px;">
                            <b>Adiciones:</b>
                            <ul>
                                @foreach($it->opciones as $op)
                                    <li>
                                        {{ $op->opcion->nombre ?? '—' }}
                                        (x{{ (int)$op->cantidad }}) —
                                        ${{ number_format((float)$op->subtotal_venta, 2, ',', '.') }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </td>

                <td>{{ (int)$it->cantidad }}</td>

                <td class="right">
                    ${{ number_format($baseLinea, 2, ',', '.') }}
                </td>

                <td class="right">
                    ${{ number_format($adicLinea, 2, ',', '.') }}
                </td>

                <td class="right">
                    <b>${{ number_format($totalLinea, 2, ',', '.') }}</b>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="totalBox">
    <table class="grid">
        <tr>
            <td>
                <div><b>Total general</b></div>
                <div class="muted">Incluye productos + adiciones.</div>
            </td>
            <td class="right">
                <div class="totalBig">${{ number_format((float)$c->total_venta, 2, ',', '.') }}</div>
            </td>
        </tr>
    </table>
</div>

@if(!empty($c->nota_cliente))
    <div class="box">
        <b>Nota del cliente</b>
        <div class="muted" style="margin-top:6px;">{{ $c->nota_cliente }}</div>
    </div>
@endif

<div class="footer">
    Este documento fue generado automáticamente. Si necesitas soporte, responde este correo o contacta a Heral Enterprises.
</div>

</body>
</html>
