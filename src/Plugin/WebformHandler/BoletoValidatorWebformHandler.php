<?php

namespace Drupal\webform_boleto_usp\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "webform_boleto_usp_validator",
 *   label = @Translation("Validação para boleto USP"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Validação para boleto USP"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class BoletoValidatorWebformHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->validateBoletoUSP($form_state, $webform_submission);
  }

  /**
   * Validate cpf e email para boleto USP.
   */
  private function validateBoletoUSP(FormStateInterface $formState, WebformSubmissionInterface $webform_submission) {

    /* Dados submetidos */
    $data = $formState->getValues();

    /* Coletando os campos que estão mapeados */
    $webform = $webform_submission->getWebform();
    $elements = $webform->getElementsInitializedAndFlattened();

    /* verifico se todos campos mapeados existem no formulário */
    $cpf_key = $elements['boletousp']['#boletousp_cpfCnpj'];
    $email_key = $elements['boletousp']['#boletousp_codigoEmail'];
    $nome_key = $elements['boletousp']['#boletousp_nomeSacado'];
    $keys = [$cpf_key, $email_key, $nome_key];

    foreach($keys as $key) {
        if(!array_key_exists($key, $data))
            $formState->setErrorByName($key, 
                $this->t('Não existe o campo '. $key . 'no formulário'));
    }
    
    /* Validação do cpf */
    $cpf = \Drupal::service('cpf')->digits($data[$cpf_key]);
    if(!\Drupal::service('cpf')->isValid($cpf)) {
      if($cpf != '99999999999')
        $formState->setErrorByName($cpf_key, 
            $this->t('O número de CPF %cpf não é válido', ['%cpf' => $data[$cpf_key]]));
    }

    /* TODO: validação do email */

    /* TODO: validação objeto de cobrança */

    /* TODO: validação objeto sacado */
  }
}
