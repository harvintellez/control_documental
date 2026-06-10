# Manual de Usuario - Sistema de Control Documental (NSEL-CLNSA)

## 📋 Índice
1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Panel Principal](#panel-principal)
4. [Registro de Oficio](#registro-de-oficio)
5. [Consulta de Documentos](#consulta-de-documentos)
6. [Búsqueda por Códigos](#búsqueda-por-códigos)
7. [Gestión Administrativa](#gestión-administrativa)
8. [Carga Masiva](#carga-masiva)
9. [Reportes](#reportes)
10. [Solución de Problemas](#solución-de-problemas)

## Introducción
**Sistema de Control Documental** para gestionar oficios legales (embargos judiciales, pensiones alimenticias, otros) vinculados a trabajadores. Permite registro digital, consulta rápida, control de estado y reportes.

**Roles**:
- **Admin**: Todo (incl. editar/eliminar/inhabilitar).
- **Consulta**: Ver/búsqueda/export (solo lectura).

**Requisitos**: Navegador moderno (Chrome/Firefox), conexión estable.

## Acceso al Sistema
1. Abrir: `http://localhost/control_documental/` (o IP servidor).
2. **Login**:
   ```
   Usuario: admin (o asignado)
   Contraseña: [proporcionada]
   ```
3. Click **Entrar al Sistema**.

![Login](img/login.png) *(Captura: Formulario centrado con logo ISA)*

**Error común**: Credenciales inválidas → verifica con admin.

## Panel Principal (`panel.php`)
Dashboard con estadísticas:
- Total trabajadores.
- Embargos judiciales.
- Otros embargos.
- Pensiones alimenticias.

**Botones rápidos**:
| Acción | Descripción |
|--------|-------------|
| **Registrar Oficio** | Nuevo registro. |
| **Consultar Documentos** | Listado completo. |
| **Generar Reporte PDF** | Impresión. |

![Panel](img/panel.png)

## Registro de Oficio (`registro.php`)
1. Click **Registrar Oficio**.
2. Completar:
   - Código trabajador (único).
   - Nombre completo, cédula.
   - **Foto perfil** (JPG/PNG).
   - **Tipo documento**: Embargo/Pensión/Otro.
   - **Descripción oficio** (detalles legales).
   - **Documento adjunto** (PDF/JPG/PNG, máx 5MB).
3. **Previsualizar** → **Confirmar**.
4. Éxito: Redirige consulta.

**Tips**:
- Duplicado código → Alerta, cancela auto.
- Archivos temp se borran si cancelas.

## Consulta de Documentos (`consulta.php`)
**Listado paginado** (15/reg):
1. Filtros:
   - **Búsqueda live**: Nombre/cédula/código → filtra instantáneo.
   - **Mostrar inhabilitados** (switch).
2. Columnas: Perfil, Código, Nombre, Cédula, Tipo, Estado, Acciones.
3. **Acciones**:
   | Icono | Acción | Admin? |
   |-------|--------|--------|
   | 📄 PDF | Ver doc | Todos |
   | ✏️ Lápiz | Editar | Admin |
   | ⛔ Slash | Inhabilitar | Admin |
   | 🔄 Flecha | Reactivar | Admin |
   | 🗑️ Basura | Eliminar | Admin |

**Modals**:
- Click fila → Detalle completo.
- Inhabilitar: Fecha + Motivo + Doc soporte.

**Paginación**: Números inferior.

## Búsqueda por Códigos (`buscar_trabajadores.php`)
1. Ir: Buscar en menú o directo.
2. Ingresar códigos: `352821, 364201` (coma).
3. **Buscar** → Tabla resultados.
4. **Double-click fila** → Modal detalle.
5. **Exportar Excel** → Descarga .xls.

## Gestión Administrativa (Solo Admin)
- **Editar** (`editar.php`): Modifica campos/docs.
- **Eliminar** (`eliminar.php`): Confirma permanente.
- **Inhabilitar** (`inhabilitar.php`): Modal w/ fecha/motivo/doc → marca gris.
- **Reactivar** (`reactivar.php`): Vuelve activo.
- **Usuarios** (`usuarios.php`): CRUD admins/consulta.

## Carga Masiva (`carga_masiva.php`)
1. Descargar `plantilla_carga.csv`.
2. Llenar (respeta columnas/encabezados).
3. **Subir CSV** → Preview.
4. **Procesar** → Insert batch.

**Límites**: 5MB, formato CSV coma.

## Reportes
- **Dashboard**: Stats cards.
- **Imprimible** (`reporte_imprimible.php`): PDF lista.
- **Excel**: Desde búsquedas.

## Solución de Problemas
| Problema | Causa | Solución |
|----------|-------|----------|
| No carga | Servidor off | Inicia XAMPP (Apache+MySQL). |
| Login falla | Credenciales | Contacta admin. |
| Upload falla | Tamaño/tipo | <5MB, PDF/JPG/PNG. |
| Búsqueda vacía | Códigos erróneos | Verifica formato. |
| Archivos no visibles | Permisos | Revisa `uploads/` 777 (Linux). |
| Base vacía | Primera uso | Registra datos. |

**Logs**: `logs/apache-*.log` para errores.

## Soporte
- Admin sistema: HTellez.
- Actualizaciones: Git (`git pull` en `/control_documental`).

**Fin del Manual. Versión: 2026.**

