# Phoenix Orders - Backend API (Laravel 11)

Este es el servidor API del sistema Phoenix Orders, desarrollado con Laravel 11. Se encarga de procesar la lógica de negocio, reglas de stock con bloqueos concurrentes y transacciones seguras en base de datos.

---

## 🛠️ Requisitos Previos

Antes de comenzar, asegúrate de tener instalado en tu computadora:
*   **PHP** (versión 8.2 o superior)
*   **Composer** (gestor de dependencias de PHP)
*   **PostgreSQL** (servidor de base de datos)

---

## 🚀 Pasos para hacerlo funcionar

Abre una terminal dentro de esta carpeta y sigue estos sencillos pasos:

### 1. Instalar dependencias
Descarga las librerías de PHP necesarias ejecutando:
```bash
composer install
```

### 2. Configurar variables de entorno
Crea una copia del archivo de configuración inicial `.env.example` y nómbralo `.env`:
```bash
cp .env.example .env
```

### 3. Configurar la Base de Datos
Abre el archivo `.env` recién creado en tu editor de código y edita los campos de conexión a tu base de datos PostgreSQL local:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=Phoenix-Orders   # Asegúrate de crear esta BD en pgAdmin
DB_USERNAME=postgres         # Tu usuario de PostgreSQL
DB_PASSWORD=tu_contraseña    # Tu contraseña de PostgreSQL
```

### 4. Generar la clave de la aplicación
Genera la clave de encriptación única de Laravel:
```bash
php artisan key:generate
```

### 5. Correr Migraciones y Datos de Prueba (Seeders)
Ejecuta las migraciones para crear las tablas en PostgreSQL e inyecta los productos y el usuario administrador inicial:
```bash
php artisan migrate:fresh --seed
```

### 6. Levantar el Servidor de la API
Arranca el servidor local de Laravel:
```bash
php artisan serve
```
*(Por defecto, la API correrá en: **`http://localhost:8000`**)*

---

## 🧪 Ejecutar Pruebas Automatizadas (Tests)
Si quieres verificar que todo el sistema de stock, transacciones y lógica de negocio funciona correctamente, ejecuta el siguiente comando:
```bash
./vendor/bin/phpunit
```
Todos los tests deben marcarse en color verde (Passed).
