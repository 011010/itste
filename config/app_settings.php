<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Paginación
    |--------------------------------------------------------------------------
    |
    | Número de elementos a mostrar por página en los listados
    |
    */

    'pagination' => [
        'per_page' => env('APP_PAGINATION_PER_PAGE', 10),
        'datatable_per_page' => env('DATATABLE_PAGINATION_PER_PAGE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Tema/UI
    |--------------------------------------------------------------------------
    |
    | Colores y configuraciones visuales del tema
    |
    */

    'theme' => [
        'primary_color' => env('THEME_PRIMARY_COLOR', '#6777ef'),
        'danger_color' => env('THEME_DANGER_COLOR', '#fc544b'),
        'success_color' => env('THEME_SUCCESS_COLOR', '#6777ef'),
        'warning_color' => env('THEME_WARNING_COLOR', '#ffa426'),
        'info_color' => env('THEME_INFO_COLOR', '#3abaf4'),

        // Configuración de logo e imágenes
        'logo' => [
            'main' => 'img/itste.png',
            'login' => 'img/loginIcon.avif',
            'width_sidebar' => 65,
            'width_login' => 45,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Archivos Permitidos
    |--------------------------------------------------------------------------
    |
    | Extensiones de archivos permitidas para upload
    |
    */

    'allowed_files' => [
        'images' => ['gif', 'png', 'jpg', 'jpeg', 'webp', 'svg'],
        'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'general' => ['gif', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf', 'doc', 'docx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de SweetAlert
    |--------------------------------------------------------------------------
    |
    | Colores y configuraciones de los diálogos de confirmación
    |
    */

    'sweetalert' => [
        'confirm_button_color' => env('THEME_PRIMARY_COLOR', '#6777ef'),
        'cancel_button_color' => env('SWEETALERT_CANCEL_COLOR', '#d33'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Módulos del Sistema
    |--------------------------------------------------------------------------
    |
    | Nombres y configuraciones de los módulos principales
    |
    */

    'modules' => [
        'usuarios' => [
            'name' => 'Usuarios',
            'icon' => 'fas fa-users',
        ],
        'roles' => [
            'name' => 'Roles',
            'icon' => 'fas fa-user-lock',
        ],
        'temas' => [
            'name' => 'Temas',
            'icon' => 'fas fa-book',
        ],
    ],

];
