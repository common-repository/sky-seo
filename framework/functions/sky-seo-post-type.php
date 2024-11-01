<?php
/**
 * Sky Seo: POST TYPE
 *
 * @author  KENT <skygame.mobi@gmail.com>
 * @since 1.0
 * 
 */

if ( !class_exists( 'Sky_Seo_Post_Type' ) ) :

	class Sky_Seo_Post_Type {

		var $prefix;

		/**
		 * Hook into the appropriate actions when the class is constructed.
		 */
		public function __construct() {
			global $wp_version;
			$this->prefix = 'sky_seo_';

			add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
			add_action( 'save_post', array( &$this, 'save_metabox' ) );

			// =====
			add_action( 'before_sky_seo_header', array( &$this, 'sky_add_meta_head' ) );
			// add_action( 'before_sky_seo_title_old', array( &$this, 'sky_set_title_old' ), 15, 3 );
			// add_action( 'before_sky_seo_title', array( &$this, 'sky_set_title' ) );
			if ( $wp_version < '4.4' ) add_filter( 'wp_title', array( &$this, 'sky_set_title_old' ), 5, 13 );
	        else add_filter( 'pre_get_document_title', array( &$this, 'sky_set_title' ) );

		}

		/**
		 * [sky_get_all_post_type description]
		 * @return array
		 */
		public function sky_get_all_post_type() {

			$post_types = get_post_types( '', 'names' ); 
			$list = array();

			foreach ( $post_types as $post_type ) {

				if ( $post_type != 'attachment' && $post_type != 'revision' && $post_type != 'nav_menu_item' ) :
			    	
			    	$list[] = $post_type;

			    endif;
			
			}

			return $list;

		}

		/**
		 * [add_meta_box description]
		 * @param string $post_type = array()
		 * Adds the meta box container.
		 * 
		 */
		public function add_meta_box( $post_type ) {
			
			$post_types = $this->sky_get_all_post_type();

			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'sky_seo_box',
					__( 'Sky Seo', 'sky-seo' ),
					array( &$this, 'sky_seo_box_content' ),
					$post_type,
					'advanced',
					'high'
				);
			}
		}

		/**
		 * Save the meta when the post is saved.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		public function save_metabox( $post_id ) {
		
			/*
			 * We need to verify this came from the our screen and with proper authorization,
			 * because save_post can be triggered at other times.
			 */

			// Check if our nonce is set.
			if ( ! isset( $_POST['sky_seo_filed_nonce'] ) )
				return $post_id;

			$nonce = $_POST['sky_seo_filed_nonce'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'sky_seo_verify' ) )
				return $post_id;

			// If this is an autosave, our form has not been submitted,
			// so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return $post_id;

			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;
		
			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}

			/* OK, its safe for us to save the data now. */

			// Sanitize the user input.
			$title       = sanitize_text_field( $_POST[ $this->prefix . 'title' ] );
			$description = sanitize_text_field( $_POST[ $this->prefix . 'description' ] );
			$keyword     = sanitize_text_field( $_POST[ $this->prefix . 'keyword' ] );
			$background  = sanitize_text_field( $_POST[ $this->prefix . 'background' ] );

			// Update the meta field.
			update_post_meta( $post_id, $this->prefix . 'title', $title );
			update_post_meta( $post_id, $this->prefix . 'description', $description );
			update_post_meta( $post_id, $this->prefix . 'keyword', $keyword );
			update_post_meta( $post_id, $this->prefix . 'background', $background );
		}

		/**
		 * Sky Seo Box content.
		 *
		 * @param WP_Post $post The post object.
		 */
		public function sky_seo_box_content( $post ) {
			// ===== Load Library
		    	if(function_exists( 'wp_enqueue_media' )) :
				
					wp_enqueue_media();
				
				else :
				
					wp_enqueue_style('thickbox');
					wp_enqueue_script('media-upload');
					wp_enqueue_script('thickbox');
				
				endif;
			// ===== Add an nonce field so we can check for it later.
				wp_nonce_field( 'sky_seo_verify', 'sky_seo_filed_nonce' );

			// ===== Use get_post_meta to retrieve an existing value from the database.
				$title = get_post_meta( $post->ID, $this->prefix . 'title', true );
				if ( empty($title) ) $title = '';

				$description = get_post_meta( $post->ID, $this->prefix . 'description', true );
				if ( empty($description) ) $description = '';

				$keyword = get_post_meta( $post->ID, $this->prefix . 'keyword', true );
				if ( empty($keyword) ) $keyword = '';

				$images_posts = get_post_meta( $post->ID, $this->prefix . 'background', true );
		    	if ($images_posts) :
					$images_post = wp_get_attachment_url( $images_posts );
				else :
					$images_post = SKY_SET_THUMBNAIL_DEFAULT;
				endif; 

					?>
					<div class="sky-seo-field">
						<div class="sky-seo-label">
							<label for="sky-seo-title"><?php esc_html_e( 'Title', 'sky-seo' ); ?></label>
						</div>
						<div class="sky-seo-input">
							<input type="text" id="sky-seo-title" name="<?php echo $this->prefix; ?>title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_html_e( 'Enter your title', 'sky-seo' ); ?>">
						</div>
					</div>

					<div class="sky-seo-field">
						<div class="sky-seo-label">
							<label for="sky-seo-description"><?php esc_html_e( 'Description', 'sky-seo' ); ?></label>
						</div>
						<div class="sky-seo-input">
							<textarea name="<?php echo $this->prefix; ?>description" placeholder="<?php esc_html_e( 'Enter your description', 'sky-seo' ); ?>"><?php echo esc_html__( $description ); ?></textarea>
						</div>
					</div>

					<div class="sky-seo-field">
						<div class="sky-seo-label">
							<label for="sky-seo-keyword"><?php esc_html_e( 'Keyword', 'sky-seo' ); ?></label>
						</div>
						<div class="sky-seo-input">
							<input type="text" id="sky-seo-keyword" name="<?php echo $this->prefix; ?>keyword" value="<?php echo esc_attr( $keyword ); ?>" placeholder="<?php esc_html_e( 'Enter your keyword', 'sky-seo' ); ?>">
						</div>
					</div>
					<div class="sky-seo-field">
						<div class="sky-seo-label">
							<label for="sky-seo-background"><?php esc_html_e( 'Background Sharing', 'sky-seo' ); ?></label>
						</div>
						<div class="sky-seo-input">
							<div id="background"><img width="100px" height="60px" src="<?php echo $images_post; ?>" /></div>
							<input type="hidden" id="backgrounds" name="<?php echo $this->prefix; ?>background" value="<?php echo $images_post; ?>" />
							<button type="button" class="upload_image_button button" data-id="background"><?php esc_html_e('Upload/Add image', 'sky-game'); ?></button>
							<button type="button" class="remove_image_button button" data-id="background"><?php esc_html_e('Remove image', 'sky-game'); ?></button>
						</div>
					</div>
			<?php
		}

		public function sky_get_post_meta( $post_ID, $key ) {

			return get_post_meta( $post_ID, $key, true );

		}

		public function sky_add_meta_head() {

			if ( is_single() || is_page() ) :

				global $post;
				$sky_seo = new SKY_SEO_WP();
				// ====
					$title = get_post_meta( $post->ID, $this->prefix . 'title', true );
					if ( empty($title) ) $title = '';

					$description = get_post_meta( $post->ID, $this->prefix . 'description', true );
					if ( empty($description) ) $description = '';

					$keyword = get_post_meta( $post->ID, $this->prefix . 'keyword', true );
					if ( empty($keyword) ) $keyword = '';

					// ==== Get url thumb
						$background = get_post_meta( $post->ID, $this->prefix . 'background', true );
						if ( empty($background) ) :
							$thumb = $sky_seo->sky_get_thumb();
						else :
							$thumb = wp_get_attachment_url( $background );
						endif;

						
				// ====
				?>
					<meta name="keywords" content="<?php echo $keyword ?>" />
					<meta property="og:title" content="<?php echo $title; ?>" />
					<meta property="og:description" content="<?php echo $description ?>" />
					<?php if ( !empty( $thumb ) ) : ?>
						<meta property="og:url" content="<?php echo $thumb ?>" />
					<?php endif; ?>
					<meta property="og:site_name" content="<?php echo bloginfo( 'name' ); ?>" />
					<?php if ( !empty( $thumb ) ) : ?>
						<meta property="og:image" content="<?php echo $thumb ?>" />
					<?php endif; ?>
					<meta name="twitter:card" content="summary" />
					<meta name="twitter:description" content="<?php echo $description ?>" />
					<meta name="twitter:title" content="<?php echo $title; ?>" />
					<?php if ( !empty( $thumb ) ) : ?>
						<meta name="twitter:image" content="<?php echo $thumb ?>" />
					<?php endif; ?>
				<?php
			endif;
			return;

		}

		public function sky_set_title_old( $title, $sep ) {

			if ( is_single() || is_page() ) :
				global $post;

				// ====
					$title_seo = get_post_meta( $post->ID, $this->prefix . 'title', true );
					if ( !isset($title_seo) || empty($title_seo) ) :
						$title_seo = $title;
					endif;
				// ====

				return $title_seo;
			endif;

			return $title;

		}

		public function sky_set_title() {

			if ( is_single() || is_page() ) :
				global $post;

				// ====
					$title_seo = get_post_meta( $post->ID, $this->prefix . 'title', true );
					if ( !isset($title_seo) || !empty($title_seo) ) :
						return $title_seo;
					endif;
			endif;

		}

	}

	new Sky_Seo_Post_Type();

endif;