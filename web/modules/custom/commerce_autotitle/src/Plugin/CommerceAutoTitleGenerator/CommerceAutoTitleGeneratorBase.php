<?php

namespace Drupal\commerce_autotitle\Plugin\CommerceAutoTitleGenerator;

use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Places an order through a series of steps.
 *
 * Checkout flows are multi-step forms that can be configured by the store
 * administrator. This configuration is stored in the commerce_checkout_flow
 * config entity and injected into the plugin at instantiation.
 */
abstract class CommerceAutoTitleGeneratorBase extends PluginBase  implements CommerceAutoTitleGeneratorInterface, ContainerFactoryPluginInterface{

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  var $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Validate if title is unique.
   *
   * @param ProductInterface $entity
   *   Product.
   * @param string $title
   *   TITLE.
   *
   * @return bool
   *   TRUE if TITLE unique FALSE otherwise.
   */
  protected function isUnique(ProductInterface $entity, $title) {
    $entities = $this->entityTypeManager->getStorage($entity->getEntityTypeId())->loadByProperties(['title' => $title]);
    if (!$entity->isNew()) {
      unset($entities[$entity->id()]);
    }

    return empty($entities);
  }

  protected function makeUnique(ProductInterface $entity, $title) {
    // Strip tags.
    $generated_title = preg_replace('/[\t\n\r\0\x0B]/', '', strip_tags($title));
    $output = $generated_title;
    $i = 0;
    while (!$this->isUnique($entity, $output)) {
      $counter_length = Unicode::strlen($i) + 1;
      $un_prefixed_max_length = 255 - $counter_length;
      $title = Unicode::substr($generated_title, 0, $un_prefixed_max_length);
      $output = $title . '_' . $i;
      $i++;
    };
    return $output;
  }

  /**
   * Generates the TITLE according to the settings.
   *
   * @param ProductInterface $entity
   *   Content entity.
   *
   * @return string
   *   A label string
   */
  public function generate(ProductInterface $entity) {
    $generated_title = $this->getTitle($entity);
    if (empty($generated_title)) {
      $generated_title = $this->getAlternativeTitle($entity);
    }
    return $this->makeUnique($entity, $generated_title);
  }

  /**
   * Gets an alternative TITLE.
   *
   * @return string
   *   Translated label string.
   */
  protected function getAlternativeTitle(ProductInterface $entity) {
    $content_type = $this->getBundleLabel($entity);

    if ($entity->id()) {
      $label = t('@type @id', array(
        '@type' => $content_type,
        '@id' => $entity->id(),
      ));
    }
    else {
      $label = $content_type;
    }

    return $label;
  }

  /**
   * Gets the entity bundle label or the entity label.
   *
   * @return string
   *   The bundle label.
   */
  protected function getBundleLabel(ProductInterface $entity) {
    $entity_type = $entity->getEntityTypeId();
    $bundle = $entity->bundle();

    // Use the the human readable name of the bundle type. If this entity has no
    // bundle, we use the name of the content entity type.
    if ($bundle != $entity_type) {
      $bundle_entity_type = $this->entityTypeManager
        ->getDefinition($entity_type)
        ->getBundleEntityType();
      $label = $this->entityTypeManager
        ->getStorage($bundle_entity_type)
        ->load($bundle)
        ->label();
    }
    else {
      $label = $this->entityTypeManager
        ->getDefinition($entity_type)
        ->getLabel();
    }

    return $label;
  }

  /**
   * {@inheritdoc}
   */
  abstract protected function getTitle(ProductInterface $entity);

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep($this->defaultConfiguration(), $configuration);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [
      'module' => [$this->pluginDefinition['provider']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->setConfiguration($values);
    }
  }

}
