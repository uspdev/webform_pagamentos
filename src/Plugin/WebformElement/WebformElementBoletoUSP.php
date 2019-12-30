<?php

namespace Drupal\webform_boleto_usp\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElementBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_boleto_usp\Gera;
use Drupal\webform\WebformInterface;

/**
 * Provides a 'boletoUSP' element.
 *
 * @WebformElement(
 *   id = "boletousp",
 *   default_key = "boletousp",
 *   api = "https://github.com/fflch/webform-boleto-usp",
 *   label = @Translation("Boleto USP"),
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
      // Flexbox.
      'flex' => 1,
      # Campos do boleto
      'boletousp_codigoFonteRecurso' => '',
      'boletousp_numeroUspUsuario' => '',
      'boletousp_estruturaHierarquica' => '',
      'boletousp_dataVencimentoBoleto' =>'',
      'boletousp_informacoesBoletoSacado' => '',
      'boletousp_instrucoesObjetoCobranca' => '',
      'boletousp_codigoEmail' => '',
      'boletousp_nomeSacado' => ''  ,
      'boletousp_cpfCnpj' => '',
      'boletousp_valorDocumento' => '',
      'boletousp_cepSacado' => '',
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

    /* Elementos para mapeamento */
    /*
    $elements = [];
    $obj_elements = $webform_submission->getWebform()->getElementsDecodedAndFlattened();
    foreach($obj_elements as $key=>$element){
        $elements[$key] = $element['#title'];
    }
    */

    /** TODO: Aqui vamos verificar se os campos chave informados
     *  pelo administrador(a) do formulário existe antes de mostrar o
     *  formulário para preenchimento. Senão existir, mostrar um erro e
     *  não deixar o mesmo ser submetido.
     **/

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

    /* Validações de email e cpf */
    $webform = $webform_submission->getWebform();
    $handler_manager = \Drupal::service('plugin.manager.webform.handler');

    $handler_configuration = [
      'id' => 'webform_boleto_usp_validator',
      'label' => 'validations',
      'handler_id' => 'webform_boleto_usp_validator',
      'status' => 1,
      'weight' => 0,
      'settings' => [],
    ];
    $handler = $handler_manager->createInstance('webform_boleto_usp_validator',
               $handler_configuration);

    // Must set original id so that the webform can be resaved.
    $webform->setOriginalId($webform->id());

    // Add webform handler which triggers Webform::save().
    $webform->addWebformHandler($handler);

    /* Gerar boleto depois da validação */
    $data = $webform_submission->getData();
    $gerar = Gera::gera($data, $element);

    $data[$element["#boletousp_cpfCnpj"]] = \Drupal::service('cpf')
                ->digits($data[$element["#boletousp_cpfCnpj"]]);

    $data['boleto_status'] = $gerar['status'];
    if($data['boleto_status']) {
      $data['boleto_id'] = $gerar['value'];
    } else {
      $data['boleto_erro'] = $gerar['value'];
    }
    $webform_submission->setData($data);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    //$boletousp_types = ['default' => $this->t('Default challenge type')];

    $form['boletousp'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Configurações do Boleto USP'),
    ];

    $form['boletousp']['boletousp_container'] = [
      '#type' => 'container',
    ];

    $form['boletousp']['boletousp_container']['boletousp_codigoFonteRecurso'] = [
      '#type'        => 'number',
      '#title'       => $this->t('Código fonte de recurso'),
      '#required'    => TRUE,
    ];

    /* Preparando centros de despesas*/
    $config = \Drupal::service('config.factory')->getEditable('webform_boleto_usp.settings');
    $temp = explode("\n",$config->get('estruturaHierarquica'));
    $centros = [];
    foreach($temp as $i){
        $centros[trim($i)] = trim($i);
    }

    $form['boletousp']['boletousp_container']['boletousp_estruturaHierarquica'] = [
      '#type'        => 'select',
      '#title' => $this->t('Centro Gerencial'),
      '#options'    => $centros,
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['boletousp_dataVencimentoBoleto'] = [
      '#type' => 'date',
      '#title' => $this->t('Data de vencimento do boleto'),
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['boletousp_informacoesBoletoSacado'] = [
      '#type' => 'textfield',
      '#attributes'  => ['size' => 125],
      '#title' => $this->t('Informações boleto sacado'),
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['boletousp_instrucoesObjetoCobranca'] = [
      '#type' => 'textfield',
      '#attributes'  => ['size' => 125],
      '#title' => $this->t('Instruções do objeto de cobrança'),
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['boletousp_valorDocumento'] = [
      '#type'        => 'textfield',
      '#description' => $this->t("Valor do boleto, exemplo: 10,50"),
      '#title'       => $this->t('Valor'),
//      '#prefix'      => 'R$',
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['mapeamento'] = [
      '#type' => 'fieldset',
      '#description' => $this->t(""),
      '#title' => $this->t('Mapeamento com campos do formulário'),
    ];

    $form['boletousp']['boletousp_container']['mapeamento']['boletousp_codigoEmail'] = [
      '#type' => 'textfield',
      '#description' => $this->t("Chave para campo de email"),
      '#attributes'  => ['size' => 25],
      '#title' => $this->t('Chave para campo de email'),
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['mapeamento']['boletousp_nomeSacado'] = [
      '#type' => 'textfield',
      '#description' => $this->t("Chave para campo nome do sacado"),
      '#attributes'  => ['size' => 25],
      '#title' => $this->t('Chave para campo nome do sacado'),
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['mapeamento']['boletousp_cpfCnpj'] = [
      '#type' => 'textfield',
      '#description' => $this->t("Chave para campo cpf"),
      '#attributes'  => ['size' => 25],
      '#title' => $this->t('Chave para campo cpf'),
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['mapeamento']['boletousp_cepSacado'] = [
      '#type' => 'textfield',
      '#description' => $this->t("Chave para campo CEP"),
      '#attributes'  => ['size' => 25],
      '#title' => $this->t('Chave para campo CEP'),
      '#required'    => FALSE,
    ];

    $form['boletousp']['boletousp_container']['mapeamento']['boletousp_numeroUspUsuario'] = [
      '#type' => 'textfield',
      '#description' => $this->t("Chave para campo número USP"),
      '#attributes'  => ['size' => 25],
      '#title' => $this->t('Chave para campo número USP'),
      '#required'    => FALSE,
    ];

    return $form;
  }
}
