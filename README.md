# 🎬 Movie API

API REST para gestionar un catálogo de **películas y géneros**, construida con **Laravel 13** y **PHP 8.4**. Incluye autenticación JWT, control de acceso por roles y funciones de **IA (Anthropic Claude)** para generar sinopsis y recomendar películas.

- Autenticación con **JWT** (`php-open-source-saver/jwt-auth`)
- Roles: `admin`, `editor`, `viewer`
- CRUD de películas y géneros con borrado lógico (soft delete) y restauración
- Filtros, ordenamiento y paginación de películas
- Subida de póster por película
- Sinopsis y recomendaciones generadas con IA
- Respuestas uniformes con **Eloquent API Resources** y API versionada (`/api/v1`)
- Pruebas con **Pest** (cobertura ~96%) y análisis con **SonarQube**

## 📘 Contenido

- Resumen ejecutivo
- Arquitectura y principios SOLID
- Requisitos
- Instalación
- Variables de entorno
- Endpoints
- Pruebas y calidad
- Buenas prácticas aplicadas
- Recursos recomendados

## 📝 Resumen ejecutivo

Movie API expone un catálogo de películas relacionadas con géneros (relación muchos a muchos). Cualquier visitante consulta películas y géneros. Los usuarios `editor` y `admin` crean y editan contenido. Solo `admin` elimina y restaura. Usuarios autenticados piden recomendaciones según sus géneros favoritos, y `editor`/`admin` generan la sinopsis de una película con IA.

## 🏗️ Arquitectura y principios SOLID

```text
app/
├── Contracts/        # Interfaces de repositorios
├── Http/
│   ├── Controllers/Api/V1/   # Controladores delgados
│   ├── Middleware/CheckRole  # Autorización por rol
│   ├── Requests/V1/          # Validación (Form Requests)
│   └── Resources/V1/         # Transformación de respuestas
├── Models/           # Movie, Genre, User
├── Observers/        # Lógica de ciclo de vida (p. ej. slug)
├── Policies/         # MoviePolicy
├── Providers/        # RepositoryServiceProvider (bindings)
├── Repositories/     # Implementaciones Eloquent
├── Services/AI/      # MovieAIService (cliente Anthropic)
└── Traits/ApiResponse
```

| Principio                 | Cómo se aplica                                                                                                                           |
| ------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| **S**ingle Responsibility | Controladores solo orquestan. Validación en Form Requests, formato en Resources, acceso a datos en Repositories, IA en `MovieAIService`. |
| **O**pen/Closed           | `BaseRepository` se extiende (`MovieRepository`, `GenreRepository`) sin modificarlo.                                                     |
| **L**iskov Substitution   | Cualquier implementación de `BaseRepositoryInterface` reemplaza a otra sin romper los controladores.                                     |
| **I**nterface Segregation | Interfaces específicas por dominio (`MovieRepositoryInterface`, `GenreRepositoryInterface`).                                             |
| **D**ependency Inversion  | Controladores dependen de interfaces. `RepositoryServiceProvider` enlaza interfaz e implementación en el contenedor.                     |

## ⚙️ Requisitos

