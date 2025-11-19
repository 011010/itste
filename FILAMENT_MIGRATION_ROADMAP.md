# 🚀 Roadmap de Migración a Filament V3

## Proyecto: ITSTE - Sistema de Gestión de Roles y Contenidos
**Inicio:** 2025-11-18
**Versión Objetivo:** Laravel 8.x + Filament 3.x
**Estado:** En Progreso

---

## 📋 Resumen Ejecutivo

### Objetivo
Migrar el sistema actual basado en Stisla + Blade a Filament V3 para obtener:
- ✅ Desarrollo 3x más rápido
- ✅ UI moderna y responsive
- ✅ Features avanzadas incluidas
- ✅ Sistema production-ready

### Alcance
- **Módulos a migrar:** Usuarios, Roles, Temas (Blogs)
- **Sistema de permisos:** Mantener Spatie Permission
- **Base de datos:** Sin cambios (100% compatible)
- **Backend:** Mantener modelos y lógica actual

### Métricas de Éxito
- [ ] Todas las funcionalidades actuales funcionando
- [ ] Sistema de permisos operativo
- [ ] Dashboard con estadísticas
- [ ] Exportación PDF/Excel
- [ ] Tests con >70% cobertura
- [ ] 0 vulnerabilidades críticas
- [ ] Auditoría completa implementada

---

## 🗺️ ROADMAP - Fases de Implementación

### **FASE 1: Preparación y Configuración Base** ⏱️ 2-3 días

#### 1.1 Actualización de Dependencias (Crítico)
**Prioridad:** 🔴 Alta - Seguridad
**Duración:** 4 horas

- [ ] Actualizar composer.json con nuevas dependencias
- [ ] Instalar Filament V3
- [ ] Instalar plugins esenciales:
  - `filament/spatie-laravel-permissions-plugin` (permisos)
  - `pxlrbt/filament-excel` (exportación)
  - `bezhansalleh/filament-shield` (escudo de permisos)
  - `spatie/laravel-activitylog` (auditoría)
- [ ] Resolver conflictos de dependencias
- [ ] Ejecutar `composer update` seguro
- [ ] Verificar compatibilidad

**Comandos:**
```bash
composer require filament/filament:"^3.0" -W
composer require bezhansalleh/filament-shield
composer require pxlrbt/filament-excel
composer require spatie/laravel-activitylog
composer update --with-all-dependencies
```

#### 1.2 Configuración del Panel Filament
**Prioridad:** 🔴 Alta
**Duración:** 2 horas

- [ ] Instalar panel de administración
- [ ] Configurar rutas personalizadas
- [ ] Configurar branding (logo, colores)
- [ ] Configurar idioma español
- [ ] Configurar navegación
- [ ] Configurar autenticación

**Archivos a crear/modificar:**
- `app/Providers/Filament/AdminPanelProvider.php`
- `config/filament.php`
- Traducciones en `lang/es/`

#### 1.3 Integración con Sistema de Permisos
**Prioridad:** 🔴 Alta
**Duración:** 3 horas

- [ ] Configurar Filament Shield
- [ ] Generar permisos automáticos para Resources
- [ ] Migrar permisos existentes
- [ ] Configurar políticas (Policies)
- [ ] Testear control de acceso

**Comandos:**
```bash
php artisan shield:install
php artisan shield:generate --all
```

---

### **FASE 2: Migración de Módulos Core** ⏱️ 3-4 días

#### 2.1 Resource: Usuarios
**Prioridad:** 🔴 Alta
**Duración:** 6 horas

**Features a implementar:**
- [ ] CRUD completo con validaciones
- [ ] Relación con Roles (Select múltiple)
- [ ] Hash automático de password
- [ ] Confirmación de password
- [ ] Filtros: por rol, email, fecha
- [ ] Búsqueda: nombre, email
- [ ] Acciones en masa: asignar rol, eliminar
- [ ] Exportación a Excel/PDF
- [ ] Vista previa (ViewAction)
- [ ] Auditoría de cambios

**Archivo:** `app/Filament/Resources/UsuarioResource.php`

**Validaciones:**
```php
- name: required|string|max:255
- email: required|email|unique:users,email
- password: required|min:8|confirmed (solo creación)
- roles: required|array|exists:roles
```

#### 2.2 Resource: Roles
**Prioridad:** 🔴 Alta
**Duración:** 5 horas

