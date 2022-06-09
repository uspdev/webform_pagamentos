<?php

namespace Drupal\webform_boleto_usp;
use Uspdev\Boleto;

class Gera {
    public static function converteData($vencimento){
        return implode('/',array_reverse(explode('-',$vencimento)));
    }

    public static function gera($data, $element){
        $config = \Drupal::service('config.factory')->getEditable('webform_boleto_usp.settings');
        $boleto = new Boleto($config->get('user_id'),$config->get('token'));
        $output = array(
            'codigoUnidadeDespesa'     => $config->get('codigoUnidadeDespesa'),
            'codigoFonteRecurso'       => $element["#boletousp_codigoFonteRecurso"],
            'estruturaHierarquica'     => $element["#boletousp_estruturaHierarquica"],
            'dataVencimentoBoleto'     => Gera::converteData($element["#boletousp_dataVencimentoBoleto"]),
            'valorDocumento'           => str_replace(',','.',$element["#boletousp_valorDocumento"]),
            'tipoSacado'               => 'PF', 
            'informacoesBoletoSacado'  => $element["#boletousp_informacoesBoletoSacado"],
            'instrucoesObjetoCobranca' => $element["#boletousp_instrucoesObjetoCobranca"],
            /* Campos mapeados */
            'nomeSacado'      => $data[$element["#boletousp_nomeSacado"]],
            'codigoEmail'     => $data[$element["#boletousp_codigoEmail"]],
            'numeroUspSacado' => $data[$element["#boletousp_numeroUspSacado"]],
        );

        if(!empty($data[$element["#boletousp_numeroUspsacado"]])){
            array_push ... $output
        }
        'numeroUspsacado' = $data[$element["#boletousp_numeroUspsacado"]]

        'cpfCnpj'         => \Drupal::service('cpf')->digits($data[$element["#boletousp_cpfCnpj"]]), 


        return $boleto->gerar($output);
    }
}
