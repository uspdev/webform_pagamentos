<?php

namespace Drupal\webform_boleto_usp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\WebformSubmission;
use Uspdev\Boleto;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BoletoController.
 */
class BoletoController extends ControllerBase {

  public function gera($webform_submission_id) {
    $webform_submission = WebformSubmission::load($webform_submission_id);

    $msg = '';

    if($webform_submission) {

      /* Verificamos se há boleto gerado para esse webform_submission */
      $data = $webform_submission->getData();
      if(isset($data['boleto_status'])){

        if($data['boleto_status'] == true) {
            $config   = \Drupal::service('config.factory')->getEditable('webform_boleto_usp.settings');
            $pay_type = $config->get('pay_type');
            $boleto   = new Boleto($config->get('user_id'),$config->get('token'));
            $obter    = $boleto->obter($data['boleto_id']);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/pdf');
            $response->setContent(base64_decode($obter['value']));
            return $response;
        } else {
            $msg = $data['boleto_erro'];
        }
      }
    }
    else {
        $msg = "Não existe submissão com id {$webform_submission_id}";
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t("Não foi possível gerar o boleto: @msg <br> Tipo de pagamento configurado: @pay_type", [
        '@msg' => $msg,
        '@pay_type' => $pay_type ?? 'não definido',
      ]),
    ];
  }

}
