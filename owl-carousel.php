<?php
/*
Plugin Name: Owl Carousel
Description: A simple plugin to include an Owl Carousel in any post
Author: Pierre Jehan
Contributer: Rasmus Taarnby
Version: 0.5.2
Text Domain: owl-carousel
Domain Path: /languages
Author URI: http://www.pierre-jehan.com
Licence: GPL2
*/

namespace Owl;

/**
 * Do not access this file directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class
 */
class Main {

	/**
	 * Text domain for translators
	 */
	const TEXT_DOMAIN = 'owl-carousel';

	/**
	 * @var string Filename of this class.
	 */
	public $file;

	/**
	 * @var string Basename of this class.
	 */
	public $basename;

	/**
	 * @var string Plugins directory for this plugin.
	 */
	public $plugin_dir;

	/**
	 * @var string Plugins url for this plugin.
	 */
	public $plugin_url;

	/**
	 * @var string Lang dir for this plugin.
	 */
	public $lang_dir;

	/**
	 * @var object Instance of this class.
	 */
	private static $instance = null;

	/**
	 * Returns the instance of this class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {

		// Setup variables and lanuage support
		$this->setup();

		// Include required files
		$this->includes();
		$this->init_hooks();

		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_v1' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		// add_action( 'plugins_loaded', array( $this, 'tiny_mce' ) );
	}


	/**
	 * General setup.
	 */
	private function setup() {
		$this->file       = __FILE__;
		$this->basename   = plugin_basename( $this->file );
		$this->plugin_url = plugin_dir_url( $this->file );
		$this->plugin_dir = plugin_dir_path( $this->file );
		$this->rel_dir    = dirname( $this->basename );
		$this->lang_dir   = $this->rel_dir . '/languages';

		load_plugin_textdomain( $this::TEXT_DOMAIN, false, $this->lang_dir );
	}


	/**
	 * Include classes
	 */
	public function includes() {
		include_once( 'includes/class-widget.php' );
	}

	public function tiny_mce() {
		include 'includes/tinymce.php';
	}

	public function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		// add_shortcode( 'owl-carousel', 'owl_function' );
		// add_action( 'init', array( 'WC_Shortcodes', 'init' ) );
	}


	/**
	 * Initilize the plugin
	 */
	public function init() {

		add_theme_support( 'post-thumbnails' );

		$labels = array(
			'name' => __( 'Owl Carousel', $this::TEXT_DOMAIN ),
			'singular_name' => __( 'Carousel Item', $this::TEXT_DOMAIN ),
			'add_new' => __( 'Add New Item', $this::TEXT_DOMAIN ),
			'add_new_item' => __( 'Add New Carousel Item', $this::TEXT_DOMAIN ),
			'edit_item' => __( 'Edit Carousel Item', $this::TEXT_DOMAIN ),
			'new_item' => __( 'Add New Carousel Item', $this::TEXT_DOMAIN ),
			'view_item' => __( 'View Item', $this::TEXT_DOMAIN ),
			'search_items' => __( 'Search Carousel', $this::TEXT_DOMAIN ),
			'not_found' => __( 'No carousel items found', $this::TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No carousel items found in trash', $this::TEXT_DOMAIN ),
		);

		register_post_type( 'owl-carousel', array(
				'public' => true,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'label' => 'Owl Carousel',
				'menu_icon' => \plugins_url( '/assets/images/owl-logo-16.png', __FILE__ ),
				'labels' => $labels,
				'capability_type' => 'post',
				'supports' => array(
					'title',
					'editor',
					'thumbnail'
				)
			) );

		register_taxonomy(
			'Carousel',
			'owl-carousel',
			array(
				'label' => __( 'Carousel' ),
				'rewrite' => array( 'slug' => 'carousel' ),
				'hierarchical' => true,
				'show_admin_column' => true,
			)
		);

		add_image_size( 'owl_widget', 180, 100, true );
		add_image_size( 'owl_function', 600, 280, true );
		add_image_size( 'owl-full-width', 1200, 675, false ); // 16/9 full-width

/*

		add_filter( "mce_external_plugins", "owl_register_tinymce_plugin" );
		add_filter( 'mce_buttons', 'owl_add_tinymce_button' );
*/

		// Add Wordpress Gallery option
/*
		add_option( 'owl_carousel_wordpress_gallery', 'off' );
		add_option( 'owl_carousel_orderby', 'post_date' );
*/

	}

	public function register_widgets() {
		register_widget( 'Owl\Owl_Widget' );
	}

	/**
	 * Enqueue frontend scripts and styles
	 */
	public function enqueue_v1() {
		// Vendor
		wp_enqueue_script( 'owl-carousel-js', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.carousel.min.js', __FILE__ ), array( 'jquery' ) );

		// Compiled
		wp_enqueue_script( 'owl-carousel-js-main', \plugins_url( '/assets/js/scripts.min.js', __FILE__ ) );

		// Vendor
		wp_enqueue_style( 'owl-carousel-style', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.carousel.css', __FILE__ ) );
		wp_enqueue_style( 'owl-carousel-style-theme', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.theme.css', __FILE__ ) );
		wp_enqueue_style( 'owl-carousel-style-transitions', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.transitions.css', __FILE__ ) );

		// Compiled
		wp_enqueue_style( 'owl-carousel-style-main', \plugins_url( '/assets/css/main.min.css', __FILE__ ) );
	}

	public function enqueue_v2() {
		// Vendor
		wp_enqueue_script( 'owl-carousel-js', \plugins_url( 'assets/vendor/owl-carousel-2.0.0-beta.2.4.4/owl.carousel.min.js', __FILE__ ), array( 'jquery' ) );

		// Compiled
		wp_enqueue_script( 'owl-carousel-js-script', \plugins_url( '/assets/js/scripts.min.js', __FILE__ ) );

		// Vendor
		wp_enqueue_style( 'owl-carousel-style', \plugins_url( 'assets/vendor/owl-carousel-2.0.0-beta.2.4.4/assets/owl.carousel.css', __FILE__ ) );

		// Compiled
		wp_enqueue_style( 'owl-carousel-style-main', \plugins_url( '/assets/css/main.min.css', __FILE__ ) );
	}


	/**
	 * Enqueue admin scripts and styles
	 */
	public function admin_enqueue() {
		wp_enqueue_style( 'owl-carousel-admin-style', \plugins_url( 'assets/css/admin-styles.css', __FILE__ ) );
		wp_enqueue_script( 'owl-carousel-admin-js', \plugins_url( 'assets/js/admin-script.js' , __FILE__ ), array( 'jquery' ), time(), true );
	}


} // end class

/**
 * Returns the main instance
 */
function main() {
	return Main::instance();;
}


main();


function register_tinymce_plugin( $plugin_array ) {
	$plugin_array['owl_button'] = plugins_url( '/owl-carousel/assets/js/tinymce-plugin.js' );
	return $plugin_array;
}

function add_tinymce_button( $buttons ) {
	$buttons[] = "owl_button";
	return $buttons;
}