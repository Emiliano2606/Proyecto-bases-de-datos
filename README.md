# Instrucciones para Cargar la Base de Datos

Sigue estos pasos para restaurar la base de datos `sistema_mascotas` en tu servidor local de PostgreSQL.

## 1. Crear la Base de Datos
Antes de importar los archivos, debes crear una base de datos vacía. Puedes hacerlo desde la terminal de PostgreSQL o pgAdmin:

```sql
CREATE DATABASE sistema_mascotas;
```

## 1.1. Para windows las tablas
Asegúrate de estar en la carpeta donde se encuentran los archivos .sql.:

```sql
psql -U postgres -d sistema_mascotas -f basededatos.sql
```

## 1.2. Para windows la informacion

```sql
psql -U postgres -d sistema_mascotas -f datos.sql
```

## 1.2. Para linux/macos las tablas
Abre la terminal en la carpeta del proyecto y ejecuta:

```sql
psql -U postgres -d sistema_mascotas < basededatos.sql
```

## 1.2. Para linux/macos la informacion
Abre la terminal en la carpeta del proyecto y ejecuta:

```sql
psql -U postgres -d sistema_mascotas < datos.sql
```


## Notas Adicionales
Usuario: El usuario por defecto es postgres. Si usas otro, cámbialo en el comando.

Orden Importante: Siempre debes cargar primero basededatos.sql (crea las tablas) y después datos.sql (llena las tablas). Si lo haces al revés, dará error porque los datos no tendrán donde guardarse.

Contraseña: Al ejecutar cada comando, el sistema te solicitará la contraseña del usuario postgres.


# Como extra 
Si ninguno de estos dos funciona dejamos un.backup que puede ser usado en pgadmin4 la interfaz grafica de postresql

Abre pgAdmin 4.

Haz clic derecho sobre tu base de datos sistema_mascotas.

Selecciona la opción Restore (Restaurar).

En la pestaña General:

Filename: Haz clic en el icono de carpeta y selecciona el archivo basededatos.backup.

Format: Selecciona Custom or tar.

Haz clic en Restore.

Repite los mismos pasos pero ahora seleccionando el archivo sistema_mascotas.backup.


##------------------------------------------------------##

## Pasos para configurar la base del sistema
Ya que la base de datos esta cargada ahora nos vamos a la carpeta donde se encuentra todo nuestro sistema y nos vamos a la carpeta de includes y vemos que hay un archivo db_connection.php debera modifcar solo estos

$dbname = "sistema_mascotas"; 
$password = "potros26"; 

Estos los adapta al nombre correcto

## Cosas Adicionales

El sistema requeire correr en un servidor lo cual le recomnedamos instalar xampp 
## 0. Instalación y Configuración de XAMPP

Para ejecutar este proyecto, es necesario tener instalado XAMPP y configurar el soporte para PostgreSQL.

### 0.1. Instalación
1. Descarga XAMPP desde [apachefriends.org](https://www.apachefriends.org/).
2. Instálalo en la ruta por defecto (generalmente `C:\xampp`).

### 0.2. Habilitar PostgreSQL en PHP
Por defecto, XAMPP trae desactivadas las funciones para conectar con PostgreSQL. Debes activarlas manualmente:

1. Abre el **XAMPP Control Panel**.
2. En la línea de **Apache**, haz clic en el botón **Config** y selecciona `PHP (php.ini)`.
3. Presiona `Ctrl + F` y busca la palabra **pgsql**.
4. Verás dos líneas como estas (tienen un punto y coma `;` al principio):
   - `;extension=pdo_pgsql`
   - `;extension=pgsql`
5. **Borra el punto y coma `;`** de ambas líneas para que queden así:
   - `extension=pdo_pgsql`
   - `extension=pgsql`
6. Guarda el archivo (`Ctrl + G`) y cierra el editor.

### 0.3. Iniciar el Servidor
1. En el Panel de XAMPP, presiona **Start** en el módulo **Apache**.
2. Asegúrate de que el servicio de **PostgreSQL** también esté iniciado (ya sea desde el panel si lo integraste o como servicio de Windows).

### 0.4. Despliegue del Proyecto
1. Copia la carpeta de este proyecto.
2. Pégala en: `C:\xampp\htdocs\`
3. Accede desde tu navegador a: `http://localhost/nombre_de_tu_carpeta`


