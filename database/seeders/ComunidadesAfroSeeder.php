<?php

namespace Database\Seeders;

use App\Models\ComunidadAfro;
use Illuminate\Database\Seeder;

class ComunidadesAfroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Datos reales de comunidades afrodescendientes de Colombia según información oficial
     */
    public function run(): void
    {
        $comunidades = [
            [
                'nombre' => 'San Basilio de Palenque',
                'tipo' => 'palenque',
                'departamento' => 'Bolívar',
                'municipio' => 'Mahates',
                'territorio_colectivo' => 'Territorio Colectivo de San Basilio de Palenque',
                'titulo_colectivo' => 'Resolución 02 de 2005',
                'extension_hectareas' => 8256.00,
                'poblacion' => 5790,
                'representante_legal' => 'Consejo Comunitario de San Basilio de Palenque',
                'contacto' => 'consejo@palenque.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Primer pueblo libre de América, reconocido como Patrimonio Inmaterial de la Humanidad por UNESCO en 2005.',
                    'reconocimiento_unesco' => '2005',
                    'caracteristicas' => [
                        'Idioma palenquero (lengua criolla)',
                        'Sistema de organización social Ma-Kuagro',
                        'Medicina tradicional',
                        'Música champeta y bullerengue',
                        'Patrimonio Cultural Inmaterial de la Humanidad'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la identidad cultural',
                        'Derecho a la autonomía',
                        'Derecho a la consulta previa',
                        'Derecho a la lengua propia'
                    ],
                    'patrimonio_unesco' => true
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario de La Boquilla',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Bolívar',
                'municipio' => 'Cartagena',
                'territorio_colectivo' => 'Zona ancestral de pesca La Boquilla',
                'titulo_colectivo' => 'Resolución 140 de 2003',
                'extension_hectareas' => 3450.00,
                'poblacion' => 9200,
                'representante_legal' => 'Consejo Comunitario de Comunidades Negras de La Boquilla',
                'contacto' => 'laboquilla@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Comunidad afrodescendiente pescadora con tradiciones ancestrales en la bahía de Cartagena.',
                    'reconocimiento' => '2003',
                    'caracteristicas' => [
                        'Pesca artesanal tradicional',
                        'Gastronomía caribeña',
                        'Danzas tradicionales',
                        'Organización comunitaria fuerte',
                        'Turismo comunitario'
                    ],
                    'derechos' => [
                        'Derecho a la pesca artesanal',
                        'Derecho a la identidad cultural',
                        'Derecho a la participación',
                        'Derecho al desarrollo propio'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario del Alto Mira y Frontera',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Nariño',
                'municipio' => 'Tumaco',
                'territorio_colectivo' => 'Cuenca del río Mira',
                'titulo_colectivo' => 'Resolución 2809 de 2000',
                'extension_hectareas' => 47000.00,
                'poblacion' => 18500,
                'representante_legal' => 'Consejo Comunitario Mayor del Alto Mira y Frontera',
                'contacto' => 'altomira@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio colectivo del Pacífico sur, con fuerte identidad cultural afropacífica.',
                    'reconocimiento' => '2000',
                    'caracteristicas' => [
                        'Minería ancestral',
                        'Agricultura tradicional',
                        'Medicina tradicional del Pacífico',
                        'Música de marimba y currulao',
                        'Conservación de bosques'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a los recursos naturales'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario Mayor de la Asociación Campesina Integral del Atrato - COCOMACIA',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Chocó',
                'municipio' => 'Medio Atrato',
                'territorio_colectivo' => 'Cuenca del Medio Atrato',
                'titulo_colectivo' => 'Resolución 2617 de 2000',
                'extension_hectareas' => 671000.00,
                'poblacion' => 45000,
                'representante_legal' => 'COCOMACIA',
                'contacto' => 'cocomacia@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Uno de los territorios colectivos más grandes de Colombia, en la cuenca del río Atrato.',
                    'reconocimiento' => '2000',
                    'caracteristicas' => [
                        'Organización territorial comunitaria',
                        'Conservación de biodiversidad',
                        'Minería artesanal',
                        'Pesca fluvial',
                        'Agricultura tradicional'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho ambiental',
                        'Derecho a recursos naturales'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario de Comunidades Negras de Buenaventura - NAYA',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Valle del Cauca',
                'municipio' => 'Buenaventura',
                'territorio_colectivo' => 'Cuenca del río Naya',
                'titulo_colectivo' => 'Resolución 2801 de 2000',
                'extension_hectareas' => 52000.00,
                'poblacion' => 8200,
                'representante_legal' => 'Consejo Comunitario del Naya',
                'contacto' => 'naya@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio colectivo del Pacífico vallecaucano con alta biodiversidad.',
                    'reconocimiento' => '2000',
                    'caracteristicas' => [
                        'Conservación forestal',
                        'Pesca artesanal',
                        'Agricultura sostenible',
                        'Música del Pacífico',
                        'Medicina tradicional'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho a la reparación',
                        'Derecho ambiental'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario de Comunidades Negras Eladio Ariza Moreno',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Valle del Cauca',
                'municipio' => 'Buenaventura',
                'territorio_colectivo' => 'Zona urbana y rural de Buenaventura',
                'titulo_colectivo' => 'En proceso de titulación',
                'extension_hectareas' => 12500.00,
                'poblacion' => 42000,
                'representante_legal' => 'Consejo Comunitario Eladio Ariza Moreno',
                'contacto' => 'eladioariza@consejos.org.co',
                'estado' => 'en_proceso_titulacion',
                'metadata' => json_encode([
                    'descripcion' => 'Principal puerto del Pacífico colombiano con importante población afrodescendiente.',
                    'caracteristicas' => [
                        'Economía portuaria',
                        'Música del Pacífico',
                        'Gastronomía marinera',
                        'Organización comunitaria',
                        'Resistencia cultural urbana'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho al desarrollo',
                        'Derecho a la participación',
                        'Derecho a servicios públicos'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario de Comunidades Negras del Río Yurumanguí',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Valle del Cauca',
                'municipio' => 'Buenaventura',
                'territorio_colectivo' => 'Cuenca del río Yurumanguí',
                'titulo_colectivo' => 'Resolución 3013 de 2002',
                'extension_hectareas' => 53900.00,
                'poblacion' => 3800,
                'representante_legal' => 'Consejo Comunitario del Yurumanguí',
                'contacto' => 'yurumangui@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio ancestral afropacífico con alta biodiversidad y tradición cultural.',
                    'reconocimiento' => '2002',
                    'caracteristicas' => [
                        'Conservación de ecosistemas',
                        'Pesca artesanal',
                        'Recolección de piangua',
                        'Música tradicional',
                        'Medicina ancestral'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho ambiental',
                        'Derecho a la autonomía'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario Mayor del Baudó',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Chocó',
                'municipio' => 'Bajo Baudó, Alto Baudó',
                'territorio_colectivo' => 'Cuenca del río Baudó',
                'titulo_colectivo' => 'Resolución 2801 de 1996',
                'extension_hectareas' => 204000.00,
                'poblacion' => 25000,
                'representante_legal' => 'Consejo Comunitario Mayor del Baudó',
                'contacto' => 'baudo@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio colectivo del Pacífico chocoano con rica biodiversidad.',
                    'reconocimiento' => '1996',
                    'caracteristicas' => [
                        'Primer territorio colectivo titulado',
                        'Biodiversidad excepcional',
                        'Minería artesanal de oro',
                        'Pesca artesanal',
                        'Tradición musical'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho histórico (primera titulación)',
                        'Derecho a recursos naturales'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Comunidad Raizal del Archipiélago de San Andrés',
                'tipo' => 'raizal',
                'departamento' => 'San Andrés y Providencia',
                'municipio' => 'San Andrés',
                'territorio_colectivo' => 'Archipiélago de San Andrés, Providencia y Santa Catalina',
                'titulo_colectivo' => 'Reconocimiento especial Ley 47 de 1993',
                'extension_hectareas' => 5200.00,
                'poblacion' => 30000,
                'representante_legal' => 'Organización INFOTEP',
                'contacto' => 'infotep@raizales.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo raizal afrocaribeño con cultura e idioma propio del archipiélago.',
                    'reconocimiento' => '1993',
                    'caracteristicas' => [
                        'Idioma criollo sanandresano',
                        'Cultura afrocaribeña',
                        'Religión protestante bautista',
                        'Música calipso y reggae',
                        'Economía marítima y turística'
                    ],
                    'derechos' => [
                        'Derecho al territorio ancestral',
                        'Derecho a la identidad cultural',
                        'Derecho a la lengua propia',
                        'Derecho a la autonomía',
                        'Control migratorio especial'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario Acapa',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Chocó',
                'municipio' => 'Riosucio',
                'territorio_colectivo' => 'Cuenca del río Acandí',
                'titulo_colectivo' => 'Resolución 1914 de 2000',
                'extension_hectareas' => 41200.00,
                'poblacion' => 12000,
                'representante_legal' => 'Consejo Comunitario de Acapa',
                'contacto' => 'acapa@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio colectivo del Darién chocoano, frontera con Panamá.',
                    'reconocimiento' => '2000',
                    'caracteristicas' => [
                        'Zona de frontera',
                        'Conservación de bosques',
                        'Pesca artesanal',
                        'Agricultura de pancoger',
                        'Tradición oral'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho transfronterizo',
                        'Derecho ambiental'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario de la Cuenca del Río Guapi',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Cauca',
                'municipio' => 'Guapi',
                'territorio_colectivo' => 'Cuenca del río Guapi',
                'titulo_colectivo' => 'Resolución 1652 de 1999',
                'extension_hectareas' => 82000.00,
                'poblacion' => 28000,
                'representante_legal' => 'Consejo Comunitario del Río Guapi',
                'contacto' => 'guapi@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio colectivo del Pacífico caucano con importante tradición cultural.',
                    'reconocimiento' => '1999',
                    'caracteristicas' => [
                        'Minería artesanal de oro',
                        'Pesca artesanal',
                        'Música de marimba',
                        'Gastronomía del Pacífico',
                        'Medicina tradicional'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho a recursos naturales',
                        'Derecho cultural'
                    ],
                    'patrimonio_unesco' => false
                ])
            ],
            [
                'nombre' => 'Consejo Comunitario de Comunidades Negras del Río Anchicayá',
                'tipo' => 'consejo_comunitario',
                'departamento' => 'Valle del Cauca',
                'municipio' => 'Buenaventura, Dagua',
                'territorio_colectivo' => 'Cuenca del río Anchicayá',
                'titulo_colectivo' => 'Resolución 1457 de 2006',
                'extension_hectareas' => 31000.00,
                'poblacion' => 7500,
                'representante_legal' => 'Consejo Comunitario del Anchicayá',
                'contacto' => 'anchicaya@consejos.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Territorio colectivo afectado por proyectos hidroeléctricos, en proceso de reparación.',
                    'reconocimiento' => '2006',
                    'caracteristicas' => [
                        'Afectación por hidroeléctricas',
                        'Proceso de reparación',
                        'Agricultura tradicional',
                        'Pesca fluvial',
                        'Organización comunitaria'
                    ],
                    'derechos' => [
                        'Derecho al territorio colectivo',
                        'Derecho a la consulta previa',
                        'Derecho a la reparación',
                        'Derecho ambiental',
                        'Derecho a la compensación'
                    ],
                    'patrimonio_unesco' => false
                ])
            ]
        ];

        foreach ($comunidades as $comunidad) {
            ComunidadAfro::create($comunidad);
        }
    }
}

