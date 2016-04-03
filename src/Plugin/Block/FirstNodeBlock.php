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
   * The first node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storageManager = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
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
   */
  public function build() {
    $node = $this->getFirstNode();
    $label = $node ? $node->label() : $this->t('No nodes yet');
    return [
      '#type' => 'html_tag',
      '#tag' => 'h4',
      '#value' => $label,
    ];
  }

}
