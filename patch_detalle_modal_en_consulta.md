Implementar modal de detalle igual a buscar_trabajadores.php en consulta.php:
1) Copiar el bloque de modal #modalVerTrabajador y el script function abrirModalTrabajador(fila) desde buscar_trabajadores.php.
2) En el listado de consulta.php, agregar data-* a cada <tr> con los campos:
   data-id, data-codigo, data-nombre, data-cedula, data-foto, data-descripcion, data-archivo, data-tipo, data-fecha-registro, data-valor-inicial, data-valor-final, data-usuarioRegistro (si existe) y data-inhabilitado.
3) Cambiar onclick del <tr> para llamar abrirModalTrabajador(this) o crear alias abrirModalTrabajadorConsulta() que llame a abrirModalTrabajador().
4) Asegurar event.stopPropagation() en enlaces/botones dentro de la fila (en especial el btn-group de acciones) para que no abra el modal al pulsarlos.

