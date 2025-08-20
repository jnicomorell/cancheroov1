# Cancheroo

Cancheroo es una plataforma para reserva de canchas de fútbol y pádel.

## Estructura

- **backend/** API REST construida en Laravel.
- **mobile/** Aplicación móvil en React Native (Expo).

## Características iniciales

- Gestión de clubes, canchas y reservas.
- Búsqueda de canchas por deporte, ciudad y disponibilidad horaria.
- Creación de reservas con cálculo automático de precio.
- Autenticación de usuarios con endpoints de registro y login basados en Laravel Sanctum.

## Desarrollo

El proyecto está preparado para ejecutarse con [DDEV](https://ddev.readthedocs.io/), lo que permite levantar un entorno de desarrollo completo mediante contenedores.

### Backend

1. Copiar el entorno pensado para DDEV y arrancar los contenedores:

   ```bash
   cp backend/.env.ddev backend/.env
   ddev start
   ```

2. Instalar dependencias y ejecutar migraciones:

   ```bash
   ddev composer install -d backend
   ddev exec -d backend php artisan migrate
   ```

3. La API quedará disponible en `https://cancheroo.ddev.site`. Las pruebas y otros comandos de Laravel pueden ejecutarse con:

   ```bash
   ddev exec -d backend php artisan test
   ```

### Mobile

Las dependencias de la app móvil también pueden manejarse desde DDEV:

```bash
ddev npm install -d mobile
ddev npm start -d mobile
```
