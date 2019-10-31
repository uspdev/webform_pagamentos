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
      'boletousp_codigoUnidadeDespesa' => '8',
      'boletousp_nomeFonte' => '',
      'boletousp_nomeSubfonte' => '',
      'boletousp_estruturaHierarquica' => '\FFLCH',
      'boletousp_dataVencimentoBoleto' =>'',
      'boletousp_valorDesconto' =>'0', // Não exposto ao usuário
      'boletousp_tipoSacado' =>'PF', // Não exposto ao usuário
      'boletousp_informacoesBoletoSacado' => 'Nome do evento, curso, palestra ...',
      'boletousp_instrucoesObjetoCobranca' => 'Não receber após o vencimento.',
      'boletousp_codigoEmail' => '',
      'boletousp_nomeSacado' => '',
      'boletousp_cpfCnpj' => '',
      'boletousp_valorDocumento' => '',
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
    $elements = [];
    $obj_elements = $webform_submission->getWebform()->getElementsDecodedAndFlattened();
    foreach($obj_elements as $key=>$element){
        $elements[$key] = $element['#title'];
    }
    /** Aqui vamos verificar se os campos chave informados
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

    $form['boletousp']['boletousp_container']['boletousp_codigoUnidadeDespesa'] = [
      '#type' => 'integer',
      '#title' => $this->t('Unidade de despesa'),
    ];

    $form['boletousp']['boletousp_container']['boletousp_nomeFonte'] = [
      '#type'        => 'select',
      '#title'       => $this->t('Fonte'),
      '#options'    => [
         'Taxas' => 'Taxas',
       ],
    ];

    $form['boletousp']['boletousp_container']['boletousp_nomeSubfonte'] = [
      '#type'        => 'select',
      '#title'       => $this->t('SubFonte'),
      '#options'    => [
         'Congressos/Seminários/Palestras/Simpósios' => 'Congressos/Seminários/Palestras/Simpósios',
         'Cursos' => 'Cursos',
         'Inscrição de Cursos' => 'Inscrição de Cursos',
       ],
    ];

    $form['boletousp']['boletousp_container']['boletousp_estruturaHierarquica'] = [
      '#type' => 'textfield',
      '#attributes'  => ['size' => 125],
      '#title' => $this->t('Centro Gerencial'),
    ];

    $form['boletousp']['boletousp_container']['boletousp_dataVencimentoBoleto'] = [
      '#type' => 'date',
      '#title' => $this->t('Data de vencimento do boleto'),
    ];

    $form['boletousp']['boletousp_container']['boletousp_informacoesBoletoSacado'] = [
      '#type' => 'textfield',
      '#attributes'  => ['size' => 125],
      '#title' => $this->t('Informações boleto sacado'),
    ];

    $form['boletousp']['boletousp_container']['boletousp_instrucoesObjetoCobranca'] = [
      '#type' => 'textfield',
      '#attributes'  => ['size' => 125],
      '#title' => $this->t('Instruçoes do objeto de cobrança'),
    ];

    $form['boletousp']['boletousp_container']['boletousp_valorDocumento'] = [
      '#type'        => 'number',
      '#title'       => $this->t('Valor'),
//      '#prefix'      => 'R$',
      '#required'    => TRUE,
    ];

    $form['boletousp']['boletousp_container']['mapeamento'] = [
      '#type' => 'fieldset',
      '#description' => $this->t("sss"),
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
    ];

    return $form;
  }

}
