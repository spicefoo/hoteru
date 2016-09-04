<?php
/*
 * Copyright (c) 2015-2016 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * Plugin Name: Really Simple CAPTCHA for cformsII
 * Plugin URI: https://wordpress.org/plugins/cforms2-really-simple-captcha/
 * Description: This enables the Really Simple CAPTCHA for the cformsII form plugin.
 * Author: Bastian Germann
 * Version: 1.0
 */
define( 'CFORMS2_RSC_VERSION', '1.0' );

function cforms2_rsc() {

	if (!class_exists('cforms2_captcha') || !class_exists('ReallySimpleCaptcha') ) {
		return;
	}

	/**
	 * Provides the pluggable captcha for cformsII, depending on the settings for
	 * the original cformsII captcha.
	 */
	class cforms2_really_simple_captcha extends cforms2_captcha {

		private $cforms_settings;
		private $rsc;
		private $url;

		public function __construct() {
			$this->cforms_settings = get_option('cforms_settings');
			$this->rsc = new ReallySimpleCaptcha();
			$this->url = plugins_url(plugin_basename($this->rsc->tmp_dir));

			$cap = $this->cforms_settings['global']['cforms_captcha_def'];

			$char_length = self::int_range_rand($cap['c1'], $cap['c2']);
			if ($char_length > 0)
				$this->rsc->char_length = intval($char_length);

			$font_size = self::int_range_rand($cap['f1'], $cap['f2']);
			if ($font_size > 0)
				$this->rsc->font_size = $font_size;

			$allowed_chars = $cap['ac'];
			if (!empty($allowed_chars))
				$this->rsc->chars = $allowed_chars;

			$width = intval($cap['w']);
			$height = intval($cap['h']);
			if ($width > 0 && $height > 0)
				$this->rsc->img_size = array($width, $height);

			$color = self::convert_hex_rgb_to_dec($cap['l']);
			$this->rsc->bg = $color;

			$color = self::convert_hex_rgb_to_dec($cap['c']);
			$this->rsc->fg = $color;



			wp_register_script(
				'cforms2_rsc',
				plugin_dir_url(__FILE__) . 'cforms2_really_simple_captcha.js',
				array('jquery'), CFORMS2_RSC_VERSION
			);
			wp_localize_script( 'cforms2_rsc', 'cforms2_rsc_ajax', array(
				'url'   => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('cforms2_rsc_reset_captcha')
			) );

			add_action( 'wp_ajax_cforms2_rsc_reset_captcha',        array($this, 'reset_captcha') );
			add_action( 'wp_ajax_nopriv_cforms2_rsc_reset_captcha', array($this, 'reset_captcha') );

		}
		
		/**
		 * Converts the two parameters to integers and returns a random number
		 * between them.
		 * 
		 * @param string $min
		 * @param string $max
		 * @return float between $min and $max
		 */
		private static function int_range_rand($min, $max) {
			$min = intval($min);
			$max = intval($max);
			return mt_rand($min, $max);
		}
		
		/**
		 * Converts an HTML hex color string to an int array.
		 * 
		 * @param string $hex HTML RGB color in the format #RRGGBB.
		 * @return array with three int elements: (R, G, B)
		 */
		private static function convert_hex_rgb_to_dec($hex) {
			$dec = array();
			$dec[] = hexdec(substr($hex,1,2));
			$dec[] = hexdec(substr($hex,3,2));
			$dec[] = hexdec(substr($hex,5,2));
			return $dec;
		}

		public function get_id() {
			return 'captcha';
		}

		public function get_name() {
			return 'Really Simple CAPTCHA';
		}

		public function check_authn_users() {
			return $this->cforms_settings['global']['cforms_captcha_def']['fo'] == '1';
		}

		public function check_response($post) {
			$hint = $post[$this->get_id() . '/hint'];
			$answer = $post[$this->get_id()];
			$check = $this->rsc->check($hint, $answer);
			$this->rsc->remove($hint);
			return $check;
		}

		public function get_request($input_id, $input_classes, $input_title) {
			$info = $this->generate_captcha();

			$req = '<label for="'.$input_id.'" class="secq"><span>CAPTCHA</span></label>'
			     . '<input type="text" name="'.$this->get_id().'" id="'.$input_id.'" '
			     . 'class="'.$input_classes.'" title="'.$input_title.'"/>'
			     . '<img class="cforms2_really_simple_captcha_img" alt="CAPTCHA" src="'.$info['url'].'" />'
			     . '<span class="dashicons dashicons-update captcha-reset"></span>'
			     . '<input type="hidden" name="'.$this->get_id().'/hint" value="' . $info['hint'] . '"/>';

			wp_enqueue_script('cforms2_rsc');
			wp_enqueue_style('dashicons');

			return $req;
		}

		private function generate_captcha() {
			$word = $this->rsc->generate_random_word();
			$hint = mt_rand();
			$url = $this->url. '/' .$this->rsc->generate_image( $hint, $word );

			return array(
				'hint' => $hint,
				'url'  => $url
			);
		}

		public function reset_captcha() {
			check_admin_referer( 'cforms2_rsc_reset_captcha' );
			header ('Content-Type: application/json');
			echo json_encode($this->generate_captcha());
			die();
		}

		public function render_settings() {
		}

	}

	add_filter('cforms2_add_captcha', array(new cforms2_really_simple_captcha(), 'add_instance'));

}

add_action( 'init', 'cforms2_rsc' , 11);


require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'cforms2_rsc_register_required_plugins' );

/**
 * Registers the required plugins for this plugin.
 */
function cforms2_rsc_register_required_plugins() {

    $plugins = array(

        array(
            'name'               => 'cformsII',
            'slug'               => 'cforms2',
            'required'           => true,
            'version'            => '14.11'
        ),

        array(
            'name'               => 'Really Simple CAPTCHA',
            'slug'               => 'really-simple-captcha',
            'required'           => true
        )

    );

    tgmpa( $plugins );

}

