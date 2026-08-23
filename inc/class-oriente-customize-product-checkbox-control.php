<?php
/** Product checklist control for the Oriente homepage Customizer. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Multi-product checklist stored as an ordered comma-separated list of product IDs. */
class Oriente_Customize_Product_Checkbox_Control extends WP_Customize_Control {
	public $type = 'oriente_product_checkboxes';

	/** Render the product checklist and its Customizer-bound hidden value. */
	public function render_content() {
		$selected_ids = array_values( array_filter( array_map( 'absint', explode( ',', (string) $this->value() ) ) ) );
		?>
		<?php if ( $this->label ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>
		<input class="oriente-product-selector-value" type="hidden" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?>>
		<ul class="oriente-product-selector">
			<?php foreach ( $this->choices as $product_id => $product_label ) : ?>
				<li>
					<label>
						<input class="oriente-product-selector-checkbox" type="checkbox" value="<?php echo esc_attr( $product_id ); ?>" <?php checked( in_array( (int) $product_id, $selected_ids, true ) ); ?>>
						<span><?php echo esc_html( $product_label ); ?></span>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
