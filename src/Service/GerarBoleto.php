<?php

namespace Drupal\webform_pagamentos\Service;
use Uspdev\Boleto;


class GerarBoleto {

public function generate(array $settings, array $submission_data): array {

    $codigoFonteRecursoSET = $settings['codigoFonteRecurso'] ?? '';
    $estruturaHierarquicaSET = $settings['estruturaHierarquica'] ?? '';
    $dataVencimentoSET = $settings['dataVencimento'] ?? '';
    $informacoesSacadoSET = $settings['informacoesSacado'] ?? '';
    $instrucoesObjetoCobrancaSET = $settings['instrucoesObjetoCobranca'] ?? '';
    $valorDocumentoSET = $settings['valorDocumento'] ?? '';
    $nomeSacadoSET = $settings['nomeSacado'] ?? '';
    $codigoEmailSET = $settings['codigoEmail'] ?? '';
    $cpfCnpjSET = $settings['cpfCnpj'] ?? '';
    $numeroUspSacadoSET = $settings['numeroUspSacado'] ?? '';


    //GUARDANDO PARA CASO PRECISE ADICIONAR INFORMAÇÕES COLOCADAS PELO USUÁRIO:

    // $codigoFonteRecurso = $submission_data[$codigoFonteRecursoSET] ?? '';
    // $estruturaHierarquica = $submission_data[$estruturaHierarquicaSET] ?? '';
    // $dataVencimento = $submission_data[$dataVencimentoSET] ?? '';
    // $informacoesSacado = $submission_data[$informacoesSacadoSET] ?? '';
    // $instrucoesObjetoCobranca = $submission_data[$instrucoesObjetoCobrancaSET] ?? '';
    // $valorDocumento = $submission_data[$valorDocumentoSET] ?? '';
    // $nomeSacado = $submission_data[$nomeSacadoSET] ?? '';
    // $codigoEmail = $submission_data[$codigoEmailSET] ?? '';
    // $cpfCnpj = $submission_data[$cpfCnpjSET] ?? '';
    // $numeroUspSacado = $submission_data[$numeroUspSacadoSET] ?? '';

    ///
    ///Recupera USER E LOGIN a partir do Settings no menuzinho lá de configuração do Drupal ;)

    $config = \Drupal::config('webform_pagamentos.settings');
    $user = $config->get('user');
    $password = $config->get('password');

    ///
    ///


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
    $id = $gerar['value'];
    $obter = $boleto->obter($id);

    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="boleto.pdf"');
    echo base64_decode($obter['value']);
}
};








//  // Inicialização do serviço com as credenciais criadas
//  // [Ambiente de DEV] = ('consumerdi','teste1')
//  // [Ambiente de PRD] = solicitar credenciais em https://servicos.sti.usp.br/ws-boleto/
// $boleto = new Boleto('usuario','senha');
//
// /* array com campos mínimos para geração do boleto */
// $data = array(
//     'codigoUnidadeDespesa' => 8,
//     'codigoFonteRecurso' => 32,
//     'estruturaHierarquica' => '\FFLCH\SCINFOR',
//     'dataVencimentoBoleto' => '10/11/2018',
//     'valorDocumento' => 18.20,
//     'tipoSacado' => 'PF',
//     'cpfCnpj' => '99999999999',
//     'nomeSacado' => 'Fulano',
//     'codigoEmail' => 'fulano@usp.br',
//     'informacoesBoletoSacado' => 'Qualquer informações que queira colocar',
//     'instrucoesObjetoCobranca' => 'Não receber após vencimento!',
// );
//
// // [Método Gerar] gerar boleto
// $gerar = $boleto->gerar($data);
// if($gerar['status']) {
//     $id = $gerar['value'];
//
// 	 // [Método Situacao] resgatar informações do boleto
//     print_r($boleto->situacao($id));
//
// 	 // [Método Obter] recupera o arquivo PDF do boleto
// 	 // (PDF no formato binário codificado para Base64)
//     $obter = $boleto->obter($codigoIDBoleto);
//
//     //redirecionando os dados binarios do pdf para o browser
//     header('Content-type: application/pdf');
//     header('Content-Disposition: attachment; filename="boleto.pdf"');
//     echo base64_decode($obter['value']);
//
//    // [Método Cancelar] cancelar boleto
// 	$boleto->cancelar($id);
// }
