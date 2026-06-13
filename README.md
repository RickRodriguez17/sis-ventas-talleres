# Sistema Web de Gestión de Ventas y Producción

Sistema administrativo para un negocio de comida rápida de autoservicio, desarrollado con PHP puro, MySQL, Bootstrap 5, JavaScript y arquitectura MVC.

## Módulo 1 incluido

- Estructura MVC escalable.
- Base de datos `db_ventas` con tablas normalizadas para ventas, producción, caja, pedidos, usuarios, configuración, auditoría y combos.
- Inicio de sesión con contraseñas cifradas.
- Control de sesiones y acceso por rol (`administrador`, `cajero`).
- Layout responsive con sidebar, navbar, tarjetas y gráfico.
- Dashboard inicial con métricas por rol.

## Requisitos

- PHP 8+
- MySQL 5.7+ / MariaDB 10+
- Extensión PDO MySQL habilitada

## Instalación local

1. Importar la base de datos:

   ```bash
   mysql -u root -p < database/database.sql
   ```

2. Configurar variables de entorno si tus credenciales no son las predeterminadas:

   ```bash
   export DB_HOST=127.0.0.1
   export DB_NAME=db_ventas
   export DB_USER=root
   export DB_PASS=
   export APP_URL=http://localhost:8000
   ```

3. Iniciar servidor local recomendado:

   ```bash
   php -S localhost:8000 -t public
   ```

4. Abrir:

   ```text
   http://localhost:8000
   ```

También puedes abrir el proyecto desde Apache apuntando a la carpeta raíz, por ejemplo:

```text
http://localhost/sis-ventas-talleres/
```

El archivo `index.php` de la raíz carga automáticamente la aplicación ubicada en `public/`.

## Usuarios de prueba

| Rol | Usuario | Contraseña |
| --- | --- | --- |
| Administrador | admin@demo.com | admin123 |
| Cajero | cajero@demo.com | cajero123 |

## Próximos módulos sugeridos

1. Productos y categorías.
2. Producción diaria e historial.
3. POS / ventas y tickets.
4. Pedidos y pantalla de pedidos listos.
5. Caja.
6. Reportes y exportaciones.
7. Configuración avanzada.
