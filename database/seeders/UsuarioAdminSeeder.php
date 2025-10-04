<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Profesion;
use App\Models\NivelUsuario;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe un administrador general
        $adminExists = Usuario::where('rol', 'adm_gen')->exists();
        
        if (!$adminExists) {
            // Buscar una profesión y nivel para el administrador
            $profesionAdmin = Profesion::where('codigo', 'ADMINISTRADOR')->first();
            $nivelAdmin = NivelUsuario::where('codigo', 'ADMIN_5')->first();
            
            $admin = Usuario::create([
                'nom' => 'Administrador',
                'ape' => 'General',
                'cor' => 'admin@csdt.gov.co',
                'con' => Hash::make('admin123456'), // Cambiar en producción
                'tel' => '3001234567',
                'doc' => '12345678',
                'tip_doc' => 'cc',
                'fec_nac' => '1990-01-01',
                'dir' => 'Oficina Principal CSDT',
                'ciu' => 'Bogotá',
                'dep' => 'Cundinamarca',
                'gen' => 'n',
                'rol' => 'adm_gen',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now(),
                // Campos de profesión y nivel
                'profesion_id' => $profesionAdmin?->id,
                'nivel_id' => $nivelAdmin?->id,
                'años_experiencia' => 15,
                'numero_matricula' => 'ADM001',
                'entidad_matricula' => 'CSDT',
                'fecha_matricula' => '2010-01-01',
                'especializaciones' => ['Administración Pública', 'Gestión Territorial', 'Desarrollo Social'],
                'certificaciones' => ['Certificado en Gestión Pública', 'Especialista en Desarrollo Territorial'],
                'perfil_profesional' => 'Administrador General del Consejo Social de Veeduría y Desarrollo Territorial con amplia experiencia en gestión pública, desarrollo territorial y administración de sistemas complejos.',
                'perfil_verificado' => true,
                'perfil_verificado_en' => now(),
                'estado_verificacion' => 'verificado'
            ]);
            
            $this->command->info("✅ Usuario administrador general creado:");
            $this->command->info("   📧 Email: admin@csdt.gov.co");
            $this->command->info("   🔑 Contraseña: admin123456");
            $this->command->info("   ⚠️  IMPORTANTE: Cambiar la contraseña en producción");
            
        } else {
            $this->command->info("ℹ️  Usuario administrador general ya existe, omitiendo creación.");
        }
        
        // Crear usuario de prueba para cada rol
        $this->crearUsuariosPrueba();
    }
    
    private function crearUsuariosPrueba()
    {
        // Usuario Cliente de prueba
        $clienteExists = Usuario::where('cor', 'cliente@csdt.gov.co')->exists();
        if (!$clienteExists) {
            $profesionCliente = Profesion::where('codigo', 'ABOGADO')->first();
            $nivelCliente = NivelUsuario::where('codigo', 'LEGAL_2')->first();
            
            Usuario::create([
                'nom' => 'Juan',
                'ape' => 'Pérez',
                'cor' => 'cliente@csdt.gov.co',
                'con' => Hash::make('cliente123'),
                'tel' => '3001111111',
                'doc' => '11111111',
                'tip_doc' => 'cc',
                'fec_nac' => '1985-05-15',
                'dir' => 'Calle 123 #45-67',
                'ciu' => 'Bogotá',
                'dep' => 'Cundinamarca',
                'gen' => 'm',
                'rol' => 'cli',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now(),
                'profesion_id' => $profesionCliente?->id,
                'nivel_id' => $nivelCliente?->id,
                'años_experiencia' => 5,
                'numero_matricula' => '12345',
                'entidad_matricula' => 'Consejo Superior de la Judicatura',
                'fecha_matricula' => '2018-01-01',
                'especializaciones' => ['Derecho Civil', 'Derecho Comercial'],
                'certificaciones' => ['Especialista en Derecho Civil'],
                'perfil_profesional' => 'Abogado especializado en derecho civil y comercial con 5 años de experiencia.',
                'perfil_verificado' => true,
                'perfil_verificado_en' => now(),
                'estado_verificacion' => 'verificado'
            ]);
            
            $this->command->info("✅ Usuario cliente de prueba creado: cliente@csdt.gov.co / cliente123");
        }
        
        // Usuario Operador de prueba
        $operadorExists = Usuario::where('cor', 'operador@csdt.gov.co')->exists();
        if (!$operadorExists) {
            $profesionOperador = Profesion::where('codigo', 'TRABAJADOR_SOCIAL')->first();
            $nivelOperador = NivelUsuario::where('codigo', 'SOC_2')->first();
            
            Usuario::create([
                'nom' => 'María',
                'ape' => 'González',
                'cor' => 'operador@csdt.gov.co',
                'con' => Hash::make('operador123'),
                'tel' => '3002222222',
                'doc' => '22222222',
                'tip_doc' => 'cc',
                'fec_nac' => '1990-08-20',
                'dir' => 'Carrera 45 #78-90',
                'ciu' => 'Medellín',
                'dep' => 'Antioquia',
                'gen' => 'f',
                'rol' => 'ope',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now(),
                'profesion_id' => $profesionOperador?->id,
                'nivel_id' => $nivelOperador?->id,
                'años_experiencia' => 3,
                'especializaciones' => ['Intervención Social', 'Desarrollo Comunitario'],
                'certificaciones' => ['Especialista en Intervención Social'],
                'perfil_profesional' => 'Trabajadora social con experiencia en intervención comunitaria y desarrollo social.',
                'perfil_verificado' => true,
                'perfil_verificado_en' => now(),
                'estado_verificacion' => 'verificado'
            ]);
            
            $this->command->info("✅ Usuario operador de prueba creado: operador@csdt.gov.co / operador123");
        }
        
        // Usuario Administrador de prueba
        $adminExists = Usuario::where('cor', 'admin@csdt.gov.co')->where('rol', 'adm')->exists();
        if (!$adminExists) {
            $profesionAdmin = Profesion::where('codigo', 'GESTOR_PUBLICO')->first();
            $nivelAdmin = NivelUsuario::where('codigo', 'ADMIN_3')->first();
            
            Usuario::create([
                'nom' => 'Carlos',
                'ape' => 'Rodríguez',
                'cor' => 'admin@csdt.gov.co',
                'con' => Hash::make('admin123'),
                'tel' => '3003333333',
                'doc' => '33333333',
                'tip_doc' => 'cc',
                'fec_nac' => '1980-03-10',
                'dir' => 'Avenida 68 #100-50',
                'ciu' => 'Bogotá',
                'dep' => 'Cundinamarca',
                'gen' => 'm',
                'rol' => 'adm',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now(),
                'profesion_id' => $profesionAdmin?->id,
                'nivel_id' => $nivelAdmin?->id,
                'años_experiencia' => 8,
                'especializaciones' => ['Gestión Pública', 'Políticas Públicas', 'Administración'],
                'certificaciones' => ['Magíster en Gestión Pública', 'Especialista en Políticas Públicas'],
                'perfil_profesional' => 'Gestor público con amplia experiencia en administración de entidades públicas y desarrollo de políticas.',
                'perfil_verificado' => true,
                'perfil_verificado_en' => now(),
                'estado_verificacion' => 'verificado'
            ]);
            
            $this->command->info("✅ Usuario administrador de prueba creado: admin@csdt.gov.co / admin123");
        }
    }
}
