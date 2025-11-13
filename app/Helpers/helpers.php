<?php

if (!function_exists('formatarTelefone')) {
    /**
     * Formata telefone: 11987654321 → (11) 98765-4321
     */
    function formatarTelefone(?string $telefone): string
    {
        if (empty($telefone)) {
            return '';
        }

        // Remove tudo que não é número
        $telefone = preg_replace('/[^0-9]/', '', $telefone);

        // Se tem 11 dígitos: (00) 00000-0000
        if (strlen($telefone) === 11) {
            return sprintf('(%s) %s-%s', 
                substr($telefone, 0, 2),
                substr($telefone, 2, 5),
                substr($telefone, 7, 4)
            );
        }

        // Se tem 10 dígitos: (00) 0000-0000
        if (strlen($telefone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($telefone, 0, 2),
                substr($telefone, 2, 4),
                substr($telefone, 6, 4)
            );
        }

        // Se não está no formato esperado, retorna como está
        return $telefone;
    }
}