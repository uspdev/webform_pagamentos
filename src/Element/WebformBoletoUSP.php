<?php

namespace Drupal\WebformBoletoUsp\Element;

use Drupal\webform_attachment\Element\WebformAttachmentBase;

class WebformBoletoUSP extends WebformAttachmentBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo() + [
      '#export_type' => 'boleto',
    ];
  }
}
