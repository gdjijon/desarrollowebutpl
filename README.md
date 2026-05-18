# desarrollowebutpl
proyecto desarrollo web 1 parcial

INSTRUCTIVO PARA VISUALIZACION DE PROYECTO

El proyecto consiste en un pequeño sistema web mediante login de PHP y MySQL
Con acceso a una zona privada, visible solo a los usuarios autenticados.
Opciones de actualización de datos básicos de perfil ( nombre y correo electrónico)
Cmabio de contraseña segura verificando la contraseña actual y almacenando la nueva con hash.
Cierre de sesión para finalizar la autenticación

Validaciones incluidas
Correo repetido y sintaxis
Validación de cedula
Validación de contraseña con requerimientos de complejidad
Contraseña guardada con hash
opciones de zona privada
opciones de cambio de contraseña, peticion de contraseña antiguam 
uso de password_verify
Uso de password_hash
mostrar mensajes de exito y error 

PASOS PARA INSTALACIÓN

1. DESCARGAR WEB DESDE LA WEB:  https://www.apachefriends.org/es/download.html

2.INSTALAR XAMPP EN EL EQUIPO Y EJECUTAR SERVICIOS MYSQL Y APACHE


3.PARA LA CREACIÓN DE LA BASE DE DATOS:
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


4. DESCARGA DE ARCHIVOS DE PAGINA WEB DESDE GITHUB:
En https://github.com/gdjijon/desarrollowebutpl

5. COLOCAR LOS ARCHIVOS EN LA RUTA LOCAL (CREAR LA CARPETA proyectodisweb) en HTDOCS
C:\xampp\htdocs\proyectodisweb

6.RUTA WEB DE LOGIN DEL PROYECTO:
http://localhost/proyectodisweb/login.php