**Features a implementar:**
- [ ] CRUD completo
- [ ] Relación con Permisos (CheckboxList)
- [ ] Protección de rol "administrador"
- [ ] Contador de usuarios por rol
- [ ] Filtros: por fecha creación
- [ ] Búsqueda: nombre
- [ ] Exportación a Excel/PDF
- [ ] Vista de permisos asignados
- [ ] Prevenir eliminación si tiene usuarios

**Archivo:** `app/Filament/Resources/RoleResource.php`

**Validaciones:**
```php
- name: required|string|unique:roles,name
- permissions: required|array|exists:permissions
```

#### 2.3 Resource: Temas (Blogs)
**Prioridad:** 🟡 Media
**Duración:** 4 horas

**Features a implementar:**
- [ ] CRUD completo
- [ ] Editor rich text (TinyMCE o Markdown)
- [ ] Fechas de creación/actualización
- [ ] Filtros: por fecha
- [ ] Búsqueda: título, contenido
- [ ] Vista previa de contenido
- [ ] Exportación a PDF
- [ ] Soft deletes con restauración

**Archivo:** `app/Filament/Resources/BlogResource.php`

**Validaciones:**
```php
- titulo: required|string|max:255
- contenido: required|string
```

---

### **FASE 3: Dashboard y Widgets** ⏱️ 1-2 días

#### 3.1 Dashboard Administrativo
**Prioridad:** 🟡 Media
**Duración:** 4 horas

**Widgets a crear:**
- [ ] **StatsOverview**: Contadores totales
  - Total usuarios
  - Total roles
  - Total temas
  - Usuarios activos hoy

- [ ] **Chart**: Usuarios registrados por mes (últimos 6 meses)

- [ ] **Table**: Últimos usuarios registrados (10)

- [ ] **Table**: Actividad reciente del sistema (15)

**Archivos:**
- `app/Filament/Widgets/StatsOverviewWidget.php`
- `app/Filament/Widgets/UsuariosChartWidget.php`
- `app/Filament/Widgets/LatestUsersWidget.php`
- `app/Filament/Widgets/ActivityLogWidget.php`

#### 3.2 Personalización de UI
**Prioridad:** 🟢 Baja
**Duración:** 2 horas

