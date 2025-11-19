# 📋 TO-DO LIST COMPLETO - Proyecto ITSTE

## Estado Actual: 15% Completado

---

## ✅ LO QUE YA ESTÁ HECHO (Sesiones Anteriores)

### Refactorización Base ✅
- [x] Corregida inconsistencia de permisos Blog/Tema
- [x] Eliminadas credenciales hardcodeadas
- [x] Creado sistema de configuración centralizado (`config/app_settings.php`)
- [x] Creados componentes Blade reutilizables
- [x] Creado trait `HasPermissionMiddleware`
- [x] Variables CSS centralizadas
- [x] JavaScript refactorizado (sin duplicación)
- [x] Rutas limpias y organizadas
- [x] Código comentado eliminado

### Preparación Filament ✅
- [x] `composer.json` actualizado con Filament 2.x
- [x] Dependencias instaladas (Filament, Activity Log, Excel, etc.)
- [x] Documentación completa creada:
  - FILAMENT_MIGRATION_ROADMAP.md
  - FILAMENT_IMPLEMENTATION_GUIDE.md
  - MIGRATION_SUMMARY.md
  - CHANGELOG_REFACTORING.md
- [x] Configuración inicial de Filament (`config/filament.php`)

---

## 🔴 CRÍTICO - HACER PRIMERO (En tu entorno local)

### 1. Setup Básico del Entorno (30 minutos)
**Prioridad:** 🔴🔴🔴 CRÍTICA

**Por qué es crítico:** Sin esto, nada funciona.

```bash
# 1. Clonar y configurar
git clone <repo-url>
cd itste
git checkout claude/review-project-context-01XVm899RzbMFnGzHFTMacdw

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar .env
cp .env.example .env
php artisan key:generate

# 4. Editar .env con tus datos
nano .env  # o tu editor preferido
```

**Configuración mínima del .env:**
```env
APP_NAME="ITSTE Admin"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=itste_filament
DB_USERNAME=root
DB_PASSWORD=tu_password_aqui

SUPERADMIN_NAME=SuperAdmin
SUPERADMIN_EMAIL=admin@itste.edu.mx
SUPERADMIN_PASSWORD=Admin2025!Secure
```

**Checklist:**
- [ ] Proyecto clonado
- [ ] Composer install exitoso
- [ ] .env configurado
- [ ] APP_KEY generado

---

### 2. Base de Datos (15 minutos)
**Prioridad:** 🔴🔴🔴 CRÍTICA

```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE itste_filament CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# Verificar
php artisan tinker
>>> \App\Models\User::count()  # Debe mostrar 1
>>> \App\Models\User::first()->email  # Debe mostrar el email del SuperAdmin
>>> exit
```

**Checklist:**
- [ ] Base de datos creada
- [ ] Migraciones ejecutadas sin errores
- [ ] Seeders ejecutados (SuperAdmin creado)
- [ ] Permisos creados (ver-usuario, crear-usuario, etc.)
- [ ] Rol administrador creado

---

### 3. Configurar Filament (10 minutos)
**Prioridad:** 🔴🔴🔴 CRÍTICA

```bash
# Publicar configuraciones
php artisan vendor:publish --tag=filament-config
php artisan vendor:publish --tag=filament-assets
php artisan vendor:publish --tag=activitylog-migrations
php artisan vendor:publish --tag=activitylog-config

# Migrar activity log
php artisan migrate

# Limpiar cache
php artisan config:clear
php artisan cache:clear
```

**Checklist:**
- [ ] Configuración de Filament publicada
- [ ] Assets de Filament generados
- [ ] Activity Log configurado
- [ ] Cache limpio

---

## 🟡 IMPORTANTE - HACER DESPUÉS (Implementación Core)

### 4. Crear Resources de Filament (2-3 horas)
**Prioridad:** 🟡🟡 ALTA

