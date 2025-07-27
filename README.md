# prueba-wewelcom

## Opciones para probar la API

### Opción 1: Usar la web pública

1. Entra en: [https://prueba-wewelcom.onrender.com/index.html](https://prueba-wewelcom.onrender.com/index.html)
2. En la casilla **API Key** pon: `1234`
3. En la casilla **API Endpoint** pon:
   - `/api/restaurantes` para hacer peticiones GET o POST
   - `/api/restaurantes/{id}` para hacer peticiones GET, PUT o DELETE por ID

### Opción 2: Levantar el proyecto localmente con Docker

1. Asegúrate de tener Docker instalado y abierto.
2. Descarga el proyecto con:
   ```bash
   git clone https://github.com/AdrianSanchezOrtega/prueba-wewelcom.git
   ```
3. Entra en la carpeta del proyecto:
   ```bash
   cd ./prueba-wewelcom
   ```
4. Levanta los servicios necesarios:
   ```bash
   docker-compose up -d
   ```
5. Accede al contenedor de la aplicación:
   ```bash
   docker-compose exec app bash
   ```
6. Instala las dependencias de PHP:
   ```bash
   composer install
   ```
7. Ejecuta las migraciones para preparar la base de datos:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
8. ¡Listo! Ya puedes probar la API en tu entorno local.

#### Test automático

Hay un pequeño test funcional. Para ejecutarlo:

```bash
php bin/phpunit
```

Esto comprobará que la API responde correctamente.