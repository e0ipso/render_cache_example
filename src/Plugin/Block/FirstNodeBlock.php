<?php

/**
 * @file
 * Contains \Drupal\Core\Block\Plugin\Block\PageTitleBlock.
 */

namespace Drupal\render_cache_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display the page title.
 *
 * @Block(
 *   id = "first_node_block",
 *   admin_label = @Translation("First Node"),
 * )
 */
class FirstNodeBlock extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storageManager;

  /**
   * The renderer.
   *
   * @var RendererInterface
   */
  protected $renderer;

  /**
   * The first node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storageManager = $entity_type_manager->getStorage('node');
    $this->renderer = $renderer;
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
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label_display' => FALSE];
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstNode() {
    if (!$this->node) {
      // Query the node.
      $results = $this->storageManager->getQuery()
        ->sort('created', 'ASC')
        ->range(0, 1)
        ->execute();
      if ($results) {
        $this->node = $this->storageManager->load(reset($results));
      }
    }
    return $this->node;
  }

  /**
   * {@inheritdoc}
   *
   * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Renderer.php/function/Renderer%3A%3AaddCacheableDependency/8
   * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21theme.api.php/group/theme_render/8#sec_caching
   */
  public function build() {
    $node = $this->getFirstNode();
    $label = $node ? $node->label() : $this->t('No nodes yet');
    $build = [
      '#type' => 'html_tag',
      '#tag' => 'h4',
      '#value' => $label,
    ];
    // Use the renderer service to add the cacheability metadata from the node
    // as a dependency to our render array. It will get the tags, contexts and
    // max age and add them to $build['#render']. In this case we are only
    // interested in the tags, but it's a good practise to add the cacheable
    // dependency as a whole.
    $this->renderer->addCacheableDependency($build, $node);
    return $build;
  }

}
