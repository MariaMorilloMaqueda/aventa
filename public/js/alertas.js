// JavaScript --> Función para manejar los mensajes de confirmación o error, aparecen y desaparecen progresivamente
window.animarMensajes = function() {

    // Se seleccionan todos los mensajes
    let alertas = document.querySelectorAll('.mensaje, .mensaje-error');

    alertas.forEach(function(alerta) {

        // Solo si el mensaje no tiene animación se animará
        if (alerta.getAttribute('data-animado') !== 'true') {

            // Le ponemos el escudo para que no se repita
            alerta.setAttribute('data-animado', 'true');

            // Temporizador --> Aparece el mensaje tras 10 segundos
            setTimeout(function() {
                alerta.style.opacity = '1';
            }, 10);

            // Temporizador --> Desaparece el mensaje tras 4000 segundos
            setTimeout(function() {
                // Se hacen transparente poco a poco (gracias al CSS)
                alerta.style.opacity = '0';
                
                // Se borran los mensajes tras 500ms
                setTimeout(function() {
                    alerta.style.display = 'none';
                }, 600);

            }, 4000); // <-- Cambiar este 4000 si queremos que dure mas o menos
        }
    });
}

// Al cargar la página llamamos a la función
document.addEventListener('DOMContentLoaded', function() {
    window.animarMensajes();
});