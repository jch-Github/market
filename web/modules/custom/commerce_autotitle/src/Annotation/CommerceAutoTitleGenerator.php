<?php

namespace Drupal\commerce_autotitle\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the commerce_autotitle Generator plugin annotation object.
 *
 * Plugin namespace: Plugin\CommerceAutoTitleGenerator.
 *
 * @Annotation
 */
class CommerceAutoTitleGenerator extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