- [ ] Configurar colores del tema (#6777ef)
- [ ] Agregar logo ITSTE
- [ ] Configurar favicon
- [ ] Modo oscuro habilitado
- [ ] Personalizar navegación

---

### **FASE 4: Features Avanzadas** ⏱️ 2-3 días

#### 4.1 Sistema de Auditoría
**Prioridad:** 🔴 Alta - Producción
**Duración:** 5 horas

- [ ] Instalar y configurar Spatie Activity Log
- [ ] Agregar trait LogsActivity a modelos
- [ ] Configurar qué campos auditar
- [ ] Crear Resource para ver logs
- [ ] Filtros: por usuario, modelo, acción, fecha
- [ ] Vista detallada de cambios (diff)

**Modelos a auditar:**
- User (cambios de roles, datos personales)
- Role (cambios de permisos)
- Blog (creación, edición, eliminación)

**Archivo:** `app/Filament/Resources/ActivityLogResource.php`

#### 4.2 Exportación Avanzada
**Prioridad:** 🟡 Media
**Duración:** 3 horas

- [ ] Configurar pxlrbt/filament-excel
- [ ] Exportar Usuarios a Excel/CSV
- [ ] Exportar Roles a Excel/CSV
- [ ] Exportar Temas a PDF (con formato)
- [ ] Configurar columnas personalizadas
- [ ] Agregar filtros en exportación

#### 4.3 Notificaciones del Sistema
**Prioridad:** 🟢 Baja
**Duración:** 2 horas

- [ ] Notificación al crear usuario
- [ ] Notificación al asignar rol
- [ ] Notificación al cambiar permisos
- [ ] Configurar persistencia en BD
- [ ] Marcar como leídas

---

### **FASE 5: Validación y Seguridad** ⏱️ 2 días

#### 5.1 Form Requests Robustos
**Prioridad:** 🔴 Alta
**Duración:** 4 horas

- [ ] Crear Form Requests para cada Resource
- [ ] Validaciones personalizadas
- [ ] Mensajes de error en español
- [ ] Validación de permisos en requests

**Archivos a crear:**
```
app/Http/Requests/
├── Usuario/
│   ├── StoreUsuarioRequest.php
│   └── UpdateUsuarioRequest.php
├── Role/
│   ├── StoreRoleRequest.php
│   └── UpdateRoleRequest.php
└── Blog/
    ├── StoreBlogRequest.php
    └── UpdateBlogRequest.php
```

#### 5.2 Manejo de Errores
**Prioridad:** 🔴 Alta
**Duración:** 3 horas

- [ ] Páginas de error personalizadas (404, 403, 500)
- [ ] Logging estructurado
- [ ] Notificaciones de errores críticos
- [ ] Try-catch en operaciones sensibles
- [ ] Rollback de transacciones

**Archivos:**
```
resources/views/errors/
├── 404.blade.php
├── 403.blade.php
└── 500.blade.php
```

#### 5.3 Seguridad Adicional
**Prioridad:** 🔴 Alta
**Duración:** 3 horas

- [ ] Rate limiting en formularios
- [ ] CSRF protection verificado
- [ ] XSS prevention
- [ ] SQL Injection prevention (usar Eloquent)
- [ ] Sanitización de inputs
- [ ] Headers de seguridad

---

### **FASE 6: Testing y Calidad** ⏱️ 2-3 días

#### 6.1 Tests Unitarios
**Prioridad:** 🟡 Media
**Duración:** 6 horas

- [ ] Tests de modelos
- [ ] Tests de relaciones
- [ ] Tests de permisos
- [ ] Tests de validaciones

**Archivos:**
```
tests/Unit/
├── Models/
│   ├── UserTest.php
│   ├── RoleTest.php
│   └── BlogTest.php
└── Requests/
    └── UsuarioRequestTest.php
```

#### 6.2 Tests de Integración
**Prioridad:** 🟡 Media
**Duración:** 6 horas

- [ ] Tests de Resources Filament
- [ ] Tests de creación de registros
- [ ] Tests de actualización
- [ ] Tests de eliminación
- [ ] Tests de exportación
- [ ] Tests de permisos en UI

**Archivos:**
```
tests/Feature/
├── Filament/
│   ├── UsuarioResourceTest.php
│   ├── RoleResourceTest.php
│   └── BlogResourceTest.php
└── Auth/
    └── PermissionsTest.php
```

#### 6.3 Code Quality
**Prioridad:** 🟢 Baja
**Duración:** 2 horas

- [ ] Configurar PHP CS Fixer
- [ ] Configurar PHPStan (level 5)
- [ ] Ejecutar análisis estático
- [ ] Corregir warnings

**Comandos:**
```bash
composer require --dev laravel/pint
composer require --dev phpstan/phpstan
./vendor/bin/pint
./vendor/bin/phpstan analyse
```

---

### **FASE 7: Optimización y Performance** ⏱️ 1 día

#### 7.1 Optimizaciones de Base de Datos
**Prioridad:** 🟡 Media
**Duración:** 3 horas

- [ ] Agregar índices faltantes
- [ ] Optimizar queries N+1
- [ ] Configurar eager loading
- [ ] Cachear contadores del dashboard

**Migraciones a crear:**
```sql
- Index en users.email
- Index en roles.name
- Index en blogs.created_at
- Index en activity_log.subject_type, subject_id
```

#### 7.2 Caching Strategy
**Prioridad:** 🟢 Baja
**Duración:** 2 horas

- [ ] Cache de permisos
- [ ] Cache de configuración
- [ ] Cache de rutas
- [ ] Cache de vistas

**Comandos:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### **FASE 8: Documentación y Deploy** ⏱️ 1-2 días

#### 8.1 Documentación Técnica
**Prioridad:** 🟡 Media
**Duración:** 4 horas

- [ ] README actualizado
- [ ] Guía de instalación
- [ ] Guía de desarrollo
- [ ] Documentación de API (si aplica)
- [ ] Changelog completo

**Archivos a crear:**
```
docs/
├── INSTALLATION.md
├── DEVELOPMENT.md
├── DEPLOYMENT.md
├── API.md (futuro)
└── TROUBLESHOOTING.md
```

#### 8.2 Guía de Despliegue
**Prioridad:** 🔴 Alta
**Duración:** 3 horas

- [ ] Checklist pre-deploy
- [ ] Scripts de deploy
- [ ] Configuración de servidor
- [ ] Variables de entorno de producción
- [ ] Backup strategy
- [ ] Rollback plan

#### 8.3 Migración de Datos (si necesario)
**Prioridad:** 🔴 Alta
**Duración:** 2 horas

- [ ] Script de migración de datos existentes
- [ ] Verificación de integridad
- [ ] Backup completo pre-migración
- [ ] Plan de rollback

---

## 📦 Dependencias Nuevas

### Composer
```json
{
    "require": {
        "filament/filament": "^3.0",
        "bezhansalleh/filament-shield": "^3.0",
        "pxlrbt/filament-excel": "^2.0",
        "spatie/laravel-activitylog": "^4.0",
        "maatwebsite/excel": "^3.1"
    },
    "require-dev": {
        "laravel/pint": "^1.0",
        "phpstan/phpstan": "^1.10"
    }
}
```

### NPM (Opcional - para customización)
```json
{
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.0",
        "@tailwindcss/typography": "^0.5.0"
    }
}
```

---

## 🗂️ Estructura de Archivos Nueva

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── UsuarioResource.php
│   │   ├── UsuarioResource/
│   │   │   ├── Pages/
│   │   │   │   ├── CreateUsuario.php
│   │   │   │   ├── EditUsuario.php
│   │   │   │   └── ListUsuarios.php
│   │   │   └── RelationManagers/
│   │   ├── RoleResource.php
│   │   ├── RoleResource/Pages/
│   │   ├── BlogResource.php
│   │   ├── BlogResource/Pages/
│   │   └── ActivityLogResource.php
│   ├── Widgets/
│   │   ├── StatsOverviewWidget.php
│   │   ├── UsuariosChartWidget.php
│   │   ├── LatestUsersWidget.php
│   │   └── ActivityLogWidget.php
│   └── Pages/
│       └── Dashboard.php
├── Http/
│   ├── Requests/
│   │   ├── Usuario/
│   │   ├── Role/
│   │   └── Blog/
│   └── Traits/
│       └── HasPermissionMiddleware.php (mantener por si acaso)
├── Models/
│   ├── User.php (actualizar con LogsActivity)
│   ├── Blog.php (actualizar con LogsActivity)
│   └── ... (sin cambios mayores)
└── Policies/
    ├── UserPolicy.php (nuevo)
    ├── RolePolicy.php (nuevo)
    └── BlogPolicy.php (nuevo)

config/
├── filament.php (nuevo)
├── activitylog.php (nuevo)
└── app_settings.php (mantener)

database/
├── migrations/
│   └── YYYY_MM_DD_add_indexes_for_performance.php
└── seeders/ (mantener actuales)

lang/
└── es/
    └── filament/ (traducciones)

tests/
├── Feature/
│   └── Filament/
└── Unit/
    └── Models/

public/
└── css/
    └── filament/ (assets compilados)
```

---

## ⚠️ Archivos a Deprecar (NO Eliminar - Mantener por Backup)

Estos archivos ya no se usarán pero se mantienen:

```
app/Http/Controllers/ (todos - reemplazados por Resources)
├── BlogController.php
├── RolController.php
└── UsuarioController.php

resources/views/ (mayoría - reemplazados por Filament)
├── blogs/
├── roles/
├── usuarios/
└── layouts/ (excepto errores)

routes/web.php (simplificado - solo Filament y auth)

resources/assets/js/ (Filament usa Livewire/Alpine)
```

**IMPORTANTE:** Mover a carpeta `legacy/` después de verificar que todo funciona.

---

## 🎯 Criterios de Aceptación por Fase

### Fase 1: ✅ Completada cuando...
- [ ] Filament instalado sin errores
- [ ] Panel accesible en /admin
- [ ] Login funcional
- [ ] Dashboard básico visible

### Fase 2: ✅ Completada cuando...
- [ ] CRUD de Usuarios funcional al 100%
- [ ] CRUD de Roles funcional al 100%
- [ ] CRUD de Blogs funcional al 100%
- [ ] Permisos aplicados correctamente
- [ ] Validaciones funcionando

### Fase 3: ✅ Completada cuando...
- [ ] Dashboard muestra estadísticas reales
- [ ] Widgets interactivos funcionando
- [ ] UI personalizada con branding ITSTE

### Fase 4: ✅ Completada cuando...
- [ ] Toda acción se registra en activity log
- [ ] Exportación PDF/Excel funcional
- [ ] Notificaciones operativas

### Fase 5: ✅ Completada cuando...
- [ ] Todas las validaciones implementadas
- [ ] Páginas de error personalizadas
- [ ] 0 vulnerabilidades críticas
- [ ] Headers de seguridad configurados

### Fase 6: ✅ Completada cuando...
- [ ] >70% cobertura de tests
- [ ] Todos los tests pasando
- [ ] PHPStan level 5 sin errores

### Fase 7: ✅ Completada cuando...
- [ ] Queries optimizadas (sin N+1)
- [ ] Tiempo de carga <500ms
- [ ] Cache configurado

### Fase 8: ✅ Completada cuando...
- [ ] Documentación completa
- [ ] Deploy exitoso
- [ ] Stakeholders aprobaron

---

## 📊 Métricas de Progreso

### Código
- **Líneas eliminadas:** ~2000 (vistas Blade)
- **Líneas agregadas:** ~1500 (Resources Filament)
- **Reducción neta:** -500 líneas
- **Archivos nuevos:** ~30
- **Archivos deprecados:** ~40

### Performance
- **Tiempo desarrollo nueva feature:**
  - Antes: 4-6 horas
  - Después: 30-60 minutos
  - **Mejora: 5-6x más rápido**

### Calidad
- **Cobertura tests:** 0% → 70%+
- **Vulnerabilidades:** 78 → 0 críticas
- **Code quality:** Sin estándar → PSR-12 + Level 5

---

## 🚨 Riesgos y Mitigación

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Conflictos de dependencias | Media | Alto | Testing en ambiente aislado |
| Pérdida de datos | Baja | Crítico | Backups antes de cada fase |
| Breaking changes de Filament | Baja | Medio | Usar versiones estables (^3.0) |
| Curva aprendizaje del equipo | Alta | Medio | Documentación + capacitación |
| Performance degradado | Baja | Medio | Profiling + optimización |

---

## 📅 Cronograma Estimado

```
Semana 1 (Días 1-5):
├─ Lunes:    Fase 1 (Preparación)
├─ Martes:   Fase 2.1 (Resource Usuarios)
├─ Miércoles: Fase 2.2-2.3 (Resources Roles y Blogs)
├─ Jueves:   Fase 3 (Dashboard y Widgets)
└─ Viernes:  Fase 4.1 (Auditoría)

Semana 2 (Días 6-10):
├─ Lunes:    Fase 4.2-4.3 (Exportación y Notificaciones)
├─ Martes:   Fase 5 (Validación y Seguridad)
├─ Miércoles: Fase 6.1-6.2 (Tests)
├─ Jueves:   Fase 6.3 + Fase 7 (Quality + Performance)
└─ Viernes:  Fase 8 (Documentación y Deploy)

Total: 10 días laborables = 2 semanas
```

---

## ✅ Checklist Final Pre-Deploy

```bash
# Seguridad
[ ] Actualizar todas las dependencias
[ ] Ejecutar audit de seguridad
[ ] Configurar HTTPS
[ ] Variables .env de producción configuradas
[ ] Credenciales seguras generadas

# Funcionalidad
[ ] Todos los módulos testeados
[ ] Permisos verificados
[ ] Exportaciones probadas
[ ] Notificaciones operativas

# Performance
[ ] Cache habilitado
[ ] Queries optimizadas
[ ] Assets minificados
[ ] CDN configurado (si aplica)

# Datos
[ ] Backup completo realizado
[ ] Seeders de producción listos
[ ] Migración de datos verificada

# Documentación
[ ] README actualizado
[ ] .env.example actualizado
[ ] Guía de deploy lista
[ ] Runbook de troubleshooting

# Monitoring
[ ] Logs configurados
[ ] Error tracking (Sentry/Bugsnag)
[ ] Uptime monitoring
[ ] Performance monitoring
```

---

## 🎓 Recursos de Capacitación

### Para el Equipo
- [ ] Filament Docs: https://filamentphp.com/docs
- [ ] Livewire Docs: https://livewire.laravel.com/docs
- [ ] Video tutorials internos
- [ ] Sesiones de pair programming

### Documentación Interna
- [ ] Guía de convenciones
- [ ] Guía de troubleshooting
- [ ] FAQ común
- [ ] Mejores prácticas

---

## 📞 Contactos y Soporte

**Lead Developer:** Claude AI Assistant
**Fecha creación roadmap:** 2025-11-18
**Última actualización:** 2025-11-18
**Versión:** 1.0

---

## 🎉 Beneficios Post-Migración

### Desarrollo
- ⚡ 5-6x más rápido para nuevas features
- 🐛 Menos bugs (validaciones automáticas)
- 🧪 Tests integrados desde el inicio
- 📦 Código más mantenible

### UX/UI
- 📱 Responsive automático
- 🎨 UI moderna y profesional
- 🌙 Modo oscuro incluido
- ♿ Accesibilidad mejorada

### Negocio
- 💰 Menor costo de mantenimiento
- 🚀 Time-to-market reducido
- 📈 Escalabilidad mejorada
- 🔒 Seguridad enterprise-grade

---

**¡Comencemos la migración! 🚀**
