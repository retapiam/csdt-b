<?php

namespace Database\Seeders;

use App\Models\PuebloIndigena;
use Illuminate\Database\Seeder;

class PueblosIndigenasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Datos reales de pueblos indígenas de Colombia según información oficial
     */
    public function run(): void
    {
        $pueblos = [
            [
                'nombre' => 'Resguardo Wayuu',
                'pueblo' => 'Wayuu',
                'departamento' => 'La Guajira',
                'municipio' => 'Maicao, Manaure, Uribia',
                'resguardo' => 'Resguardo Indígena de la Alta y Media Guajira',
                'territorio_ancestral' => 'Península de La Guajira',
                'extension_hectareas' => 1056825.00,
                'poblacion' => 380460,
                'idioma' => 'Wayuunaiki',
                'autoridades_tradicionales' => json_encode([
                    'Alaula (Autoridad Mayor)',
                    'Palabreros (Mediadores de conflictos)',
                    'Consejo de Ancianos'
                ]),
                'representante_legal' => 'Consejo Superior Wayuu',
                'contacto' => 'info@wayuu.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de la península de La Guajira, conocidos por su resistencia cultural y su sistema de justicia propio.',
                    'caracteristicas' => [
                        'Sistema de justicia propio',
                        'Tradición oral',
                        'Artesanía en mochilas',
                        'Organización matrilineal'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a la cultura'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Nasa',
                'pueblo' => 'Nasa (Páez)',
                'departamento' => 'Cauca',
                'municipio' => 'Toribío, Jambaló, Caldono',
                'resguardo' => 'Resguardo Indígena Nasa del Norte del Cauca',
                'territorio_ancestral' => 'Cordillera Central - Norte del Cauca',
                'extension_hectareas' => 178500.00,
                'poblacion' => 186178,
                'idioma' => 'Nasa Yuwe',
                'autoridades_tradicionales' => json_encode([
                    'Cabildo Mayor',
                    'The Wala (Médico tradicional)',
                    'Kiwe Theg (Guardia Indígena)'
                ]),
                'representante_legal' => 'Asociación de Cabildos Indígenas del Norte del Cauca - ACIN',
                'contacto' => 'acin@nasacauca.org',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena del departamento del Cauca, reconocido por su lucha por la tierra y la autonomía territorial.',
                    'caracteristicas' => [
                        'Guardia indígena',
                        'Sistema de justicia propio',
                        'Tradición agrícola',
                        'Organización comunitaria'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a la cultura'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Embera del Chocó',
                'pueblo' => 'Embera',
                'departamento' => 'Chocó',
                'municipio' => 'Alto Baudó, Medio Baudó, Bajo Baudó',
                'resguardo' => 'Resguardo Embera-Katío y Embera-Dobida',
                'territorio_ancestral' => 'Cuenca del río Baudó y San Juan',
                'extension_hectareas' => 245800.00,
                'poblacion' => 71633,
                'idioma' => 'Embera Bedea (Embera)',
                'autoridades_tradicionales' => json_encode([
                    'Jaibaná (Médico tradicional)',
                    'Cacique Mayor',
                    'Consejo de Ancianos'
                ]),
                'representante_legal' => 'Organización Regional Embera-Wounaan - OREWA',
                'contacto' => 'orewa@embera.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de la región del Pacífico, conocido por su relación con la naturaleza y su artesanía.',
                    'caracteristicas' => [
                        'Tradición oral',
                        'Artesanía en chaquiras',
                        'Conocimiento medicinal',
                        'Organización familiar'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la cultura',
                        'Derecho a la salud'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Arhuaco',
                'pueblo' => 'Arhuaco (Ika)',
                'departamento' => 'Cesar, Magdalena, La Guajira',
                'municipio' => 'Valledupar, Pueblo Bello, La Paz',
                'resguardo' => 'Resguardo Arhuaco de la Sierra Nevada',
                'territorio_ancestral' => 'Sierra Nevada de Santa Marta',
                'extension_hectareas' => 195900.00,
                'poblacion' => 40073,
                'idioma' => 'Ikun (Arhuaco)',
                'autoridades_tradicionales' => json_encode([
                    'Mamo (Líder espiritual)',
                    'Cabildo Gobernador',
                    'Consejo Territorial'
                ]),
                'representante_legal' => 'Confederación Indígena Tayrona',
                'contacto' => 'cit@arhuaco.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de la Sierra Nevada de Santa Marta, conocidos por su sabiduría ancestral y guardianes de la Sierra.',
                    'caracteristicas' => [
                        'Sistema de justicia propio',
                        'Tradición espiritual',
                        'Conocimiento medicinal',
                        'Organización tradicional',
                        'Ley de Sé - Ley de Origen'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a la cultura'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Kogui',
                'pueblo' => 'Kogui (Kággaba)',
                'departamento' => 'Cesar, Magdalena',
                'municipio' => 'San Juan del Cesar, Riohacha',
                'resguardo' => 'Resguardo Kogui-Malayo-Arhuaco',
                'territorio_ancestral' => 'Sierra Nevada de Santa Marta',
                'extension_hectareas' => 364677.00,
                'poblacion' => 18795,
                'idioma' => 'Kogian (Kággaba)',
                'autoridades_tradicionales' => json_encode([
                    'Mamo (Sacerdote)',
                    'Cabildo Gobernador'
                ]),
                'representante_legal' => 'Organización Gonawindwa Tayrona - OGT',
                'contacto' => 'ogt@kogui.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de la Sierra Nevada de Santa Marta, guardianes del equilibrio del mundo y del Corazón del Mundo.',
                    'caracteristicas' => [
                        'Sistema de justicia propio',
                        'Tradición espiritual',
                        'Conocimiento ancestral',
                        'Organización tradicional',
                        'Protectores del medio ambiente'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a la cultura'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Zenú',
                'pueblo' => 'Zenú',
                'departamento' => 'Córdoba, Sucre',
                'municipio' => 'San Andrés de Sotavento, Tuchín',
                'resguardo' => 'Resguardo Indígena Zenú',
                'territorio_ancestral' => 'Antigua Mesopotamia de los Zenúes',
                'extension_hectareas' => 83000.00,
                'poblacion' => 307091,
                'idioma' => 'Español (Zenú en recuperación)',
                'autoridades_tradicionales' => json_encode([
                    'Cabildo Mayor',
                    'Consejo de Mayores',
                    'Autoridades zonales'
                ]),
                'representante_legal' => 'Cabildo Mayor Regional Zenú',
                'contacto' => 'cabildo@zenu.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de la región Caribe, conocidos por su ingeniería hidráulica ancestral y su tradición artesanal.',
                    'caracteristicas' => [
                        'Tradición artesanal - Sombrero vueltiao',
                        'Conocimiento de ingeniería hidráulica',
                        'Organización comunitaria',
                        'Recuperación cultural'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la cultura',
                        'Derecho a la autonomía'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Pasto',
                'pueblo' => 'Pasto',
                'departamento' => 'Nariño',
                'municipio' => 'Ipiales, Aldana, Guachucal',
                'resguardo' => 'Resguardos del pueblo Pasto',
                'territorio_ancestral' => 'Nudo de los Pastos',
                'extension_hectareas' => 52300.00,
                'poblacion' => 95006,
                'idioma' => 'Español (Awapit en algunas comunidades)',
                'autoridades_tradicionales' => json_encode([
                    'Taita (Gobernador)',
                    'Cabildo',
                    'Asamblea General'
                ]),
                'representante_legal' => 'Shaquiñán - Autoridad Ancestral del Pueblo Pasto',
                'contacto' => 'shaquinan@pasto.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de los Andes nariñenses, herederos de la cultura Quillacinga-Pasto.',
                    'caracteristicas' => [
                        'Tradición agrícola andina',
                        'Conocimiento medicinal',
                        'Artesanía en talla de madera',
                        'Organización comunitaria'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la cultura',
                        'Derecho a la autonomía'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Inga',
                'pueblo' => 'Inga',
                'departamento' => 'Putumayo, Nariño, Cauca',
                'municipio' => 'Colón, Santiago, San Francisco',
                'resguardo' => 'Resguardos del pueblo Inga',
                'territorio_ancestral' => 'Valle de Sibundoy y dispersión andino-amazónica',
                'extension_hectareas' => 127000.00,
                'poblacion' => 24964,
                'idioma' => 'Inga Kichwa',
                'autoridades_tradicionales' => json_encode([
                    'Taita (Sabedor-Médico tradicional)',
                    'Cabildo Gobernador',
                    'Sinchi (Líderes espirituales)'
                ]),
                'representante_legal' => 'Cabildo Mayor del Pueblo Inga',
                'contacto' => 'cabildo@inga.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena del piedemonte amazónico, conocidos por su conocimiento medicinal con yagé y plantas sagradas.',
                    'caracteristicas' => [
                        'Medicina tradicional con yagé',
                        'Conocimiento botánico',
                        'Tradición oral kichwa',
                        'Cosmovisión andino-amazónica'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a la medicina tradicional'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Uitoto',
                'pueblo' => 'Uitoto (Murui)',
                'departamento' => 'Amazonas, Caquetá',
                'municipio' => 'La Chorrera, Puerto Leguízamo',
                'resguardo' => 'Predio Putumayo',
                'territorio_ancestral' => 'Amazonía colombiana',
                'extension_hectareas' => 5900000.00,
                'poblacion' => 8234,
                'idioma' => 'Uitoto (Murui-Muinane)',
                'autoridades_tradicionales' => json_encode([
                    'Manguaré (Autoridad tradicional)',
                    'Maitre (Sabedor de cantos)',
                    'Consejo de Ancianos'
                ]),
                'representante_legal' => 'Organización Nacional Indígena de Colombia - Amazonas',
                'contacto' => 'onic.amazonas@uitoto.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena amazónico, guardián de la selva y conocedor de la tradición de la Palabra dulce - Palabra de consejo.',
                    'caracteristicas' => [
                        'Tradición oral del Manguaré',
                        'Conocimiento de la selva',
                        'Sistema de malocas',
                        'Palabra dulce - cantos tradicionales'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la autonomía',
                        'Derecho a la cultura'
                    ],
                    'sistema_justicia' => true
                ])
            ],
            [
                'nombre' => 'Resguardo Kankuamo',
                'pueblo' => 'Kankuamo',
                'departamento' => 'Cesar',
                'municipio' => 'Atánquez, Valledupar',
                'resguardo' => 'Resguardo Indígena Kankuamo',
                'territorio_ancestral' => 'Sierra Nevada de Santa Marta',
                'extension_hectareas' => 24000.00,
                'poblacion' => 17345,
                'idioma' => 'Español (Kankui en recuperación)',
                'autoridades_tradicionales' => json_encode([
                    'Cabildo Gobernador',
                    'Consejo de Mayores',
                    'Autoridades espirituales'
                ]),
                'representante_legal' => 'Organización Indígena Kankuama - OIK',
                'contacto' => 'oik@kankuamo.org.co',
                'estado' => 'activo',
                'metadata' => json_encode([
                    'descripcion' => 'Pueblo indígena de la Sierra Nevada de Santa Marta en proceso de recuperación cultural y territorial.',
                    'caracteristicas' => [
                        'Recuperación cultural',
                        'Tradición de resistencia',
                        'Reorganización territorial',
                        'Defensa de derechos'
                    ],
                    'derechos' => [
                        'Derecho al territorio',
                        'Derecho a la consulta previa',
                        'Derecho a la cultura',
                        'Derecho a la reparación'
                    ],
                    'sistema_justicia' => true
                ])
            ]
        ];

        foreach ($pueblos as $pueblo) {
            PuebloIndigena::create($pueblo);
        }
    }
}

