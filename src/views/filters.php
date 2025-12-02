<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

$opengraphxyz = get_post_meta(get_the_ID(), 'opengraph-xyz', true);
$filters = isset($opengraphxyz['filters']) ? $opengraphxyz['filters'] : array();

$published_date_filter = isset($filters['published_date']) ? $filters['published_date'] : array();
$is_published_enabled = isset($published_date_filter['enabled']) && $published_date_filter['enabled'] === '1';
$published_condition = isset($published_date_filter['condition']) ? $published_date_filter['condition'] : 'after';
$published_date_value = isset($published_date_filter['date']) ? $published_date_filter['date'] : '';

$modified_date_filter = isset($filters['modified_date']) ? $filters['modified_date'] : array();
$is_modified_enabled = isset($modified_date_filter['enabled']) && $modified_date_filter['enabled'] === '1';
$modified_condition = isset($modified_date_filter['condition']) ? $modified_date_filter['condition'] : 'after';
$modified_date_value = isset($modified_date_filter['date']) ? $modified_date_filter['date'] : '';

?>
<div class="opengraph-xyz-filters">
    <p class="description" style="margin-bottom: 15px;">
        <?php _e('Only apply this template to your selected page types if ANY of the enabled filters match.', 'opengraph-xyz'); ?>
    </p>

    <p>
        <label>
            <input type="checkbox" name="opengraph[filters][published_date][enabled]" value="1" <?php checked($is_published_enabled); ?> />
            <strong><?php _e('Published Date', 'opengraph-xyz'); ?></strong>
        </label>
    </p>

    <div class="published-date-options filter-options"
        style="<?php echo $is_published_enabled ? '' : 'display:none;'; ?>">
        <select name="opengraph[filters][published_date][condition]">
            <option value="after" <?php selected($published_condition, 'after'); ?>>
                <?php _e('After', 'opengraph-xyz'); ?>
            </option>
            <option value="before" <?php selected($published_condition, 'before'); ?>>
                <?php _e('Before', 'opengraph-xyz'); ?>
            </option>
        </select>
        <input type="date" name="opengraph[filters][published_date][date]"
            value="<?php echo esc_attr($published_date_value); ?>" />
    </div>

    <p>
        <label>
            <input type="checkbox" name="opengraph[filters][modified_date][enabled]" value="1" <?php checked($is_modified_enabled); ?> />
            <strong><?php _e('Modified Date', 'opengraph-xyz'); ?></strong>
        </label>
    </p>

    <div class="modified-date-options filter-options"
        style="<?php echo $is_modified_enabled ? '' : 'display:none;'; ?>">
        <select name="opengraph[filters][modified_date][condition]">
            <option value="after" <?php selected($modified_condition, 'after'); ?>>
                <?php _e('After', 'opengraph-xyz'); ?>
            </option>
            <option value="before" <?php selected($modified_condition, 'before'); ?>>
                <?php _e('Before', 'opengraph-xyz'); ?>
            </option>
        </select>
        <input type="date" name="opengraph[filters][modified_date][date]"
            value="<?php echo esc_attr($modified_date_value); ?>" />
    </div>
</div>

<style>
    .opengraph-xyz-filters .filter-options {
        margin-top: 5px;
        margin-bottom: 15px;
        padding-left: 24px;
        display: flex;
        gap: 10px;
    }

    .opengraph-xyz-filters .filter-options select,
    .opengraph-xyz-filters .filter-options input[type="date"] {
        width: 70%;
        box-sizing: border-box;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        $('input[name="opengraph[filters][published_date][enabled]"]').change(function () {
            if ($(this).is(':checked')) {
                $('.published-date-options').slideDown();
            } else {
                $('.published-date-options').slideUp();
            }
        });

        $('input[name="opengraph[filters][modified_date][enabled]"]').change(function () {
            if ($(this).is(':checked')) {
                $('.modified-date-options').slideDown();
            } else {
                $('.modified-date-options').slideUp();
            }
        });
    });
</script>