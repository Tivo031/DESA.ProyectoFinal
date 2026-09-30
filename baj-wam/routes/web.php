<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CambioPasswordController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\SolicitudCitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;

// PÁGINA PÚBLICA

Route::get('/', [SolicitudCitaController::class, 'index'])
    ->name('inicio');

Route::post('/solicitudes-cita', [SolicitudCitaController::class, 'store'])
    ->name('solicitudes-cita.store');

Route::get('/catalogo', [CatalogoController::class, 'index'])
    ->name('catalogo.publico');


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

        Route::get('/dashboard', [DashboardController::class, 'index'])
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


        // ROLES

        Route::get('/roles', [RolController::class, 'index'])
            ->middleware('can:roles.ver')
            ->name('roles.index');

        Route::get('/roles/create', [RolController::class, 'create'])
            ->middleware('can:roles.crear')
            ->name('roles.create');

        Route::post('/roles', [RolController::class, 'store'])
            ->middleware('can:roles.crear')
            ->name('roles.store');

        Route::get('/roles/{id}/edit', [RolController::class, 'edit'])
            ->middleware('can:roles.editar')
            ->name('roles.edit');

        Route::put('/roles/{id}', [RolController::class, 'update'])
            ->middleware('can:roles.editar')
            ->name('roles.update');

        Route::delete('/roles/{id}', [RolController::class, 'destroy'])
            ->middleware('can:roles.desactivar')
            ->name('roles.destroy');


        // PERMISOS

        Route::get('/permisos', [PermisoController::class, 'index'])
            ->middleware('can:permisos.ver')
            ->name('permisos.index');

        Route::get('/permisos/create', [PermisoController::class, 'create'])
            ->middleware('can:permisos.crear')
            ->name('permisos.create');

        Route::post('/permisos', [PermisoController::class, 'store'])
            ->middleware('can:permisos.crear')
            ->name('permisos.store');

        Route::get('/permisos/{id}/edit', [PermisoController::class, 'edit'])
            ->middleware('can:permisos.editar')
            ->name('permisos.edit');

        Route::put('/permisos/{id}', [PermisoController::class, 'update'])
            ->middleware('can:permisos.editar')
            ->name('permisos.update');

        Route::delete('/permisos/{id}', [PermisoController::class, 'destroy'])
            ->middleware('can:permisos.desactivar')
            ->name('permisos.destroy');


        // PACIENTES

        Route::get('/pacientes', [PacienteController::class, 'index'])
            ->middleware('can:pacientes.ver')
            ->name('pacientes.index');

        Route::get('/pacientes/create', [PacienteController::class, 'create'])
            ->middleware('can:pacientes.crear')
            ->name('pacientes.create');

        Route::post('/pacientes', [PacienteController::class, 'store'])
            ->middleware('can:pacientes.crear')
            ->name('pacientes.store');

        Route::get('/pacientes/{id}', [PacienteController::class, 'show'])
            ->middleware('can:pacientes.ver')
            ->whereNumber('id')
            ->name('pacientes.show');

        Route::get('/pacientes/{id}/edit', [PacienteController::class, 'edit'])
            ->middleware('can:pacientes.editar')
            ->whereNumber('id')
            ->name('pacientes.edit');

        Route::put('/pacientes/{id}', [PacienteController::class, 'update'])
            ->middleware('can:pacientes.editar')
            ->whereNumber('id')
            ->name('pacientes.update');

        Route::delete('/pacientes/{id}', [PacienteController::class, 'destroy'])
            ->middleware('can:pacientes.editar')
            ->whereNumber('id')
            ->name('pacientes.destroy');


        // CITAS

        Route::get('/citas', [CitaController::class, 'index'])
            ->middleware('can:citas.ver')
            ->name('citas.index');

        Route::get('/citas/create', [CitaController::class, 'create'])
            ->middleware('can:citas.crear')
            ->name('citas.create');

        Route::post('/citas', [CitaController::class, 'store'])
            ->middleware('can:citas.crear')
            ->name('citas.store');

        Route::get('/citas/{id}', [CitaController::class, 'show'])
            ->middleware('can:citas.ver')
            ->whereNumber('id')
            ->name('citas.show');

        Route::get('/citas/{id}/edit', [CitaController::class, 'edit'])
            ->middleware('can:citas.editar')
            ->whereNumber('id')
            ->name('citas.edit');

        Route::put('/citas/{id}', [CitaController::class, 'update'])
            ->middleware('can:citas.editar')
            ->whereNumber('id')
            ->name('citas.update');

        Route::delete('/citas/{id}', [CitaController::class, 'destroy'])
            ->middleware('can:citas.cancelar')
            ->whereNumber('id')
            ->name('citas.destroy');


        // CONSULTAS

        Route::get('/consultas', [ConsultaController::class, 'index'])
            ->middleware('can:consultas.ver')
            ->name('consultas.index');

        Route::get('/consultas/create', [ConsultaController::class, 'create'])
            ->middleware('can:consultas.crear')
            ->name('consultas.create');

        Route::post('/consultas', [ConsultaController::class, 'store'])
            ->middleware('can:consultas.crear')
            ->name('consultas.store');

        Route::get('/consultas/{id}', [ConsultaController::class, 'show'])
            ->middleware('can:consultas.ver')
            ->whereNumber('id')
            ->name('consultas.show');


        // PRODUCTOS

        Route::get('/productos', [ProductoController::class, 'index'])
            ->middleware('can:productos.ver')
            ->name('productos.index');

        Route::get('/productos/create', [ProductoController::class, 'create'])
            ->middleware('can:productos.crear')
            ->name('productos.create');

        Route::post('/productos', [ProductoController::class, 'store'])
            ->middleware('can:productos.crear')
            ->name('productos.store');

        Route::get('/productos/{id}', [ProductoController::class, 'show'])
            ->middleware('can:productos.ver')
            ->whereNumber('id')
            ->name('productos.show');

        Route::get('/productos/{id}/edit', [ProductoController::class, 'edit'])
            ->middleware('can:productos.editar')
            ->whereNumber('id')
            ->name('productos.edit');

        Route::put('/productos/{id}', [ProductoController::class, 'update'])
            ->middleware('can:productos.editar')
            ->whereNumber('id')
            ->name('productos.update');

        Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])
            ->middleware('can:productos.editar')
            ->whereNumber('id')
            ->name('productos.destroy');

        Route::patch('/productos/{id}/activar', [ProductoController::class, 'activar'])
            ->middleware('can:productos.editar')
            ->whereNumber('id')
            ->name('productos.activar');


        // CATÁLOGO DE PRODUCTOS

        Route::patch(
            '/productos/{id}/catalogo/publicar',
            [ProductoController::class, 'publicarCatalogo']
        )
            ->middleware('can:productos.catalogo')
            ->whereNumber('id')
            ->name('productos.catalogo.publicar');

        Route::patch(
            '/productos/{id}/catalogo/retirar',
            [ProductoController::class, 'retirarCatalogo']
        )
            ->middleware('can:productos.catalogo')
            ->whereNumber('id')
            ->name('productos.catalogo.retirar');


        // INVENTARIO

        Route::get('/inventario', [InventarioController::class, 'index'])
            ->middleware('can:inventario.ver')
            ->name('inventario.index');

        Route::get(
            '/inventario/movimientos',
            [InventarioController::class, 'historial']
        )
            ->middleware('can:inventario.ver')
            ->name('inventario.historial');

        Route::get(
            '/inventario/movimientos/create',
            [InventarioController::class, 'create']
        )
            ->middleware('can:inventario.movimiento')
            ->name('inventario.create');

        Route::post(
            '/inventario/movimientos',
            [InventarioController::class, 'store']
        )
            ->middleware('can:inventario.movimiento')
            ->name('inventario.store');


        // SOLICITUDES DE CITA

        Route::get(
            '/solicitudes-cita',
            [SolicitudCitaController::class, 'solicitudes']
        )
            ->middleware('can:solicitudes.ver')
            ->name('solicitudes-cita.index');

        Route::get(
            '/solicitudes-cita/{id}/procesar',
            [SolicitudCitaController::class, 'procesar']
        )
            ->middleware('can:solicitudes.procesar')
            ->whereNumber('id')
            ->name('solicitudes-cita.procesar');

        Route::post(
            '/solicitudes-cita/{id}/aprobar',
            [SolicitudCitaController::class, 'aprobar']
        )
            ->middleware('can:solicitudes.procesar')
            ->whereNumber('id')
            ->name('solicitudes-cita.aprobar');

        Route::patch(
            '/solicitudes-cita/{id}/rechazar',
            [SolicitudCitaController::class, 'rechazar']
        )
            ->middleware('can:solicitudes.procesar')
            ->whereNumber('id')
            ->name('solicitudes-cita.rechazar');
    });
});