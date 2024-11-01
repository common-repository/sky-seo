<?php 
/**
 * Sky Seo: HOME PAGE
 *
 * @author KENT <skygame.mobi@gmail.com>
 * @since 1.0
 * 
 */
// ===== <<< [ ***** ] >>> ===== //

/**
 * Create class Sky_Seo_Home_Page
 */

if ( !class_exists( 'Sky_Seo_Home_Page' ) ) :

	class Sky_Seo_Home_Page{

		/**
		 * Auto load
		 */
		public function __construct() {

			/**
			 * Load action/filter
			 */
			add_action( 'before_sky_seo_header', array( &$this, 'sky_content' ) );
			// add_filter( 'wp_title', array( &$this, 'sky_set_title' ), 10, 2 );

		}

		public static function get_setting($id = null, $default = null){

			$sky_seo = get_option('sky_seo');

			if(isset($sky_seo[$id]))
				return $sky_seo[$id];

			return $default;

		}

		/**
		 * [sky_content description]
		 * @return [type] [description]
		 */
		public function sky_content() {

			if ( is_home() || is_front_page() ) :
				
				// ===== <<< [ VAR ] >>> ===== //
					$title = self::get_setting( 'title_home' );
					$description = self::get_setting( 'description_home' );
					$keyword = self::get_setting( 'keyword_home' );
					$images_home = self::get_setting('images_home') ? self::get_setting('images_home') : null;
			    	if ($images_home) :
						$images_home_url = wp_get_attachment_url( $images_home );
					else :
						$images_home_url = SKY_SET_THUMBNAIL_DEFAULT;
					endif; 

				if ( !empty( $description ) ) :

					echo '<meta name="description" content="' . $description . '" />';

				endif;

				// ===== OG META
					if ( !empty( $description ) ) :

						?>
							<?php if ( !empty( $title ) ) : ?><meta property="og:title" content="<?php echo esc_attr( $title ) ?>" /><?php endif; ?>
							<?php if ( !empty( $description ) ) : ?><meta property="og:description" content="<?php echo esc_attr( $description ) ?>" /><?php endif; ?>
							<?php if ( !empty( $images_home_url ) ) : ?><meta property="og:image" content="<?php echo $images_home_url; ?>" /><?php endif; ?>
							<meta name="twitter:card" content="summary" />
							<?php if ( !empty( $description ) ) : ?><meta name="twitter:description" content="<?php echo esc_attr( $description ) ?>" /><?php endif; ?>
							<?php if ( !empty( $title ) ) : ?><meta name="twitter:title" content="<?php echo esc_attr( $title ) ?>" /><?php endif; ?>
							<?php if ( !empty( $images_home_url ) ) : ?><meta name="twitter:image" content="<?php echo $images_home_url; ?>" /><?php endif; ?>
							<meta property="og:url" content="<?php echo get_bloginfo( 'url' ); ?>" />
							<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>" />
						<?php

					endif;

			endif;

		}

		public function sky_set_title( $title, $sep ) {
			
			if ( is_home() || is_front_page() ) :
			
				$title_seo = self::get_setting( 'title_home' );
				if ( !isset($title_seo) || empty($title_seo) ) :
					$title_seo = $title;
				endif;

				// ====

					return $title_seo;

			endif;
			return $title;

		}

	}

endif;

/**
 * End class Sky_Seo_Home_Page
 */

// ===== <<< [ ***** ] >>> ===== //