**Usar el código de:** `FILAMENT_IMPLEMENTATION_GUIDE.md`

#### 4.1 UsuarioResource (45 min)
```bash
# Crear directorios
mkdir -p app/Filament/Resources/UsuarioResource/Pages
```

**Archivos a crear:**
- [ ] `app/Filament/Resources/UsuarioResource.php` (copiar de GUIDE)
- [ ] `app/Filament/Resources/UsuarioResource/Pages/ListUsuarios.php` (copiar de GUIDE)
- [ ] `app/Filament/Resources/UsuarioResource/Pages/CreateUsuario.php` (copiar de GUIDE)
- [ ] `app/Filament/Resources/UsuarioResource/Pages/EditUsuario.php` (copiar de GUIDE)

**Probar:**
```bash
php artisan serve
# Ir a http://localhost:8000/admin/usuarios
```

#### 4.2 RoleResource (45 min)
```bash
mkdir -p app/Filament/Resources/RoleResource/Pages
```

**Archivos a crear:**
- [ ] `app/Filament/Resources/RoleResource.php` (copiar de GUIDE)
- [ ] `app/Filament/Resources/RoleResource/Pages/ListRoles.php`
- [ ] `app/Filament/Resources/RoleResource/Pages/CreateRole.php`
- [ ] `app/Filament/Resources/RoleResource/Pages/EditRole.php`

**Probar:**
```bash
# Ir a http://localhost:8000/admin/roles
```

#### 4.3 BlogResource (45 min)
```bash
mkdir -p app/Filament/Resources/BlogResource/Pages
```

**Crear siguiendo el mismo patrón que UsuarioResource pero para Blog.**

**Archivos a crear:**
- [ ] `app/Filament/Resources/BlogResource.php`
- [ ] `app/Filament/Resources/BlogResource/Pages/ListBlogs.php`
- [ ] `app/Filament/Resources/BlogResource/Pages/CreateBlog.php`
- [ ] `app/Filament/Resources/BlogResource/Pages/EditBlog.php`

**Características específicas para Blog:**
```php
// En BlogResource.php
Forms\Components\RichEditor::make('contenido')
    ->label('Contenido')
    ->required()
    ->columnSpan('full'),
```

---

### 5. Actualizar Modelos (30 min)
**Prioridad:** 🟡🟡 ALTA

#### 5.1 Actualizar User Model
**Archivo:** `app/Models/User.php`

