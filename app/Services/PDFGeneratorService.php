<?php

namespace App\Services;

use TCPDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PDFGeneratorService
{
    /**
     * Generar PDF de análisis legal
     */
    public function generarAnalisisLegal($datos)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configuración del documento
        $pdf->SetCreator('CSDT - Consejo Social de Veeduría y Desarrollo Territorial');
        $pdf->SetAuthor('Sistema CSDT');
        $pdf->SetTitle('Análisis Legal con Inteligencia Artificial');
        $pdf->SetSubject('Análisis Jurídico');
        $pdf->SetKeywords('CSDT, Análisis Legal, IA, Veeduría');
        
        // Configurar márgenes
        $pdf->SetMargins(20, 30, 20);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);
        
        // Configurar fuente
        $pdf->SetFont('helvetica', '', 10);
        
        // Agregar página
        $pdf->AddPage();
        
        // Título principal
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(30, 60, 114);
        $pdf->Cell(0, 10, 'ANÁLISIS LEGAL CON INTELIGENCIA ARTIFICIAL', 0, 1, 'C');
        
        // Línea decorativa
        $pdf->SetDrawColor(30, 60, 114);
        $pdf->SetLineWidth(2);
        $pdf->Line(20, 45, 190, 45);
        
        // Información del documento
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 8, 'Fecha de análisis: ' . $datos['fecha'], 0, 1);
        $pdf->Cell(0, 8, 'Proveedor de IA: ' . ($datos['analisis']['ai_provider'] ?? 'N/A'), 0, 1);
        $pdf->Cell(0, 8, 'Confianza: ' . (($datos['analisis']['confidence_score'] ?? 0) * 100) . '%', 0, 1);
        
        // Hechos del caso
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'HECHOS DEL CASO', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 6, $datos['hechos'], 0, 'L');
        
        // Resumen del análisis
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'RESUMEN DEL ANÁLISIS', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 6, $datos['analisis']['summary'] ?? 'No disponible', 0, 'L');
        
        // Clasificaciones legales
        if (isset($datos['analisis']['classifications']) && count($datos['analisis']['classifications']) > 0) {
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'CLASIFICACIONES LEGALES', 0, 1);
            
            // Crear tabla de clasificaciones
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(30, 60, 114);
            $pdf->SetTextColor(255, 255, 255);
            
            $pdf->Cell(60, 8, 'Categoría', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Confianza', 1, 0, 'C', true);
            $pdf->Cell(80, 8, 'Base Legal', 1, 1, 'C', true);
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(248, 249, 250);
            
            foreach ($datos['analisis']['classifications'] as $index => $clasificacion) {
                $fill = ($index % 2 == 0);
                $pdf->Cell(60, 8, $clasificacion['category'], 1, 0, 'L', $fill);
                $pdf->Cell(30, 8, ($clasificacion['confidence'] * 100) . '%', 1, 0, 'C', $fill);
                $pdf->Cell(80, 8, $clasificacion['legal_basis'] ?? 'N/A', 1, 1, 'L', $fill);
            }
        }
        
        // Recomendaciones
        if (isset($datos['analisis']['recommendations']) && count($datos['analisis']['recommendations']) > 0) {
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(0, 10, 'RECOMENDACIONES', 0, 1);
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(60, 60, 60);
            
            foreach ($datos['analisis']['recommendations'] as $index => $recomendacion) {
                $pdf->Cell(0, 6, ($index + 1) . '. ' . $recomendacion, 0, 1);
            }
        }
        
        // Evaluación de riesgo
        if (isset($datos['analisis']['risk_assessment'])) {
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'EVALUACIÓN DE RIESGO', 0, 1);
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(60, 60, 60);
            
            $nivelRiesgo = strtoupper($datos['analisis']['risk_assessment']['level']);
            $puntuacion = ($datos['analisis']['risk_assessment']['score'] * 100);
            
            $pdf->Cell(0, 6, 'Nivel de riesgo: ' . $nivelRiesgo, 0, 1);
            $pdf->Cell(0, 6, 'Puntuación: ' . $puntuacion . '%', 0, 1);
            
            if (isset($datos['analisis']['risk_assessment']['factors']) && count($datos['analisis']['risk_assessment']['factors']) > 0) {
                $pdf->Ln(5);
                $pdf->Cell(0, 6, 'Factores considerados:', 0, 1);
                
                foreach ($datos['analisis']['risk_assessment']['factors'] as $factor) {
                    $pdf->Cell(0, 6, '• ' . $factor, 0, 1);
                }
            }
        }
        
        // Pie de página
        $pdf->SetY(-30);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 6, 'Generado por CSDT - Consejo Social de Veeduría y Desarrollo Territorial', 0, 1, 'C');
        $pdf->Cell(0, 6, 'Sistema de Inteligencia Artificial Legal', 0, 1, 'C');
        
        return $pdf;
    }

    /**
     * Generar PDF de PQRSFD
     */
    public function generarPQRSFD($datos)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configuración del documento
        $pdf->SetCreator('CSDT - Consejo Social de Veeduría y Desarrollo Territorial');
        $pdf->SetAuthor('Sistema CSDT');
        $pdf->SetTitle('PQRSFD - Petición, Queja, Reclamo, Sugerencia, Felicitación o Denuncia');
        $pdf->SetSubject('PQRSFD');
        $pdf->SetKeywords('CSDT, PQRSFD, Participación Ciudadana');
        
        // Configurar márgenes
        $pdf->SetMargins(20, 30, 20);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);
        
        // Configurar fuente
        $pdf->SetFont('helvetica', '', 10);
        
        // Agregar página
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(30, 60, 114);
        $pdf->Cell(0, 10, 'PETICIÓN, QUEJA, RECLAMO, SUGERENCIA, FELICITACIÓN O DENUNCIA', 0, 1, 'C');
        
        // Información del solicitante
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'INFORMACIÓN DEL SOLICITANTE', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, 6, 'Nombre:', 0, 0);
        $pdf->Cell(0, 6, $datos['nombre'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Identificación:', 0, 0);
        $pdf->Cell(0, 6, $datos['identificacion'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Teléfono:', 0, 0);
        $pdf->Cell(0, 6, $datos['telefono'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Email:', 0, 0);
        $pdf->Cell(0, 6, $datos['email'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Dirección:', 0, 0);
        $pdf->Cell(0, 6, $datos['direccion'] ?? 'N/A', 0, 1);
        
        // Detalles del PQRSFD
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'DETALLES DEL PQRSFD', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, 6, 'Tipo:', 0, 0);
        $pdf->Cell(0, 6, $datos['tipo'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Asunto:', 0, 0);
        $pdf->Cell(0, 6, $datos['asunto'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Fecha:', 0, 0);
        $pdf->Cell(0, 6, $datos['fecha'] ?? date('d/m/Y'), 0, 1);
        $pdf->Cell(50, 6, 'Estado:', 0, 0);
        $pdf->Cell(0, 6, $datos['estado'] ?? 'Pendiente', 0, 1);
        
        // Descripción
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'DESCRIPCIÓN', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 6, $datos['descripcion'] ?? 'No disponible', 0, 'L');
        
        // Pie de página
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 6, 'CSDT - Sistema de Gestión de PQRSFD', 0, 1, 'C');
        
        return $pdf;
    }

    /**
     * Generar PDF de donación
     */
    public function generarDonacion($datos)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configuración del documento
        $pdf->SetCreator('CSDT - Consejo Social de Veeduría y Desarrollo Territorial');
        $pdf->SetAuthor('Sistema CSDT');
        $pdf->SetTitle('Comprobante de Donación');
        $pdf->SetSubject('Donación');
        $pdf->SetKeywords('CSDT, Donación, Transparencia');
        
        // Configurar márgenes
        $pdf->SetMargins(20, 30, 20);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);
        
        // Configurar fuente
        $pdf->SetFont('helvetica', '', 10);
        
        // Agregar página
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(30, 60, 114);
        $pdf->Cell(0, 10, 'COMPROBANTE DE DONACIÓN', 0, 1, 'C');
        
        // Información de la donación
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'INFORMACIÓN DE LA DONACIÓN', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, 6, 'Donante:', 0, 0);
        $pdf->Cell(0, 6, $datos['donante'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Monto:', 0, 0);
        $pdf->Cell(0, 6, '$' . number_format($datos['monto'] ?? 0, 0, ',', '.'), 0, 1);
        $pdf->Cell(50, 6, 'Método de pago:', 0, 0);
        $pdf->Cell(0, 6, $datos['metodoPago'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Fecha:', 0, 0);
        $pdf->Cell(0, 6, $datos['fecha'] ?? date('d/m/Y'), 0, 1);
        $pdf->Cell(50, 6, 'Concepto:', 0, 0);
        $pdf->Cell(0, 6, $datos['concepto'] ?? 'Donación al CSDT', 0, 1);
        
        // Mensaje de agradecimiento
        $pdf->Ln(20);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(0, 100, 0);
        $pdf->Cell(0, 10, '¡GRACIAS POR SU DONACIÓN!', 0, 1, 'C');
        
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $mensaje = 'Su contribución nos ayuda a mantener y mejorar el sistema de veeduría ciudadana y desarrollo territorial. ' .
                  'Los fondos se utilizan para el desarrollo de nuevas funcionalidades, mantenimiento del sistema y ' .
                  'capacitación de usuarios en el uso de las herramientas de control social.';
        $pdf->MultiCell(0, 6, $mensaje, 0, 'L');
        
        // Pie de página
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 6, 'CSDT - Sistema de Donaciones', 0, 1, 'C');
        
        return $pdf;
    }

    /**
     * Generar PDF de reporte de veeduría
     */
    public function generarReporteVeeduria($datos)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configuración del documento
        $pdf->SetCreator('CSDT - Consejo Social de Veeduría y Desarrollo Territorial');
        $pdf->SetAuthor('Sistema CSDT');
        $pdf->SetTitle('Reporte de Veeduría Ciudadana');
        $pdf->SetSubject('Veeduría');
        $pdf->SetKeywords('CSDT, Veeduría, Control Social');
        
        // Configurar márgenes
        $pdf->SetMargins(20, 30, 20);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);
        
        // Configurar fuente
        $pdf->SetFont('helvetica', '', 10);
        
        // Agregar página
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(30, 60, 114);
        $pdf->Cell(0, 10, 'REPORTE DE VEEDURÍA CIUDADANA', 0, 1, 'C');
        
        // Información del veedor
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'INFORMACIÓN DEL VEEDOR', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, 6, 'Nombre:', 0, 0);
        $pdf->Cell(0, 6, $datos['veedor']['nombre'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Identificación:', 0, 0);
        $pdf->Cell(0, 6, $datos['veedor']['identificacion'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Teléfono:', 0, 0);
        $pdf->Cell(0, 6, $datos['veedor']['telefono'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Email:', 0, 0);
        $pdf->Cell(0, 6, $datos['veedor']['email'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Organización:', 0, 0);
        $pdf->Cell(0, 6, $datos['veedor']['organizacion'] ?? 'N/A', 0, 1);
        
        // Detalles del reporte
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'DETALLES DEL REPORTE', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, 6, 'Proyecto:', 0, 0);
        $pdf->Cell(0, 6, $datos['proyecto'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Entidad:', 0, 0);
        $pdf->Cell(0, 6, $datos['entidad'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Fecha de inicio:', 0, 0);
        $pdf->Cell(0, 6, $datos['fechaInicio'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Fecha de finalización:', 0, 0);
        $pdf->Cell(0, 6, $datos['fechaFinalizacion'] ?? 'N/A', 0, 1);
        $pdf->Cell(50, 6, 'Estado:', 0, 0);
        $pdf->Cell(0, 6, $datos['estado'] ?? 'En proceso', 0, 1);
        
        // Hallazgos
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'HALLAZGOS', 0, 1);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 6, $datos['hallazgos'] ?? 'No hay hallazgos reportados', 0, 'L');
        
        // Recomendaciones
        if (isset($datos['recomendaciones']) && count($datos['recomendaciones']) > 0) {
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(0, 10, 'RECOMENDACIONES', 0, 1);
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(60, 60, 60);
            
            foreach ($datos['recomendaciones'] as $index => $recomendacion) {
                $pdf->Cell(0, 6, ($index + 1) . '. ' . $recomendacion, 0, 1);
            }
        }
        
        // Pie de página
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 6, 'CSDT - Sistema de Veeduría Ciudadana', 0, 1, 'C');
        
        return $pdf;
    }

    /**
     * Guardar PDF en storage
     */
    public function guardarPDF($pdf, $nombreArchivo, $categoria = 'general')
    {
        $ruta = 'pdfs/' . $categoria . '/' . $nombreArchivo . '.pdf';
        
        // Crear directorio si no existe
        $directorio = 'pdfs/' . $categoria;
        if (!Storage::disk('public')->exists($directorio)) {
            Storage::disk('public')->makeDirectory($directorio);
        }
        
        // Guardar PDF
        $contenido = $pdf->Output('', 'S');
        Storage::disk('public')->put($ruta, $contenido);
        
        return $ruta;
    }

    /**
     * Generar y guardar PDF completo
     */
    public function generarYGuardarPDF($tipo, $datos, $nombreArchivo = null)
    {
        if (!$nombreArchivo) {
            $nombreArchivo = $tipo . '_' . time() . '_' . Str::random(8);
        }
        
        $pdf = null;
        
        switch ($tipo) {
            case 'analisis_legal':
                $pdf = $this->generarAnalisisLegal($datos);
                break;
            case 'pqrsfd':
                $pdf = $this->generarPQRSFD($datos);
                break;
            case 'donacion':
                $pdf = $this->generarDonacion($datos);
                break;
            case 'reporte_veeduria':
                $pdf = $this->generarReporteVeeduria($datos);
                break;
            default:
                throw new \Exception('Tipo de PDF no soportado: ' . $tipo);
        }
        
        $ruta = $this->guardarPDF($pdf, $nombreArchivo, $tipo);
        
        return [
            'pdf' => $pdf,
            'ruta' => $ruta,
            'nombre_archivo' => $nombreArchivo . '.pdf',
            'url' => Storage::disk('public')->url($ruta)
        ];
    }
}
