# Documentación de IAs Especializadas en Derechos

## Descripción General

Este sistema implementa Inteligencia Artificial especializada en diferentes áreas de derechos, proporcionando análisis jurídicos exhaustivos con nivel post-doctorado. Las IAs están diseñadas para analizar casos complejos en múltiples especialidades del derecho.

## Especialidades Disponibles

### 1. Derechos Mineros
- **Endpoint**: `POST /api/ia-derechos/mineros`
- **Descripción**: Análisis especializado de derechos mineros nacionales e internacionales
- **Tipos**: general, oro, carbon, esmeraldas, petroleo, gas
- **Incluye**: Marco normativo, títulos mineros, consulta previa, impacto ambiental, regalías

### 2. Derechos Catastrales
- **Endpoint**: `POST /api/ia-derechos/catastrales`
- **Descripción**: Análisis de derechos catastrales e inmobiliarios
- **Tipos**: general, urbano, rural, comercial, residencial
- **Incluye**: Marco normativo catastral, derechos de propiedad, servidumbres, avalúos

### 3. Desarrollo Territorial
- **Endpoint**: `POST /api/ia-derechos/desarrollo-territorial`
- **Descripción**: Análisis de desarrollo territorial y planificación
- **Niveles**: municipal, departamental, nacional
- **Incluye**: Competencias territoriales, planificación urbana, participación ciudadana

### 4. Planes de Desarrollo y Gobierno
- **Endpoint**: `POST /api/ia-derechos/planes-desarrollo-gobierno`
- **Descripción**: Análisis de planes de desarrollo y gobierno
- **Tipos**: desarrollo, gobierno, ordenamiento, presupuesto
- **Incluye**: Marco normativo de planificación, competencias, procedimientos

### 5. Derechos Internacionales
- **Endpoint**: `POST /api/ia-derechos/internacionales`
- **Descripción**: Análisis de derechos internacionales y derecho internacional público
- **Áreas**: derechos_humanos, derecho_humanitario, derecho_ambiental, derecho_economico
- **Incluye**: Tratados internacionales, jurisprudencia internacional, mecanismos de protección

### 6. Derechos CAN e INCA
- **Endpoint**: `POST /api/ia-derechos/can-inca`
- **Descripción**: Análisis de derechos de integración regional
- **Tipos**: can, inca
- **Incluye**: Marco normativo de integración, derecho comunitario andino, libre circulación

### 7. Derechos Latinoamericanos
- **Endpoint**: `POST /api/ia-derechos/latinoamericanos`
- **Descripción**: Análisis de derechos comparado latinoamericano
- **Países**: colombia, venezuela, ecuador, peru, bolivia, chile, argentina, brasil, mexico
- **Incluye**: Derecho comparado, sistemas jurídicos latinoamericanos, integración regional

### 8. Derechos de Propiedad
- **Endpoint**: `POST /api/ia-derechos/propiedad`
- **Descripción**: Análisis de derechos de propiedad en raíz y propiedad
- **Tipos**: raiz, mueble, inmueble, intelectual, industrial
- **Incluye**: Derechos reales, adquisición de propiedad, protección, limitaciones

### 9. Derechos en Comunidades Étnicas
- **Endpoint**: `POST /api/ia-derechos/comunidades-etnicas`
- **Descripción**: Análisis de derechos en comunidades étnicas
- **Tipos**: indigena, afrodescendiente, raizal, palenquero
- **Incluye**: Convenio 169 OIT, Declaración ONU, derechos territoriales, consulta previa

## Uso de la API

### Autenticación
Todas las rutas requieren autenticación mediante token Bearer:
```bash
Authorization: Bearer {tu_token}
```

### Ejemplo de Uso - Derechos Mineros
```bash
curl -X POST "https://tu-dominio.com/api/ia-derechos/mineros" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "datos_mineros": "Análisis de concesión minera en territorio indígena Wayuu en La Guajira",
    "tipo_mineria": "oro"
  }'
```

### Ejemplo de Uso - Derechos Catastrales
```bash
curl -X POST "https://tu-dominio.com/api/ia-derechos/catastrales" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "datos_catastrales": "Análisis de derechos de propiedad en predio urbano en Bogotá",
    "tipo_predio": "residencial"
  }'
```

### Ejemplo de Uso - Desarrollo Territorial
```bash
curl -X POST "https://tu-dominio.com/api/ia-derechos/desarrollo-territorial" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "datos_territoriales": "Análisis de plan de desarrollo municipal en Medellín",
    "nivel_gobierno": "municipal"
  }'
```

## Análisis Combinados

### Análisis Mineros y Étnicos
```bash
curl -X POST "https://tu-dominio.com/api/ia-derechos-combinados/mineros-etnicos" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "datos_mineros": "Concesión minera en territorio indígena",
    "tipo_mineria": "oro",
    "datos_etnicos": "Comunidad Wayuu afectada por minería",
    "tipo_comunidad": "indigena"
  }'
```

