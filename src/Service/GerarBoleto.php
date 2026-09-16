<?php

namespace Drupal\webform_pagamentos\Service;

use Uspdev\Boleto;

use Symfony\Component\HttpFoundation\RedirectResponse;


class GerarBoleto {

    public function generate(array $settings, array $submission_data): array {

        // Campos oriundos da configuração
        $codigoFonteRecursoSET = $settings['codigoFonteRecurso'] ?? '';
        $estruturaHierarquicaSET = $settings['estruturaHierarquica'] ?? '';
        $dataVencimentoSET = $settings['dataVencimento'] ?? '';
        $informacoesSacadoSET = $settings['informacoesSacado'] ?? '';
        $instrucoesObjetoCobrancaSET = $settings['instrucoesObjetoCobranca'] ?? '';
        $valorDocumentoSET = $settings['valorDocumento'] ?? '';

        // Campos preenchidos no formulário
        $nomeSacadoSET = $submission_data[$settings['nomeSacado']] ?? '';
        $codigoEmailSET = $submission_data[$settings['codigoEmail']] ?? '';
        $cpfCnpjSET = $submission_data[$settings['cpfCnpj']] ?? '';
        $numeroUspSacadoSET = $submission_data[$settings['numeroUspSacado']] ?? '';

        // Usuário e senha da API do boleto da configuração global
        $config = \Drupal::config('webform_pagamentos.settings');
        $user = $config->get('user');
        $password = $config->get('password');

        $boleto = new Boleto($user, $password);
        /* array com campos mínimos para geração do boleto */
        $data = array(
            'codigoUnidadeDespesa' => 8,
            'codigoFonteRecurso' => $codigoFonteRecursoSET,
            'estruturaHierarquica' => $estruturaHierarquicaSET,
            'dataVencimentoBoleto' => $dataVencimentoSET,
            'valorDocumento' => $valorDocumentoSET,
            'tipoSacado' => 'PF',
            'cpfCnpj' =>  $cpfCnpjSET,
            'nomeSacado' => $nomeSacadoSET,
            'codigoEmail' => $codigoEmailSET,
            'informacoesBoletoSacado' => $informacoesSacadoSET,
            'instrucoesObjetoCobranca' => $instrucoesObjetoCobrancaSET,
        );


        $gerar = $boleto->gerar($data);
        dd($gerar);

        if ($gerar['status'] == false) {
            \Drupal::messenger()->addError(t('Não foi possível gerar o boleto: @erro', [
                '@erro' => $gerar['value'],
            ]));
            dd($gerar);
            dd('Isaac: Redirecionar para o formulário:');
        } else {
            $id = $gerar['value'];
            $obter = $boleto->obter($id);

            // Isaac: guardar o código do boleto id hash, submission_id, e a mensagem de erro quando for erro

            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="boleto.pdf"');
            echo base64_decode($obter['value']);
        }
    }
}
