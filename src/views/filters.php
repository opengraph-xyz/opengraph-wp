<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

$opengraphxyz = get_post_meta(get_the_ID(), 'opengraph-xyz', true);
$filters_data = isset($opengraphxyz['filters_data']) ? $opengraphxyz['filters_data'] : array();

// Migration logic for old filters
if (empty($filters_data) && isset($opengraphxyz['filters'])) {
    $old_filters = $opengraphxyz['filters'];
    $group = array();

    if (isset($old_filters['published_date']['enabled']) && $old_filters['published_date']['enabled'] === '1') {
        $group[] = array(
            'field' => 'published_date',
            'operator' => isset($old_filters['published_date']['condition']) ? $old_filters['published_date']['condition'] : 'after',
            'value' => isset($old_filters['published_date']['date']) ? $old_filters['published_date']['date'] : ''
        );
    }

    if (isset($old_filters['modified_date']['enabled']) && $old_filters['modified_date']['enabled'] === '1') {
        $group[] = array(
            'field' => 'modified_date',
            'operator' => isset($old_filters['modified_date']['condition']) ? $old_filters['modified_date']['condition'] : 'after',
            'value' => isset($old_filters['modified_date']['date']) ? $old_filters['modified_date']['date'] : ''
        );
    }

    if (!empty($group)) {
        $filters_data = array($group);
    }
}

// Ensure it's a valid JSON string for the data attribute
$filters_json = json_encode($filters_data);
?>
<div class="opengraph-xyz-filters">
    <p class="description" style="margin-bottom: 15px;">
        <?php _e('Only apply this template to your selected page types if the following conditions match.', 'opengraph-xyz'); ?>
    </p>

    <div id="opengraph-xyz-filters-container" data-filters="<?php echo esc_attr($filters_json); ?>">
        <!-- JS will render filters here -->
    </div>

    <input type="hidden" name="opengraph[filters_data]" id="opengraph-xyz-filters-data"
        value="<?php echo esc_attr($filters_json); ?>">
</div>