<?php

namespace Drupal\webform_boleto_usp;

class Gera {

    public static function gera($data, $element){
        // $element['#boletousp_valor'];
        die("Cheguei");
    }

    function dados_boleto($mapping, $submission,$nid){
      $dados = array();
      foreach($mapping as $k => $valor) {
        if($k === 'codigoUnidadeDespesa') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'nomeFonte') {
          $dados[$k] = utf8_decode($submission->data[$mapping[$k]][0]);
        }
        else if($k === 'nomeSubfonte') {
          $dados[$k] =  utf8_decode($submission->data[$mapping[$k]][0]);
        }
        else if($k === 'estruturaHierarquica') {
          $dados[$k] = utf8_decode($submission->data[$mapping[$k]][0]);
        }
        else if($k === 'codigoConvenio') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'dataVencimentoBoleto') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'valorDocumento') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'valorDesconto') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'tipoSacado') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'cpfCnpj') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'nomeSacado') {
          $dados[$k] =  utf8_decode($submission->data[$mapping[$k]][0]);
        }
        else if($k === 'codigoEmail') {
          $dados[$k] = $submission->data[$mapping[$k]][0];
        }
        else if($k === 'informacoesBoletoSacado') {
          $dados[$k] =  utf8_decode($submission->data[$mapping[$k]][0]);
        }
        else if($k === 'instrucoesObjetoCobranca') {
          $dados[$k] =  utf8_decode($submission->data[$mapping[$k]][0]);
        }
      }    
      gerar_boleto($dados,$submission,$nid);
    }

    function gerar_boleto($dados,$submission,$nid) {
      require_once('config.php');
      require_once('nusoap/lib/nusoap.php');
      
      $retorno = null;
      $erro = null;
      
      $wsdl_path = 'http://' . $_SERVER['HTTP_HOST'] . base_path() . drupal_get_path('module', 'boletousp') . '/wsdl/boleto.wsdl';
      $clienteSoap = new nusoap_client($wsdl_path, 'wsdl');

      $erro = $clienteSoap->getError();
      if ($erro){
        print_r($erro); // issue3: mandar para log do Drupal.
        exit;
      }

      $soapHeaders = array('username' => USERNAME_WSDL, 'password' => PASSWORD_WSDL);
      $clienteSoap->setHeaders($soapHeaders);

      //faz a requisição SOAP para gerar o codigo do boleto
      $retorno = $clienteSoap->call('gerarBoleto', array('requisicao' => $dados));

      //verifica se houve erro na geração do boleto.
      if ($clienteSoap->fault) {
        print_r($retorno["detail"]["WSException"]); // issue3: mandar para log do Drupal.
        exit;
      }
      else {
        $codigoIDBoleto = $retorno['identificacao']['codigoIDBoleto'];
        $param = array('codigoIDBoleto' => $codigoIDBoleto);
	      $retorno = $clienteSoap->call('obterBoleto', array('identificacao' => $param));
	      if ($clienteSoap->fault) {
		      print_r($retorno);  // issue3: mandar para log do Drupal.
		      exit;
	      }
	      if ($clienteSoap->getError()){
		      print_r($retorno); // issue3: mandar para log do Drupal.
		      exit;
	      } 
        file_save_data(base64_decode($retorno['boletoPDF']),'public://' . "/{$nid}{$submission->sid}{$dados['cpfCnpj']}" . ".pdf");
      }
}
