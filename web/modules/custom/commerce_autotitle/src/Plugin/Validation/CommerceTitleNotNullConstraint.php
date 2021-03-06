<?php

namespace Drupal\commerce_autotitle\Plugin\Validation;

use Drupal\Core\Validation\Plugin\Validation\Constraint\NotNullConstraint;
use Symfony\Component\Validator\Constraint;

/**
 * Custom override of NotNull constraint to allow empty entity labels to
 * validate before the automatic label is set.
 */
class CommerceTitleNotNullConstraint extends NotNullConstraint {}
