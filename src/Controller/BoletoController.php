<?php

namespace Drupal\webform_boleto_usp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\WebformSubmission;
use Uspdev\Boleto;

/**
 * Class BoletoController.
 */
class BoletoController extends ControllerBase {

  public function gera($webform_submission_id) {
    $webform_submission = WebformSubmission::load($webform_submission_id);

    if($webform_submission) {

      /* Verificamos se há boleto gerado para esse webform_submission */
      $data = $webform_submission->getData();
      if(isset($data['id_boleto']) && !empty($data['id_boleto'])){
        
        $config = \Drupal::service('config.factory')->getEditable('webform_boleto_usp.settings');

        $boleto = new Boleto($config->get('user_id'),$config->get('token'));

        $boleto->obter($data['id_boleto']);

      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Não foi possível gerar o boleto.'),
    ];
  }

}
