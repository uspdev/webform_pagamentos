<?php

namespace Drupal\WebformBoletoUsp\Plugin\WebformElement;

use Drupal\webform_attachment\Plugin\WebformElement\WebformAttachmentBase;

class WebformBoletoUSP extends WebformAttachmentBase {

  /**
   * {@inheritdoc}
   */
  public function getDefaultProperties() {
    return parent::getDefaultProperties() + [
      'title' => t('Boleto'),
    ];
  }

}
