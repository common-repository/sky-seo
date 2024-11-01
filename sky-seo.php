<?php
/*
Plugin Name:    Sky Seo
Plugin URI:     http://skygame.mobi
Description:    Improve your WordPress SEO: Write better content and have a fully optimized WordPress site using Sky SEO plugin.
Version:        1.0.1
Author:         KENT
Author URI:     http://skygame.mobi
 
Text Domain:   sky-seo
Domain Path:   /languages/
*/ 

if ( !class_exists( 'SKY_SEO_WP' ) ) :

	class SKY_SEO_WP {

		private $sky_title;

		private $sky_fields;

		public function __construct() {
			global $wp_version;

			// ===== <<< [ ***** ] >>> ===== //
			if ( !defined( 'SKY_SEO_PATH' ) ) :

				define( 'SKY_SEO_PATH', dirname(__FILE__) );

			endif;

			if ( !defined( 'SKY_SEO_ASSETS' ) ) :

				define( 'SKY_SEO_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );

			endif;

			if ( !defined( 'SKY_SEO_LANGUAGE' ) ) :

				define( 'SKY_SEO_LANGUAGE', SKY_SEO_PATH . '/languages' );

			endif;

			load_plugin_textdomain( 'sky-seo', false, SKY_SEO_LANGUAGE );

			/**
			 * VAR
			 */
				$this->sky_title = esc_html__( 'Sky Seo', 'sky-seo' );
				$this->sky_fields = 'sky_seo';

			/*
			 * Load script
			 */
				if ( is_admin() && ( strpos( $_SERVER['SCRIPT_NAME'], 'post-new.php' ) || strpos( $_SERVER['SCRIPT_NAME'], 'post.php' ) ) !== false || isset( $_GET['page'] ) == 'sky-seo' ) :
					add_action( 'admin_enqueue_scripts', array( &$this, 'load_enqueue_script' ));
				endif;



			$this->sky_autoload( SKY_SEO_PATH . '/framework/functions' );

			/*
			 * Checking version new
			 */
				$plugin = plugin_basename( __FILE__ );
				add_filter( "plugin_action_links_$plugin", array( &$this, 'plugin_add_settings_link' ) );

	        /**
	         * Register Menu
	         */
	        add_action( 'admin_menu', array( &$this, 'sky_register_menu' ) );
	        add_action( 'admin_init', array( &$this, 'admin_init' ) );
	        add_action( 'after_' . $this->sky_fields . '_header', array( &$this, 'sky_save' ) );
	        add_action( 'wp_head', array( &$this, 'sky_load_header' ) );
	        // if ( $wp_version < '4.4' ) add_filter( 'wp_title', array( &$this, 'sky_load_title_old' ), 5, 13 );
	        // else add_filter( 'pre_get_document_title', array( &$this, 'sky_load_title' ) );

		}

		public function sky_autoload( $dir = null ) {

			if ( empty( $dir ) ) $dir = dirname(__FILE__);
			foreach (scandir( $dir ) as $filename) {
	            $path = $dir . '/' . $filename;
	            if (is_file($path)) {
	                require $path;
	            }
	        }

		}

		public function plugin_add_settings_link( $links ) {
			if( is_plugin_active( 'sky-seo/sky-seo.php' ) ) {
			    $settings_link = '<a href="' . admin_url( 'admin.php?page=sky-seo' ) . '">' . esc_html__( 'Settings' ) . '</a>';
			    array_unshift( $links, $settings_link );
			}
		  	return $links;
		}

		public function load_enqueue_script( $hook ) {

			if ( $hook == 'post-new.php' || $hook == 'post.php' || isset( $_GET['page'] ) == 'sky-seo' ) :
				
				wp_register_style( 'sky-seo', SKY_SEO_ASSETS . 'css/sky-seo.css' );
				wp_enqueue_style( 'sky-seo' );

			endif;

			wp_register_script( 'sky-seo', SKY_SEO_ASSETS . 'js/min/sky-seo.min.js', array( 'jquery'), null, true );

			wp_enqueue_script( 'sky-seo' );

			wp_localize_script( 'sky-seo', 'skySeo', array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'security'  => wp_create_nonce( 'noo-course' ),
				'title'     => esc_html__( 'Choose an image', 'sky-game' ),
				'text'      => esc_html__( 'Use image', 'sky-game' ),
				'thumbnail' => ''
			));

		}

		public function admin_init(){

			register_setting( ' . $this->sky_fields . ' , ' . $this->sky_fields . ' );

		}

		public static function get_setting($id = null, $default = null){

			$sky_seo = get_option('sky_seo');

			if(isset($sky_seo[$id]))
				return $sky_seo[$id];

			return $default;

		}

		public function sky_register_menu() {

			add_menu_page( esc_html__( $this->sky_title . ' Settings', 'sky-seo' ), $this->sky_title, 'manage_options', 'sky-seo', array( &$this, 'sky_setting_page' ), SKY_SEO_ASSETS . '/images/icon-menu.png', 82 ); 

		}

		public function sky_save() {

			if ( isset( $_POST['submit'] ) ) :

				update_option( $this->sky_fields, $_POST[ $this->sky_fields ] );
				$this->sky_notice();

			endif;

		}

		public function sky_notice() {

			?>
			    <div class="updated">
			        <p><?php esc_html_e( 'Updated!', 'sky-game' ); ?></p>
			    </div>
		    <?php

		}

		public function sky_load_title_old() {

			do_action( 'before_' . $this->sky_fields . '_title_old' );

			do_action( 'after_' . $this->sky_fields . '_title_old' );

		}

		public function sky_load_title() {

			do_action( 'before_' . $this->sky_fields . '_title' );

			do_action( 'after_' . $this->sky_fields . '_title' );

		}

		public function sky_load_header() {

			do_action( 'before_' . $this->sky_fields . '_header' );

			do_action( 'after_' . $this->sky_fields . '_header' );

		}

		public function sky_setting_page() {
		    do_action( 'before_' . $this->sky_fields . '_setting' );
		    // ===== Load Library
		    	if(function_exists( 'wp_enqueue_media' )) :
				
					wp_enqueue_media();
				
				else :
				
					wp_enqueue_style('thickbox');
					wp_enqueue_script('media-upload');
					wp_enqueue_script('thickbox');
				
				endif;
		    // ===== set default
		    	$title_home = self::get_setting('title_home') ? self::get_setting('title_home') : get_bloginfo( 'name' ); 
		    	$description_home = self::get_setting('description_home') ? self::get_setting('description_home') : get_bloginfo( 'description' ); 
		    	$images_home = self::get_setting('images_home') ? self::get_setting('images_home') : null;
		    	if ($images_home) :
					$images_home_url = wp_get_attachment_url( $images_home );
				else :
					$images_home_url = SKY_SET_THUMBNAIL_DEFAULT;
				endif; 

			?>
				<div class="wrap sky-seo-settings">
	
					<header>
						<?php do_action( 'before_' . $this->sky_fields . '_header' ); ?>
						<h1><?php esc_html_e( $this->sky_title . ' Settings', 'sky-seo' ); ?></h1>
						<?php do_action( 'after_' . $this->sky_fields . '_header' ); ?>
					</header><!-- /header -->

					<section class="setting">

						<form method="post">

							<?php do_action( 'before_' . $this->sky_fields . '_form' ); ?>

							<?php include_once SKY_SEO_PATH . '/framework/sky-settings.php'; ?>

							<?php do_action( 'after_' . $this->sky_fields . '_form' ); ?>

							<?php submit_button() ?>

						</form>

					</section><!-- /. section setting -->

					<footer>
						
					</footer><!-- /footer -->

				</div><!-- /.sky-seo-settings -->
			<?php
			do_action('after_' . $this->sky_fields . '_setting');

		}

		public function sky_get_thumb( $post_ID = null , $meta_thumb = 'sky_thumbnail_url', $image_size = 'thumbnail' , $tag = false ) {
			
			if ( $post_ID ) :
				$post = get_post($post_ID);
				if ( get_query_var( 'id_game' ) ) : $post_ID = get_query_var( 'id_game' ); endif;
			else :
				global $post;
				$post_ID = $post->ID;
			endif;
			// === <<< Check value metabox
				$sky_thumbnail_url = sky_get_post_meta( $post_ID, 'sky_thumbnail_url' );
				if ( !empty( $thumbnail ) ) :
					
					$post_thumbnail_src = $sky_thumbnail_url;

			// === <<< Check isset featured thumbnail
				elseif ( has_post_thumbnail( $post_ID ) ) :

					$thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_ID), $image_size);
					$post_thumbnail_src = $thumbnail_src[0];

			// === <<< get image in content
				else :

					$post_thumbnail_src = '';
					ob_start();
					ob_end_clean();
					$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/is', $post->post_content, $matches);
					if ( $output )
						$post_thumbnail_src = $matches[1][0];

				endif;

			// ==== <<< Checking isset image if not isset then set thumbnail default
				if( empty( $post_thumbnail_src ) ) :
					$post_thumbnail_src = null;
				endif;

			return $post_thumbnail_src;

		}

	}

	new SKY_SEO_WP();

endif;
