# 📘 Guía Completa de Implementación - Migración a Filament

## ⚠️ IMPORTANTE: Sigue esta guía paso a paso

Esta guía contiene TODOS los archivos necesarios listos para copiar y pegar.
El `composer.json` ya está actualizado en el proyecto.

---

## 🚀 Paso 1: Instalación Inicial (15 minutos)

### 1.1 Instalar Dependencias

```bash
# Instalar dependencias PHP
composer install

# Publicar configuración de Filament
php artisan vendor:publish --tag=filament-config

# Publicar assets de Filament
php artisan filament:assets

# Publicar configuración de Activity Log
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
```

### 1.2 Configurar Base de Datos

Edita tu `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=itste_filament
DB_USERNAME=root
DB_PASSWORD=tu_password

# SuperAdmin Credentials (cambiar en producción)
SUPERADMIN_NAME=SuperAdmin
SUPERADMIN_EMAIL=admin@itste.edu.mx
SUPERADMIN_PASSWORD=Admin@2025!Secure
```

### 1.3 Ejecutar Migraciones

```bash
# Crear base de datos (si no existe)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS itste_filament CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones con seeders
php artisan migrate:fresh --seed

# Verificar que todo esté correcto
php artisan tinker
>>> \App\Models\User::count()
>>> exit
```

---

## 📁 Paso 2: Crear Estructura de Filament

### 2.1 Configurar Filament

Edita `config/filament.php`:

```php
<?php

return [
    'path' => env('FILAMENT_PATH', 'admin'),
    'domain' => env('FILAMENT_DOMAIN', null),

    'brand' => 'ITSTE Admin',

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Http\Livewire\Auth\Login::class,
        ],
    ],

    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [
            \Filament\Pages\Dashboard::class,
        ],
    ],

    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
    ],

    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [
            \App\Filament\Widgets\StatsOverviewWidget::class,
        ],
    ],

    'livewire' => [
        'namespace' => 'App\\Filament',
        'path' => app_path('Filament'),
    ],

    'dark_mode' => true,

    'database_notifications' => [
        'enabled' => true,
        'polling_interval' => '30s',
    ],

    'broadcasting' => [
        'echo' => [
            'broadcaster' => 'pusher',
            'key' => env('VITE_PUSHER_APP_KEY'),
            'cluster' => env('VITE_PUSHER_APP_CLUSTER', 'mt1'),
            'wsHost' => env('VITE_PUSHER_HOST'),
            'wsPort' => env('VITE_PUSHER_PORT', 443),
            'wssPort' => env('VITE_PUSHER_PORT', 443),
            'authEndpoint' => '/broadcasting/auth',
            'disableStats' => true,
            'encrypted' => true,
            'forceTLS' => true,
        ],
    ],

    'layout' => [
        'footer' => [
            'should_show_logo' => true,
        ],
        'max_content_width' => null,
        'notifications' => [
            'vertical_alignment' => 'top',
            'alignment' => 'right',
        ],
        'sidebar' => [
            'is_collapsible_on_desktop' => true,
            'groups' => [
                'are_collapsible' => true,
            ],
            'width' => null,
            'collapsed_width' => null,
        ],
    ],

    'favicon' => null,

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),
];
```

### 2.2 Actualizar Modelo User

Edita `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Configuración para Filament
     */
    public function canAccessFilament(): bool
    {
        // Permitir acceso si tiene algún permiso del sistema
        return $this->hasAnyPermission([
            'ver-usuario',
            'ver-rol',
            'ver-tema'
        ]);
    }

    /**
     * Configuración de Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

### 2.3 Actualizar Modelo Blog

Edita `app/Models/Blog.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Blog extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['titulo', 'contenido'];

    protected $dates = ['deleted_at'];

    /**
     * Configuración de Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'contenido'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

---

## 📝 Paso 3: Crear Resources de Filament

### 3.1 Resource: Usuarios

Crea el archivo `app/Filament/Resources/UsuarioResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class UsuarioResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $pluralLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUsuario)
                            ->minLength(8)
                            ->same('password_confirmation')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUsuario)
                            ->minLength(8)
                            ->dehydrated(false),

                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->colors([
                        'primary',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name')
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Creado desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Creado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()
                    ->label('Exportar Seleccionados'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit' => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('ver-usuario');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('crear-usuario');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('editar-usuario');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('borrar-usuario');
    }
}
```

Crea `app/Filament/Resources/UsuarioResource/Pages/ListUsuarios.php`:

```php
<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListUsuarios extends ListRecords
{
    protected static string $resource = UsuarioResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->label('Exportar Todo'),
        ];
    }
}
```

Crea `app/Filament/Resources/UsuarioResource/Pages/CreateUsuario.php`:

```php
<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUsuario extends CreateRecord
{
    protected static string $resource = UsuarioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Asignar roles después de crear
        $this->record->syncRoles($this->data['roles']);
    }
}
```

Crea `app/Filament/Resources/UsuarioResource/Pages/EditUsuario.php`:

```php
<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsuario extends EditRecord
{
    protected static string $resource = UsuarioResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Actualizar roles
        $this->record->syncRoles($this->data['roles']);
    }
}
```

### 3.2 Resource: Roles

Crea `app/Filament/Resources/RoleResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Spatie\Permission\Models\Role;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $pluralLabel = 'Roles';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permisos')
                            ->relationship('permissions', 'name')
                            ->columns(3)
                            ->required()
                            ->bulkToggleable()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Role $record) {
                        if ($record->name === 'administrador') {
                            \Filament\Notifications\Notification::make()
                                ->title('No se puede eliminar')
                                ->body('El rol "administrador" no puede ser eliminado.')
                                ->danger()
                                ->send();
                            return;
                        }

                        if ($record->users()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('No se puede eliminar')
                                ->body('Este rol tiene usuarios asignados.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()
                    ->label('Exportar Seleccionados'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('ver-rol');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('crear-rol');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('editar-rol');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('borrar-rol');
    }
}
```

Continúa en el siguiente archivo...

---

## ⚡ Acceso Rápido

Una vez instalado todo, accede a:

```
http://localhost/admin
```

**Credenciales por defecto:**
- Email: admin@example.com (o el configurado en .env)
- Password: ChangeThisPassword123! (o el configurado en .env)

---

**Continúa con el archivo FILAMENT_RESOURCES_COMPLETE.md para el resto de los Resources...**
