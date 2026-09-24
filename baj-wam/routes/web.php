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

// PÁGINA PÚBLICA

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

        Route::view('/dashboard', 'dashboard.index')
            ->middleware('can:dashboard.ver')
            ->name('dashboard');

        // USUARIOS

        Route::post('/usuarios/{usuario}/restablecer-password', [UsuarioController::class, 'restablecerPassword'])
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

        //PERMISOS

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

        // TEMPORAL MIENTRAS IMPLEMENTAMOS EDICIÓN

        Route::view('/usuarios/1/edit', 'usuarios.edit')
            ->middleware('can:usuarios.editar')
            ->name('usuarios.edit');

        // PACIENTES 
        Route::get('/pacientes', [PacienteController::class, 'index'])
            ->name('pacientes.index');

        Route::get('/pacientes/create', [PacienteController::class, 'create'])
            ->name('pacientes.create');

        Route::post('/pacientes', [PacienteController::class, 'store'])
            ->name('pacientes.store');

        Route::get('/pacientes/{id}', [PacienteController::class, 'show'])
            ->name('pacientes.show');

        Route::get('/pacientes/{id}/edit', [PacienteController::class, 'edit'])
            ->name('pacientes.edit');

        Route::put('/pacientes/{id}', [PacienteController::class, 'update'])
            ->name('pacientes.update');

        Route::delete('/pacientes/{id}', [PacienteController::class, 'destroy'])
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
            ->name('citas.show');

        Route::get('/citas/{id}/edit', [CitaController::class, 'edit'])
            ->middleware('can:citas.editar')
            ->name('citas.edit');

        Route::put('/citas/{id}', [CitaController::class, 'update'])
            ->middleware('can:citas.editar')
            ->name('citas.update');

        Route::delete('/citas/{id}', [CitaController::class, 'destroy'])
            ->middleware('can:citas.cancelar')
            ->name('citas.destroy');

        // CONSULTAS - TEMPORALES

        Route::view('/consultas', 'consultas.index')
            ->name('consultas.index');

        Route::view('/consultas/create', 'consultas.create')
            ->name('consultas.create');

        Route::view('/consultas/1', 'consultas.show')
            ->name('consultas.show');

        Route::view('/consultas/1/edit', 'consultas.edit')
            ->name('consultas.edit');

        // PRODUCTOS 
        Route::get('/productos', [ProductoController::class, 'index'])
            ->name('productos.index');

        Route::get('/productos/create', [ProductoController::class, 'create'])
            ->name('productos.create');

        Route::post('/productos', [ProductoController::class, 'store'])
            ->name('productos.store');

        Route::get('/productos/{id}', [ProductoController::class, 'show'])
            ->name('productos.show');

        Route::get('/productos/{id}/edit', [ProductoController::class, 'edit'])
            ->name('productos.edit');

        Route::put('/productos/{id}', [ProductoController::class, 'update'])
            ->name('productos.update');

        Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])
            ->name('productos.destroy');

        Route::patch('/productos/{id}/activar', [ProductoController::class, 'activar'])
            ->name('productos.activar');

        // INVENTARIO

        Route::get('/inventario', [InventarioController::class, 'index'])
            ->name('inventario.index');

        Route::get('/inventario/movimientos/create', [InventarioController::class, 'create'])
            ->name('inventario.create');

        Route::post('/inventario/movimientos', [InventarioController::class, 'store'])
            ->name('inventario.store');

        Route::get('/inventario', [InventarioController::class, 'index'])
            ->name('inventario.index');

        Route::get('/inventario/movimientos', [InventarioController::class, 'historial'])
            ->name('inventario.historial');

        Route::get('/inventario/movimientos/create', [InventarioController::class, 'create'])
            ->name('inventario.create');

        Route::post('/inventario/movimientos', [InventarioController::class, 'store'])
            ->name('inventario.store');

    });

    // SOLICITUDES DE CITA
    Route::get(
        '/solicitudes-cita',
        [SolicitudCitaController::class, 'solicitudes']
    )
        ->middleware('can:citas.ver')
        ->name('solicitudes-cita.index');

    Route::get(
        '/solicitudes-cita/{id}/procesar',
        [SolicitudCitaController::class, 'procesar']
    )
        ->middleware('can:citas.crear')
        ->name('solicitudes-cita.procesar');

    Route::post(
        '/solicitudes-cita/{id}/aprobar',
        [SolicitudCitaController::class, 'aprobar']
    )
        ->middleware('can:citas.crear')
        ->name('solicitudes-cita.aprobar');

    Route::patch(
        '/solicitudes-cita/{id}/rechazar',
        [SolicitudCitaController::class, 'rechazar']
    )
        ->middleware('can:citas.cancelar')
        ->name('solicitudes-cita.rechazar');

    Route::patch(
        '/productos/{id}/catalogo/publicar',
        [ProductoController::class, 'publicarCatalogo']
    )->name('productos.catalogo.publicar');

    Route::patch(
        '/productos/{id}/catalogo/retirar',
        [ProductoController::class, 'retirarCatalogo']
    )->name('productos.catalogo.retirar');
});