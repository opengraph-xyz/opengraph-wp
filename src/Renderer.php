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
        if ($this->check_filters($template_meta, $post)) {
          $this->og_image_url = $this->build_og_image_url($template_meta);
          return $this->og_image_url;
        }
      }
    }

    return null;
  }

  /**
   * Check if the post matches the template filters.
   *
   * @param array $template_meta
   * @param WP_Post $post
   * @return boolean
   */
  private function check_filters($template_meta, $post)
  {
    // New Filters Logic (filters_data)
    if (isset($template_meta['filters_data']) && is_array($template_meta['filters_data']) && !empty($template_meta['filters_data'])) {
      $groups = $template_meta['filters_data'];

      // AND logic between groups
      foreach ($groups as $group) {
        if (!is_array($group) || empty($group)) {
          continue;
        }

        $group_match = false;
        // OR logic between conditions in a group
        foreach ($group as $condition) {
          if ($this->check_condition($condition, $post)) {
            $group_match = true;
            break;
          }
        }

        if (!$group_match) {
          return false;
        }
      }

      return true;
    }

    return true;
  }

  /**
   * Check a single condition against a post.
   * 
   * @param array $condition
   * @param WP_Post $post
   * @return boolean
   */
  private function check_condition($condition, $post)
  {
    $field = isset($condition['field']) ? $condition['field'] : '';
    $operator = isset($condition['operator']) ? $condition['operator'] : '';
    $value = isset($condition['value']) ? $condition['value'] : '';

    switch ($field) {
      case 'post_title':
        return $this->compare_string($post->post_title, $operator, $value);

      case 'post_author':
        // Value is array of {id, name}
        $author_ids = array();
        if (is_array($value)) {
          foreach ($value as $item) {
            if (isset($item['id'])) {
              $author_ids[] = $item['id'];
            }
          }
        }

        if ($operator === 'is_one_of') {
          return in_array((string) $post->post_author, $author_ids, true);
        }
        return false;

      case 'category':
        $cat_ids = array();
        if (is_array($value)) {
          foreach ($value as $item) {
            if (isset($item['id'])) {
              $cat_ids[] = $item['id'];
            }
          }
        }

        $post_cats = wp_get_post_categories($post->ID);
        $intersect = array_intersect($post_cats, $cat_ids);

        switch ($operator) {
          case 'is_one_of':
            return count($intersect) > 0;
          case 'is_all_of':
            return count($intersect) === count($cat_ids);
          case 'is_not_one_of':
          case 'is_none_of':
            return count($intersect) === 0;
        }
        return false;

      case 'post_tag':
        $tag_ids = array();
        if (is_array($value)) {
          foreach ($value as $item) {
            if (isset($item['id'])) {
              $tag_ids[] = $item['id'];
            }
          }
        }

        $post_tags = wp_get_post_tags($post->ID, array('fields' => 'ids'));
        $intersect = array_intersect($post_tags, $tag_ids);

        switch ($operator) {
          case 'is_one_of':
            return count($intersect) > 0;
          case 'is_all_of':
            return count($intersect) === count($tag_ids);
          case 'is_not_one_of':
          case 'is_none_of':
            return count($intersect) === 0;
        }
        return false;

      case 'published_date':
        return $this->compare_date(get_the_date('Y-m-d', $post), $operator, $value);

      case 'modified_date':
        return $this->compare_date(get_the_modified_date('Y-m-d', $post), $operator, $value);
    }

    return false;
  }

  private function compare_string($haystack, $operator, $needle)
  {
    $haystack = strtolower($haystack);
    $needle = strtolower($needle);

    switch ($operator) {
      case 'begins_with':
        return strpos($haystack, $needle) === 0;
      case 'ends_with':
        return substr($haystack, -strlen($needle)) === $needle;
      case 'contains':
        return strpos($haystack, $needle) !== false;
      case 'exact':
        return $haystack === $needle;
      case 'not_begins_with':
        return strpos($haystack, $needle) !== 0;
      case 'not_ends_with':
        return substr($haystack, -strlen($needle)) !== $needle;
      case 'not_contains':
        return strpos($haystack, $needle) === false;
      case 'not_exact':
        return $haystack !== $needle;
    }
    return false;
  }

  private function compare_date($date1, $operator, $date2)
  {
    if (empty($date2))
      return true;

    switch ($operator) {
      case 'before':
        return $date1 < $date2;
      case 'after':
        return $date1 > $date2;
    }
    return false;
  }  /**
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