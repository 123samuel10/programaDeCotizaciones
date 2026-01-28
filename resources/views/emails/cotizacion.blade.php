@php
  $c = $cotizacion;
  $token = $c->token ?? '';

  // 🔗 Enlaces públicos
  $urlVer      = $token ? route('public.cotizacion.ver', ['token' => $token]) : '#';

  // 👉 VAN A CONFIRMAR (NO a la acción directa)
  $urlAceptar  = $token ? route('public.cotizacion.confirmar.aceptar', ['token' => $token]) : '#';
  $urlRechazar = $token ? route('public.cotizacion.confirmar.rechazar', ['token' => $token]) : '#';

  // 📄 Descargar PDF
  $urlPdf      = $token ? route('public.cotizacion.pdf', ['token' => $token]) : '#';
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cotización #{{ $c->id }}</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
<tr>
<td align="center">

{{-- CONTENEDOR --}}
<table width="640" cellpadding="0" cellspacing="0"
       style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,.08);">

  {{-- HEADER --}}
  <tr>
    <td style="background:#1f2937;color:#ffffff;padding:24px;">
      <h1 style="margin:0;font-size:22px;">
        Cotización #{{ $c->id }}
      </h1>
      <p style="margin:6px 0 0;font-size:14px;opacity:.85;">
        Heral Enterprises
      </p>
    </td>
  </tr>

  {{-- SALUDO --}}
  <tr>
    <td style="padding:24px;">
      <p style="margin:0 0 12px;font-size:15px;">
        Hola <b>{{ $c->usuario->name }}</b>,
      </p>

      <p style="margin:0;font-size:14px;line-height:1.6;">
        Te compartimos el detalle completo de tu cotización.
        Revisa los productos, adiciones y el total final.
      </p>
    </td>
  </tr>

  {{-- ITEMS --}}
  <tr>
    <td style="padding:0 24px 16px;">
      @foreach($c->items as $item)
        @php
          $p = $item->producto;
          $base = (float)$item->precio_base_venta * (int)$item->cantidad;
          $adic = (float)$item->opciones->sum('subtotal_venta');
          $totalLinea = $base + $adic;
        @endphp

        <table width="100%" cellpadding="0" cellspacing="0"
               style="border:1px solid #e5e7eb;border-radius:12px;margin-bottom:16px;">
          <tr>
            <td style="background:#f9fafb;padding:14px 16px;">
              <b style="font-size:15px;">
                {{ $p->nombre_producto ?? 'Producto' }}
              </b><br>
              <span style="font-size:12px;color:#6b7280;">
                {{ $p->marca ?? '' }} {{ $p->modelo ?? '' }}
              </span>
            </td>
          </tr>

          <tr>
            <td style="padding:14px 16px;font-size:13px;">
              <p><b>Cantidad:</b> {{ (int)$item->cantidad }}</p>
              <p><b>Base:</b> ${{ number_format($base, 2, ',', '.') }}</p>
              <p><b>Adiciones:</b> ${{ number_format($adic, 2, ',', '.') }}</p>

              @if($item->opciones->count())
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="border-collapse:collapse;font-size:12px;margin-bottom:10px;">
                  @foreach($item->opciones as $op)
                    <tr>
                      <td style="padding:4px 0;color:#374151;">
                        • {{ $op->opcion->nombre ?? 'Adición' }} (x{{ (int)$op->cantidad }})
                      </td>
                      <td align="right">
                        ${{ number_format((float)$op->subtotal_venta, 2, ',', '.') }}
                      </td>
                    </tr>
                  @endforeach
                </table>
              @endif

              <p style="padding-top:10px;border-top:1px dashed #e5e7eb;">
                <b>Total producto:</b>
                ${{ number_format($totalLinea, 2, ',', '.') }}
              </p>
            </td>
          </tr>
        </table>
      @endforeach
    </td>
  </tr>

  {{-- TOTAL --}}
  <tr>
    <td style="padding:16px 24px;background:#eff6ff;">
      <table width="100%">
        <tr>
          <td><b>Total cotización</b></td>
          <td align="right" style="font-size:20px;font-weight:800;color:#1e3a8a;">
            ${{ number_format((float)$c->total_venta, 2, ',', '.') }}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- BOTONES --}}
  <tr>
    <td style="padding:24px;text-align:center;">

      {{-- ACEPTAR --}}
      <a href="{{ $urlAceptar }}"
         style="display:inline-block;padding:12px 22px;
                background:#16a34a;color:#ffffff;
                text-decoration:none;border-radius:10px;
                font-weight:800;margin-right:8px;">
        ✅ Aceptar cotización
      </a>

      {{-- RECHAZAR --}}
      <a href="{{ $urlRechazar }}"
         style="display:inline-block;padding:12px 22px;
                background:#dc2626;color:#ffffff;
                text-decoration:none;border-radius:10px;
                font-weight:800;">
        ❌ Rechazar
      </a>

      {{-- DESCARGAR PDF --}}
      <div style="margin-top:14px;">
        <a href="{{ $urlPdf }}"
           style="display:inline-block;padding:10px 20px;
                  background:#2563eb;color:#ffffff;
                  text-decoration:none;border-radius:10px;
                  font-weight:800;">
          ⬇️ Descargar cotización en PDF
        </a>
      </div>

      <p style="margin:16px 0 0;font-size:12px;color:#6b7280;">
        También puedes verla en línea:
        <br>
        <a href="{{ $urlVer }}" style="color:#2563eb;font-weight:600;">
          Ver cotización
        </a>
      </p>

    </td>
  </tr>

  {{-- FOOTER --}}
  <tr>
    <td style="background:#f9fafb;padding:16px;text-align:center;
               font-size:11px;color:#6b7280;">
      © {{ date('Y') }} Heral Enterprises · Correo automático
    </td>
  </tr>

</table>
</td>
</tr>
</table>

</body>
</html>
