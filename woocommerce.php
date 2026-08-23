<?php
/** Main WooCommerce template. @package Oriente */
get_header();
$is_single_product = function_exists( 'is_product' ) && is_product();
$is_product_category = function_exists( 'is_product_category' ) && is_product_category();
$is_main_category  = function_exists( 'oriente_main_product_category_context' ) && oriente_main_product_category_context();
?>
<main id="primary" class="site-main">
	<div class="woocommerce-page-shell<?php echo $is_single_product ? ' single-product-page-shell' : ''; ?>">
		<?php
		if ( $is_product_category && ! $is_main_category && function_exists( 'oriente_archive_breadcrumbs' ) ) {
			oriente_archive_breadcrumbs();
		}
		woocommerce_content();
		?>
	</div>
</main>
<?php
get_footer();
