<?php
/**
 * Oriente single-product content.
 *
 * The gallery and buying information share a dedicated layout boundary so the
 * sticky summary ends before product details and related products begin.
 *
 * @package Oriente
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<div class="oriente-product-top">
		<div class="oriente-product-gallery-column">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>

		<div class="summary entry-summary">
			<?php do_action( 'woocommerce_single_product_summary' ); ?>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