**Agregar:**
```php
use Filament\Models\Contracts\FilamentUser;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser
{
    use LogsActivity;

    public function canAccessFilament(): bool
    {
        return $this->hasAnyPermission(['ver-usuario', 'ver-rol', 'ver-tema']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

**Checklist:**
- [ ] User implementa FilamentUser
- [ ] canAccessFilament() configurado
- [ ] LogsActivity agregado
- [ ] getActivitylogOptions() configurado

#### 5.2 Actualizar Blog Model
**Archivo:** `app/Models/Blog.php`

**Agregar:**
```php
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Blog extends Model
{
    use SoftDeletes, LogsActivity;

    protected $dates = ['deleted_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'contenido'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

**Migración para soft deletes:**
```bash
php artisan make:migration add_soft_deletes_to_blogs_table
```

Contenido:
```php
public function up()
{
    Schema::table('blogs', function (Blueprint $table) {
        $table->softDeletes();
    });
}
```

**Checklist:**
- [ ] Blog con SoftDeletes
- [ ] Blog con LogsActivity
- [ ] Migración de soft deletes creada y ejecutada

---

### 6. Crear Dashboard con Widgets (1-2 horas)
**Prioridad:** 🟡 MEDIA

#### 6.1 Widget de Estadísticas
```bash
php artisan make:filament-widget StatsOverviewWidget --stats-overview
```

**Archivo:** `app/Filament/Widgets/StatsOverviewWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Blog;
use Spatie\Permission\Models\Role;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverviewWidget extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Usuarios', User::count())
                ->description('Usuarios registrados en el sistema')
                ->descriptionIcon('heroicon-s-users')
                ->color('success'),

            Card::make('Total Roles', Role::count())
                ->description('Roles configurados')
                ->descriptionIcon('heroicon-s-shield-check')
                ->color('warning'),

            Card::make('Total Temas', Blog::count())
                ->description('Temas publicados')
                ->descriptionIcon('heroicon-s-book-open')
                ->color('primary'),

            Card::make('Usuarios Hoy', User::whereDate('created_at', today())->count())
                ->description('Nuevos usuarios hoy')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),
        ];
    }
}
```

**Registrar en config/filament.php:**
```php
'widgets' => [
    'register' => [
        \App\Filament\Widgets\StatsOverviewWidget::class,
    ],
],
```

**Checklist:**
- [ ] StatsOverviewWidget creado
- [ ] Cards configurados
- [ ] Registrado en config
- [ ] Visible en dashboard

#### 6.2 Widget de Gráfica (Opcional)
```bash
php artisan make:filament-widget UsuariosChartWidget --chart
```

**Checklist:**
- [ ] ChartWidget creado (opcional)

---

### 7. Configurar Activity Log Resource (1 hora)
**Prioridad:** 🟡 MEDIA

**Crear Resource para ver logs:**
```bash
mkdir -p app/Filament/Resources/ActivityLogResource/Pages
```

**Archivo:** `app/Filament/Resources/ActivityLogResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Registro de Actividad';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción'),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn ($state) => class_basename($state)),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->default('Sistema'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Tipo de Modelo')
                    ->options([
                        'App\Models\User' => 'Usuario',
                        'App\Models\Blog' => 'Tema',
                        'Spatie\Permission\Models\Role' => 'Rol',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
```

**Página:**
```php
<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;
}
```

**Checklist:**
- [ ] ActivityLogResource creado
- [ ] Tabla configurada con columnas
- [ ] Filtros agregados
- [ ] Página de listado creada
- [ ] Visible en menú de Sistema

---

## 🟢 OPCIONAL - HACER CUANDO TENGAS TIEMPO

### 8. Exportación Avanzada (30 min)
**Prioridad:** 🟢 BAJA

**Ya configurado en los Resources, solo verificar que funciona:**

```bash
# En cada Resource ListPage, debe haber:
ExportAction::make()->label('Exportar Todo')
```

**Checklist:**
- [ ] Exportación Excel funciona en Usuarios
- [ ] Exportación Excel funciona en Roles
- [ ] Exportación Excel funciona en Blogs

---

### 9. Tests Automatizados (3-4 horas)
**Prioridad:** 🟢 BAJA (pero importante para producción)

```bash
# Test de Resource
php artisan make:test Filament/UsuarioResourceTest

# Test de Modelo
php artisan make:test Unit/UserTest --unit
```

**Ejemplo básico:**
```php
<?php

namespace Tests\Feature\Filament;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Filament\Resources\UsuarioResource;

class UsuarioResourceTest extends TestCase
{
    public function test_can_list_usuarios()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('ver-usuario');

        $this->actingAs($user);

        $this->get(UsuarioResource::getUrl('index'))
            ->assertSuccessful();
    }
}
```

**Checklist:**
- [ ] Tests de Resources creados
- [ ] Tests de Modelos creados
- [ ] Tests de Permisos creados
- [ ] Cobertura >70%

---

### 10. Optimización y Performance (1-2 horas)
**Prioridad:** 🟢 BAJA

```bash
# Crear índices
php artisan make:migration add_indexes_for_performance
```

**Contenido:**
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->index('email');
        $table->index('created_at');
    });

    Schema::table('blogs', function (Blueprint $table) {
        $table->index('created_at');
        $table->index('deleted_at');
    });

    Schema::table('activity_log', function (Blueprint $table) {
        $table->index(['subject_type', 'subject_id']);
        $table->index('causer_id');
        $table->index('created_at');
    });
}
```

**Optimización de cache:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Checklist:**
- [ ] Índices de BD agregados
- [ ] Eager loading configurado
- [ ] Cache habilitado
- [ ] Queries optimizadas (sin N+1)

---

### 11. Seguridad Extra (1 hora)
**Prioridad:** 🟢 MEDIA

**Crear páginas de error personalizadas:**
```bash
mkdir -p resources/views/errors
```

**404.blade.php:**
```blade
@extends('filament::layouts.base')

