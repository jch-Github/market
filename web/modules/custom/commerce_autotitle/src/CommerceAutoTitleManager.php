<?php
/**
 * @file
 * Contains \Drupal\commerce_autotitle\AutoEntityLabelManager.
 */

namespace Drupal\commerce_autotitle;

use Drupal\commerce_autotitle\Plugin\CommerceAutoTitleGenerator\CommerceAutoTitleGeneratorInterface;
use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\commerce_product\Entity\ProductTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class CommerceAutoTitleManager implements CommerceAutoTitleManagerInterface {
  use StringTranslationTrait;

  /**
   * Automatic label is disabled.
   */
  const DISABLED = 'disabled';

  /**
   * Automatic label is enabled. Will always be generated.
   */
  const ENABLED = 'enabled';

  /**
   * Automatic label is optional. Will only be generated if no label was given.
   */
  const OPTIONAL = 'optional';

  /**
   * The content entity.
   *
   * @var ProductInterface
   */
  protected $entity;

  /**
   * The type of the entity.
   *
   * @var string
   */
  protected $entity_type;

  /**
   * The bundle of the entity.
   *
   * @var string
   */
  protected $entity_bundle;

  /**
   * The bundle of the entity.
   *
   * @var ProductTypeInterface
   */
  protected $bundle_entity_type;

  /**
   * Indicates if the automatic label has been applied.
   *
   * @var bool
   */
  protected $auto_title_applied = FALSE;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Token service.
   *
   * @var CommerceAutoTitleGeneratorManagerInterface
   */
  protected $generatorManager;

  /**
   * Constructs an AutoEntityLabelManager object.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to add the automatic label to.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager
   * @param CommerceAutoTitleGeneratorManagerInterface $generatorManager
   *   Token manager.
   */
  public function __construct(ContentEntityInterface $entity, EntityTypeManagerInterface $entity_type_manager, CommerceAutoTitleGeneratorManagerInterface $generatorManager) {
    $this->entity = $entity;
    $this->entityTypeManager = $entity_type_manager;
    $this->generatorManager = $generatorManager;

    $entity_type_id = $entity->getEntityTypeId();
    $bundle_id = $entity->bundle();
    $bundle_entity_type_id = $entity_type_manager->getDefinition($entity_type_id)->getBundleEntityType();
    $this->bundle_entity_type = $this->entityTypeManager->getStorage($bundle_entity_type_id)->load($bundle_id);


  }

  /**
   * Checks if the entity has a label.
   *
   * @return bool
   *   True if the entity has a label property.
   */
  public function hasTitle() {
    /** @var \Drupal\Core\Entity\EntityTypeInterface $definition */
    $definition = $this->entityTypeManager->getDefinition($this->entity->getEntityTypeId());
    // @todo cleanup.
    $hasKey = $definition->hasKey('title');
    if ($hasKey) {
      return $hasKey;
    }
    $entityManager = \Drupal::service('entity_field.manager');
    $fields = $entityManager->getFieldDefinitions($this->entity->getEntityTypeId(), $this->entity->bundle());
    if (isset($fields['title'])) {
      return TRUE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle() {

    if (!$this->hasTitle()) {
      throw new \Exception('This entity has no TITLE.');
    }

    $configuration = $this->getConfig('configuration');
    $instance_id = $this->getConfig('plugin');
    /** @var CommerceAutoTitleGeneratorInterface $generator */
    $generator = $this->generatorManager->createInstance($instance_id, $configuration);
    $title = $generator->generate($this->entity);

    $title_name = $this->getTitleName();
    $this->entity->{$title_name}->setValue($title);

    $this->auto_title_applied = TRUE;
    return $title;
  }

  /**
   * {@inheritdoc}
   */
  public function hasAutoTitle() {
    return $this->getConfig('mode') == self::ENABLED;
  }

  /**
   * {@inheritdoc}
   */
  public function hasOptionalAutoTitle() {
    return $this->getConfig('mode') == self::OPTIONAL;
  }

  /**
   * {@inheritdoc}
   */
  public function autoTitleNeeded() {
    $not_applied = empty($this->auto_title_applied);
    $required = $this->hasAutoTitle();
    $optional = $this->hasOptionalAutoTitle() && empty($this->entity->label());
    return $not_applied && ($required || $optional);
  }

  /**
   * Gets the field name of the entity label.
   *
   * @return string
   *   The entity label field name. Empty if the entity has no label.
   */
  public function getTitleName() {
    $title_field = '';

    if ($this->hasTitle()) {
      $entityManager = \Drupal::service('entity_field.manager');
      /** @var BaseFieldDefinition[] $fields */
      $fields = $entityManager->getFieldDefinitions($this->entity->getEntityTypeId(), $this->entity->bundle());
      $title_field  = $fields['title']->getFieldStorageDefinition()->getName();
    }

    return $title_field;
  }

  /**
   * Returns automatic token title configuration of the product type.
   *
   * @param string $key
   *   The configuration key to get.
   *
   * @return bool|mixed
   */
  protected function getConfig($key) {
    $config = $this->bundle_entity_type->getThirdPartySettings('commerce_autotitle');
    return isset($config[$key]) ? $config[$key] : FALSE;
  }

  /**
   * Constructs the list of options for the given bundle.
   */
  public static function commerce_autotitle_options() {
    return [
      CommerceAutoTitleManager::DISABLED => t('Disabled'),
      CommerceAutoTitleManager::ENABLED => t('Automatically generate the TITLE and hide the label field'),
      CommerceAutoTitleManager::OPTIONAL => t('Automatically generate the TITLE if the TITLE field is left empty'),
    ];
  }

}