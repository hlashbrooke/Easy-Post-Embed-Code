<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Easy_Post_Embed_Code {

	/**
	 * The single instance of Easy_Post_Embed_Code.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'easy_post_embed_code';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Add meta box for embed code
		add_action( 'add_meta_boxes', array( $this, 'meta_box_setup' ), 10, 2 );

		// Update embed code when dimensions are updated
		add_action( 'wp_ajax_update_embed_code', array( $this, 'update_embed_code' ) );
		add_action( 'wp_ajax_nopriv_update_embed_code', array( $this, 'update_embed_code' ) );

		// Add block with shortcode fallback
		add_action( 'init', array( $this, 'embed_code_block_init' ) );

	} // End __construct ()

	public function meta_box_setup ( $post_type, $post ) {
		global $pagenow;
		if( 'post.php' == $pagenow && 'publish' == $post->post_status ) {
			add_meta_box( 'embed-code', __( 'Embed Code' , 'easy-post-embed-code' ), array( $this, 'meta_box_content' ), $post_type, 'side', 'low', array( '__block_editor_compatible_meta_box' => true, '__back_compat_meta_box' => false ) );
		}
	}

	public function meta_box_content ( $post ) {
		$embed_code = get_post_embed_html( 500, 350, $post );

		$html = '<p><em>' . __( 'Customise the size of your post embed below, then copy the HTML to your clipboard.', 'easy-post-embed-code' ) . '</em></p>';
		$html .= '<p>';
			$html .= '<span class="buttons">';
				$html .= '<label for="embed_code_width-' . $post->ID . '">' . __( 'Width:', 'easy-post-embed-code' ) . '</label> <input id="embed_code_width-' . $post->ID . '" class="embed_code_size_option" type="number" value="500" length="3" min="0" step="1" /> &nbsp;&nbsp;&nbsp;&nbsp;';
				$html .= '<label for="embed_code_height-' . $post->ID . '">' . __( 'Height:', 'easy-post-embed-code' ) . '</label> <input id="embed_code_height-' . $post->ID . '" class="embed_code_size_option" type="number" value="350" length="3" min="0" step="1" />';
				$html .= '<input type="hidden" class="embed_code_post_id" value="' . $post->ID . '" />';
			$html .= '</span><br/>';
			$html .= '<textarea id="easy_post_embed_code" class="easy_post_embed_code" readonly="readonly" onclick="this.focus();this.select()">' . $embed_code . '</textarea>';
		$html .= '</p>';

		echo $html;
	}

	public function update_embed_code () {

		if( ! isset( $_POST['post_id'] ) || ! $_POST['post_id'] ) {
			return;
		}

		$post_id = (int) $_POST['post_id'];
		$width = (int) $_POST['width'];
		$height = (int) $_POST['height'];

		echo get_post_embed_html( $width, $height, $post_id );
		exit;
	}

	public function embed_code_block_init () {

		// Stop here if Gutenberg is not active
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register block editor script.
		wp_register_script(
			'embed-code-block',
			$this->assets_url . 'js/embed-code-block.js',
			array( 'wp-blocks', 'wp-components', 'wp-i18n', 'wp-element' ),
			filemtime( $this->assets_dir . '/js/embed-code-block.js' )
		);

		// Register block
		register_block_type( 'easy-post-embed-code/embed-code-block', array(
				'editor_script' => 'embed-code-block',
				'render_callback' => array( $this, 'embed_code_render' )
			)
		);

		// Add localisation strings
		wp_add_inline_script(
			'embed-code-block',
			sprintf(
				'var easy_post_embed_code = { localeData: %s };',
				json_encode( gutenberg_get_jed_locale_data( 'easy-post-embed-code' ) )
			),
			'before'
		);

		// Define shortcode using same render function as the block
		add_shortcode( 'embed_code', array( $this, 'embed_code_render' ) );
	}

	public function embed_code_render ( $atts = array() ) {

		$html = '';

		if( is_admin() ) {
			$html .= '<textarea class="easy_post_embed_code" readonly="readonly">' . __( 'Embed code for this post.', 'easy-post-embed-code' ) . '</textarea>';
		} else {
			global $post;

			// Get data from shortcode attributes
			$data = shortcode_atts( array( 'post' => 0, 'width' => 500, 'height' => 350 ), $atts, 'embed_code' );

			// Get current post ID if none is set
			if( ! $data['post'] ) {
				global $post;

				if( ! $post->ID ) {
					return;
				}

				$data['post'] = $post->ID;
			}

			$html = '';

			$embed_code = get_post_embed_html( $data['width'], $data['height'], $data['post'] );

			$html .= '<p class="easy_post_embed_code_container">';
				$html .= '<textarea id="easy_post_embed_code-' . $data['post'] . '" class="easy_post_embed_code" readonly="readonly" rows="4" style="resize: none;">' . $embed_code . '</textarea>';
				$html .= '<span class="buttons">';
					$html .= '<label for="embed_code_width' . $data['post'] . '">' . __( 'Width:', 'easy-post-embed-code' ) . '</label> <input id="embed_code_width-' . $data['post'] . '" class="embed_code_size_option embed_code_width" type="number" value="' . $data['width'] . '" length="3" min="0" step="1" />&nbsp;';
					$html .= '<label for="embed_code_height' . $data['post'] . '">' . __( 'Height:', 'easy-post-embed-code' ) . '</label> <input id="embed_code_height-' . $data['post'] . '" class="embed_code_size_option embed_code_height" type="number" value="' . $data['height'] . '" length="3" min="0" step="1" />&nbsp;';
					$html .= '<input type="hidden" class="embed_code_post_id" value="' . $data['post'] . '" />';
					$html .= '<button class="copy-embed-code">' . __( 'Copy embed code', 'easy-post-embed-code' ) . '</button>';
				$html .= '</span>';
			$html .= '</p>';
		}

		return $html;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_styles ( $hook = '' ) {
		if( apply_filters( $this->_token . '_use_css', true ) ) {
			wp_register_style( $this->_token . '-styles', esc_url( $this->assets_url ) . 'css/styles.css', array(), $this->_version );
			wp_enqueue_style( $this->_token . '-styles' );
		}
	} // End admin_enqueue_styles ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-scripts', esc_url( $this->assets_url ) . 'js/scripts.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-scripts' );

		// Set AJAX URL for frontend requests
		wp_localize_script( $this->_token . '-scripts', 'easy_post_embed_code_obj', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	} // End enqueue_scripts ()

	/**
	 * Main Easy_Post_Embed_Code Instance
	 *
	 * Ensures only one instance of Easy_Post_Embed_Code is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Easy_Post_Embed_Code()
	 * @return Main Easy_Post_Embed_Code instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
