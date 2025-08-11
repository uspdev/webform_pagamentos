<?php

namespace Drupal\webform_boleto_usp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class WebformBoletoUspForm
 */
class WebformBoletoUspForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'webform_boleto_usp_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'webform_boleto_usp.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('webform_boleto_usp.settings');
    $form['auth'] = [
      '#type'          => 'details',
      '#title'         => $this->t('WebServer Autenticação'),
      '#description'   => $this->t(''),
      '#open'          => TRUE,
    ];
     $form['auth']['user_id'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Usuário'),
      '#size'          => 15,
      '#required'      => TRUE,
      '#default_value' => $config->get('user_id'),
    ];
    $form['auth']['token'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Token'),
      '#required'      => TRUE,
      '#default_value' => $config->get('token'),
    ];
    $form['auth']['codigoUnidadeDespesa'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Código da Unidade de Despesa'),
      '#size'          => 15,
      '#required'      => TRUE,
      '#default_value' => $config->get('codigoUnidadeDespesa'),
    ];
    $form['auth']['estruturaHierarquica'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Centros de Despesas da Unidade'),
      '#description'   => $this->t("Um Centro por linha"),
      '#size'          => 15,
      '#required'      => TRUE,
      '#default_value' => $config->get('estruturaHierarquica'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('webform_boleto_usp.settings')
         ->set('user_id', $values['user_id'])
         ->set('token', $values['token'])
         ->set('codigoUnidadeDespesa', $values['codigoUnidadeDespesa'])
         ->set('estruturaHierarquica', $values['estruturaHierarquica'])
         ->save();
    parent::submitForm($form, $form_state);
  }

}
