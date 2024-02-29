> [!IMPORTANT]
>
>## Comandos para clonar plataforma de API_CARNET_LARAVEL_SQLSRV ## 
>### Para clonar el repositorio e instalarlo en produccion o pruebas se deben tener en cuenta los siguientes comandos: ###

> [!NOTE]
> - [Clonar el repositorio](#).
>   ```bash
>   https://github.com/MORJAN-CUN/API_CARNET_LARAVEL_SQLSRV.git
>- [Intalar dependencias del proyecto composer](#).
>   ```bash
>   composer install
>- [crear archivo .env a partir del archivo de ejemplo](#).
>   ```bash
>   cp .env.example .env
>- [Generar enlace simbolico de storage para poder manipular imagenes de usuario logueado](#).
>   ```bash
>   php artisan storage:link
>- [Generar llave de aplicacion para que no de error](#).
>   ```bash
>   php artisan key:generate
>- [Asignar credenciales de conexion a la DB, usuario y contraseña, en archivo .env ](#).
>   ```bash
>   DB_DATABASE=Database name  
>   DB_USERNAME=user database name  
>   DB_PASSWORD=password database name
>- [Ejecutar migraciones de la base de datos para que se ejecuten las tabla del proyecto ](#).
>   ```bash
>   php artisan migrate
>- [opcional ejecutar este comando si se cuenta con datos de prueba iniciales en la base de datos](#).
>   ```bash
>   php artisan migrate --seed

> [!TIP]
>## Propietarios de la plataforma API_CARNET_LARAVEL_SQLSRV ##
> 
> ...

> [!TIP]
> ## Documentación del proyecto API_CARNET_LARAVEL_SQLSRV##
>
> El proyecto será realizado y escrito con el framework laravel en su version 8, esta es su documentación: [Laravel documentation](https://laravel.com/docs/).

> [!TIP]
> ## Licencia ##
>
> La plataforma cuenta con la licencia de código abierto [MIT license](https://opensource.org/licenses/MIT).
