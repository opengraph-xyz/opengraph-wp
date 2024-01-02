<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$opengraphxyz = get_post_meta( get_the_ID(), 'opengraph-xyz', true );
$enabled_post_types = isset($opengraphxyz['post_types']) ? $opengraphxyz['post_types'] : array();

foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) :
    $checked = in_array($post_type->name, $enabled_post_types) ? 'checked' : '';
    ?>
    <p>
      <label>
          <input type="checkbox" name="opengraph[post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php echo $checked; ?> />
          <?php echo esc_html( $post_type->label ); ?>
      </label>
    </p>
<?php endforeach; ?>