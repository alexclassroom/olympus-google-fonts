<?php
/**
 * Blocks Package
 *
 * @package   olympus-google-fonts
 * @copyright Copyright (c) 2018, Danny Cooper
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for backend editor.
 */
function olympus_google_fonts_block_js() {
	wp_enqueue_script(
		'olympus_google_fonts-block-js',
		plugins_url( '/dist/blocks.build.js', __FILE__ ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
		OGF_VERSION
	);
}

add_action( 'enqueue_block_editor_assets', 'olympus_google_fonts_block_js' );

/**
 * Registers the 'olympus-google-fonts/google-fonts' block on server.
 */
function olympus_google_fonts_register_block() {
	// Check if the register function exists.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	register_block_type(
		'olympus-google-fonts/google-fonts',
		array(
			'attributes'      => array(
				'fontID' => array(
					'type'    => 'string',
					'default' => '',
				),
				'variant' => array(
					'type'    => 'string',
					'default' => 'regular',
				),
				'fontSize' => array(
					'type' => 'number',
				),
				'lineHeight' => array(
					'type' => 'number',
				),
				'align' => array(
					'type' => 'string',
				),
				'content' => array(
					'type' => 'string',
				),
			),
			'render_callback' => 'olympus_google_fonts_block_render',
		)
	);
}
add_action( 'init', 'olympus_google_fonts_register_block' );

/**
 * Front end render function for 'olympus-google-fonts/google-fonts'.
 *
 * @param array $attributes The block attributes.
 */
function olympus_google_fonts_block_render( $attributes ) {

	$font_id     = isset( $attributes['fontID'] ) ? sanitize_text_field( $attributes['fontID'] ) : '';
	$variant     = isset( $attributes['variant'] ) ? sanitize_text_field( $attributes['variant'] ) : '';
	$font_size   = isset( $attributes['fontSize'] ) ? intval( $attributes['fontSize'] ) : '';
	$line_height = isset( $attributes['lineHeight'] ) ? floatval( $attributes['lineHeight'] ) : '';
	$align       = isset( $attributes['align'] ) ? sanitize_text_field( $attributes['align'] ) : '';
	$content     = isset( $attributes['content'] ) ? sanitize_text_field( $attributes['content'] ) : '';
	$output      = '';
	$style       = '';

	if ( $font_id ) {

		$font_family = str_replace( '+', ' ', $font_id );
		$font_id     = str_replace( '+', '-', strtolower( $font_id ) );

		$fonts    = ogf_fonts_array();
		$variants = $fonts[ $font_id ]['variants'];
		unset( $variants[0] );
		$variants_for_url = join( array_keys( $variants ), ',' );

		wp_enqueue_style( 'google-font-' . $font_id, esc_url( 'https://fonts.googleapis.com/css?family=' . $font_family . ':' . $variants_for_url ), array(), OGF_VERSION );

		$style = "font-family: {$font_family};";

		if ( $variant && 'regular' !== $variant ) {
			$style .= "font-weight: {$variant};";
		}

		if ( $font_size ) {
			$style .= "font-size: {$font_size}px;";
		}

		if ( $line_height ) {
			$style .= "line-height: {$line_height};";
		}

		if ( $align ) {
			$style .= "text-align: {$align};";
		}
	}

	$output .= '<p class="google-fonts-blocks" style="' . $style . '">';
	$output .= $content;
	$output .= '</p>';

	return $output;
}
