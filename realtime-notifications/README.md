# Realtime Notifications - Backend Node.js

## Descripción

Este proyecto Node.js se encarga de escuchar eventos en tiempo real para notificaciones de pedidos. Utiliza `Pusher` para las notificaciones en tiempo real y `MongoDB` como base de datos. El proyecto está estructurado de manera sencilla pero eficiente, con una carpeta `src` que contiene toda la lógica principal.

## Cómo levantar el proyecto

1. Clonar el repositorio.
2. Ejecutar `npm install` para instalar las dependencias.
3. Copiar `.env.example` a `.env` y llenar las variables de entorno.
4. Ejecutar `npm start` para iniciar el proyecto.

## Variables de entorno (.env)

### Configuración de MongoDB

- `MONGODB_URI`: URI completa para la conexión a MongoDB.
- `MONGODB_DATABASE`: Nombre de la base de datos.
- `MONGODB_COLLECTION`: Nombre de la colección donde se guardarán las notificaciones.
- `ME_CONFIG_MONGODB_SERVER`: Servidor de MongoDB.
- `ME_CONFIG_MONGODB_PORT`: Puerto para la conexión a MongoDB.
- `ME_CONFIG_MONGODB_ENABLE_ADMIN`: Habilitar el modo administrador.
- `ME_CONFIG_MONGODB_ADMINUSERNAME`: Nombre de usuario del administrador.
- `ME_CONFIG_MONGODB_ADMINPASSWORD`: Contraseña del administrador.

### Configuración de Pusher

- `PUSHER_APP_ID`: ID de la aplicación en Pusher.
- `PUSHER_APP_KEY`: Clave de la aplicación en Pusher.
- `PUSHER_APP_CLUSTER`: Cluster de la aplicación en Pusher.

**Nota**: Es obligatorio proporcionar las credenciales de Pusher para las notificaciones en tiempo real.

