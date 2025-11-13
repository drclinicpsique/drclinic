<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfissionalUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profissionalId = (int) ($this->route('id') ?? $this->route('profissional') ?? 0);
        $profissional = \App\Models\Profissional::find($profissionalId);
        $usuarioId = $profissional->usuario_id ?? null;

        return [
            // Usuário
            'nome_completo' => ['required', 'string', 'max:150', 'min:3'],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('usuarios', 'email')->ignore($usuarioId),
            ],
            'telefone' => ['nullable', 'string', 'digits_between:10,11'],

            // Profissional
            'crm' => [
                'required',
                'string',
                'max:20',
                'min:3',
                Rule::unique('profissionais', 'crm')->ignore($profissionalId),
                'regex:/^[0-9]{4,10}\/[A-Z]{2}$/',
            ],
            'especialidade' => ['required', 'string', 'max:100', 'min:3'],
            'telefone_consultorio' => ['nullable', 'string', 'digits_between:10,11'],
            'formacao_academica' => ['nullable', 'string', 'max:2000'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'ativo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome_completo.required' => 'O nome completo é obrigatório.',
            'nome_completo.min' => 'O nome completo deve ter no mínimo 3 caracteres.',

            'email.required' => 'O email é obrigatório.',
            'email.unique' => 'Este email já está cadastrado para outro usuário.',

            'telefone.digits_between' => 'O telefone deve ter entre 10 e 11 dígitos.',

            'crm.required' => 'O CRM é obrigatório.',
            'crm.min' => 'O CRM deve ter no mínimo 3 caracteres.',
            'crm.unique' => 'Este CRM já está cadastrado para outro profissional.',
            'crm.regex' => 'O CRM deve estar no formato 00000/UF (exemplo: 12345/SP).',

            'especialidade.required' => 'A especialidade é obrigatória.',
            'especialidade.min' => 'A especialidade deve ter no mínimo 3 caracteres.',

            'telefone_consultorio.digits_between' => 'O telefone do consultório deve ter entre 10 e 11 dígitos.',

            'formacao_academica.max' => 'A formação acadêmica deve ter no máximo 2000 caracteres.',
            'observacoes.max' => 'As observações devem ter no máximo 2000 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normaliza CRM para o padrão 00000/UF
        if ($this->crm) {
            $crmLimpo = strtoupper(preg_replace('/[^0-9A-Z\/]/', '', $this->crm));
            $this->merge(['crm' => $crmLimpo]);
        }

        // Remove TODA a máscara dos telefones (salva apenas números)
        if ($this->telefone) {
            $telefoneLimpo = preg_replace('/[^0-9]/', '', $this->telefone);
            if (empty($telefoneLimpo)) {
                $this->request->remove('telefone');
            } else {
                $this->merge(['telefone' => $telefoneLimpo]);
            }
        }

        if ($this->telefone_consultorio) {
            $telefoneLimpo = preg_replace('/[^0-9]/', '', $this->telefone_consultorio);
            if (empty($telefoneLimpo)) {
                $this->request->remove('telefone_consultorio');
            } else {
                $this->merge(['telefone_consultorio' => $telefoneLimpo]);
            }
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validação adicional de CRM
            if ($this->crm) {
                $partes = explode('/', $this->crm);
                if (count($partes) === 2) {
                    $uf = $partes[1];

                    $ufsValidas = [
                        'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA',
                        'MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN',
                        'RS','RO','RR','SC','SP','SE','TO'
                    ];

                    if (!in_array($uf, $ufsValidas, true)) {
                        $validator->errors()->add('crm', 'A UF do CRM é inválida. Use siglas válidas (ex: SP, RJ, MG).');
                    }
                } else {
                    $validator->errors()->add('crm', 'O CRM deve conter o número e a UF separados por barra (/).');
                }
            }
        });
    }
}