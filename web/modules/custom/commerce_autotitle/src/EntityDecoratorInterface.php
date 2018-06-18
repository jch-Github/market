<?php
/**
 * @file
 * Contains \Drupal\commerce_autotitle\EntityDecoratorInterface.
 */

namespace Drupal\commerce_autotitle;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface for EntityDecorator.
 */
interface EntityDecoratorInterface {

  /**
   * Automatic entity label entity decorator.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return \Drupal\commerce_autotitle\CommerceAutoTitleManager|\Drupal\Core\Entity\ContentEntityInterface
   */
  public function decorate(ContentEntityInterface $entity);
}
