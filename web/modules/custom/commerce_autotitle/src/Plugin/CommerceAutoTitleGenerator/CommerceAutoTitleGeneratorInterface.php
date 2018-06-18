<?php

namespace Drupal\commerce_autotitle\Plugin\CommerceAutoTitleGenerator;

use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\BaseFormIdInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Places an order through a series of steps.
 *
 * Checkout flows are multi-step forms that can be configured by the store
 * administrator. This configuration is stored in the commerce_checkout_flow
 * config entity and injected into the plugin at instantiation.
 */
interface CommerceAutoTitleGeneratorInterface extends  ConfigurablePluginInterface, PluginFormInterface, PluginInspectionInterface, DerivativeInspectionInterface {

  /**
   * Generated TITLE getter.
   *
   * @param \Drupal\commerce_product\Entity\ProductInterface $entity
   *   Entity TITLE generated for.
   *
   * @return string
   *   Generated TITLE for giv.
   */
  public function generate(ProductInterface $entity);

}
