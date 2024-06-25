**CSV Importer for WooCommerce**

**Descripción**

El **CSV Importer for WooCommerce** es un plugin para WordPress que permite importar archivos CSV a WooCommerce, facilitando la carga de productos y datos de mayoristas de manera rápida y eficiente.

**Características**

- **Importación de mayoristas**: Agrega nuevos mayoristas a la base de datos.
- **Carga de archivos CSV**: Permite cargar y procesar archivos CSV.
- **Gestión de productos**: Importa productos y datos relacionados desde archivos CSV.
- **Interfaz de administración**: Opciones de configuración y gestión desde el panel de administración de WordPress.

**Instalación**

1. **Descargar el plugin**: Descarga el archivo ZIP del plugin desde el repositorio de GitHub.
1. **Subir el plugin a WordPress**:
   1. Ve a Plugins > Añadir nuevo en el panel de administración de WordPress.
   1. Haz clic en Subir plugin y selecciona el archivo ZIP descargado.
   1. Instala y activa el plugin.
1. **Configuración**: Navega a Ajustes > CSV Importer para configurar el plugin.

**Uso**

**Agregar Mayorista**

1. Navega a Mayoristas > Añadir nuevo.
1. Ingresa el nombre del mayorista y haz clic en Guardar.

**Cargar Archivo CSV**

1. Navega a Importar CSV.
1. Selecciona el archivo CSV que deseas cargar.
1. Asocia el archivo con un mayorista y selecciona la fecha de importación.
1. Haz clic en Cargar para procesar el archivo.

**Archivos Principales**

- **csvimporter.php**: Archivo principal del plugin.
- **admin/**
  - config.php: Configuración administrativa.
  - dealers.php: Gestión de mayoristas.
  - upload.php: Carga y procesamiento de archivos CSV.
  - list.php: Listado de archivos importados.
  - product.php: Gestión de productos importados.
- **includes/**
  - functions.php: Funciones para agregar mayoristas y archivos.
  - options.php: Opciones del plugin.
  - datatables/client.php: Manejo de tablas de datos.
  - js/data\_list.js: JavaScript para manejo de listas de datos.

**Contribuir**

1. Haz un fork del repositorio.
1. Crea una nueva rama (git checkout -b feature/nueva-funcionalidad).
1. Realiza tus cambios y haz commit (git commit -am 'Agregar nueva funcionalidad').
1. Haz push a la rama (git push origin feature/nueva-funcionalidad).
1. Abre un Pull Request.

**Licencia**

Este proyecto está licenciado bajo la Licencia MIT - ver el archivo LICENSE para más detalles.

**Contacto**

Para cualquier duda o consulta, por favor, abre un issue en el repositorio o contacta al mantenedor del proyecto.

