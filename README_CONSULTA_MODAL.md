Pendiente técnico: modal de detalle al click en `consulta.php`

Objetivo
- Al hacer clic en cualquier fila del listado en `consulta.php`, abrir una ventana modal con detalle del registro (igual a la modal existente en `buscar_trabajadores.php`).

Estado actual (verificado)
- `consulta.php` ya tiene: onclick="abrirModalTrabajadorConsulta(this)" en cada <tr>
- `consulta.php` NO tiene: modal #modalVerTrabajador
- `consulta.php` NO tiene: función JS abrirModalTrabajadorConsulta (ni alias)
- `consulta.php` NO tiene: data-* completos en cada <tr>

Implementación requerida
1) Copiar al final de `consulta.php`:
   - El bloque HTML de la modal #modalVerTrabajador
   - La función JS abrirModalTrabajador(fila)
   - Crear alias: `function abrirModalTrabajadorConsulta(fila){ return abrirModalTrabajador(fila); }`
2) En el <tr> del listado, agregar data-*
   Usar exactamente los nombres que lee la función JS:
   data-codigo, data-nombre, data-cedula, data-foto, data-descripcion, data-archivo, data-tipo, data-fecha-registro, data-valor-inicial, data-valor-final, data-usuarioRegistro (según exista), data-inhabilitado.

Archivo de referencia
- `buscar_trabajadores.php` contiene ambos: modal y función JS.

