<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UsuariosRolesSeguridadTest extends TestCase
{
    private int $secuencia = 0;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles');
        Schema::enableForeignKeyConstraints();

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre')->unique();
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->unsignedInteger('id_rol');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('correo')->unique();
            $table->string('telefono')->nullable();
            $table->string('usuario')->unique();
            $table->string('password');
            $table->boolean('activo')->default(true);
            $table->boolean('debe_cambiar_password')->default(false);
            $table->dateTime('fecha_ultimo_cambio_password')->nullable();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->increments('id_permiso');
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('modulo');
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
        });

        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_permiso');
            $table->primary(['id_rol', 'id_permiso']);
        });
    }

    public function test_no_se_puede_asignar_un_rol_inactivo_al_crear_un_usuario(): void
    {
        $actor = $this->crearActorConPermisos(['usuarios.crear']);
        $rolInactivo = $this->crearRol('INACTIVO', false);

        $response = $this->actingAs($actor)->post('/usuarios', $this->datosUsuario($rolInactivo));

        $response->assertSessionHasErrors('id_rol');
        $this->assertDatabaseCount('usuarios', 1);
    }

    public function test_no_se_puede_asignar_un_rol_inactivo_al_editar_un_usuario(): void
    {
        $actor = $this->crearActorConPermisos(['usuarios.editar']);
        $rolActivo = $this->crearRol('OPERADOR');
        $rolInactivo = $this->crearRol('INACTIVO', false);
        $usuario = $this->crearUsuario($rolActivo);

        $response = $this->actingAs($actor)->put(
            '/usuarios/'.$usuario->id_usuario,
            $this->datosUsuario($rolInactivo, 'edicion')
        );

        $response->assertSessionHasErrors('id_rol');
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $usuario->id_usuario,
            'id_rol' => $rolActivo->id_rol,
        ]);
    }

    public function test_un_usuario_con_rol_inactivo_no_tiene_permisos_efectivos(): void
    {
        $rol = $this->crearRol('INACTIVO', false);
        $this->otorgarPermisos($rol, ['usuarios.ver']);
        $usuario = $this->crearUsuario($rol);

        $this->assertFalse($usuario->fresh()->tienePermiso('usuarios.ver'));

        $usuarioConRelaciones = $usuario->fresh()->load('rol.permisos');
        $this->assertFalse($usuarioConRelaciones->tienePermiso('usuarios.ver'));
    }

    public function test_no_se_puede_desactivar_un_rol_con_usuarios_activos_desde_edicion(): void
    {
        $actor = $this->crearActorConPermisos(['roles.editar', 'roles.desactivar']);
        $rol = $this->crearRol('OPERADOR');
        $this->crearUsuario($rol);

        $response = $this->actingAs($actor)->put('/roles/'.$rol->id_rol, [
            'nombre' => $rol->nombre,
            'descripcion' => $rol->descripcion,
            'activo' => '0',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', [
            'id_rol' => $rol->id_rol,
            'activo' => 1,
        ]);
    }

    public function test_no_se_puede_desactivar_un_rol_con_usuarios_activos_desde_la_accion_especifica(): void
    {
        $actor = $this->crearActorConPermisos(['roles.desactivar']);
        $rol = $this->crearRol('OPERADOR');
        $this->crearUsuario($rol);

        $response = $this->actingAs($actor)->delete('/roles/'.$rol->id_rol);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', [
            'id_rol' => $rol->id_rol,
            'activo' => 1,
        ]);
    }

    public function test_un_usuario_no_puede_desactivar_su_propia_cuenta(): void
    {
        $actor = $this->crearActorConPermisos(['usuarios.desactivar']);

        $response = $this->actingAs($actor)->delete('/usuarios/'.$actor->id_usuario);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $actor->id_usuario,
            'activo' => 1,
        ]);
    }

    public function test_un_usuario_no_puede_desactivarse_a_si_mismo_mediante_edicion(): void
    {
        $actor = $this->crearActorConPermisos([
            'usuarios.editar',
            'usuarios.desactivar',
        ]);

        $response = $this->actingAs($actor)->put(
            '/usuarios/'.$actor->id_usuario,
            $this->datosUsuario($actor->rol, 'propio', ['activo' => '0'])
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $actor->id_usuario,
            'activo' => 1,
        ]);
    }

    public function test_no_se_puede_desactivar_al_ultimo_administrador_activo(): void
    {
        $actor = $this->crearActorConPermisos(['usuarios.desactivar']);
        $rolAdministrador = $this->crearRol('ADMINISTRADOR');
        $administrador = $this->crearUsuario($rolAdministrador);

        $response = $this->actingAs($actor)->delete('/usuarios/'.$administrador->id_usuario);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $administrador->id_usuario,
            'activo' => 1,
        ]);
    }

    public function test_el_ultimo_administrador_no_puede_cambiar_a_un_rol_no_administrativo_y_no_hay_cambios_parciales(): void
    {
        $actor = $this->crearActorConPermisos(['usuarios.editar']);
        $rolAdministrador = $this->crearRol('ADMINISTRADOR');
        $rolOperador = $this->crearRol('OPERADOR');
        $administrador = $this->crearUsuario($rolAdministrador, true, [
            'nombres' => 'Admin Original',
        ]);

        $response = $this->actingAs($actor)->put(
            '/usuarios/'.$administrador->id_usuario,
            $this->datosUsuario($rolOperador, 'sin-cambios', [
                'nombres' => 'Nombre No Debe Guardarse',
            ])
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $administrador->id_usuario,
            'id_rol' => $rolAdministrador->id_rol,
            'nombres' => 'Admin Original',
        ]);
    }

    public function test_no_se_puede_renombrar_el_rol_administrador_con_usuarios_activos(): void
    {
        $rolAdministrador = $this->crearRol('ADMINISTRADOR');
        $this->otorgarPermisos($rolAdministrador, ['roles.editar']);
        $actor = $this->crearUsuario($rolAdministrador);

        $response = $this->actingAs($actor)->put('/roles/'.$rolAdministrador->id_rol, [
            'nombre' => 'SUPERADMIN',
            'descripcion' => $rolAdministrador->descripcion,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', [
            'id_rol' => $rolAdministrador->id_rol,
            'nombre' => 'ADMINISTRADOR',
        ]);
    }

    public function test_con_varios_administradores_se_puede_desactivar_a_otro_administrador(): void
    {
        $rolAdministrador = $this->crearRol('ADMINISTRADOR');
        $this->otorgarPermisos($rolAdministrador, ['usuarios.desactivar']);
        $actor = $this->crearUsuario($rolAdministrador);
        $otroAdministrador = $this->crearUsuario($rolAdministrador);

        $response = $this->actingAs($actor)->delete('/usuarios/'.$otroAdministrador->id_usuario);

        $response->assertRedirect('/usuarios');
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $otroAdministrador->id_usuario,
            'activo' => 0,
        ]);
    }

    private function crearActorConPermisos(array $permisos): Usuario
    {
        $rol = $this->crearRol('GESTOR '.$this->secuencia);
        $this->otorgarPermisos($rol, $permisos);

        return $this->crearUsuario($rol);
    }

    private function crearRol(string $nombre, bool $activo = true): Rol
    {
        return Rol::create([
            'nombre' => $nombre,
            'descripcion' => 'Rol de pruebas',
            'activo' => $activo,
        ]);
    }

    private function crearUsuario(
        Rol $rol,
        bool $activo = true,
        array $atributos = []
    ): Usuario {
        $this->secuencia++;

        return Usuario::create(array_merge([
            'id_rol' => $rol->id_rol,
            'nombres' => 'Usuario '.$this->secuencia,
            'apellidos' => 'Prueba',
            'correo' => 'usuario'.$this->secuencia.'@prueba.test',
            'telefono' => '5555-0000',
            'usuario' => 'usuario'.$this->secuencia,
            'password' => Hash::make('Password@123'),
            'activo' => $activo,
        ], $atributos));
    }

    private function otorgarPermisos(Rol $rol, array $codigos): void
    {
        foreach ($codigos as $codigo) {
            $permiso = Permiso::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $codigo,
                    'modulo' => 'PRUEBAS',
                    'descripcion' => 'Permiso de pruebas',
                    'activo' => true,
                ]
            );

            $rol->permisos()->syncWithoutDetaching([$permiso->id_permiso]);
        }
    }

    private function datosUsuario(
        Rol $rol,
        string $sufijo = '',
        array $sobrescribir = []
    ): array {
        $this->secuencia++;

        return array_merge([
            'nombres' => 'Nuevo '.$sufijo,
            'apellidos' => 'Usuario',
            'correo' => 'nuevo'.$this->secuencia.'@prueba.test',
            'telefono' => '5555-0001',
            'usuario' => 'nuevo'.$this->secuencia,
            'id_rol' => $rol->id_rol,
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'activo' => '1',
        ], $sobrescribir);
    }
}
