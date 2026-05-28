Matched Foods
"Sincroniza tu hambre"

Matched Foods es una aplicación web diseñada para resolver el clásico dilema de grupos: ¿Dónde vamos a comer?. A través de un sistema de salas y votación tipo "match", los usuarios pueden sincronizar sus preferencias gastronómicas para encontrar el restaurante ideal sin discusiones.

🚀 Características Principales
Creación de Salas: Generación de códigos únicos para agrupar amigos.

Unión mediante Código: Acceso rápido a sesiones activas.

Interfaz Dinámica: Experiencia fluida con navegación sin recargas.

Votación: Mecánica simple para decidir dónde comer.

Modo Oscuro/Moderno: Diseño enfocado en UI/UX con Tailwind CSS.

🛠 Tecnologías Utilizadas
Backend: Laravel (PHP 8.4+)

Frontend: JavaScript (Fetch API), Tailwind CSS

Base de Datos: MySQL

Despliegue: Railway.app

Servidor: Apache/Nginx (vía Nixpacks)

📦 Instalación Local
Para ejecutar este proyecto en tu máquina local, sigue estos pasos:

Clonar el repositorio:

Bash
git clone https://github.com/tu-usuario/nombre-del-repo.git
cd nombre-del-repo
Instalar dependencias:

Bash
composer install
npm install
Configurar el entorno:

Bash
cp .env.example .env
php artisan key:generate
Configurar Base de Datos:
Modifica tu archivo .env con las credenciales de tu base de datos local y ejecuta:

Bash
php artisan migrate
Compilar y Ejecutar:

Bash
npm run build
php artisan serve