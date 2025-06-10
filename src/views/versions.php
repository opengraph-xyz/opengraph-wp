<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

$versions = opengraphxyz_get_template_versions($post->ID);
$meta = get_post_meta($post->ID, 'opengraph-xyz', true);
$current_version = isset($meta['template_version']) ? $meta['template_version'] : null;
?>

<select id="template-version-dropdown" name="template_version" style="width: 100%; margin-top: 6px">
    <?php
    // Check if the current version is in the last 10 versions
    $current_version_in_list = false;
    foreach ($versions as $version) {
        if ($version['versionNumber'] == $current_version) {
            $current_version_in_list = true;
            break;
        }
    }

    // Populate the dropdown with the last 10 versions
    foreach ($versions as $version) {
        $selected = ($version['versionNumber'] == $current_version) ? ' selected' : '';
        echo '<option value="' . esc_attr($version['versionNumber']) . '"' . esc_attr($selected) . '>Version ' . esc_html($version['versionNumber']) . '</option>';
    }


    // Add the current version if it's not in the list
    if (!$current_version_in_list && $current_version) {
        echo '<option value="' . esc_attr($current_version) . '" selected>Version ' . esc_html($current_version) . ' (Current)</option>';
    }
    ?>
</select>