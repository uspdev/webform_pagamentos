<?php

namespace Drupal\webform_boleto_usp\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElementBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'boletoUSP' element.
 *
 * @WebformElement(
 *   id = "boletousp",
 *   default_key = "boletousp",
 *   api = "https://github.com/fflch/webform-boleto-usp",
 *   label = @Translation("BOLETOUSP"),
 *   description = @Translation("Provides a form element that manage boleto USP"),
 *   category = @Translation("Advanced elements"),
 *   states_wrapper = TRUE,
 * )
 */
class WebformElementBoletoUSP extends WebformElementBase {

  /**
   * {@inheritdoc}
   */
  public function getDefaultProperties() {
    return [
      'boletousp_type' => 'default',
      'boletousp_admin_mode' => FALSE,
      'boletousp_title' => '',
      'boletousp_description' => '',
      // Flexbox.
      'flex' => 1,
      // Conditional logic.
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isInput(array $element) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isContainer(array $element) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getItemDefaultFormat() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getItemFormats() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    parent::prepare($element, $webform_submission);
    $element['#after_build'][] = [get_class($this), 'afterBuildBoletoUSP'];
  }

  /**
   * {@inheritdoc}
   */
  public function preview() {
    $element = parent::preview() + [
      '#boletousp_admin_mode' => TRUE,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(array &$element, WebformSubmissionInterface $webform_submission) {
    /*
    $key = $element['#webform_key'];
    $data = $webform_submission->getData();
    unset($data[$key]);
    $sub_keys = ['sid', 'token', 'response'];
    foreach ($sub_keys as $sub_key) {
      unset($data[$key . '_' . $sub_key]);
    }
    $webform_submission->setData($data);
    */
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $boletousp_types = ['default' => $this->t('Default challenge type')];

    $form['boletousp'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('BoletoUSP settings'),
    ];
    $form['boletousp']['boletousp_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Challenge type'),
      '#required' => TRUE,
      '#options' => $boletousp_types,
    ];
    // Custom title and description.
    $form['boletousp']['boletousp_container'] = [
      '#type' => 'container',
      '#states' => [
        'invisible' => [[':input[name="properties[boletousp_type]"]' => ['value' => 'recaptcha/reCAPTCHA']]],
      ],
    ];
    $form['boletousp']['boletousp_container']['boletousp_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Question title'),
    ];
    $form['boletousp']['boletousp_container']['boletousp_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Question description'),
    ];
    // Admin mode.
    $form['boletousp']['boletousp_admin_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Admin mode'),
      '#description' => $this->t('Bla'),
      '#return_value' => TRUE,
    ];
    return $form;
  }

  /**
   * After build handler for CAPTCHA elements.
   */
  public static function afterBuildBoletoUSP(array $element, FormStateInterface $form_state) {
    // Make sure that the CAPTCHA response supports #title.

    if (isset($element['boletousp_widgets'])
      && isset($element['boletousp_widgets']['boletousp_response'])
      && isset($element['boletousp_widgets']['boletousp_response']['#title'])) {
      if (!empty($element['#boletousp_title'])) {
        $element['boletousp_widgets']['boletousp_response']['#title'] = $element['#boletousp_title'];
      }
      if (!empty($element['#boletousp_description'])) {
        $element['boletousp_widgets']['boletousp_response']['#description'] = $element['#boletousp_description'];
      }
    }
    return $element;
  }


}
