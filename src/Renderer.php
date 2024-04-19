<?php

namespace OpenGraphXYZ;

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Renderer
{

  /**
   * Initialize hooks and filters.
   */
  public function init()
  {
    add_action('wp_head', array($this, 'render_og_tag'));
  }

  /**
   * Render OpenGraph meta tags.
   */
  public function render_og_tag()
  {
    $post = get_post();

    // Maybe abort early.
    if (empty($post) || is_admin() || !is_singular()) {
      return;
    }

    $templates = opengraphxyz_init()->get_templates();

    foreach ($templates as $template) {
      $template_meta = get_post_meta($template->ID, 'opengraph-xyz', true);
      $post_type = $post->post_type;

      if (in_array($post_type, $template_meta['post_types'], true)) {
        $og_image_url = $this->build_og_image_url($template_meta);

        echo '<meta property="og:image" content="' . esc_url($og_image_url) . '" />' . "\n";
        break;
      }
      //check here if the template has the post_type in the array then we will show metatag there
    }
  }

  /**
   * Build the OpenGraph image URL based on template data.
   * 
   * @param array $template_data
   * @return string|null
   */
  private function build_og_image_url($template_data)
  {
    $base_url = 'https://ogcdn.net/'; // Base URL for the OpenGraph image service.
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

    $url = $base_url . $template_id . '/' . $version . '/' . implode('/', $modifications_encoded) . '/og.png';
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
}