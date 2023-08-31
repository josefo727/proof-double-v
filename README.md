# Proof Double V

Este repositorio contiene dos proyectos principales: un backend desarrollado en Laravel y un servicio receptor de notificaciones en tiempo real con Node.js.

## Backend en Laravel (`backend`)

El backend en Laravel se encarga de la lógica de negocio, autenticación y notificaciones en tiempo real. El proyecto sigue los principios SOLID y utiliza varios patrones de diseño para asegurar un código limpio, mantenible y extensible. Para más detalles, consulte el archivo README.md en el directorio `backend`.

## Servicio de Notificaciones en Tiempo Real con Node.js (`realtime-notifications`)

Este proyecto se encarga de escuchar eventos en tiempo real para notificaciones de pedidos. Utiliza `Pusher` para las notificaciones en tiempo real y `MongoDB` como base de datos. Para más detalles, consulte el archivo README.md en el directorio `realtime-notifications`.


## Pruebas con Postman

Para facilitar la prueba de las API, se proporciona una colección de Postman preconfigurada. La colección, denominada `Proof-Double-V.postman_collection.json`, se encuentra en la raíz del repositorio. Siga los pasos a continuación para utilizarla:

1. Importe la colección `Proof-Double-V.postman_collection.json` en Postman.
2. Ejecute la solicitud de "Login" para autenticarse. Al hacerlo, el token de autenticación se almacenará automáticamente y se utilizará para todas las llamadas subsiguientes en Postman.

De esta manera, podrá probar fácilmente todas las funcionalidades del proyecto directamente desde Postman, sin tener que preocuparse por la autenticación en cada llamada.

### Variables de Entorno

Ambos proyectos requieren la configuración de variables de entorno para funcionar correctamente. Consulte los archivos `.env.example` en cada directorio para más detalles.

### Levantar los Proyectos

Para levantar el backend en Laravel, siga las instrucciones en el archivo README.md del directorio `backend`.

Para levantar el servicio de notificaciones en tiempo real con Node.js, siga las instrucciones en el archivo README.md del directorio `realtime-notifications`.

