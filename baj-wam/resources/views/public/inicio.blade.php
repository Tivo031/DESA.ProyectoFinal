@extends('layouts.public')

@section('title', 'Inicio')

@section('content')
<section class="hero-section" id="inicio">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="hero-kicker"><i class="bi bi-flower1"></i>Bienestar integral y medicina natural</span>
                <h1 class="hero-title">Equilibrio para tu cuerpo, <span>bienestar para tu vida.</span></h1>
                <p class="hero-copy">Tratamientos de acupuntura, medicina natural, quiropráctica, kinesiología y terapia nutricional con atención personalizada.</p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a class="btn btn-brand btn-lg" href="#cita"><i class="bi bi-calendar2-check me-2"></i>Solicitar una cita</a>
                    <a class="btn btn-outline-brand btn-lg" href="#servicios">Conocer servicios</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-logo-card"><img src="{{ asset('assets/img/logo-baj-wam.png') }}" alt="BAJ WAM Acupuntura"></div>
            </div>
        </div>
    </div>
</section>

<section class="section-space" id="servicios">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 720px;">
            <div class="section-eyebrow">Nuestros servicios</div>
            <h2 class="section-title">Atención integral para tu bienestar</h2>
            <p class="text-secondary">Cada tratamiento se adapta a las necesidades particulares del paciente.</p>
        </div>
        <div class="row g-4">
            @foreach ([
                ['icono' => 'bi-flower2', 'nombre' => 'Acupuntura', 'texto' => 'Terapia tradicional orientada a favorecer el equilibrio y el bienestar integral.'],
                ['icono' => 'bi-lightning-charge', 'nombre' => 'Acupuntura láser', 'texto' => 'Estimulación de puntos mediante tecnología láser de baja intensidad.'],
                ['icono' => 'bi-person-standing', 'nombre' => 'Quiropráctico', 'texto' => 'Atención orientada al cuidado postural y al sistema musculoesquelético.'],
                ['icono' => 'bi-activity', 'nombre' => 'Kinesiología', 'texto' => 'Evaluación del movimiento y acompañamiento para mejorar la función corporal.'],
                ['icono' => 'bi-tree', 'nombre' => 'Botánica', 'texto' => 'Uso responsable de alternativas naturales como apoyo al bienestar.'],
                ['icono' => 'bi-apple', 'nombre' => 'Terapia nutricional', 'texto' => 'Orientación nutricional personalizada según los objetivos de cada paciente.'],
            ] as $servicio)
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="service-card p-4">
                        <div class="service-icon mb-3"><i class="bi {{ $servicio['icono'] }}"></i></div>
                        <h3 class="h5 fw-bold">{{ $servicio['nombre'] }}</h3>
                        <p class="text-secondary mb-0">{{ $servicio['texto'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-space bg-light" id="productos">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
            <div>
                <div class="section-eyebrow">Catálogo</div>
                <h2 class="section-title mb-0">Productos naturales</h2>
            </div>
            <a class="btn btn-outline-brand" href="{{ url('/catalogo') }}">Ver catálogo completo</a>
        </div>
        <div class="row g-4">
            @foreach ([
                ['nombre' => 'Extracto de valeriana', 'categoria' => 'Extractos', 'presentacion' => '30 ml'],
                ['nombre' => 'Té digestivo', 'categoria' => 'Infusiones', 'presentacion' => '20 sobres'],
                ['nombre' => 'Aceite de árnica', 'categoria' => 'Aceites', 'presentacion' => '60 ml'],
            ] as $producto)
                <div class="col-12 col-md-4">
                    <article class="product-card overflow-hidden">
                        <div class="product-placeholder"><i class="bi bi-flower2"></i></div>
                        <div class="p-4">
                            <span class="badge bg-soft-green text-green-bw mb-2">{{ $producto['categoria'] }}</span>
                            <h3 class="h5 fw-bold">{{ $producto['nombre'] }}</h3>
                            <p class="text-secondary mb-0">Presentación: {{ $producto['presentacion'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="appointment-section section-space" id="cita">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 text-white">
                <div class="section-eyebrow text-white-50">Solicitud de cita</div>
                <h2 class="display-6 fw-bold mt-2">Da el primer paso hacia tu bienestar.</h2>
                <p class="text-white-50">Envía tus datos y el personal de la clínica confirmará la disponibilidad del horario solicitado.</p>
                <div class="d-flex align-items-center gap-3 mt-4"><i class="bi bi-whatsapp fs-3"></i><div><small class="d-block text-white-50">También puedes comunicarte por WhatsApp</small><strong>+502 5555-0000</strong></div></div>
            </div>
            <div class="col-lg-7">
                <div class="appointment-panel p-4 p-lg-5">
                    <form method="POST" action="{{ url('/solicitudes-cita') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nombre completo</label><input class="form-control" name="nombre" required></div>
                            <div class="col-md-6"><label class="form-label">Teléfono</label><input class="form-control" name="telefono" required></div>
                            <div class="col-md-6"><label class="form-label">Servicio</label><select class="form-select" name="servicio" required><option value="">Seleccionar</option><option>Acupuntura</option><option>Quiropráctico</option><option>Terapia nutricional</option></select></div>
                            <div class="col-md-3"><label class="form-label">Fecha preferida</label><input class="form-control" type="date" name="fecha" required></div>
                            <div class="col-md-3"><label class="form-label">Hora preferida</label><input class="form-control" type="time" name="hora" required></div>
                            <div class="col-12"><label class="form-label">Comentario</label><textarea class="form-control" rows="3" name="comentario" placeholder="Cuéntanos brevemente cómo podemos ayudarte"></textarea></div>
                            <div class="col-12"><button class="btn btn-brand w-100" type="submit"><i class="bi bi-send me-2"></i>Enviar solicitud</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
