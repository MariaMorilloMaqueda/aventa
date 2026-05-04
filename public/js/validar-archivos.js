document.addEventListener('DOMContentLoaded', function() {
    // Buscamos TODOS los inputs que tengan la clase 'validar-peso'
    let inputsValidacionImagen = document.querySelectorAll('.validar-peso');

    // Le aplicamos la vigilancia a cada uno de ellos
    inputsValidacionImagen.forEach(function(input) {
        input.addEventListener('change', function() {
            // Comprobamos si el usuario ha seleccionado un archivo
            if (this.files && this.files[0]) {
                let archivo = this.files[0];
                let tamañoMaximo = 2 * 1024 * 1024; // 2MB pasados a bytes

                if (archivo.size > tamañoMaximo) {
                    // Le avisamos al instante
                    alert("Atención: La imagen pesa demasiado. El tamaño máximo permitido es 2MB.");
                    // Reseteamos el campo para que no intente enviarlo
                    this.value = ''; 
                }
            }
        });
    });
});