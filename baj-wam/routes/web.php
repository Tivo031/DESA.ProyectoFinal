<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas temporales de demostración
|--------------------------------------------------------------------------
| Estas rutas solamente muestran las vistas de la plantilla.
| Después serán sustituidas por controladores y rutas resource.
*/

Route::view('/', 'public.inicio')->name('inicio');

Route::view('/login', 'auth.login')->name('login');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

Route::view('/pacientes', 'pacientes.index')
    ->name('pacientes.index');

Route::view('/pacientes/create', 'pacientes.form')
    ->name('pacientes.create');

Route::view('/citas', 'citas.index')
    ->name('citas.index');

Route::view('/consultas', 'consultas.index')
    ->name('consultas.index');

Route::view('/productos', 'productos.index')
    ->name('productos.index');

Route::view('/inventario', 'inventario.index')
    ->name('inventario.index');

Route::view('/usuarios', 'usuarios.index')
    ->name('usuarios.index');

Route::view('/usuarios', 'usuarios.index');
Route::view('/usuarios/create', 'usuarios.create');
Route::view('/usuarios/1', 'usuarios.show');
Route::view('/usuarios/1/edit', 'usuarios.edit');

Route::view('/pacientes', 'pacientes.index');
Route::view('/pacientes/create', 'pacientes.create');
Route::view('/pacientes/1', 'pacientes.show');
Route::view('/pacientes/1/edit', 'pacientes.edit');

Route::view('/usuarios', 'usuarios.index');
Route::view('/usuarios/create', 'usuarios.create');
Route::view('/usuarios/1', 'usuarios.show');
Route::view('/usuarios/1/edit', 'usuarios.edit');

Route::view('/pacientes', 'pacientes.index');
Route::view('/pacientes/create', 'pacientes.create');
Route::view('/pacientes/1', 'pacientes.show');
Route::view('/pacientes/1/edit', 'pacientes.edit');

Route::view('/productos', 'productos.index');
Route::view('/productos/create', 'productos.create');
Route::view('/productos/1', 'productos.show');
Route::view('/productos/1/edit', 'productos.edit');

Route::view('/citas', 'citas.index');
Route::view('/citas/create', 'citas.create');
Route::view('/citas/1', 'citas.show');
Route::view('/citas/1/edit', 'citas.edit');

Route::view('/consultas', 'consultas.index');
Route::view('/consultas/create', 'consultas.create');
Route::view('/consultas/1', 'consultas.show');
Route::view('/consultas/1/edit', 'consultas.edit');
