INSTRUCTIVO PARA VISUALIZACION DE PROYECTO

1.INSTALAR XAMPP Y EJECUTAR SERVICIOS MYSQL Y APACHE


2.PARA LA CREACIÓN DE LA BASE DE DATOS:
Ingresar a http://localhost/phpmyadmin   opción SQL ejecutar:

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(15) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME DEFAULT CURRENT_TIMESTAMP,
    intentos_fallidos INT DEFAULT 0
);


3. DESCARGA DE ARCHIVOS DE PAGINA WEB DESDE GITHUB:
En https://github.com/gdjijon/desarrollowebutpl

3. COLOCAR LOS ARCHIVOS EN LA RUTA LOCAL (CREAR LA CARPETA proyectodisweb) en HTDOCS
C:\xampp\htdocs\proyectodisweb

3.RUTA DE LOGIN DEL PROYECTO:
http://localhost/proyectodisweb/login.php
