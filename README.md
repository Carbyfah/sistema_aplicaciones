# SISTEMA DE GESTIÓN DE APLICACIONES - MINDEF

DESCRIPCIÓN DEL PROYECTO
Sistema completo de gestión de proyectos de desarrollo de aplicaciones, construido con arquitectura MVC. Permite la adstración de proyectos, asignación de personal, control de costos, gestión de documentación y auditoría completa del sistema.

CARACTERÍSTICAS PRINCIPALES
Módulos del Sistema

- Dashboard - Vista general y estadísticas

- Gestión de Proyectos - Catálogo y asignaciones

- Control de Costos - Cálculo automático con factores de complejidad y seguridad

- Base de Datos - Gestión de estructura de tablas y campos

- Documentación - Sistema de archivos y categorías

- Personal - Gestión de equipo y asignaciones

- Configuración - Usuarios, roles, permisos granulares

- Auditoría - Logs de actividad y sesiones

- Manuales - Documentación del sistema

Tecnologías Implementadas
Backend: PHP 7.2+ con arquitectura MVC

Frontend: JavaScript moderno con Webpack

Base de Datos: MySQL/MariaDB

UI: AdminLTE 3.2 + Bootstrap 4.6

Herramientas: DataTables, Select2, Chart.js, SweetAlert2

REQUERIMIENTOS TÉCNICOS
Servidor
PHP 7.2.4 o superior

Extensión PDO_MYSQL

MySQL 5.7+ o MariaDB 10.3+

Apache con mod_rewrite activo

Composer 2.3+

Node.js 17.9.0+

NPM 8.5+

Configuración PHP
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
INSTALACIÓN Y CONFIGURACIÓN

1. Configuración del Servidor
   <Directory /var/www/html>
   AllowOverride All
   </Directory>
2. Clonar y Configurar

# Clonar repositorio

git clone https://github.com/Carbyfah/sistema_aplicaciones.git

# crear .env

DEBUG_MODE = 0
DB_HOST=localhost
DB_SERVICE=3306
DB_SERVER=mysql_server
DB_NAME=apps
DB_USER=developer
DB_PASS=rootpassword

# crear .gitignore en raiz

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
includes/.env

# crear .htaccess (raíz)

RewriteEngine on
RewriteRule ^$ public/ [L]
RewriteRule (.\*) public/$1 [L]

# cerar .htaccess en public

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]

# comandos para crear la base de datos

CREATE DATABASE apps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'developer'@'%' IDENTIFIED BY 'rootpassword';
GRANT ALL PRIVILEGES ON apps.\* TO 'developer'@'%';
FLUSH PRIVILEGES;

# Dependencias PHP

composer install

# Dependencias Node.js

npm install

# Construcción de assets

npm run build
ESTRUCTURA DEL PROYECTO

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

Adstración
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

# Desarrollo con watch

npm run watch

# Producción

npm run build

# Verificar instalaciones

npm list --depth=0
Dependencias Principales
AdminLTE 3.2.0 - Panel de adstración

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

Sistema desarrollado para el sterio de Defensa - Gestión de Proyectos de Aplicaciones
