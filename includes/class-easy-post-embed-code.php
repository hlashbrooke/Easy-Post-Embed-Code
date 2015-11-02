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

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Add meta box for embed code
		add_action( 'add_meta_boxes', array( $this, 'meta_box_setup' ), 10, 2 );

		add_action( 'wp_ajax_update_embed_code', array( $this, 'update_embed_code' ) );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	public function meta_box_setup ( $post_type, $post ) {
		global $pagenow;
		if( 'post.php' == $pagenow && 'publish' == $post->post_status ) {
			add_meta_box( 'embed-code', __( 'Embed Code' , 'easy-post-embed-code' ), array( $this, 'meta_box_content' ), $post_type, 'side', 'low' );
		}
	}

	public function meta_box_content ( $post ) {
		$embed_code = get_post_embed_html( 500, 350, $post );

		$html = '<p><em>' . __( 'Customise the size of your post embed below, then copy the HTML to your clipboard.', 'easy-post-embed-code' ) . '</em></p>';
		$html .= '<p><label for="embed_code_width">' . __( 'Width:', 'easy-post-embed-code' ) . '</label> <input id="embed_code_width" class="embed_code_size_option" type="number" value="500" length="3" min="0" step="1" /> &nbsp;&nbsp;&nbsp;&nbsp;<label for="embed_code_height">' . __( 'Height:', 'easy-post-embed-code' ) . '</label> <input id="embed_code_height" class="embed_code_size_option" type="number" value="350" length="3" min="0" step="1" /></p>';
		$html .= '<p><textarea readonly id="easy_post_embed_code">' . $embed_code . '</textarea></p>';

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
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'easy-post-embed-code', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'easy-post-embed-code';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

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
