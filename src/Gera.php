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
            'codigoUnidadeDespesa' => $element["#boletousp_codigoUnidadeDespesa"],
            'codigoFonteRecurso' => $element["#boletousp_codigoFonteRecurso"], 
            'estruturaHierarquica' => $element["#boletousp_estruturaHierarquica"],   
            'codigoConvenio' => 0 ,  
            'dataVencimentoBoleto' => Gera::converteData($element["#boletousp_dataVencimentoBoleto"]),
            'valorDocumento' => str_replace(',','.',$element["#boletousp_valorDocumento"]),
            'valorDesconto' => 0, 
            'tipoSacado' => 'PF', 
            'informacoesBoletoSacado' => $element["#boletousp_informacoesBoletoSacado"],
            'instrucoesObjetoCobranca' => $element["#boletousp_instrucoesObjetoCobranca"],
            /* Campos mapeados */
            'cpfCnpj' => $data[$element["#boletousp_cpfCnpj"]], 
            'nomeSacado' => $data[$element["#boletousp_nomeSacado"]],
            'codigoEmail' => $data[$element["#boletousp_codigoEmail"]]
        );
        
        /* Campos opicionais */
        if(!is_null($element["#boletousp_numeroUspUsuario"]) && !empty($element["#boletousp_numeroUspUsuario"])){
            $output['numeroUspUsuario'] = $data[$element["#boletousp_numeroUspUsuario"]];
        }

        if(!is_null($element["#boletousp_cepSacado"]) && !empty($element["#boletousp_cepSacado"])){
            $output['cepSacado'] = $data[$element["#boletousp_cepSacado"]];
        }

        return $boleto->gerar($output);
    }
}

