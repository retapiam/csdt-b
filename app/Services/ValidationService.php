<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    /**
     * Validar datos de usuario
     */
    public static function validarUsuario(array $data, $excludeId = null): array
    {
        $rules = [
            'nom' => 'required|string|max:100',
            'ape' => 'required|string|max:100',
            'cor' => 'required|email|max:150',
            'con' => 'required|string|min:8',
            'tel' => 'nullable|string|max:20',
            'doc' => 'required|string|max:20',
            'tip_doc' => 'required|in:cc,ce,ti,pp,nit',
            'fec_nac' => 'nullable|date|before:today',
            'dir' => 'nullable|string|max:255',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|in:m,f,o,n',
            'rol' => 'required|in:cli,ope,adm,adm_gen',
            'est' => 'nullable|in:act,ina,sus,pen',
        ];

        // Agregar reglas de unicidad excluyendo el ID actual
        if ($excludeId) {
            $rules['cor'] .= '|unique:usu,cor,' . $excludeId;
            $rules['doc'] .= '|unique:usu,doc,' . $excludeId;
        } else {
            $rules['cor'] .= '|unique:usu,cor';
            $rules['doc'] .= '|unique:usu,doc';
        }

        $messages = [
            'nom.required' => 'El nombre es obligatorio',
            'nom.max' => 'El nombre no puede tener más de 100 caracteres',
            'ape.required' => 'El apellido es obligatorio',
            'ape.max' => 'El apellido no puede tener más de 100 caracteres',
            'cor.required' => 'El correo electrónico es obligatorio',
            'cor.email' => 'El correo electrónico debe tener un formato válido',
            'cor.unique' => 'Este correo electrónico ya está registrado',
            'con.required' => 'La contraseña es obligatoria',
            'con.min' => 'La contraseña debe tener al menos 8 caracteres',
            'doc.required' => 'El documento es obligatorio',
            'doc.unique' => 'Este documento ya está registrado',
            'tip_doc.required' => 'El tipo de documento es obligatorio',
            'tip_doc.in' => 'El tipo de documento no es válido',
            'rol.required' => 'El rol es obligatorio',
            'rol.in' => 'El rol no es válido',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar datos de veeduría
     */
    public static function validarVeeduria(array $data): array
    {
        $rules = [
            'tit' => 'required|string|max:200',
            'des' => 'required|string',
            'tip' => 'required|in:pet,que,rec,sug,fel,den',
            'cat' => 'required|in:inf,ser,seg,edu,sal,tra,amb,otr',
            'pri' => 'required|in:baj,med,alt,urg',
            'est' => 'nullable|in:pen,pro,rad,cer,can',
            'usu_id' => 'required|exists:usu,id',
            'ope_id' => 'nullable|exists:usu,id',
            'ubi' => 'nullable|string|max:200',
            'fec_ven' => 'nullable|date|after:today',
        ];

        $messages = [
            'tit.required' => 'El título es obligatorio',
            'tit.max' => 'El título no puede tener más de 200 caracteres',
            'des.required' => 'La descripción es obligatoria',
            'tip.required' => 'El tipo de veeduría es obligatorio',
            'tip.in' => 'El tipo de veeduría no es válido',
            'cat.required' => 'La categoría es obligatoria',
            'cat.in' => 'La categoría no es válida',
            'pri.required' => 'La prioridad es obligatoria',
            'pri.in' => 'La prioridad no es válida',
            'usu_id.required' => 'El usuario es obligatorio',
            'usu_id.exists' => 'El usuario no existe',
            'ope_id.exists' => 'El operador no existe',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar datos de donación
     */
    public static function validarDonacion(array $data): array
    {
        $rules = [
            'usu_id' => 'required|exists:usu,id',
            'vee_id' => 'nullable|exists:vee,id',
            'mon' => 'required|numeric|min:0.01',
            'tip' => 'required|in:efectivo,transferencia,tarjeta,cheque',
            'est' => 'nullable|in:pen,pro,con,rej,can',
            'ref' => 'nullable|string|max:100',
            'des' => 'nullable|string|max:500',
            'fec_don' => 'nullable|date',
            'fec_con' => 'nullable|date|after:fec_don',
        ];

        $messages = [
            'usu_id.required' => 'El usuario es obligatorio',
            'usu_id.exists' => 'El usuario no existe',
            'vee_id.exists' => 'La veeduría no existe',
            'mon.required' => 'El monto es obligatorio',
            'mon.numeric' => 'El monto debe ser numérico',
            'mon.min' => 'El monto debe ser mayor a 0',
            'tip.required' => 'El tipo de pago es obligatorio',
            'tip.in' => 'El tipo de pago no es válido',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar datos de tarea
     */
    public static function validarTarea(array $data): array
    {
        $rules = [
            'tit' => 'required|string|max:200',
            'des' => 'required|string',
            'pri' => 'required|in:baj,med,alt,urg',
            'est' => 'nullable|in:pen,pro,com,can,sus',
            'vee_id' => 'required|exists:vee,id',
            'asig_por' => 'required|exists:usu,id',
            'asig_a' => 'nullable|exists:usu,id',
            'fec_ven' => 'nullable|date|after:today',
            'fec_ini' => 'nullable|date',
            'fec_fin' => 'nullable|date|after:fec_ini',
        ];

        $messages = [
            'tit.required' => 'El título es obligatorio',
            'tit.max' => 'El título no puede tener más de 200 caracteres',
            'des.required' => 'La descripción es obligatoria',
            'pri.required' => 'La prioridad es obligatoria',
            'pri.in' => 'La prioridad no es válida',
            'vee_id.required' => 'La veeduría es obligatoria',
            'vee_id.exists' => 'La veeduría no existe',
            'asig_por.required' => 'El asignador es obligatorio',
            'asig_por.exists' => 'El asignador no existe',
            'asig_a.exists' => 'El asignado no existe',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar datos de archivo
     */
    public static function validarArchivo(array $data): array
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'tip' => 'required|string|max:50',
            'tam' => 'required|integer|min:1',
            'ruta' => 'required|string|max:500',
            'usu_id' => 'required|exists:usu,id',
            'vee_id' => 'nullable|exists:vee,id',
            'tar_id' => 'nullable|exists:tar,id',
            'des' => 'nullable|string|max:500',
        ];

        $messages = [
            'nom.required' => 'El nombre del archivo es obligatorio',
            'nom.max' => 'El nombre del archivo no puede tener más de 255 caracteres',
            'tip.required' => 'El tipo de archivo es obligatorio',
            'tam.required' => 'El tamaño del archivo es obligatorio',
            'tam.integer' => 'El tamaño del archivo debe ser numérico',
            'tam.min' => 'El tamaño del archivo debe ser mayor a 0',
            'ruta.required' => 'La ruta del archivo es obligatoria',
            'usu_id.required' => 'El usuario es obligatorio',
            'usu_id.exists' => 'El usuario no existe',
            'vee_id.exists' => 'La veeduría no existe',
            'tar_id.exists' => 'La tarea no existe',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar datos de login
     */
    public static function validarLogin(array $data): array
    {
        $rules = [
            'cor' => 'required|email',
            'con' => 'required|string|min:8',
        ];

        $messages = [
            'cor.required' => 'El correo electrónico es obligatorio',
            'cor.email' => 'El correo electrónico debe tener un formato válido',
            'con.required' => 'La contraseña es obligatoria',
            'con.min' => 'La contraseña debe tener al menos 8 caracteres',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validar datos de registro
     */
    public static function validarRegistro(array $data): array
    {
        $rules = [
            'nom' => 'required|string|max:100',
            'ape' => 'required|string|max:100',
            'cor' => 'required|email|unique:usu,cor',
            'con' => 'required|string|min:8|confirmed',
            'tel' => 'nullable|string|max:20',
            'doc' => 'required|string|max:20|unique:usu,doc',
            'tip_doc' => 'required|in:cc,ce,ti,pp,nit',
            'fec_nac' => 'nullable|date|before:today',
            'dir' => 'nullable|string|max:255',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|in:m,f,o,n',
            'rol' => 'required|in:cli,ope,adm,adm_gen',
        ];

        $messages = [
            'nom.required' => 'El nombre es obligatorio',
            'ape.required' => 'El apellido es obligatorio',
            'cor.required' => 'El correo electrónico es obligatorio',
            'cor.email' => 'El correo electrónico debe tener un formato válido',
            'cor.unique' => 'Este correo electrónico ya está registrado',
            'con.required' => 'La contraseña es obligatoria',
            'con.min' => 'La contraseña debe tener al menos 8 caracteres',
            'con.confirmed' => 'Las contraseñas no coinciden',
            'doc.required' => 'El documento es obligatorio',
            'doc.unique' => 'Este documento ya está registrado',
            'tip_doc.required' => 'El tipo de documento es obligatorio',
            'tip_doc.in' => 'El tipo de documento no es válido',
            'rol.required' => 'El rol es obligatorio',
            'rol.in' => 'El rol no es válido',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
