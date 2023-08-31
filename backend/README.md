# Backend Laravel Project README

## Overview

Este proyecto de backend Laravel está diseñado para abordar la lógica de negocio, la autenticación y las notificaciones en tiempo real en un sistema de gestión de pedidos. El código sigue los principios SOLID y emplea diversos patrones de diseño para garantizar que sea limpio, mantenible y extensible. En las secciones siguientes, se destacan las características y componentes más relevantes del proyecto, aunque no es una lista exhaustiva debido a limitaciones de espacio.

## Features

### Events

- `OrderCreated.php`: Notifica la creación de una nueva orden. (Patrón Observer)
- `OrderStatusChanged.php`: Notifica cualquier cambio en el estado de una orden. (Patrón Observer)

### Exceptions

- `Handler.php`: Manejo global de excepciones para respuestas JSON en caso de errores. (Principio de Sustitución de Liskov)

### Http

#### Controllers

- `LoginController.php`: Maneja la lógica de autenticación y generación de tokens. (Principio de Responsabilidad Única)

#### Requests

- `BaseFormRequest.php`: Modifica la respuesta de errores de validación para incluirla en la respuesta JSON. (Principio de Abierto/Cerrado)
- Todos los demás `*Request.php`: Validan el cuerpo de las peticiones API. (Principio de Responsabilidad Única)

#### Resources

- `*Collection.php` y `*Resource.php`: Formatean las respuestas de los recursos. (Principio de Responsabilidad Única)
- `PaginationResource.php`: Controla la paginación de las llamadas API para listados de registros. (Principio de Responsabilidad Única)

### Observers

- `OrderObserver.php`: Dispara los eventos `OrderCreated` y `OrderStatusChanged`, y libera el inventario al cancelar una orden. (Patrón Observer)

### Providers

- `ResponseMacroServiceProvider.php`: Encapsula la lógica de respuestas de éxito y error para las llamadas API y estandariza el formato de respuesta. (Principio de Inversión de Dependencia)

### Rules

- `QuantityAvailable.php`: Regla personalizada para evitar la creación de pedidos con demanda mayor al stock disponible. (Principio de Sustitución de Liskov)

### Services

- `OrderStatus.php`: Asigna y controla los estados de una orden y el flujo de las transiciones. (Principio de Responsabilidad Única)

## Tests

El proyecto cuenta con 41 pruebas unitarias que respaldan todas las funcionalidades del sistema.

![Batería de Tests](/public/unit-test.png)

## Levantar el Proyecto con Laravel Sail

### Requisitos Previos

- Docker
- Credenciales de Pusher para notificaciones en tiempo real

### Pasos para la Instalación

1. **Clonar el Repositorio**: 
    ```bash
    git clone git@github.com:josefo727/proof-double-v.git
    ```

2. **Ir al Directorio del Proyecto**:
    ```bash
    cd proof-double-v/backend
    ```

3. **Copiar el Archivo `.env.example` a `.env`**:
    ```bash
    cp .env.example .env
    ```

4. **Configurar las Credenciales de Pusher en `.env`**:
    ```env
    PUSHER_APP_ID=<TU_APP_ID>
    PUSHER_APP_KEY=<TU_APP_KEY>
    PUSHER_APP_SECRET=<TU_APP_SECRET>
    PUSHER_APP_CLUSTER=<TU_APP_CLUSTER>
    ```

5. **Levantar los Contenedores de Docker con Laravel Sail**:
    ```bash
    ./vendor/bin/sail up
    ```

    O si prefieres usar Docker directamente:
    ```bash
    docker-compose up -d
    ```

6. **Ejecutar las Migraciones y Seeders**:
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

### Notas Importantes

- Las credenciales de Pusher son obligatorias para el funcionamiento de las notificaciones en tiempo real en este proyecto. Asegúrate de configurarlas correctamente en el archivo `.env`.

