<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizacion;


class CotizacionMail extends Mailable
{
      use Queueable, SerializesModels;

    public Cotizacion $cotizacion;

    public function __construct(Cotizacion $cotizacion)
    {
        $this->cotizacion = $cotizacion;
    }

    public function build()
    {
        // IMPORTANTE: cargar relaciones para que el PDF salga completo
        $cotizacion = $this->cotizacion->load([
            'usuario',
            'items.producto',
            'items.opciones.opcion',
        ]);

        // Generar PDF (misma vista que ya usas)
        $pdf = Pdf::loadView('public.cotizacion-pdf', [
            'cotizacion' => $cotizacion
        ])->setPaper('a4');

        $nombreArchivo = 'Cotizacion_' . $cotizacion->id . '.pdf';

        return $this->subject('Tu cotización #' . $cotizacion->id)
            ->view('emails.cotizacion')
            ->with(['cotizacion' => $cotizacion])
            ->attachData(
                $pdf->output(),
                $nombreArchivo,
                ['mime' => 'application/pdf']
            );
    }
}
