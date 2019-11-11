<?php

namespace Drupal\webform_boleto_usp;
use Uspdev\Boleto;

class Gera {

    public static function gera($data, $element){

        /* Ricardo: Criar uma tela de configuração para salvar user/token
          Exemplo: https://github.com/uspdev/senhaunicausp-drupal/blob/8.x-1.x/src/Form/SenhaunicauspForm.php */
        $boleto = new Boleto('fflch','abc'); 

        /* Augusto: fazer o bind de element/data para gerar o form */
        $data = array(
            'codigoUnidadeDespesa' => 8,
            'nomeFonte' => 'Taxas', 
            'nomeSubfonte' => utf8_decode('Congressos/Seminários/Palestras/Simpósios') , 
            'estruturaHierarquica' => '\FFLCH\SCINFOR',   
            'codigoConvenio' => 0 ,  
            'dataVencimentoBoleto' => '12/12/2019', 
            'valorDocumento' => 1.5,
            'valorDesconto' => 0, 
            'tipoSacado' => 'PF', 
            'cpfCnpj' => '33838180801', 
            'nomeSacado' => $data[$element->boletousp_nomeSacado],
            'codigoEmail' => 'thiago.verissimo@usp.br',  
            'informacoesBoletoSacado' => utf8_decode('Qualquer informações que queira colocar'),
            'instrucoesObjetoCobranca' => utf8_decode('Não receber após vencimento!')
        );

        return $boleto->gerar($data);
        /* Thiago: Dado um id, mostrar o pdf do boleto */
        /* Nos resultados, exportar a situação: pago, cancelado, vencido etc*/
    }
}

