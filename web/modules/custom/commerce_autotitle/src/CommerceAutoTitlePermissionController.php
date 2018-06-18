<?php

/**
 * @file
 * Contains \Drupal\commerce_autotitle\AutoEntityLabelPermissionController.
 */

namespace Drupal\commerce_autotitle;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Config\Entity\ConfigEntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic permissions of the commerce_autotitle module.
 */
class CommerceAutoTitlePermissionController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new AutoEntityLabelPermissionController instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'));
  }

  /**
   * Returns an array of commerce_autotitle permissions
   *
   * @return array
   */
  public function autoTitlePermissions() {
    $permissions = [];

    foreach ($this->entityManager->getDefinitions() as $entity_type_id => $entity_type) {
      // Create a permission for each entity type to manage the entity
      // labels.
      if ($entity_type->hasLinkTemplate('auto-title') && $entity_type->hasKey('label')) {
        $permissions['administer ' . $entity_type_id . ' TITLE'] = [
          'title' => $this->t('%entity_label: Administer automatic token Title', ['%entity_label' => $entity_type->getLabel()]),
          'restrict access' => TRUE,
        ];
      }
    }

    return $permissions;
  }

}
