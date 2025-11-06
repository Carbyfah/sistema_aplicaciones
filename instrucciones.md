# entrar a contenedor

docker exec -it mariadb mariadb -uroot -prootpassword

DROP DATABASE IF EXISTS apps;
CREATE DATABASE apps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE apps;

CREATE USER IF NOT EXISTS 'developer'@'%' IDENTIFIED BY 'rootpassword';
GRANT ALL PRIVILEGES ON _._ TO 'developer'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

SHOW DATABASES;
USE apps;
SHOW TABLES;
EXIT;

# INSTALACIÓN DE TODAS LAS DEPENDENCIAS FALTANTES

cd C:\docker\sistema_aplicaciones

# Admin LTE

npm install admin-lte@3.2.0

# Font Awesome

npm install @fortawesome/fontawesome-free@6.4.0

# DataTables

npm install datatables.net@1.13.6
npm install datatables.net-bs5@1.13.6
npm install datatables.net-responsive@2.5.0
npm install datatables.net-responsive-bs5@2.5.0

# Select2

npm install select2@4.1.0-rc.0

# Intro.js (para tutoriales)

npm install intro.js@7.2.0

# Chart.js (para gráficos del dashboard)

npm install chart.js@4.4.0

# Popper.js (requerido por Bootstrap)

npm install @popperjs/core@2.11.8

# Verificar todas las instalaciones

npm list --depth=0

# src/js/app.js debe ser asi:

// JQUERY (debe ser primero)
import $ from 'jquery';
window.jQuery = $;
window.$ = $;

// POPPER
import '@popperjs/core';

// BOOTSTRAP
import 'bootstrap';

// DATATABLES
import 'datatables.net';
import 'datatables.net-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';

// SELECT2
import 'select2';

// SWEETALERT2
import Swal from 'sweetalert2';
window.Swal = Swal;

// CHART.JS
import Chart from 'chart.js/auto';
window.Chart = Chart;

// INTRO.JS
import introJs from 'intro.js';
window.introJs = introJs;

// CSS - ORDEN IMPORTANTE
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';
import 'select2/dist/css/select2.min.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'intro.js/introjs.css';

// CSS PERSONALIZADO
import '../scss/app.scss';

console.log('Sistema cargado correctamente');

# package.json

{
"name": "demo-app",
"version": "1.0.0",
"description": "Aplicacion MVC demo",
"main": "index.js",
"scripts": {
"build": "webpack --mode production",
"dev": "webpack --mode development",
"watch": "webpack --watch --mode development",
"test": "echo \"Error: no test specified\" && exit 1"
},
"author": "",
"license": "ISC",
"devDependencies": {
"autoprefixer": "^10.4.21",
"css-loader": "^6.11.0",
"file-loader": "^6.2.0",
"mini-css-extract-plugin": "^2.9.4",
"postcss-loader": "^8.2.0",
"sass": "^1.93.0",
"sass-loader": "^13.3.3",
"style-loader": "^3.3.4",
"webpack": "^5.102.1",
"webpack-cli": "^4.10.0"
},
"dependencies": {
"@fortawesome/fontawesome-free": "^6.4.0",
"admin-lte": "^3.2.0",
"bootstrap": "^4.6.2",
"bootstrap-icons": "^1.13.1",
"chart.js": "^4.4.0",
"datatables.net": "^1.13.6",
"datatables.net-bs4": "^1.13.6",
"datatables.net-responsive": "^2.5.0",
"datatables.net-responsive-bs4": "^2.5.0",
"intro.js": "^7.2.0",
"jquery": "^3.7.1",
"popper.js": "^1.16.1",
"select2": "^4.1.0-rc.0",
"sweetalert2": "^11.26.3"
}
}

# cambiar tamanios de archivos

docker exec -it dockerApps bash

cd ~

ls -lh /etc/php/7.2/apache2/php.ini
tail -20 /etc/php/7.2/apache2/php.ini
grep -E "upload_max_filesize|post_max_size|max_execution_time" /etc/php/7.2/apache2/php.ini

echo "" >> /etc/php/7.2/apache2/php.ini
echo "; Custom upload limits" >> /etc/php/7.2/apache2/php.ini
echo "upload_max_filesize = 100M" >> /etc/php/7.2/apache2/php.ini
echo "post_max_size = 100M" >> /etc/php/7.2/apache2/php.ini
echo "max_execution_time = 300" >> /etc/php/7.2/apache2/php.ini
echo "memory_limit = 256M" >> /etc/php/7.2/apache2/php.ini

tail -10 /etc/php/7.2/apache2/php.ini

service apache2 restart
