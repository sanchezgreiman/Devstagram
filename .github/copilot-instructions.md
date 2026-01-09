# Devstagram - Instrucciones para Agentes IA

## Descripción del Proyecto
Devstagram es una plataforma social tipo Instagram construida con **Laravel 12**, **Livewire 3** y **Tailwind CSS**. Los usuarios pueden registrarse, crear publicaciones con imágenes, seguir a otros usuarios, dar likes y comentar.

## Convención de Nombres
Este proyecto usa **español** para nombres de modelos, tablas, campos y variables. Mantener esta convención en código nuevo:
- Modelos: `Comentario`, `Follower`, `Like`, `Post`, `User`
- Campos: `titulo`, `descripcion`, `imagen`, `comentario`
- Tablas: `comentarios`, `followers`, `likes`, `posts`, `users`

---

## Modelos y Esquema de Base de Datos

### User
**Tabla:** `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Clave primaria |
| `name` | string | Nombre completo |
| `email` | string | Email único |
| `username` | string | Nombre de usuario único (usado en URLs) |
| `password` | string | Contraseña hasheada |
| `imagen` | string (nullable) | Nombre del archivo de imagen de perfil |
| `email_verified_at` | timestamp | Verificación de email |
| `created_at/updated_at` | timestamps | |

**Relaciones:**
```php
posts()      → hasMany(Post::class)
likes()      → hasMany(Like::class)
followers()  → belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
followings() → belongsToMany(User::class, 'followers', 'follower_id', 'user_id')
```

**Métodos útiles:**
- `siguiendo(User $user)` - Verifica si el usuario actual sigue a otro usuario

### Post
**Tabla:** `posts`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Clave primaria |
| `titulo` | string | Título de la publicación |
| `descripcion` | text | Descripción/contenido |
| `imagen` | string | Nombre del archivo de imagen |
| `user_id` | foreignId | Autor (cascade delete) |
| `created_at/updated_at` | timestamps | |

**Relaciones:**
```php
user()        → belongsTo(User::class)->select(['name','username'])
comentarios() → hasMany(Comentario::class)
likes()       → hasMany(Like::class)
```

**Métodos útiles:**
- `checkLike(User $user)` - Verifica si un usuario dio like al post

### Comentario
**Tabla:** `comentarios`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Clave primaria |
| `user_id` | foreignId | Autor del comentario |
| `post_id` | foreignId | Post comentado |
| `comentario` | string | Texto del comentario |
| `created_at/updated_at` | timestamps | |

**Relaciones:**
```php
user() → belongsTo(User::class)
```

### Like
**Tabla:** `likes`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Clave primaria |
| `user_id` | foreignId | Usuario que dio like |
| `post_id` | foreignId | Post con like |
| `created_at/updated_at` | timestamps | |

### Follower
**Tabla:** `followers` (tabla pivote)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Clave primaria |
| `user_id` | foreignId | Usuario siendo seguido |
| `follower_id` | foreignId | Usuario que sigue |
| `created_at/updated_at` | timestamps | |

---

## Sistema de Almacenamiento de Imágenes

### Flujo de Subida de Imágenes de Posts
1. **Frontend**: Dropzone.js captura la imagen en `/resources/js/app.js`
2. **Subida AJAX**: POST a `/imagenes` → `ImagenController@store`
3. **Procesamiento**:
   ```php
   $manager = new ImageManager(new Driver());  // GD Driver
   $imagenServidor = $manager->read($imagen);
   $imagenServidor->cover(1000, 1000);         // Redimensiona a cuadrado
   $imagenServidor->save(public_path('uploads/' . $nombreImagen));
   ```
4. **Respuesta**: JSON con nombre de imagen → se guarda en input hidden
5. **Guardado**: Al crear el post, solo se guarda el nombre del archivo

### Flujo de Imágenes de Perfil
Procesado en `PerfilController@store`:
```php
$imagenServidor->cover(1000, 1000);
$imagenServidor->save(public_path('perfiles') . '/' . $nombreImagen);
```

### Ubicaciones de Almacenamiento
| Tipo | Directorio | Acceso |
|------|------------|--------|
| Posts | `/public/uploads/` | `asset('uploads/' . $post->imagen)` |
| Perfiles | `/public/perfiles/` | `asset('perfiles/' . $user->imagen)` |

### Eliminación de Imágenes
Al eliminar un post en `PostController@destroy`:
```php
$imagen_path = public_path('uploads/' . $post->imagen);
if(File::exists($imagen_path)) {
    unlink($imagen_path);
}
```

---

## Patrones Clave

### Middleware en Constructores
```php
public function __construct() {
    $this->middleware('auth')->except(['show', 'index']);
}
```

### Route Model Binding con Username
```php
Route::get('/{user:username}/posts/{post}', [PostController::class, 'show']);
```

### Controladores Invocables
```php
// HomeController.php - una sola acción
public function __invoke() { ... }
// Ruta: Route::get('/', HomeController::class);
```

### Autorización con Policies
Ver [app/Policies/PostPolicy.php](app/Policies/PostPolicy.php) para autorización de `delete`.

---

## Componentes Livewire
Ubicados en `app/Livewire/` con vistas en `resources/views/livewire/`.
- `LikePost` - Maneja likes en tiempo real sin recargar página

## Componentes Blade
- **Con clase**: `app/View/Components/` (ej: `ListarPost`)
- **Anónimos**: `resources/views/components/`
- **Uso**: `<x-listar-post :posts="$posts" />`

---

## Comandos de Desarrollo

```bash
# Entorno completo (servidor + queue + logs + vite)
composer dev

# Configuración inicial
composer setup

# Ejecutar tests
composer test

# Entorno Docker/Sail
./vendor/bin/sail up
```

## Frontend
- **Tailwind CSS** para estilos
- **Vite** para bundling
- **Dropzone.js** para subida drag-and-drop
- Vistas usan `@extends('layouts.app')` con `@section('contenido')`

## Estructura de Archivos
| Ruta | Propósito |
|------|-----------|
| `app/Http/Controllers/` | Controladores por dominio |
| `app/Livewire/` | Componentes interactivos |
| `app/Policies/` | Lógica de autorización |
| `resources/views/layouts/app.blade.php` | Layout principal |
| `public/uploads/` | Imágenes de posts |
| `public/perfiles/` | Imágenes de perfil |
