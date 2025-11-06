SISTEMA DE GESTIÓN DE APLICACIONES - MINDEF V. 1.0
REQUERIMIENTOS
PHP V7.2.4 o superior

Extensión PDO_MYSQL

NODE JS V17.9.0

NPM V8.5

COMPOSER V2.3 o superior

GIT V2.35 o superior.

MOD_REWRITE activo en el servidor

PASOS PARA INICIAR

1. Verificar MOD_REWRITE
   El servidor deberá poseer al menos esta configuración

conf
<Directory /var/www/html>
AllowOverride All
</Directory>
En un servidor Ubuntu esta configuración debe colocarse en /etc/apache2/sites-available/

2. Clonar repositorio
   Clonarlo en la carpeta que se este utilizando como base en el servidor (Ej. C:\docker)

bash
git clone https://github.com/Carbyfah/sistema_aplicaciones.git 3. Crear archivo GIT IGNORE (.gitignore)
Debe colocarse en la raíz del proyecto, con el siguiente contenido

git
node_modules
vendor
composer.lock
packagelock.json
public/
build
.gitignore
.htaccess
public/.htaccess
temp
storage
includes/.env 4. Crear archivos HTACCESS
Estos archivos se usaran para redirigir las consultas hacia el archivo index.php

Archivo htaccess de la raíz
Deberá colocarse en la raíz del proyecto

text
RewriteEngine on
RewriteRule ^$ public/ [L]
RewriteRule (.\*) public/$1 [L]
Archivo htaccess de la carpeta public
Deberá colocarse dentro de la carpeta public

text
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L] 5. Crear archivo .env
Este archivo deberá contener la información según el entorno en que se ejecute el proyecto y deberá contener esta información

text
DEBUG_MODE = 0
DB_HOST=localhost
DB_SERVICE=3306
DB_SERVER=mysql_server
DB_NAME=apps
DB_USER=developer
DB_PASS=rootpassword

APP_NAME = "sistema_aplicaciones" 6. Configurar Base de Datos
Ejecutar los siguientes comandos en MySQL/MariaDB:

sql
CREATE DATABASE apps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'developer'@'%' IDENTIFIED BY 'rootpassword';
GRANT ALL PRIVILEGES ON apps.\* TO 'developer'@'%';
FLUSH PRIVILEGES; 7. Instalar paquetes de node
Ejecutar en consola el comando siguiente y esperar a que termine su ejecución

text
npm install 8. Instalar paquetes de composer
Ejecutar en consola el comando siguiente y esperar a que termine su ejecución

text
composer install 9. Construir archivos en la carpeta pública
Ejecutar en consola el comando siguiente y esperar a que termine su ejecución

text
npm run build
Este comando permanecerá en ejecución mientras se este trabajando en el proyecto

10. Configurar versiones y descripción del proyecto
    Configurar los archivos con la información del proyecto y la versión en la que se esta trabajando

package.json

composer.json

DESCRIPCIÓN DEL PROYECTO
Sistema completo de gestión de proyectos de desarrollo de aplicaciones, construido con arquitectura MVC. Permite la administración de proyectos, asignación de personal, control de costos, gestión de documentación y auditoría completa del sistema.

CARACTERÍSTICAS PRINCIPALES
Módulos del Sistema
Dashboard - Vista general y estadísticas

Gestión de Proyectos - Catálogo y asignaciones

Control de Costos - Cálculo automático con factores de complejidad y seguridad

Base de Datos - Gestión de estructura de tablas y campos

Documentación - Sistema de archivos y categorías

Personal - Gestión de equipo y asignaciones

Configuración - Usuarios, roles, permisos granulares

Auditoría - Logs de actividad y sesiones

Manuales - Documentación del sistema

Tecnologías Implementadas
Backend: PHP 7.2+ con arquitectura MVC

Frontend: JavaScript moderno con Webpack

Base de Datos: MySQL/MariaDB

UI: AdminLTE 3.2 + Bootstrap 4.6

Herramientas: DataTables, Select2, Chart.js, SweetAlert2

ESTRUCTURA DEL PROYECTO
text
sistema_aplicaciones/
├── controllers/ # Controladores MVC
├── models/ # Modelos de datos
├── views/ # Vistas del sistema
├── src/
│ ├── js/ # JavaScript modular
│ └── scss/ # Estilos Sass
├── public/ # Archivos públicos
├── includes/ # Configuración y utilidades
└── uploads/ # Archivos subidos
MÓDULOS DISPONIBLES
Gestión de Proyectos
Catálogo de proyectos

Proyectos asignados

Estados de proyectos

Gestión de tareas

Control de Costos
Niveles de complejidad

Factores de seguridad

Cálculo automático de costos

Presupuestos detallados

Base de Datos
Tipos de tabla

Tipos de clave

Tipos de dato

Gestión de tablas y campos

Administración
Gestión de usuarios

Sistema de roles

Permisos granulares

Módulos del sistema

Auditoría
Logs de actividad

Control de sesiones

Registro de intentos de login

DESARROLLO
Comandos Útiles
bash

# Desarrollo con watch

npm run watch

# Producción

npm run build

# Verificar instalaciones

npm list --depth=0
Dependencias Principales
AdminLTE 3.2.0 - Panel de administración

DataTables 1.13.6 - Tablas interactivas

Select2 4.1.0 - Selectores avanzados

Chart.js 4.4.0 - Gráficos y estadísticas

Intro.js 7.2.0 - Tutoriales interactivos

SEGURIDAD
Autenticación de usuarios

Permisos granulares por módulo

Registro completo de actividades

Control de sesiones

Validación de archivos subidos

Protección contra inyecciones SQL

MANTENIMIENTO
Backups Recomendados
Base de datos regularmente

Archivos de uploads/

Configuración del sistema

Monitoreo
Revisar logs de actividad

Control de espacio en disco

Actualizaciones de seguridad

VERSIONES
Versión Actual: 1.0.0

PHP: 7.2+

MySQL: 5.7+

Node.js: 17.9.0+

CONTACTO Y SOPORTE
Desarrollador: Carbyfah
Repositorio: https://github.com/Carbyfah/sistema_aplicaciones

Sistema desarrollado para el Ministerio de Defensa - Gestión de Proyectos de Aplicaciones
