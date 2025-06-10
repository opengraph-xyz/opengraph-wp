<?php

namespace OpenGraphXYZ;

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Plugin
{
  /**
   * The single instance of the class.
   *
   * @var Plugin|null
   */
  private static $instance = null;

  /**
   * @var Dynamic_Tags
   */
  public $dynamic_tags;

  /**
   * @var Renderer
   */
  public $renderer;

  /**
   * Main Plugin Instance.
   *
   * Ensures only one instance of the plugin is loaded or can be loaded.
   *
   * @return Plugin
   */
  public static function instance()
  {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Class Constructor.
   */
  public function __construct()
  {
    // Load files.
    $this->load_files();

    // Initialize components on plugins_loaded
    add_action('plugins_loaded', array($this, 'init_components'), 20);

    // Register custom post type
    add_action('init', array($this, 'register_custom_post_type'));

    if (is_admin()) {
      add_action('plugins_loaded', array($this, 'init_admin'), 20);
    }
  }

  /**
   * Initialize plugin components.
   */
  public function init_components()
  {
    // Init class properties.
    $this->dynamic_tags = new Dynamic_Tags();
    $this->dynamic_tags->init();

    $this->renderer = new Renderer();
    $this->renderer->init();
  }

  /**
   * Initialize admin functionality.
   */
  public function init_admin()
  {
    $admin = new Admin();
    $admin->init();
  }

  /**
   * Return all tags
   *
   * @return array
   */
  public function get_tags()
  {
    return $this->dynamic_tags->all();
  }

  public function get_templates()
  {
    static $templates;

    if (!isset($templates)) {
      $templates = get_posts(
        array(
          'post_type' => 'opengraph_template',
          'posts_per_page' => -1,
          'post_status' => 'publish',
        )
      );
    }

    return $templates;
  }

  /**
   * Include required core files used in admin and on the frontend.
   */
  public function load_files()
  {
    $includes = plugin_dir_path(__FILE__);

    // Functions.
    require_once $includes . 'functions.php';
  }

  public function register_custom_post_type()
  {
    if (!is_blog_installed() || post_type_exists('opengraph_template')) {
      return;
    }

    $labels = array(
      'name' => _x('OG Image Templates', 'post type general name', 'opengraph-xyz'),
      'singular_name' => _x('OG Image Template', 'post type singular name', 'opengraph-xyz'),
      'menu_name' => _x('OG Image Templates', 'admin menu', 'opengraph-xyz'),
      'name_admin_bar' => _x('OG Image Template', 'add new on admin bar', 'opengraph-xyz'),
      'add_new' => _x('Add New', 'template', 'opengraph-xyz'),
      'add_new_item' => __('Add New Template', 'opengraph-xyz'),
      'new_item' => __('New Template', 'opengraph-xyz'),
      'edit_item' => __('Edit Template', 'opengraph-xyz'),
      'view_item' => __('View Template', 'opengraph-xyz'),
      'all_items' => __('All Templates', 'opengraph-xyz'),
      'search_items' => __('Search Templates', 'opengraph-xyz'),
      'parent_item_colon' => __('Parent Templates:', 'opengraph-xyz'),
      'not_found' => __('No templates found.', 'opengraph-xyz'),
      'not_found_in_trash' => __('No templates found in Trash.', 'opengraph-xyz')
    );

    $args = array(
      'labels' => $labels,
      'description' => __('Description.', 'opengraph-xyz'),
      'public' => false,
      'show_ui' => true,
      'map_meta_cap' => true,
      'publicly_queryable' => false,
      'exclude_from_search' => true,
      'hierarchical' => false,
      'query_var' => false,
      'supports' => array('title'),
      'has_archive' => false,
      'show_in_nav_menus' => false,
      'show_in_rest' => false,
      'show_in_menu' => false,
      'can_export' => false,
    );

    register_post_type('opengraph_template', $args);
  }
}