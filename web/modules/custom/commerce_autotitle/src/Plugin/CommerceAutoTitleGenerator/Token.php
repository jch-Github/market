<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.07.17
 * Time: 14:56
 */

namespace Drupal\commerce_autotitle\Plugin\CommerceAutoTitleGenerator;
use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the token commerce_autotitle generator.
 *
 * @CommerceAutoTitleGenerator(
 *   id = "token",
 *   label = @Translation("Token"),
 * )
 */
class Token extends CommerceAutoTitleGeneratorBase {

  /**
   * Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

    /**
     * Constructs a new Token object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Utility\Token $token
     *   The token manager.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, \Drupal\Core\Utility\Token $token) {
      parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager);
      $this->token = $token;
      $this->setConfiguration($configuration);
    }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('token')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['pattern'] = [
      '#type' => 'textarea',
      '#title' => t('Pattern for the TITLE'),
      '#description' => t('Leave blank for using the per default generated TITLE. Otherwise this string will be used as TITLE. Use the syntax [token] if you want to insert a replacement pattern.'),
      '#default_value' => $this->getConfiguration()['pattern'],
    ];

    $form['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user', 'site', 'commerce_product'],
      '#dialog' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(ProductInterface $entity) {
    $entity_type = $entity->getEntityTypeId();
    $configuration = $this->getConfiguration();

    return $this->token->replace($configuration['pattern'], [$entity_type => $entity], [
      'sanitize' => FALSE,
      'clear' => TRUE
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue($form['#parents']);
    $tokens = $this->token->scan($values['pattern']);
    if (empty($tokens) && !empty($values['pattern'])) {
      $form_state->setError($form['pattern'], 'At least one token from available tokens list required');
    }
  }

}