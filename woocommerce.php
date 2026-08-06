<?php
/** Main WooCommerce template. @package Oriente */
get_header();
$is_single_product = function_exists( 'is_product' ) && is_product();
?>
<main id="primary" class="site-main">
	<div class="woocommerce-page-shell<?php echo $is_single_product ? ' single-product-page-shell' : ''; ?>">
		<?php
		if ( $is_single_product && function_exists( 'woocommerce_breadcrumb' ) ) {
			woocommerce_breadcrumb();
		}
		woocommerce_content();
		?>
	</div>
</main>
<?php
get_footer();
