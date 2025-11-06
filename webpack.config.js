const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  mode: "development",
  entry: {
    "js/app": "./src/js/app.js",
    "js/inicio": "./src/js/inicio.js",
    "js/layout": "./src/js/layout.js",

    "js/helpers/crud": "./src/js/helpers/crud.js",

    // Gestion de Proyectos
    "js/api/aplicacion": "./src/js/api/aplicacion.js",
    "js/api/ordenes_aplicaciones": "./src/js/api/ordenes_aplicaciones.js",
    "js/api/tareas_aplicaciones": "./src/js/api/tareas_aplicaciones.js",
    "js/api/estados": "./src/js/api/estados.js",

    // Costos y Configuracion
    "js/api/complejidad_opciones": "./src/js/api/complejidad_opciones.js",
    "js/api/seguridad_opciones": "./src/js/api/seguridad_opciones.js",
    "js/api/aplicacion_costos": "./src/js/api/aplicacion_costos.js",

    // Base de Datos
    "js/api/tipos_tabla": "./src/js/api/tipos_tabla.js",
    "js/api/tipos_dato": "./src/js/api/tipos_dato.js",
    "js/api/tipos_clave": "./src/js/api/tipos_clave.js",
    "js/api/aplicacion_tablas": "./src/js/api/aplicacion_tablas.js",
    "js/api/aplicacion_campos": "./src/js/api/aplicacion_campos.js",

    // Personal y Usuarios
    "js/api/persona": "./src/js/api/persona.js",
    "js/api/usuarios": "./src/js/api/usuarios.js",
    "js/api/usuarios-permisos": "./src/js/api/usuarios-permisos.js",
    "js/api/personal_proyecto": "./src/js/api/personal_proyecto.js",

    // Documentos
    "js/api/documentos": "./src/js/api/documentos.js",
    "js/api/categorias-documentos": "./src/js/api/categorias-documentos.js",

    // Sistema
    "js/api/configuracion-sistema": "./src/js/api/configuracion-sistema.js",
    "js/api/logs_actividas": "./src/js/api/logs_actividas.js",
    "js/api/notificaciones": "./src/js/api/notificaciones.js",
    "js/api/sesiones-usuarios": "./src/js/api/sesiones-usuarios.js",
    "js/api/intentos-login": "./src/js/api/intentos-login.js",
    "js/api/modulos": "./src/js/api/modulos.js",

    // Graficos
    "js/api/estadisticas": "./src/js/api/estadisticas.js",
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "public/build"),
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "css/app.css",
    }),
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          "css-loader",
          "sass-loader",
        ],
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: "asset/resource",
        generator: {
          filename: "images/[name][ext]",
        },
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        type: "asset/resource",
        generator: {
          filename: "fonts/[name][ext]",
        },
      },
    ],
  },
  devtool: "source-map",
  optimization: {
    splitChunks: false,
  },
};
