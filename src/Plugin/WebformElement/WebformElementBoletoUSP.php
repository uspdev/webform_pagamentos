<?php

namespace Drupal\webform_boleto_usp\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElementBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_boleto_usp\Gera;

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

  private $elements = [];

  /**
   * {@inheritdoc}
   */
  public function getDefaultProperties() {
    return [
      // Flexbox.
      'flex' => 1,
      # Campos do boleto
      'boletousp_informacoesboletosacado' => 'Nome do evento, curso etc',
      'boletousp_datavencimentoboleto' =>'', 
      'boletousp_valor' => '10,00',
      'cpf' => '',
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

    /* Elements para mapeamento CPF e email */
    //
    $elements = $webform_submission->getWebform()->getElementsDecodedAndFlattened();
    foreach($elements as $key=>$element){
        $this->$elements[$key] = $element['#title'];
    }
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
    
    $data = $webform_submission->getData();
    $data['codigo_boleto_gerado'] = Gera::gera($data, $element);
    $webform_submission->setData($data);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $boletousp_types = ['default' => $this->t('Default challenge type')];

    $form['boletousp'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Configurações do BoletoUSP'),
    ];

    $form['boletousp']['boletousp_container'] = [
      '#type' => 'container',
    ];
    $form['boletousp']['boletousp_container']['boletousp_informacoesboletosacado'] = [
      '#type' => 'textfield',
      '#attributes'  => ['size' => 125],
      '#title' => $this->t('Informações boleto sacado'),
    ];

    $form['boletousp']['boletousp_container']['boletousp_valor'] = [
      '#type'        => 'number',
      '#title'       => $this->t('Valor'),
      '#prefix'      => 'R$',
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['boletousp_datavencimentoboleto'] = [
      '#type' => 'date',
      '#title' => $this->t('Data de vencimento do boleto'),
    ];

    $form['boletousp']['boletousp_container']['cpf'] = [
      '#type'        => 'select',
      '#title'       => $this->t('Cpf'),
      '#options' => $this->$elements,
    ];

    return $form;
  }

}
