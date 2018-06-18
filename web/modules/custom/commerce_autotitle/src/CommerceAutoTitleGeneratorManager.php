<?php
/**
 * @file
 * Contains \Drupal\commerce_autotitle\AutoEntityLabelManager.
 */

namespace Drupal\commerce_autotitle;

use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Utility\Token;

class CommerceAutoTitleGeneratorManager extends DefaultPluginManager implements CommerceAutoTitleGeneratorManagerInterface, PluginManagerInterface {

  /**
   * Constructs a CommerceAutoTitleGeneratorManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/CommerceAutoTitleGenerator',
      $namespaces,
      $module_handler,
      'Drupal\commerce_autotitle\Plugin\CommerceAutoTitleGenerator\CommerceAutoTitleGeneratorInterface',
      'Drupal\commerce_autotitle\Annotation\CommerceAutoTitleGenerator');

    $this->alterInfo('commerce_autotitle_generator_info');
    $this->setCacheBackend($cache_backend, 'commerce_autotitle_generator_info_plugins');
  }
}