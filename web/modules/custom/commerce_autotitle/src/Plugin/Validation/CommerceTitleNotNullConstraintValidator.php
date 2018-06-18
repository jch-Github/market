<?php

namespace Drupal\commerce_autotitle\Plugin\Validation;

use Drupal\Core\Validation\Plugin\Validation\Constraint\NotNullConstraintValidator;
use Drupal\Core\Field\FieldItemList;
use Symfony\Component\Validator\Constraint;

/**
 * EntityLabelNotNull constraint validator.
 *
 * Custom override of NotNull constraint to allow empty entity labels to
 * validate before the automatic label is set.
 */
class CommerceTitleNotNullConstraintValidator extends NotNullConstraintValidator {
  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $typed_data = $this->getTypedData();
    if ($typed_data instanceof FieldItemList && $typed_data->isEmpty()) {
      $entity = $typed_data->getEntity();
      if (!$entity->hasField('title')) {
        return;
      }
      $decorator = \Drupal::service('commerce_autotitle.entity_decorator');
      /** @var \Drupal\commerce_autotitle\CommerceAutoTitleManager $decorated_entity */
      $decorated_entity = $decorator->decorate($entity);

      if ($decorated_entity->hasTitle() && $decorated_entity->autoTitleNeeded()) {
        return;
      }
    }
    parent::validate($value, $constraint);
  }
}
