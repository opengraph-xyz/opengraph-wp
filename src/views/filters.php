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
$fields = array(
    'post_title' => __('Post Title', 'opengraph-xyz'),
    'post_author' => __('Post Author', 'opengraph-xyz'),
    'category' => __('Category', 'opengraph-xyz'),
    'post_tag' => __('Tag', 'opengraph-xyz'),
    'published_date' => __('Published Date', 'opengraph-xyz'),
    'modified_date' => __('Modified Date', 'opengraph-xyz'),
);
$operators = array(
    'begins_with' => __('Begins with', 'opengraph-xyz'),
    'ends_with' => __('Ends with', 'opengraph-xyz'),
    'contains' => __('Contains', 'opengraph-xyz'),
    'exact' => __('Exactly matches', 'opengraph-xyz'),
    'not_begins_with' => __('Does not begin with', 'opengraph-xyz'),
    'not_ends_with' => __('Does not end with', 'opengraph-xyz'),
    'not_contains' => __('Does not contain', 'opengraph-xyz'),
    'not_exact' => __('Does not exactly match', 'opengraph-xyz'),
    'is_one_of' => __('Is one of', 'opengraph-xyz'),
    'is_all_of' => __('Is all of', 'opengraph-xyz'),
    'is_not_one_of' => __('Is not one of', 'opengraph-xyz'),
    'is_none_of' => __('Is none of', 'opengraph-xyz'),
    'after' => __('Is after', 'opengraph-xyz'),
    'before' => __('Is before', 'opengraph-xyz'),
);
$categories = get_terms(array('taxonomy' => 'category', 'hide_empty' => false));
$tags = get_terms(array('taxonomy' => 'post_tag', 'hide_empty' => false));
$author_query = array('fields' => array('ID', 'display_name'));
if (version_compare(get_bloginfo('version'), '5.9', '>=')) {
    $author_query['capability'] = 'edit_posts';
} else {
    $author_query['who'] = 'authors';
}
$authors = get_users($author_query);
?>
<div class="opengraph-xyz-filters">
    <p class="description" style="margin-bottom: 15px;">
        <?php _e('Only apply this template to your selected page types if the following conditions match.', 'opengraph-xyz'); ?>
    </p>

    <div id="opengraph-xyz-filters-container" data-filters="<?php echo esc_attr($filters_json); ?>">
        <?php if (empty($filters_data)): ?>
            <button type="submit" class="button button-primary" name="opengraph_filter_action" value="add_group">
                <?php _e('Add Filter', 'opengraph-xyz'); ?>
            </button>
        <?php else: ?>
            <?php foreach ($filters_data as $group_index => $group): ?>
                <?php if ($group_index > 0): ?>
                    <div class="og-filter-separator"><span><?php _e('AND', 'opengraph-xyz'); ?></span></div>
                <?php endif; ?>

                <div class="og-filter-group">
                    <div class="og-filter-conditions">
                        <?php foreach ($group as $condition_index => $condition): ?>
                            <?php
                            $field = isset($condition['field']) ? $condition['field'] : 'post_title';
                            $operator = isset($condition['operator']) ? $condition['operator'] : 'contains';
                            $value = isset($condition['value']) ? $condition['value'] : '';
                            $selected_ids = array();
                            if (is_array($value)) {
                                foreach ($value as $item) {
                                    $selected_ids[] = is_array($item) && isset($item['id']) ? (string) $item['id'] : (string) $item;
                                }
                            }
                            $input_name = sprintf('opengraph[filter_groups][%d][%d]', $group_index, $condition_index);
                            ?>
                            <?php if ($condition_index > 0): ?>
                                <div class="og-condition-separator"><span><?php _e('OR', 'opengraph-xyz'); ?></span></div>
                            <?php endif; ?>

                            <div class="og-filter-row">
                                <label class="screen-reader-text"
                                    for="og-filter-field-<?php echo esc_attr($group_index . '-' . $condition_index); ?>">
                                    <?php _e('Field', 'opengraph-xyz'); ?>
                                </label>
                                <select id="og-filter-field-<?php echo esc_attr($group_index . '-' . $condition_index); ?>"
                                    name="<?php echo esc_attr($input_name . '[field]'); ?>" class="og-field-select">
                                    <?php foreach ($fields as $field_value => $field_label): ?>
                                        <option value="<?php echo esc_attr($field_value); ?>" <?php selected($field, $field_value); ?>>
                                            <?php echo esc_html($field_label); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <label class="screen-reader-text"
                                    for="og-filter-operator-<?php echo esc_attr($group_index . '-' . $condition_index); ?>">
                                    <?php _e('Operator', 'opengraph-xyz'); ?>
                                </label>
                                <select id="og-filter-operator-<?php echo esc_attr($group_index . '-' . $condition_index); ?>"
                                    name="<?php echo esc_attr($input_name . '[operator]'); ?>" class="og-operator-select">
                                    <?php foreach ($operators as $operator_value => $operator_label): ?>
                                        <option value="<?php echo esc_attr($operator_value); ?>" <?php selected($operator, $operator_value); ?>><?php echo esc_html($operator_label); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if ($field === 'category' || $field === 'post_tag' || $field === 'post_author'): ?>
                                    <?php
                                    $options = $field === 'category' ? $categories : ($field === 'post_tag' ? $tags : $authors);
                                    ?>
                                    <label class="screen-reader-text"
                                        for="og-filter-value-<?php echo esc_attr($group_index . '-' . $condition_index); ?>">
                                        <?php _e('Values', 'opengraph-xyz'); ?>
                                    </label>
                                    <select id="og-filter-value-<?php echo esc_attr($group_index . '-' . $condition_index); ?>"
                                        name="<?php echo esc_attr($input_name . '[value][]'); ?>" class="og-value-select" multiple>
                                        <?php if (!is_wp_error($options)): ?>
                                            <?php foreach ($options as $option): ?>
                                                <?php
                                                $option_id = $field === 'post_author' ? $option->ID : $option->term_id;
                                                $option_name = $field === 'post_author' ? $option->display_name : $option->name;
                                                ?>
                                                <option value="<?php echo esc_attr($option_id); ?>" <?php selected(in_array((string) $option_id, $selected_ids, true)); ?>><?php echo esc_html($option_name); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                <?php else: ?>
                                    <label class="screen-reader-text"
                                        for="og-filter-value-<?php echo esc_attr($group_index . '-' . $condition_index); ?>">
                                        <?php _e('Value', 'opengraph-xyz'); ?>
                                    </label>
                                    <input id="og-filter-value-<?php echo esc_attr($group_index . '-' . $condition_index); ?>"
                                        name="<?php echo esc_attr($input_name . '[value]'); ?>"
                                        type="<?php echo in_array($field, array('published_date', 'modified_date'), true) ? 'date' : 'text'; ?>"
                                        class="og-value-input" value="<?php echo esc_attr(is_array($value) ? '' : $value); ?>">
                                <?php endif; ?>

                                <div class="og-condition-actions">
                                    <button type="submit" class="button button-secondary og-add-condition-inline"
                                        name="opengraph_filter_action" value="add_condition:<?php echo esc_attr($group_index); ?>">
                                        <?php _e('OR', 'opengraph-xyz'); ?>
                                    </button>
                                    <button type="submit" class="dashicons dashicons-remove og-remove-condition"
                                        name="opengraph_filter_action"
                                        value="remove_condition:<?php echo esc_attr($group_index . ':' . $condition_index); ?>"
                                        aria-label="<?php esc_attr_e('Remove condition', 'opengraph-xyz'); ?>"></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="og-actions">
                <button type="submit" class="button button-secondary og-add-group" name="opengraph_filter_action"
                    value="add_group">
                    <?php _e('AND', 'opengraph-xyz'); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="opengraph[filters_data]" id="opengraph-xyz-filters-data"
        value="<?php echo esc_attr($filters_json); ?>">
</div>