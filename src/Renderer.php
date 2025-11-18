<?php

namespace OpenGraphXYZ;

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Renderer
{
  private $og_image_url = null;
  private $og_image_width = 1200;
  private $og_image_height = 630;
  private $og_image_type = 'image/png';
  private $meta_tag_class = 'opengraph-xyz-meta-tag';

  /**
   * Initialize hooks and filters.
   */
  public function init()
  {
    $seo_plugin_active = false;

    if (defined('WPSEO_VERSION')) {
      // If Yoast SEO is active
      add_filter('wpseo_opengraph_image', array($this, 'maybe_replace_yoast_og_image'), 10, 1);
      add_filter('wpseo_opengraph_image_width', array($this, 'set_og_image_width'), 10, 1);
      add_filter('wpseo_opengraph_image_height', array($this, 'set_og_image_height'), 10, 1);
      add_filter('wpseo_opengraph_image_type', array($this, 'set_og_image_type'), 10, 1);
      $seo_plugin_active = true;
    }

    if (class_exists('RankMath')) {
      // If Rank Math SEO is active
      add_filter('rank_math/opengraph/facebook/image', array($this, 'maybe_replace_rankmath_og_image'), 10, 1);
      add_filter('rank_math/opengraph/facebook/image_width', array($this, 'set_og_image_width'), 10, 1);
      add_filter('rank_math/opengraph/facebook/image_height', array($this, 'set_og_image_height'), 10, 1);
      add_filter('rank_math/opengraph/facebook/image_type', array($this, 'set_og_image_type'), 10, 1);
      add_filter('rank_math/opengraph/twitter/image', array($this, 'maybe_replace_rankmath_og_image'), 10, 1);
      add_filter('rank_math/opengraph/twitter/image_width', array($this, 'set_og_image_width'), 10, 1);
      add_filter('rank_math/opengraph/twitter/image_height', array($this, 'set_og_image_height'), 10, 1);
      add_filter('rank_math/opengraph/twitter/image_type', array($this, 'set_og_image_type'), 10, 1);
      $seo_plugin_active = true;
    }

    if (!$seo_plugin_active) {
      // If no SEO plugin is active
      add_action('wp_head', array($this, 'render_og_tags'), 10);
    }
  }

  /**
   * Render OpenGraph meta tags when Yoast SEO is not active.
   */
  public function render_og_tags()
  {
    $og_image_url = $this->get_og_image_url();
    if ($og_image_url) {
      echo '<meta property="og:image" content="' . esc_url($og_image_url) . '" class="' . esc_attr($this->meta_tag_class) . '" />' . "\n";
      echo '<meta property="og:image:width" content="' . esc_attr($this->og_image_width) . '" class="' . esc_attr($this->meta_tag_class) . '" />' . "\n";
      echo '<meta property="og:image:height" content="' . esc_attr($this->og_image_height) . '" class="' . esc_attr($this->meta_tag_class) . '" />' . "\n";
      echo '<meta property="og:image:type" content="' . esc_attr($this->og_image_type) . '" class="' . esc_attr($this->meta_tag_class) . '" />' . "\n";
    }
  }

  /**
   * Get the OpenGraph image URL.
   * 
   * @return string|null
   */
  private function get_og_image_url()
  {
    if ($this->og_image_url !== null) {
      return $this->og_image_url;
    }

    $post = get_post();

    // Maybe abort early.
    if (empty($post) || is_admin() || !is_singular()) {
      return null;
    }

    $templates = opengraphxyz_init()->get_templates();

    foreach ($templates as $template) {
      $template_meta = get_post_meta($template->ID, 'opengraph-xyz', true);
      $post_type = $post->post_type;

      if (in_array($post_type, $template_meta['post_types'], true)) {
        $this->og_image_url = $this->build_og_image_url($template_meta);
        return $this->og_image_url;
      }
    }

    return null;
  }

  /**
   * Build the OpenGraph image URL based on template data.
   * 
   * @param array $template_data
   * @return string|null
   */
  private function build_og_image_url($template_data)
  {
    $base_url = opengraphxyz_get_base_image_url();
    $template_id = $template_data['template_id'];
    $version = 'v' . $template_data['template_version'];
    $modifications = $this->prepare_modifications($template_data);

    // Construct the URL with encoded modifications.
    $modifications_encoded = array();
    foreach ($modifications as $modification) {
      foreach ($modification as $key => $value) {
        if ($key !== 'name') {
          $modifications_encoded[] = !empty($value) ? rawurlencode($value) : '_';
        }
      }
    }

    $url = $base_url . '/' . $template_id . '/' . $version . '/' . implode('/', $modifications_encoded) . '/og.png';
    return $url;
  }

  public function prepare_modifications($config)
  {

    $new = array();
    $modifications = isset($config['modifications']) ? $config['modifications'] : array();
    $custom_fields = isset($config['custom_fields']) ? $config['custom_fields'] : array();

    foreach ($modifications as $name => $vars) {

      $modification = array(
        'name' => $name,
      );

      foreach ($vars as $var => $value) {

        // Abort if no value.
        if ('' === $value) {
          continue;
        }

        // Maybe set the custom field key.
        if (in_array($value, array('{post_meta}', '{user_meta}', '{author_meta}'), true)) {

          if (empty($custom_fields[$name][$var])) {
            continue;
          }

          $value = str_replace('}', ' meta_key="' . $custom_fields[$name][$var] . '"}', $value);
        }

        // Replace post vars.
        $modification[$var] = apply_filters('opengraph-xyz_dynamic_tags_text', $value);
      }

      if (1 < count($modification)) {
        $new[] = $modification;
      }
    }

    return $new;
  }

  /**
   * Maybe replace Yoast's OG image.
   */
  public function maybe_replace_yoast_og_image($image)
  {
    $og_image_url = $this->get_og_image_url();
    return $og_image_url ? $og_image_url : $image;
  }

  /**
   * Maybe replace Rank Math's OG image.
   */
  public function maybe_replace_rankmath_og_image($image)
  {
    $og_image_url = $this->get_og_image_url();
    return $og_image_url ? $og_image_url : $image;
  }

  /**
   * Set OG image width.
   */
  public function set_og_image_width($width)
  {
    return $this->get_og_image_url() ? $this->og_image_width : $width;
  }

  /**
   * Set OG image height.
   */
  public function set_og_image_height($height)
  {
    return $this->get_og_image_url() ? $this->og_image_height : $height;
  }

  /**
   * Set OG image type.
   */
  public function set_og_image_type($type)
  {
    return $this->get_og_image_url() ? $this->og_image_type : $type;
  }
}