- PHP **8.4** con extensiones habituales de Laravel (`mbstring`, `xml`, `pdo_sqlite` o `pdo_pgsql`)
- Composer 2
- Node.js y npm
- Docker (opcional, para PostgreSQL)
- Clave de API de [Anthropic](https://console.anthropic.com/) (para las funciones de IA)

## ▶️ Instalación

```bash
git clone https://github.com/EdwinAlexanderBernardinoMoran/movie-api.git
cd movie-api

composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Configura la base de datos en `.env` (ver siguiente sección) y ejecuta:

```bash
php artisan migrate --seed
php artisan storage:link
composer run dev
```

Atajo equivalente: `composer run setup` instala dependencias, crea `.env`, genera la clave, migra y compila assets.

La API queda disponible en `http://localhost:8000/api/v1`. Prueba con:

```bash
curl http://localhost:8000/api/v1/health
```

### Base de datos

- **SQLite** (por defecto en `.env.example`): no requiere configuración extra.
- **PostgreSQL** con Docker: define `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`, y levanta el servicio:

```bash
docker compose up -d
```

### Usuarios de prueba (seeders)

| Rol    | Email                   | Contraseña    |
| ------ | ----------------------- | ------------- |
| admin  | `admin@movie-api.test`  | `password123` |
| editor | `editor@movie-api.test` | `password123` |
| viewer | `viewer@movie-api.test` | `password123` |

> Cambia estas credenciales en cualquier entorno que no sea local.

## 🔐 Variables de entorno

| Variable            | Descripción                                          |
| ------------------- | ---------------------------------------------------- |
| `JWT_SECRET`        | Secreto de firma JWT (`php artisan jwt:secret`)      |
| `ANTHROPIC_API_KEY` | Clave de API de Anthropic                            |
| `ANTHROPIC_MODEL`   | Modelo de Claude a usar (p. ej. `claude-sonnet-5-5`) |
| `FRONTEND_URL`      | Origen del frontend permitido (CORS)                 |
| `FILESYSTEM_DISK`   | Disco para pósters (`public` por defecto)            |

## 🛣️ Endpoints

Prefijo: `/api/v1`. Rutas protegidas requieren `Authorization: Bearer <token>`.

| Método                               | Ruta                                                                                                           | Acceso        |
| ------------------------------------ | -------------------------------------------------------------------------------------------------------------- | ------------- |
| GET                                  | `/health`                                                                                                      | Público       |
| POST                                 | `/auth/register`, `/auth/login`                                                                                | Público       |
| POST                                 | `/auth/logout`, `/auth/refresh` · GET `/auth/me`                                                               | Autenticado   |
| GET                                  | `/genres`, `/genres/{id}`, `/genres/slug/{slug}`                                                               | Público       |
| POST / PUT / PATCH                   | `/genres`, `/genres/{id}`                                                                                      | admin, editor |
| DELETE · POST `/genres/restore/{id}` | `/genres/{id}`                                                                                                 | admin         |
| GET                                  | `/movies` (filtros: `search`, `year`, `min_rating`, `genre_id`, `sort_by`, `order`, `perPage`), `/movies/{id}` | Público       |
| POST / PUT / PATCH                   | `/movies`, `/movies/{id}`                                                                                      | admin, editor |
| POST                                 | `/movies/{id}/poster`                                                                                          | admin, editor |
| POST                                 | `/movies/{id}/generate-synopsis`                                                                               | admin, editor |
| DELETE                               | `/movies/{id}`                                                                                                 | admin         |
| POST                                 | `/ai/recommendations` (`genres`: 1 a 5)                                                                        | Autenticado   |

Lista completa: `php artisan route:list --path=api`. Para generar una colección de Postman: `php artisan api:postman`.

## 🧪 Pruebas y calidad

```bash
php artisan test --compact            # suite completa
XDEBUG_MODE=coverage php artisan test --coverage   # cobertura
vendor/bin/pint                       # formato de código
```

- Las pruebas de IA no llaman a Anthropic: usan un transporte HTTP falso.
- `sonar-project.properties` define el análisis de SonarQube. Genera `coverage.xml` y `junit.xml` antes de ejecutar el scanner. El quality gate exige ≥ 80% de cobertura en código nuevo.

## 🧩 Buenas prácticas aplicadas

- Controladores delgados y una responsabilidad por clase
- Patrón Repository con inyección de dependencias por interfaz
- Validación con Form Requests y mensajes en español
- API versionada y respuestas con API Resources
- Autorización con middleware de roles y Policies
- Manejo de errores centralizado con respuestas JSON consistentes
- Errores del proveedor de IA devueltos como `502 Bad Gateway`
- Formato automático con Laravel Pint

## 📚 Recursos recomendados

- [Laravel](https://laravel.com/docs)
- [Pest](https://pestphp.com/)
- [JWT Auth (php-open-source-saver)](https://github.com/PHP-Open-Source-Saver/jwt-auth)
- [Anthropic API](https://docs.anthropic.com/)