@section('content')
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800">404</h1>
            <p class="text-xl text-gray-600 mt-4">Página no encontrada</p>
            <a href="{{ route('filament.pages.dashboard') }}" class="mt-6 btn btn-primary">
                Volver al Dashboard
            </a>
        </div>
    </div>
@endsection
```

**Agregar headers de seguridad en middleware:**
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);

    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');

    return $response;
}
```

**Checklist:**
- [ ] Páginas de error (404, 403, 500)
- [ ] Headers de seguridad
- [ ] Rate limiting configurado
- [ ] HTTPS forzado en producción

---

## 📊 RESUMEN PRIORIZADO

### SEMANA 1 - CRÍTICO (20-25 horas)
```
DÍA 1-2 (6h):
├─ ✅ Setup del entorno
├─ ✅ Base de datos
├─ ✅ Configurar Filament
└─ ✅ Actualizar modelos

DÍA 3-4 (8h):
├─ ✅ UsuarioResource completo
├─ ✅ RoleResource completo
└─ ✅ BlogResource completo

DÍA 5 (6h):
├─ ✅ Widgets del Dashboard
├─ ✅ ActivityLogResource
└─ ✅ Pruebas generales
```

### SEMANA 2 - PULIDO (15-20 horas)
```
DÍA 1-2 (8h):
├─ ✅ Tests automatizados
└─ ✅ Corrección de bugs

DÍA 3-4 (6h):
├─ ✅ Optimización
├─ ✅ Seguridad extra
└─ ✅ Documentación final

DÍA 5 (4h):
├─ ✅ Deploy a staging
├─ ✅ Testing final
└─ ✅ Deploy a producción
```

---

## 🎯 CHECKLIST MÍNIMO VIABLE

Para tener un sistema funcional básico:

```
[ ] 1. Entorno configurado
[ ] 2. Base de datos migrada
[ ] 3. Filament instalado
[ ] 4. UsuarioResource funcionando
[ ] 5. RoleResource funcionando
[ ] 6. BlogResource funcionando
[ ] 7. Modelos actualizados
[ ] 8. Dashboard con widgets básicos
[ ] 9. Login funcionando
[ ] 10. Permisos aplicados correctamente

TIEMPO TOTAL: 15-20 horas
```

---

## 🚀 PRÓXIMA ACCIÓN INMEDIATA

**EMPIEZA AQUÍ:**

```bash
# 1. En tu máquina local
git clone <tu-repo>
cd itste
git checkout claude/review-project-context-01XVm899RzbMFnGzHFTMacdw

# 2. Instalar
composer install

# 3. Configurar
cp .env.example .env
php artisan key:generate
nano .env  # Configurar BD

# 4. Base de datos
mysql -u root -p -e "CREATE DATABASE itste_filament"
php artisan migrate:fresh --seed

# 5. Filament
php artisan vendor:publish --tag=filament-config
php artisan vendor:publish --tag=activitylog-migrations
php artisan migrate

# 6. Probar
php artisan serve
# Ir a http://localhost:8000/admin
```

---

**¿Por dónde empezar?** → Sección "CRÍTICO - HACER PRIMERO" (puntos 1-3)

**Última actualización:** 2025-11-18
**Progreso actual:** 15%
**Tiempo estimado total:** 35-45 horas
