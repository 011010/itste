<?php

namespace App\Http\Traits;

/**
 * Trait HasPermissionMiddleware
 *
 * Proporciona funcionalidad compartida para aplicar middleware de permisos
 * en controladores de recursos CRUD
 *
 * @package App\Http\Traits
 */
trait HasPermissionMiddleware
{
    /**
     * Aplica middleware de permisos estándar para un controlador de recursos
     *
     * @param string $permissionPrefix El prefijo del permiso (ej: 'usuario', 'rol', 'tema')
     * @return void
     */
    protected function applyPermissionMiddleware(string $permissionPrefix)
    {
        $this->middleware(
            "permission:ver-{$permissionPrefix}|crear-{$permissionPrefix}|editar-{$permissionPrefix}|borrar-{$permissionPrefix}",
            ['only' => ['index']]
        );

        $this->middleware(
            "permission:crear-{$permissionPrefix}",
            ['only' => ['create', 'store']]
        );

        $this->middleware(
            "permission:editar-{$permissionPrefix}",
            ['only' => ['edit', 'update']]
        );

        $this->middleware(
            "permission:borrar-{$permissionPrefix}",
            ['only' => ['destroy']]
        );
    }
}
