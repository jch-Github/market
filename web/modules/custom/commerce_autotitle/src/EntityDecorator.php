<?php

/**
 * @file
 * Contains \Drupal\commerce_autotitle\EntityDecorator.
 */

namespace Drupal\commerce_autotitle;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Utility\Token;

/**
 * Provides an content entity decorator for automatic label generation.
 */
class EntityDecorator implements EntityDecoratorInterface {

  /**
   * The content entity that is decorated.
   *
   * @var ContentEntityInterface
   */
  protected $entity;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Token service.
   *
   * @var CommerceAutoTitleGeneratorManagerInterface
   */
  protected $generatorManager;

  /**
   * Constructs an EntityDecorator object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Configuration factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager
   * @param \Drupal\Core\Utility\Token $token
   *   Token manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, CommerceAutoTitleGeneratorManagerInterface $generatorManager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->generatorManager = $generatorManager;
  }

  /**
   * {@inheritdoc}
   */
  public function decorate(ContentEntityInterface $entity) {
    $this->entity = new CommerceAutoTitleManager($entity, $this->entityTypeManager, $this->generatorManager);
    return $this->entity;
  }
}