### Análisis Integral
```bash
curl -X POST "https://tu-dominio.com/api/ia-derechos-combinados/integral" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "datos_mineros": "Concesión minera",
    "tipo_mineria": "oro",
    "datos_catastrales": "Derechos prediales afectados",
    "tipo_predio": "rural",
    "datos_etnicos": "Comunidad indígena afectada",
    "tipo_comunidad": "indigena"
  }'
```

## Gestión de Análisis

### Obtener Especialidades Disponibles
```bash
curl -X GET "https://tu-dominio.com/api/ia-derechos/especialidades" \
  -H "Authorization: Bearer {tu_token}"
```

### Obtener Análisis Realizados
```bash
curl -X GET "https://tu-dominio.com/api/analisis-ia-derechos" \
  -H "Authorization: Bearer {tu_token}"
```

### Obtener Estadísticas
```bash
curl -X GET "https://tu-dominio.com/api/analisis-ia-derechos/estadisticas" \
  -H "Authorization: Bearer {tu_token}"
```

## Respuesta de la API

### Estructura de Respuesta Exitosa
```json
{
  "success": true,
  "analisisCompleto": {
    "conceptoGeneralConsolidado": "Análisis completo del caso...",
    "metadata": {
      "area": "derechos_mineros",
      "tipo_mineria": "oro",
      "nivel": "post_doctorado",
      "especializacion": "derecho_minero_internacional"
    },
    "timestamp": "2024-01-15T10:30:00.000Z",
    "modelo": "gpt-4",
    "tokens_usados": 1250
  },
  "respuesta": "Análisis completo del caso..."
}
```

### Estructura de Respuesta de Error
```json
{
  "success": false,
  "message": "Datos de entrada inválidos",
  "errors": {
    "datos_mineros": ["El campo datos mineros es requerido"]
  }
}
```

## Características Técnicas

### Modelos de IA Utilizados
- **GPT-4**: Modelo principal para análisis jurídicos
- **Fallback**: Sistema de respaldo cuando la IA no está disponible

### Configuración
- **Temperatura**: 0.3 (para respuestas más precisas)
- **Max Tokens**: 4000
- **Timeout**: 60 segundos
- **Cache**: 1 hora (3600 segundos)

### Niveles de Análisis
- **Post-Doctorado**: Todos los análisis están configurados para nivel post-doctorado
- **Especialización**: Cada área tiene especialización específica
- **Jurisdicción**: Principalmente Colombia, con alcance internacional

## Monitoreo y Logs

### Métricas Disponibles
- Total de análisis realizados
- Análisis por área de derecho
- Análisis por estado
- Tokens utilizados
- Tiempo de procesamiento promedio
- Análisis recientes

### Logs de Sistema
- Todos los análisis se registran en la base de datos
- Logs de errores en el sistema de logging de Laravel
- Métricas de rendimiento disponibles

## Seguridad

### Autenticación
- Requiere token Bearer válido
- Verificación de permisos por usuario
- Logs de acceso y uso

### Validación
- Validación estricta de datos de entrada
- Sanitización de contenido
- Límites de tamaño de datos

### Privacidad
- Los datos se procesan de forma segura
- No se almacenan datos sensibles en logs
- Cumplimiento con normativas de protección de datos

## Soporte y Mantenimiento

### Contacto
- Para soporte técnico, contactar al administrador del sistema
- Documentación actualizada en este archivo
- Código fuente disponible en el repositorio del proyecto

### Actualizaciones
- El sistema se actualiza regularmente
- Nuevas especialidades se pueden agregar fácilmente
- Mejoras de rendimiento continuas

## Ejemplos de Casos de Uso

### 1. Análisis de Concesión Minera
```json
{
  "datos_mineros": "Solicitud de concesión minera de oro en territorio indígena Emberá en Chocó. La empresa minera solicita concesión por 20 años para explotación de oro aluvial. El territorio afectado incluye resguardo indígena y área de protección ambiental.",
  "tipo_mineria": "oro"
}
```

### 2. Análisis de Derechos Prediales
```json
{
  "datos_catastrales": "Conflicto de linderos entre dos predios rurales en Cundinamarca. Un predio de 50 hectáreas tiene conflicto de linderos con predio vecino. Se requiere análisis de derechos de propiedad y servidumbres.",
  "tipo_predio": "rural"
}
```

### 3. Análisis de Plan de Desarrollo
```json
{
  "datos_territoriales": "Plan de Desarrollo Municipal 2024-2027 en Bucaramanga. El plan incluye proyectos de infraestructura, vivienda social y protección ambiental. Se requiere análisis de competencias territoriales y participación ciudadana.",
  "nivel_gobierno": "municipal"
}
```

Este sistema de IAs especializadas proporciona análisis jurídicos de alta calidad para múltiples áreas del derecho, facilitando la toma de decisiones informadas en casos complejos.
