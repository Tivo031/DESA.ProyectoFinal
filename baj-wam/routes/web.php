<?php

use App\Http\Controllers\CambioPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


// PÁGINA PÚBLICA

Route::view('/', 'public.inicio')
    ->name('inicio');


// LOGIN

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'index'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'autenticar'])
        ->name('login.autenticar');
});


// RUTAS PROTEGIDAS

Route::middleware(['auth', 'usuario.activo'])->group(function () {

    // LOGOUT

    Route::post('/logout', [LoginController::class, 'cerrarSesion'])
        ->name('logout');


    // CAMBIO OBLIGATORIO DE CONTRASEÑA

    Route::get(
        '/cambiar-password',
        [CambioPasswordController::class, 'edit']
    )->name('password.cambiar');

    Route::put(
        '/cambiar-password',
        [CambioPasswordController::class, 'update']
    )->name('password.actualizar');


    // SISTEMA

    Route::middleware('password.cambio')->group(function () {

        // PERFIL

        Route::get('/perfil', [UsuarioController::class, 'perfil'])
            ->name('perfil');


        // DASHBOARD

        Route::view('/dashboard', 'dashboard.index')
            ->middleware('can:dashboard.ver')
            ->name('dashboard');


        // USUARIOS

        Route::post(
            '/usuarios/{usuario}/restablecer-password',
            [UsuarioController::class, 'restablecerPassword']
        )
            ->middleware('can:usuarios.restablecer_password')
            ->name('usuarios.restablecer-password');

        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->middleware('can:usuarios.ver')
            ->name('usuarios.index');

        Route::get('/usuarios/create', [UsuarioController::class, 'create'])
            ->middleware('can:usuarios.crear')
            ->name('usuarios.create');

        Route::post('/usuarios', [UsuarioController::class, 'store'])
            ->middleware('can:usuarios.crear')
            ->name('usuarios.store');

        Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])
            ->middleware('can:usuarios.ver')
            ->name('usuarios.show');

        Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])
            ->middleware('can:usuarios.editar')
            ->name('usuarios.edit');

        Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])
            ->middleware('can:usuarios.editar')
            ->name('usuarios.update');

        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])
            ->middleware('can:usuarios.desactivar')
            ->name('usuarios.destroy');

        // TEMPORAL MIENTRAS IMPLEMENTAMOS EDICIÓN

        Route::view('/usuarios/1/edit', 'usuarios.edit')
            ->middleware('can:usuarios.editar')
            ->name('usuarios.edit');


        // PACIENTES - TEMPORALES

        Route::view('/pacientes', 'pacientes.index')
            ->name('pacientes.index');

        Route::view('/pacientes/create', 'pacientes.create')
            ->name('pacientes.create');

        Route::view('/pacientes/1', 'pacientes.show')
            ->name('pacientes.show');

        Route::view('/pacientes/1/edit', 'pacientes.edit')
            ->name('pacientes.edit');


        // CITAS - TEMPORALES

        Route::view('/citas', 'citas.index')
            ->name('citas.index');

        Route::view('/citas/create', 'citas.create')
            ->name('citas.create');

        Route::view('/citas/1', 'citas.show')
            ->name('citas.show');

        Route::view('/citas/1/edit', 'citas.edit')
            ->name('citas.edit');


        // CONSULTAS - TEMPORALES

        Route::view('/consultas', 'consultas.index')
            ->name('consultas.index');

        Route::view('/consultas/create', 'consultas.create')
            ->name('consultas.create');

        Route::view('/consultas/1', 'consultas.show')
            ->name('consultas.show');

        Route::view('/consultas/1/edit', 'consultas.edit')
            ->name('consultas.edit');


        // PRODUCTOS - TEMPORALES

        Route::view('/productos', 'productos.index')
            ->name('productos.index');

        Route::view('/productos/create', 'productos.create')
            ->name('productos.create');

        Route::view('/productos/1', 'productos.show')
            ->name('productos.show');

        Route::view('/productos/1/edit', 'productos.edit')
            ->name('productos.edit');


        // INVENTARIO - TEMPORAL

        Route::view('/inventario', 'inventario.index')
            ->name('inventario.index');

    });
});