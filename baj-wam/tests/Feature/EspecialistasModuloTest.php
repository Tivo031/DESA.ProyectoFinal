<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Especialista;
use App\Models\EstadoCita;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EspecialistasModuloTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();
        foreach (['citas', 'estados_cita', 'especialistas', 'rol_permiso', 'permisos', 'usuarios', 'roles'] as $tabla) Schema::dropIfExists($tabla);
        Schema::enableForeignKeyConstraints();
        Schema::create('roles', function (Blueprint $t) { $t->increments('id_rol'); $t->string('nombre')->unique(); $t->boolean('activo')->default(true); });
        Schema::create('usuarios', function (Blueprint $t) {
            $t->increments('id_usuario'); $t->unsignedInteger('id_rol'); $t->string('nombres'); $t->string('apellidos'); $t->string('correo')->unique(); $t->string('telefono')->nullable(); $t->string('usuario')->unique(); $t->string('password'); $t->boolean('activo')->default(true); $t->boolean('debe_cambiar_password')->default(false); $t->dateTime('fecha_ultimo_cambio_password')->nullable(); $t->dateTime('fecha_creacion')->nullable(); $t->dateTime('fecha_actualizacion')->nullable();
        });
        Schema::create('permisos', function (Blueprint $t) { $t->increments('id_permiso'); $t->string('codigo')->unique(); $t->string('nombre'); $t->string('modulo'); $t->boolean('activo')->default(true); });
        Schema::create('rol_permiso', function (Blueprint $t) { $t->unsignedInteger('id_rol'); $t->unsignedInteger('id_permiso'); $t->primary(['id_rol', 'id_permiso']); });
        Schema::create('especialistas', function (Blueprint $t) { $t->increments('id_especialista'); $t->unsignedInteger('id_usuario')->unique(); $t->string('profesion', 100); $t->boolean('activo')->default(true); });
        Schema::create('estados_cita', function (Blueprint $t) { $t->increments('id_estado_cita'); $t->string('codigo')->unique(); });
        Schema::create('citas', function (Blueprint $t) { $t->increments('id_cita'); $t->unsignedInteger('id_paciente'); $t->unsignedInteger('id_especialista'); $t->unsignedInteger('id_servicio'); $t->unsignedInteger('id_estado_cita'); $t->unsignedInteger('id_usuario_registro'); $t->dateTime('inicio'); $t->dateTime('fin'); $t->dateTime('fecha_creacion')->nullable(); $t->dateTime('fecha_actualizacion')->nullable(); });
    }

    public function test_crea_usuario_y_especialista_en_una_sola_operacion(): void
    {
        $actor = $this->usuarioConPermisos(['especialistas.crear']);
        $this->rol('ESPECIALISTA');

        $this->actingAs($actor)->post('/especialistas', $this->datosEspecialista())
            ->assertRedirect(route('especialistas.index'));

        $usuario = Usuario::where('usuario', 'especialista.test')->firstOrFail();
        $this->assertDatabaseHas('especialistas', ['id_usuario' => $usuario->id_usuario, 'profesion' => 'Fisioterapeuta', 'activo' => 1]);
    }

    public function test_la_profesion_es_obligatoria_y_permiso_especialista_no_habilita_usuarios_generales(): void
    {
        $actor = $this->usuarioConPermisos(['especialistas.crear']);
        $this->rol('ESPECIALISTA');

        $this->actingAs($actor)->post('/especialistas', $this->datosEspecialista(['profesion' => '']))->assertSessionHasErrors('profesion');
        $this->actingAs($actor)->post('/usuarios', $this->datosEspecialista())->assertForbidden();
        $this->assertDatabaseCount('especialistas', 0);
    }

    public function test_cambio_a_especialista_crea_o_actualiza_su_registro(): void
    {
        $actor = $this->usuarioConPermisos(['usuarios.editar', 'usuarios.desactivar']);
        $rolRecepcion = $this->rol('RECEPCION');
        $rolEspecialista = $this->rol('ESPECIALISTA');
        $usuario = $this->usuario($rolRecepcion);

        $this->actingAs($actor)->put('/usuarios/'.$usuario->id_usuario, $this->datosEdicion($usuario, ['id_rol' => $rolEspecialista->id_rol, 'profesion' => 'Acupunturista']))
            ->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('especialistas', ['id_usuario' => $usuario->id_usuario, 'profesion' => 'Acupunturista']);
    }

    public function test_no_permite_cambiar_rol_ni_desactivar_especialista_con_citas_futuras(): void
    {
        $actor = $this->usuarioConPermisos(['usuarios.editar', 'usuarios.desactivar']);
        $rolEspecialista = $this->rol('ESPECIALISTA');
        $rolRecepcion = $this->rol('RECEPCION');
        $usuario = $this->usuario($rolEspecialista);
        $especialista = Especialista::create(['id_usuario' => $usuario->id_usuario, 'profesion' => 'Fisioterapeuta', 'activo' => true]);
        $estado = EstadoCita::create(['codigo' => 'CONFIRMADA']);
        Cita::create(['id_paciente' => 1, 'id_especialista' => $especialista->id_especialista, 'id_servicio' => 1, 'id_estado_cita' => $estado->id_estado_cita, 'id_usuario_registro' => $actor->id_usuario, 'inicio' => now()->addDay(), 'fin' => now()->addDay()->addMinutes(30)]);

        $this->actingAs($actor)->put('/usuarios/'.$usuario->id_usuario, $this->datosEdicion($usuario, ['id_rol' => $rolRecepcion->id_rol]))
            ->assertSessionHas('error');
        $this->actingAs($actor)->delete('/usuarios/'.$usuario->id_usuario)->assertSessionHas('error');
        $this->assertTrue($usuario->fresh()->activo);
        $this->assertTrue($especialista->fresh()->activo);
    }

    private function rol(string $nombre): Rol { return Rol::firstOrCreate(['nombre' => $nombre], ['activo' => true]); }
    private function usuarioConPermisos(array $permisos): Usuario { $rol = $this->rol('ADMIN_PRUEBA_'.count($permisos)); foreach ($permisos as $permiso) { $id = \DB::table('permisos')->insertGetId(['codigo' => $permiso, 'nombre' => $permiso, 'modulo' => 'PRUEBA', 'activo' => true]); \DB::table('rol_permiso')->insert(['id_rol' => $rol->id_rol, 'id_permiso' => $id]); } return $this->usuario($rol); }
    private function usuario(Rol $rol): Usuario { return Usuario::create(['id_rol' => $rol->id_rol, 'nombres' => 'Usuario', 'apellidos' => 'Prueba', 'correo' => uniqid().'@test.local', 'telefono' => '5555-5555', 'usuario' => uniqid('user'), 'password' => 'secreto', 'activo' => true]); }
    private function datosEspecialista(array $extra = []): array { return array_merge(['nombres' => 'Ana', 'apellidos' => 'Especialista', 'correo' => 'especialista@test.local', 'telefono' => '5555-5555', 'usuario' => 'especialista.test', 'password' => 'ClaveSegura!1', 'password_confirmation' => 'ClaveSegura!1', 'activo' => 1, 'profesion' => 'Fisioterapeuta'], $extra); }
    private function datosEdicion(Usuario $usuario, array $extra = []): array { return array_merge(['nombres' => $usuario->nombres, 'apellidos' => $usuario->apellidos, 'correo' => $usuario->correo, 'telefono' => $usuario->telefono, 'usuario' => $usuario->usuario, 'id_rol' => $usuario->id_rol, 'activo' => 1, 'profesion' => 'Fisioterapeuta'], $extra); }
}
