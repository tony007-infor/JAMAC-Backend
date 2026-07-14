# Phoenix-Orders - Sistema de Pedidos (VILT Stack)

Este es un sistema completo de gestión de clientes, productos y pedidos desarrollado bajo el ecosistema **VILT** (Laravel 11, Vue 3, Inertia.js y Tailwind CSS) utilizando **PostgreSQL** como base de datos con restricciones estrictas de integridad y bloqueos de concurrencia. La moneda predeterminada es Bolivianos (`Bs.`).

---

## 📋 Requisitos del Sistema

Antes de iniciar el proyecto, asegúrate de tener instalado lo siguiente en tu sistema:

*   **PHP:** Versión `^8.3` (con la extensión `pdo_pgsql` habilitada).
*   **Composer:** Versión `^2.0` (gestor de dependencias de PHP).
*   **Node.js:** Versión `^20.0` o superior (junto con `npm`).
*   **PostgreSQL:** Versión `^15` o superior.

---

## 🚀 Guía de Instalación y Configuración

Sigue estos pasos detallados para instalar y poner a correr el proyecto en tu entorno local:

### 1. Instalar Dependencias del Backend
En la raíz del proyecto, ejecuta el siguiente comando para descargar e instalar todas las dependencias de PHP:
```bash
composer install
```

### 2. Instalar Dependencias del Frontend
Instala todos los paquetes de JavaScript necesarios para Vue 3 y Tailwind CSS:
```bash
npm install
```

### 3. Configurar el Archivo de Entorno (`.env`)
Crea una copia del archivo de configuración inicial `.env.example` y nómbrala `.env`:
```bash
cp .env.example .env
```
Abre el archivo `.env` recién creado en tu editor de texto y configura las variables de conexión a tu base de datos **PostgreSQL**:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario_postgres
DB_PASSWORD=tu_contraseña_postgres
```

### 4. Generar la Clave de Aplicación (Application Key)
Genera la clave de encriptación única de Laravel:
```bash
php artisan key:generate
```

### 5. Ejecutar las Migraciones de la Base de Datos
Crea las tablas en PostgreSQL, incluyendo las claves primarias/foráneas en formato UUID y las restricciones CHECK a nivel de base de datos (`price > 0`, `stock >= 0`, etc.):
```bash
php artisan migrate
```

---

## ⚙️ Cómo Ejecutar la Aplicación en Local

Una vez completada la instalación, abre dos terminales diferentes para correr los servidores de desarrollo de backend y frontend:

### Terminal 1: Iniciar el Servidor Laravel (Backend)
Inicia el servidor local de desarrollo de PHP:
```bash
php artisan serve
```
El backend estará disponible en: [http://localhost:8000](http://localhost:8000)

### Terminal 2: Iniciar el Compilador Vite (Frontend)
Inicia el compilador en tiempo real de Vite para actualizar tus componentes de Vue:
```bash
npm run dev
```

---

## 🧪 Ejecutar las Pruebas Automatizadas

El proyecto incluye un conjunto de pruebas unitarias y de integración para asegurar que la lógica de transacciones, bloqueos de concurrencia y cálculos de precios funcionen correctamente.

Para ejecutar los tests, corre el siguiente comando en tu terminal:
```bash
./vendor/bin/phpunit tests/Feature/OrderCreationTest.php
```

---

## ✨ Características Clave Implementadas

1.  **Integridad en Base de Datos (UUIDs):** Todas las tablas utilizan identificadores UUID en lugar de enteros autoincrementales, aumentando la seguridad y escalabilidad del sistema.
2.  **Validación y Seguridad del Backend:**
    *   Los precios y totales de venta se recalculan estrictamente en el backend consultando la base de datos, ignorando los valores que se envíen desde el cliente/navegador.
    *   Bloqueos de fila de base de datos (`lockForUpdate`) para evitar condiciones de carrera al descontar stock simultáneamente en compras concurrentes.
    *   Transacciones atómicas (`DB::transaction`) que hacen rollback completo si falla la validación de stock de algún producto.
3.  **Baja Lógica (Soft Deletes):** Clientes y productos implementan la eliminación lógica para evitar pérdida de registros históricos en pedidos facturados.
4.  **UI Interactiva (Vue 3 + Inertia):** Diseñado con Tailwind CSS y Vue 3 Composition API (`<script setup>`), incluyendo un carrito de compras dinámico y avisos de stock.
