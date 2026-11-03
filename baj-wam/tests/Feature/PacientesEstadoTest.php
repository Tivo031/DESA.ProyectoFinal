<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Rol;
use App\Models\Usuario;
use Database\Seeders\PacientesPermisosSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PacientesEstadoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!app()->environment('testing')
            || config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Estas pruebas solo se ejecutan en SQLite en memoria.');
        }

        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        Schema::disableForeignKeyConstraints();
        foreach (['consulta_productos', 'consultas', 'solicitudes_cita', 'citas', 'pacientes', 'especialistas', 'estados_cita', 'servicios', 'productos', 'rol_permiso', 'permisos', 'usuarios', 'roles'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
        Schema::enableForeignKeyConstraints();

        Schema::create('roles', function (Blueprint $t) {
            $t->increments('id_rol');
            $t->string('nombre')->unique();
            $t->boolean('activo')->default(true);
        });
        Schema::create('usuarios', function (Blueprint $t) {
            $t->increments('id_usuario'); $t->unsignedInteger('id_rol'); $t->string('nombres'); $t->string('apellidos');
            $t->string('correo')->unique(); $t->string('telefono')->nullable(); $t->string('usuario')->unique(); $t->string('password');
            $t->boolean('activo')->default(true); $t->boolean('debe_cambiar_password')->default(false);
            $t->dateTime('fecha_ultimo_cambio_password')->nullable(); $t->dateTime('fecha_creacion')->nullable(); $t->dateTime('fecha_actualizacion')->nullable();
        });
        Schema::create('permisos', function (Blueprint $t) { $t->increments('id_permiso'); $t->string('codigo')->unique(); $t->string('nombre'); $t->string('modulo'); $t->string('descripcion')->nullable(); $t->boolean('activo')->default(true); });
        Schema::create('rol_permiso', function (Blueprint $t) { $t->unsignedInteger('id_rol'); $t->unsignedInteger('id_permiso'); $t->primary(['id_rol', 'id_permiso']); });
        Schema::create('servicios', function (Blueprint $t) { $t->increments('id_servicio'); $t->string('nombre'); $t->string('descripcion')->nullable(); $t->integer('duracion_minutos'); $t->decimal('precio', 10, 2)->nullable(); $t->boolean('activo')->default(true); $t->dateTime('fecha_creacion')->nullable(); });
        Schema::create('productos', function (Blueprint $t) { $t->increments('id_producto'); $t->string('nombre'); $t->boolean('activo')->default(true); });
        Schema::create('especialistas', function (Blueprint $t) { $t->increments('id_especialista'); $t->unsignedInteger('id_usuario'); $t->string('profesion'); $t->boolean('activo')->default(true); });
        Schema::create('estados_cita', function (Blueprint $t) { $t->increments('id_estado_cita'); $t->string('codigo')->unique(); });
        Schema::create('pacientes', function (Blueprint $t) {
            $t->increments('id_paciente'); $t->unsignedInteger('id_usuario_registro'); $t->char('dpi', 13)->nullable()->unique();
            $t->string('nombres', 80); $t->string('apellidos', 80); $t->date('fecha_nacimiento')->nullable(); $t->string('sexo')->nullable();
            $t->string('telefono', 20); $t->string('correo', 120)->nullable(); $t->string('direccion', 250)->nullable(); $t->boolean('activo')->default(true);
            $t->dateTime('fecha_registro')->nullable(); $t->dateTime('fecha_actualizacion')->nullable();
        });
        Schema::create('citas', function (Blueprint $t) {
            $t->increments('id_cita'); $t->unsignedInteger('id_paciente'); $t->unsignedInteger('id_especialista'); $t->unsignedInteger('id_servicio'); $t->unsignedInteger('id_estado_cita'); $t->unsignedInteger('id_usuario_registro');
            $t->dateTime('inicio'); $t->dateTime('fin'); $t->string('observaciones', 500)->nullable(); $t->string('motivo_cancelacion', 250)->nullable(); $t->dateTime('fecha_creacion')->nullable(); $t->dateTime('fecha_actualizacion')->nullable();
        });
        Schema::create('solicitudes_cita', function (Blueprint $t) {
            $t->increments('id_solicitud'); $t->unsignedInteger('id_servicio'); $t->unsignedInteger('id_cita')->nullable()->unique(); $t->string('nombre', 160); $t->string('telefono', 20); $t->char('dpi', 13)->nullable();
            $t->string('numero_expediente', 20)->nullable();
            $t->date('fecha'); $t->time('hora'); $t->string('comentario', 500)->nullable(); $t->string('estado')->default('PENDIENTE'); $t->dateTime('fecha_solicitud')->nullable(); $t->dateTime('fecha_respuesta')->nullable();
        });
        Schema::create('consultas', function (Blueprint $t) { $t->increments('id_consulta'); $t->unsignedInteger('id_cita')->unique(); $t->unsignedInteger('id_usuario_registro'); $t->dateTime('fecha_consulta'); $t->text('motivo_consulta'); $t->text('observaciones')->nullable(); $t->text('diagnostico')->nullable(); $t->text('tratamiento_realizado'); $t->text('recomendaciones')->nullable(); $t->dateTime('fecha_actualizacion')->nullable(); });
        Schema::create('consulta_productos', function (Blueprint $t) { $t->unsignedInteger('id_consulta'); $t->unsignedInteger('id_producto'); });
    }


    public function test_activar_y_desactivar_conserva_el_mismo_registro(): void
    {
        $actor = $this->actor(['pacientes.ver', 'pacientes.editar', 'pacientes.desactivar']);
        $paciente = $this->paciente($actor);

        $this->actingAs($actor)->from('/pacientes')->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => '0'])
            ->assertRedirect('/pacientes')->assertSessionHas('success');
        $this->assertDatabaseHas('pacientes', ['id_paciente' => $paciente->id_paciente, 'activo' => 0]);

        $this->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => '1'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('pacientes', ['id_paciente' => $paciente->id_paciente, 'activo' => 1]);
        $this->assertDatabaseCount('pacientes', 1);
    }

    public function test_no_alterna_el_estado_si_la_misma_peticion_se_repite(): void
    {
        $actor = $this->actor(['pacientes.desactivar']);
        $paciente = $this->paciente($actor);
        $this->actingAs($actor);
        foreach ([1, 2] as $intento) {
            $this->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => '0'])
                ->assertSessionHas('success');
        }
        $this->assertDatabaseHas('pacientes', ['id_paciente' => $paciente->id_paciente, 'activo' => 0]);
    }

    public function test_exige_permiso_de_estado_aunque_tenga_permiso_de_edicion(): void
    {
        $actor = $this->actor(['pacientes.ver', 'pacientes.editar']);
        $paciente = $this->paciente($actor);
        $this->actingAs($actor)->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => 0])->assertForbidden();
        $this->delete('/pacientes/'.$paciente->id_paciente)->assertForbidden();
        $this->patch('/pacientes/'.$paciente->id_paciente.'/activar')->assertForbidden();
        $this->assertDatabaseHas('pacientes', ['id_paciente' => $paciente->id_paciente, 'activo' => 1]);
    }

    public function test_las_rutas_anteriores_siguen_funcionando_con_el_permiso_correcto(): void
    {
        $actor = $this->actor(['pacientes.desactivar']);
        $paciente = $this->paciente($actor);
        $this->actingAs($actor)->delete('/pacientes/'.$paciente->id_paciente)->assertSessionHas('success');
        $this->assertFalse($paciente->fresh()->activo);
        $this->patch('/pacientes/'.$paciente->id_paciente.'/activar')->assertSessionHas('success');
        $this->assertTrue($paciente->fresh()->activo);
    }

    public function test_valida_el_estado_y_no_permite_modificar_otros_campos(): void
    {
        $actor = $this->actor(['pacientes.desactivar']);
        $paciente = $this->paciente($actor);
        $this->actingAs($actor)->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => 'incorrecto'])
            ->assertSessionHasErrors('activo');
        $this->patch('/pacientes/'.$paciente->id_paciente.'/estado', [])->assertSessionHasErrors('activo');
        $this->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => '0', 'nombres' => 'Cambiar'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('pacientes', ['id_paciente' => $paciente->id_paciente, 'nombres' => 'Paciente']);
    }

    public function test_editar_permanece_en_listado_y_detalle_de_activos_e_inactivos(): void
    {
        $actor = $this->actor(['pacientes.ver', 'pacientes.editar', 'pacientes.desactivar']);
        $paciente = $this->paciente($actor);
        $this->actingAs($actor);

        foreach ([true, false] as $activo) {
            $paciente->update(['activo' => $activo]);
            foreach (['/pacientes', '/pacientes/'.$paciente->id_paciente] as $url) {
                $this->get($url)->assertOk()
                    ->assertSee(route('pacientes.edit', $paciente->id_paciente), false)
                    ->assertSee($activo ? 'Desactivar paciente' : 'Activar paciente')
                    ->assertSee('name="activo" value="'.($activo ? '0' : '1').'"', false)
                    ->assertSee('pacientes-estado.js');
            }
            $this->get('/pacientes/'.$paciente->id_paciente.'/edit')->assertOk();
        }
    }

    public function test_permiso_de_solo_lectura_no_muestra_editar_ni_estado(): void
    {
        $actor = $this->actor(['pacientes.ver']);
        $this->paciente($actor);
        $this->actingAs($actor)->get('/pacientes')->assertOk()
            ->assertDontSee('title="Editar paciente"', false)
            ->assertDontSee('data-paciente-estado', false);
    }

    public function test_seed_es_aditivo_e_idempotente(): void
    {
        $actor = $this->actor(['productos.ver']);
        $recepcion = Rol::create(['nombre' => 'RECEPCION', 'activo' => true]);
        $especialista = Rol::create(['nombre' => 'ESPECIALISTA', 'activo' => true]);
        $this->seed(PacientesPermisosSeeder::class);
        $conteo = DB::table('rol_permiso')->count();
        $this->seed(PacientesPermisosSeeder::class);
        $this->assertSame($conteo, DB::table('rol_permiso')->count());
        $this->assertTrue($actor->fresh()->tienePermiso('productos.ver'));
        $this->assertTrue($actor->fresh()->tienePermiso('pacientes.editar'));
        $this->assertTrue($actor->fresh()->tienePermiso('pacientes.desactivar'));
        $this->assertSame(4, DB::table('rol_permiso')->where('id_rol', $recepcion->id_rol)->count());
        $this->assertSame(0, DB::table('rol_permiso')->where('id_rol', $especialista->id_rol)->count());
    }

    public function test_un_visitante_no_puede_cambiar_el_estado(): void
    {
        $actor = $this->actor([]);
        $paciente = $this->paciente($actor);
        $this->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => 0])->assertRedirect('/login');
        $this->assertTrue($paciente->fresh()->activo);
    }

    public function test_cambiar_estado_no_borra_citas_ni_consultas(): void
    {
        $actor = $this->actor(['pacientes.desactivar']);
        $paciente = $this->paciente($actor);
        $idCita = DB::table('citas')->insertGetId([
            'id_paciente' => $paciente->id_paciente, 'id_especialista' => 1,
            'id_servicio' => 1, 'id_estado_cita' => 1, 'id_usuario_registro' => $actor->id_usuario,
            'inicio' => now(), 'fin' => now()->addHour(),
        ]);
        $idConsulta = DB::table('consultas')->insertGetId([
            'id_cita' => $idCita, 'id_usuario_registro' => $actor->id_usuario,
            'fecha_consulta' => now(), 'motivo_consulta' => 'Prueba', 'tratamiento_realizado' => 'Prueba',
        ]);
        $this->actingAs($actor)->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => 0])->assertSessionHas('success');
        $this->patch('/pacientes/'.$paciente->id_paciente.'/estado', ['activo' => 1])->assertSessionHas('success');
        $this->assertDatabaseHas('citas', ['id_cita' => $idCita, 'id_paciente' => $paciente->id_paciente]);
        $this->assertDatabaseHas('consultas', ['id_consulta' => $idConsulta, 'id_cita' => $idCita]);
    }

    private function actor(array $codigos): Usuario
    {
        $rol = Rol::create(['nombre' => 'ADMINISTRADOR', 'activo' => true]);
        foreach ($codigos as $codigo) {
            $idPermiso = DB::table('permisos')->insertGetId([
                'codigo' => $codigo, 'nombre' => $codigo, 'modulo' => 'PRUEBA', 'activo' => true,
            ]);
            DB::table('rol_permiso')->insert(['id_rol' => $rol->id_rol, 'id_permiso' => $idPermiso]);
        }
        return Usuario::create([
            'id_rol' => $rol->id_rol, 'nombres' => 'Admin', 'apellidos' => 'Prueba',
            'correo' => 'admin@example.test', 'usuario' => 'admin', 'password' => 'no-utilizada',
            'activo' => true, 'debe_cambiar_password' => false,
        ]);
    }

    private function paciente(Usuario $usuario): Paciente
    {
        return Paciente::create([
            'id_usuario_registro' => $usuario->id_usuario, 'nombres' => 'Paciente', 'apellidos' => 'Prueba',
            'telefono' => '5555-5555', 'activo' => true,
        ]);
    }
}
