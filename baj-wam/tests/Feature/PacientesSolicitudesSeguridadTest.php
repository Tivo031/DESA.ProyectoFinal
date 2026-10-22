<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Especialista;
use App\Models\EstadoCita;
use App\Models\Paciente;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\SolicitudCita;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PacientesSolicitudesSeguridadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
        Schema::create('permisos', function (Blueprint $t) { $t->increments('id_permiso'); $t->string('codigo')->unique(); $t->string('nombre'); $t->string('modulo'); $t->boolean('activo')->default(true); });
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

    public function test_la_solicitud_publica_persiste_un_dpi_valido_y_admite_ausencia_de_dpi(): void
    {
        $servicio = $this->servicio();
        $datos = $this->datosSolicitud($servicio, ['dpi' => '1234567890123']);

        $this->post('/solicitudes-cita', $datos)->assertRedirect(route('inicio').'#cita');
        $this->assertDatabaseHas('solicitudes_cita', ['dpi' => '1234567890123', 'estado' => 'PENDIENTE']);

        $this->post('/solicitudes-cita', $this->datosSolicitud($servicio, ['dpi' => null]))->assertRedirect(route('inicio').'#cita');
        $this->assertDatabaseCount('solicitudes_cita', 2);
    }

    public function test_la_solicitud_publica_rechaza_dpi_invalido(): void
    {
        $response = $this->post('/solicitudes-cita', $this->datosSolicitud($this->servicio(), ['dpi' => '123']));

        $response->assertSessionHasErrors('dpi');
        $this->assertDatabaseCount('solicitudes_cita', 0);
    }

    public function test_la_solicitud_publica_acepta_expediente_normalizado_sin_revelar_coincidencias(): void
    {
        $actor = $this->usuario([]);
        $paciente = $this->paciente($actor, ['nombres' => 'Dato Privado']);

        $response = $this->post('/solicitudes-cita', $this->datosSolicitud($this->servicio(), [
            'dpi' => null,
            'numero_expediente' => strtolower($paciente->numero_expediente),
        ]));

        $response->assertRedirect(route('inicio').'#cita')->assertDontSee('Dato Privado');
        $this->assertDatabaseHas('solicitudes_cita', ['numero_expediente' => $paciente->numero_expediente]);
    }

    public function test_la_solicitud_publica_admite_identificadores_vacios_y_rechaza_expediente_invalido(): void
    {
        $servicio = $this->servicio();

        $this->post('/solicitudes-cita', $this->datosSolicitud($servicio, ['dpi' => null, 'numero_expediente' => null]))
            ->assertRedirect(route('inicio').'#cita');
        $this->post('/solicitudes-cita', $this->datosSolicitud($servicio, ['numero_expediente' => 'DROP TABLE pacientes']))
            ->assertSessionHasErrors('numero_expediente');
    }

    public function test_el_numero_de_expediente_se_deriva_del_id_sin_limite_de_seis_digitos(): void
    {
        $paciente = new Paciente(['nombres' => 'Paciente', 'apellidos' => 'Prueba']);
        $paciente->id_paciente = 1234567;

        $this->assertSame('PAC-1234567', $paciente->numero_expediente);
    }

    public function test_el_buscador_interno_exige_permiso_y_no_es_publico(): void
    {
        $this->get('/pacientes/buscar?q=Ana')->assertRedirect('/login');

        $actor = $this->usuario(['pacientes.ver']);
        $this->paciente($actor, ['nombres' => 'Ana']);

        $this->actingAs($actor)->getJson('/pacientes/buscar?q=Ana')
            ->assertOk()
            ->assertJsonPath('0.nombre_completo', 'Ana Prueba');
    }

    public function test_el_buscador_interno_localiza_por_numero_de_expediente(): void
    {
        $actor = $this->usuario(['pacientes.ver']);
        $paciente = $this->paciente($actor);

        $this->actingAs($actor)->getJson('/pacientes/buscar?q='.$paciente->numero_expediente)
            ->assertOk()
            ->assertJsonPath('0.id_paciente', $paciente->id_paciente)
            ->assertJsonPath('0.numero_expediente', $paciente->numero_expediente);
    }

    public function test_el_procesamiento_sugiere_coincidencia_exacta_por_dpi(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $paciente = $this->paciente($actor, ['dpi' => '1234567890123']);
        $solicitud = $this->solicitud($this->servicio(), ['dpi' => $paciente->dpi]);

        $this->actingAs($actor)->get('/solicitudes-cita/'.$solicitud->id_solicitud.'/procesar')
            ->assertOk()
            ->assertSee($paciente->nombre_completo);
    }

    public function test_el_procesamiento_identifica_paciente_sin_dpi_por_expediente(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $paciente = $this->paciente($actor, ['dpi' => null]);
        $solicitud = $this->solicitud($this->servicio(), ['numero_expediente' => $paciente->numero_expediente]);

        $this->actingAs($actor)->get('/solicitudes-cita/'.$solicitud->id_solicitud.'/procesar')
            ->assertOk()
            ->assertSee($paciente->nombre_completo)
            ->assertSee('confirmar_identidad');
    }

    public function test_identificadores_coincidentes_exigen_confirmacion_y_los_contradictorios_se_bloquean(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $especialista = $this->especialista($actor);
        $this->estadoConfirmada();
        $uno = $this->paciente($actor, ['dpi' => '1234567890123']);
        $dos = $this->paciente($actor, ['dpi' => '3210987654321']);
        $solicitud = $this->solicitud($this->servicio(), ['dpi' => $uno->dpi, 'numero_expediente' => $uno->numero_expediente]);
        $datos = $this->datosAprobacion($especialista, ['tipo_paciente' => 'existente', 'id_paciente' => $uno->id_paciente]);

        $this->actingAs($actor)->post('/solicitudes-cita/'.$solicitud->id_solicitud.'/aprobar', $datos)
            ->assertSessionHasErrors('confirmar_identidad');
        $this->actingAs($actor)->post('/solicitudes-cita/'.$solicitud->id_solicitud.'/aprobar', $datos + ['confirmar_identidad' => '1'])
            ->assertRedirect();
        $this->assertDatabaseHas('citas', ['id_paciente' => $uno->id_paciente]);

        $contradictoria = $this->solicitud($this->servicio(), ['dpi' => $uno->dpi, 'numero_expediente' => $dos->numero_expediente]);
        $this->actingAs($actor)->post('/solicitudes-cita/'.$contradictoria->id_solicitud.'/aprobar', $this->datosAprobacion($especialista, [
            'tipo_paciente' => 'existente', 'id_paciente' => $dos->id_paciente, 'confirmar_identidad' => '1',
        ]))->assertSessionHasErrors('identificacion');
        $this->assertDatabaseCount('citas', 1);
    }

    public function test_expediente_inactivo_no_se_puede_usar_para_crear_una_cita(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $especialista = $this->especialista($actor);
        $paciente = $this->paciente($actor, ['activo' => false]);
        $solicitud = $this->solicitud($this->servicio(), ['numero_expediente' => $paciente->numero_expediente]);

        $this->actingAs($actor)->post('/solicitudes-cita/'.$solicitud->id_solicitud.'/aprobar', $this->datosAprobacion($especialista, [
            'tipo_paciente' => 'existente', 'id_paciente' => $paciente->id_paciente, 'confirmar_identidad' => '1',
        ]))->assertSessionHasErrors('id_paciente');
        $this->assertDatabaseCount('citas', 0);
    }

    public function test_no_se_puede_asociar_ni_duplicar_un_paciente_inactivo_por_solicitud(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $especialista = $this->especialista($actor);
        $paciente = $this->paciente($actor, ['dpi' => '1234567890123', 'activo' => false]);
        $solicitud = $this->solicitud($this->servicio(), ['dpi' => $paciente->dpi]);

        $response = $this->actingAs($actor)->post('/solicitudes-cita/'.$solicitud->id_solicitud.'/aprobar', $this->datosAprobacion($especialista, ['tipo_paciente' => 'existente', 'id_paciente' => $paciente->id_paciente]));

        $response->assertSessionHasErrors('id_paciente');
        $this->assertDatabaseCount('citas', 0);
        $this->assertDatabaseCount('pacientes', 1);
    }

    public function test_aprobar_crea_paciente_y_cita_de_forma_integrada(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $especialista = $this->especialista($actor);
        $this->estadoConfirmada();
        $solicitud = $this->solicitud($this->servicio(), ['dpi' => '1234567890123']);

        $this->actingAs($actor)->post('/solicitudes-cita/'.$solicitud->id_solicitud.'/aprobar', $this->datosAprobacion($especialista, ['dpi' => $solicitud->dpi]))
            ->assertRedirect();

        $this->assertDatabaseHas('pacientes', ['dpi' => $solicitud->dpi, 'telefono' => '55555555']);
        $this->assertDatabaseHas('solicitudes_cita', ['id_solicitud' => $solicitud->id_solicitud, 'estado' => 'APROBADA']);
        $this->assertDatabaseCount('citas', 1);
    }

    public function test_no_se_crea_un_duplicado_por_dpi_y_no_se_procesa_dos_veces(): void
    {
        $actor = $this->usuario(['solicitudes.procesar']);
        $especialista = $this->especialista($actor);
        $this->paciente($actor, ['dpi' => '1234567890123']);
        $solicitud = $this->solicitud($this->servicio(), ['dpi' => null]);

        $this->actingAs($actor)->post('/solicitudes-cita/'.$solicitud->id_solicitud.'/aprobar', $this->datosAprobacion($especialista, ['dpi' => '1234567890123']))
            ->assertSessionHasErrors('dpi');
        $this->assertDatabaseCount('pacientes', 1);
        $this->assertDatabaseCount('citas', 0);
    }

    public function test_la_edicion_del_paciente_muestra_errores_de_validacion_por_campo(): void
    {
        $actor = $this->usuario(['pacientes.editar']);
        $existente = $this->paciente($actor, ['dpi' => '1234567890123']);
        $objetivo = $this->paciente($actor, ['dpi' => '3210987654321']);

        $this->actingAs($actor)->put('/pacientes/'.$objetivo->id_paciente, [
            'dpi' => $existente->dpi, 'nombres' => 'Paciente', 'apellidos' => 'Prueba', 'telefono' => '55555555',
        ])->assertSessionHasErrors('dpi');
    }

    public function test_un_especialista_no_puede_ver_ni_registrar_consultas_ajenas(): void
    {
        $especialistaUsuario = $this->usuario(['consultas.ver', 'consultas.crear'], 'ESPECIALISTA');
        $especialista = $this->especialista($especialistaUsuario);
        $otroUsuario = $this->usuario([], 'RECEPCION');
        $otroEspecialista = $this->especialista($otroUsuario);
        $servicio = $this->servicio();
        $confirmada = $this->estadoConfirmada();
        $paciente = $this->paciente($otroUsuario);
        $citaAjena = $this->cita($paciente, $otroEspecialista, $servicio, $confirmada, $otroUsuario);

        $consultaId = \DB::table('consultas')->insertGetId([
            'id_cita' => $citaAjena->id_cita, 'id_usuario_registro' => $otroUsuario->id_usuario,
            'fecha_consulta' => now(), 'motivo_consulta' => 'Dato clínico ajeno', 'tratamiento_realizado' => 'Tratamiento',
        ]);

        $this->actingAs($especialistaUsuario)->get('/consultas/'.$consultaId)
            ->assertRedirect('/consultas');

        $citaNuevaAjena = $this->cita($paciente, $otroEspecialista, $servicio, $confirmada, $otroUsuario, '11:00');
        $this->actingAs($especialistaUsuario)->post('/consultas', [
            'id_cita' => $citaNuevaAjena->id_cita, 'fecha_consulta' => now()->format('Y-m-d H:i:s'),
            'motivo_consulta' => 'Intento ajeno', 'tratamiento_realizado' => 'Tratamiento',
        ])->assertRedirect();
        $this->assertDatabaseMissing('consultas', ['id_cita' => $citaNuevaAjena->id_cita]);
    }

    private function usuario(array $permisos, string $rolNombre = 'RECEPCION'): Usuario
    {
        $rol = Rol::create(['nombre' => $rolNombre, 'activo' => true]);
        foreach ($permisos as $codigo) {
            $id = \DB::table('permisos')->insertGetId(['codigo' => $codigo, 'nombre' => $codigo, 'modulo' => 'PRUEBA', 'activo' => true]);
            \DB::table('rol_permiso')->insert(['id_rol' => $rol->id_rol, 'id_permiso' => $id]);
        }
        return Usuario::create(['id_rol' => $rol->id_rol, 'nombres' => 'Usuario', 'apellidos' => 'Prueba', 'correo' => uniqid().'@test.local', 'telefono' => '55555555', 'usuario' => uniqid('user'), 'password' => 'secreto', 'activo' => true]);
    }

    private function servicio(): Servicio { return Servicio::create(['nombre' => 'Consulta', 'duracion_minutos' => 30, 'activo' => true]); }
    private function especialista(Usuario $usuario): Especialista { return Especialista::create(['id_usuario' => $usuario->id_usuario, 'profesion' => 'Especialista', 'activo' => true]); }
    private function estadoConfirmada(): EstadoCita { return EstadoCita::firstOrCreate(['codigo' => 'CONFIRMADA']); }
    private function cita(Paciente $paciente, Especialista $especialista, Servicio $servicio, EstadoCita $estado, Usuario $usuario, string $hora = '10:00'): Cita { $inicio = now()->addDay()->setTimeFromTimeString($hora); return Cita::create(['id_paciente' => $paciente->id_paciente, 'id_especialista' => $especialista->id_especialista, 'id_servicio' => $servicio->id_servicio, 'id_estado_cita' => $estado->id_estado_cita, 'id_usuario_registro' => $usuario->id_usuario, 'inicio' => $inicio, 'fin' => $inicio->copy()->addMinutes(30)]); }
    private function paciente(Usuario $usuario, array $atributos = []): Paciente { return Paciente::create(array_merge(['id_usuario_registro' => $usuario->id_usuario, 'nombres' => 'Paciente', 'apellidos' => 'Prueba', 'telefono' => '55555555', 'activo' => true], $atributos)); }
    private function solicitud(Servicio $servicio, array $atributos = []): SolicitudCita { return SolicitudCita::create(array_merge(['id_servicio' => $servicio->id_servicio, 'nombre' => 'Paciente Prueba', 'telefono' => '55555555', 'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00', 'estado' => 'PENDIENTE'], $atributos)); }
    private function datosSolicitud(Servicio $servicio, array $atributos = []): array { return array_merge(['nombre' => 'Paciente Público', 'telefono' => '55555555', 'id_servicio' => $servicio->id_servicio, 'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00'], $atributos); }
    private function datosAprobacion(Especialista $especialista, array $atributos = []): array { return array_merge(['tipo_paciente' => 'nuevo', 'nombres' => 'Nuevo', 'apellidos' => 'Paciente', 'telefono' => '55555555', 'id_especialista' => $especialista->id_especialista, 'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00'], $atributos); }
}
