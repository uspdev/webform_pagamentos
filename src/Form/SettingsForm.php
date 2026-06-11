<?php

namespace Drupal\webform_pagamentos\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames(): array {
    return ['webform_pagamentos.settings'];
  }

  public function getFormId(): string {
    return 'webform_pagamentos_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config('webform_pagamentos.settings');

    $form['user'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Usuário'),
      '#default_value' => $config->get('user'),
      '#required' => FALSE,
      '#attributes' => [
      'placeholder' => $this->t('Exemplo: fflch'),
  ],
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Senha'),
      '#default_value' => $config->get('password'),
      '#required' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {

      $config = $this->configFactory()
      ->getEditable('webform_pagamentos.settings');

      $config->set('user', $form_state->getValue('user'));

      // Only update password if a new one was entered.
      if ($password = $form_state->getValue('password')) {
        $config->set('password', $password);
      }

      $config->save();

      parent::submitForm($form, $form_state);
}

}
