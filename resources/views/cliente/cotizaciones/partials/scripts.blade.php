<script>
    // Accordion por cotización
    function toggleCotizacion(id){
        const el = document.getElementById(id);
        if(!el) return;
        el.classList.toggle('hidden');
    }

    // Modal responder (aceptar/rechazar) + nota
    (function(){
        const modal = document.getElementById('respuestaModal');
        const panel = document.getElementById('respuestaPanel');
        const title = document.getElementById('respuestaTitle');
        const form  = document.getElementById('respuestaForm');
        const submit= document.getElementById('respuestaSubmit');

        window.openRespuestaModal = function(tipo, actionUrl){
            form.action = actionUrl;

            if(tipo === 'aceptar'){
                title.textContent = 'Aceptar cotización';
                submit.textContent = 'Aceptar';
                submit.className = 'px-5 py-2 rounded-xl font-extrabold text-white bg-green-600 hover:bg-green-700';
            }else{
                title.textContent = 'Rechazar cotización';
                submit.textContent = 'Rechazar';
                submit.className = 'px-5 py-2 rounded-xl font-extrabold text-white bg-red-600 hover:bg-red-700';
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });
        }

        window.closeRespuestaModal = function(){
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                const ta = form.querySelector('textarea[name="nota_cliente"]');
                if(ta) ta.value = '';
            }, 160);
        }

        document.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') window.closeRespuestaModal();
        });
    })();

    // Modal de imagen (zoom)
    (function(){
        const modal = document.getElementById('productImageModal');
        const panel = document.getElementById('productImagePanel');
        const img = document.getElementById('productImageTag');
        const title = document.getElementById('productImageTitle');
        const sub = document.getElementById('productImageSub');

        window.openProductImageModal = function(url, t, s){
            title.textContent = t || 'Producto';
            sub.textContent = s || '';
            img.src = url || '';
            img.alt = t || 'Producto';

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });
        }

        window.closeProductImageModal = function(){
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                img.src = '';
            }, 160);
        }

        document.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') window.closeProductImageModal();
        });
    })();
</script>
