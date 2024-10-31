<?php
/**
 * Onlim Admin
 *
 * @package  Onlim/Admin
 * @version  1.0.0
 */


defined( 'ABSPATH' ) || exit;


/**
 * Onlim_Admin class.
 */
class Onlim_Admin {

  /**
	 * Constructor.
	 */
	public function __construct() {
    add_action( 'init', array( $this, 'onlim_includes' ) );
    add_action( 'admin_init', array( $this, 'onlim_buffer' ), 1 );
    add_action( 'admin_init', array( $this, 'onlim_prevent_access' ) );
    add_action( 'admin_init', array( $this, 'onlim_settings_init' ) );
    add_action( 'admin_menu', array( $this, 'onlim_add_admin_menu' ) );
		add_action( 'admin_bar_menu', array( $this, 'onlim_add_admin_bar_menu' ), 999 );
    add_action( 'admin_enqueue_scripts', array( $this, 'onlim_admin_styles' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'onlim_admin_scripts' ) );
  }

  /**
   * Output buffering allows admin screens to make redirects later on.
   */
  public function onlim_buffer() {
    ob_start();
  }

  /**
	 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin.
	 */
	public function onlim_prevent_access() {
		$prevent_access = false;

		if ( ! is_doing_ajax() && basename( $_SERVER['SCRIPT_FILENAME'] ) !== 'admin-post.php' ) {
			$has_cap     = false;
			$access_caps = array( 'manage_onlim', 'view_admin_dashboard' );

			foreach ( $access_caps as $access_cap ) {
				if ( current_user_can( $access_cap ) ) {
					$has_cap = true;
					break;
				}
			}

			if ( ! $has_cap ) {
				$prevent_access = true;
			}
		}

		if ( apply_filters( 'onlim_prevent_access', $prevent_access ) ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
	}

  /**
	 * Include any classes we need within admin.
	 */
	public function onlim_includes() {
		include_once( dirname( __FILE__ ) . '/onlim-admin-functions.php' );
	}

  public function onlim_add_admin_menu() {
    add_menu_page( __( 'Onlim Livechat and Chatbot', 'onlim' ), __( 'Onlim', 'onlim' ), 'manage_options', 'onlim', array( $this, 'onlim_load_main_page' ), 'dashicons-format-status' );
  	add_submenu_page( 'onlim', __( 'Onlim Settings', 'onlim' ), __( 'Settings', 'onlim' ), 'manage_options', 'onlim_settings', array( $this, 'onlim_settings_page' ) );
  }

	public function onlim_add_admin_bar_menu() {
		global $wp_admin_bar;
		$node = array(
			'id'     => 'onlim-admin-menu',
			'title'  => '<span class="ab-icon"></span> <span class="ab-label">' . __( 'Onlim', 'onlim' ) . '</span>',
			'parent' => FALSE,
			'href'   => onlim_external_url( 'app' ),
			'meta'   => array( 'title' => __( 'Onlim', 'onlim' ), 'target' => '_blank' )
		);
		$wp_admin_bar->add_node( $node );
	}

  public function onlim_load_main_page() {
		ob_start();
		do_action( 'onlim_admin_page_content' );
		$content = ob_get_contents();
		ob_end_clean();
		if ( empty($content) ) {
			?>
			<div class="wrap">
				<div id="wp-onlim-wrapper">
					<h1 class="onlim-title"><?php echo esc_html__( 'Onlim Livechat and Chatbot', 'onlim' ); ?></h1>
					<h3 class="onlim-subtitle">
						<?php echo esc_html__( 'Integrate awesome and free Livechat in Wordpress for everyone.', 'onlim' ); ?>
					</h3>
					<div class="onlim-alert">
						<img class="onlim-alert-img" src="<?php echo onlim()->plugin_url() . '/assets/images/onlim-logo.svg' ?>" />
						<div class="onlim-alert-content">
							<h4><?php echo esc_html__( 'Chatbots & Voice Assistants from Onlim.', 'onlim' ); ?></h4>
							<p>
								<?php echo esc_html__( 'Automate your customer communication with our fully integrated Conversational AI platform. Results from over 150 man-years of research from our core team flow into our solution and allow us to deliver more knowledge and better conversations.', 'onlim' ); ?>
							</p>
							<p>
								<strong>
								<?php
								echo sprintf(
									__( 'Please visit %1$sour website%2$s to get more information!', 'onlim' ),
									'<a href="' . onlim_external_url( 'web' ) . '" target="_blank">',
									'</a>'
								);
								?>
								</strong>
							</p>
						</div>
					</div>
					<h3 class="onlim-getit">
						<?php echo esc_html__( 'How to get an Onlim Livechat and Chatbot?', 'onlim' ); ?>
					</h3>
					<div class="onlim-buttons">
						<div class="onlim-login">
							<p>
								<?php echo esc_html__( 'You have already an Onlim account?', 'onlim' ); ?>
							</p>
							<div>
								<a class="onlim-btn" href="<?php echo onlim_external_url( 'app-login' ); ?>" target="_blank"><?php echo esc_html__( 'Sign in', 'onlim' ); ?></a>
							</div>
						</div>
						<div class="onlim-register">
							<p>
								<?php echo esc_html__( 'You don\'t have an Onlim account?', 'onlim' ); ?>
							</p>
							<div>
								<a class="onlim-btn" href="<?php echo onlim_external_url( 'app-register' ); ?>" target="_blank"><?php echo esc_html__( 'Sign up', 'onlim' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		} else {
			echo $content;
		}
  }

  public function onlim_settings_init() {
  	register_setting( 'onlim_settings', 'onlim_general_settings' );

  	add_settings_section(
  		'onlim_settings_section',
  		__( 'Onlim widget settings', 'onlim' ),
  		array( $this, 'onlim_settings_section_callback' ),
  		'onlim_settings'
  	);

  	add_settings_field(
  		'onlim_settings_widget_code',
  		__( 'Widget code', 'onlim' ),
  		array( $this, 'onlim_settings_widget_code_render' ),
  		'onlim_settings',
  		'onlim_settings_section'
  	);

    add_settings_field(
      'onlim_settings_inclusion',
      __( 'Widget inclusion', 'onlim' ),
      array( $this, 'onlim_settings_inclusion_render' ),
      'onlim_settings',
      'onlim_settings_section'
    );

  	add_settings_field(
  		'onlim_settings_active',
  		__( 'Activate widget', 'onlim' ),
  		array( $this, 'onlim_settings_active_render' ),
  		'onlim_settings',
  		'onlim_settings_section'
  	);
  }

  public function onlim_settings_section_callback() {
		?> <p class="onlim-settings-section-description"> <?php
			echo sprintf(
				__( 'Please configure the widget for your Wordpress website here. The widget code can be copied from Onlim chat management area, after %1$sregister for an account%2$s.', 'onlim' ),
				'<a href="' . admin_url( 'admin.php?page=onlim' ) . '">',
				'</a>'
			);
		?> </p> <?php
  }

  public function onlim_settings_widget_code_render() {
    $options = get_option( 'onlim_general_settings' );
  	?>
  	<textarea name='onlim_general_settings[onlim_settings_widget_code]' rows='6' style='width:100%;'><?php echo isset( $options['onlim_settings_widget_code'] ) ? esc_attr( $options['onlim_settings_widget_code'] ) : ''; ?></textarea>
  	<?php
  }

  public function onlim_settings_inclusion_render() {
    $options = get_option( 'onlim_general_settings' );
		if ( empty( $options['onlim_settings_inclusion'] ) ) $options['onlim_settings_inclusion'] = 'footer';
    ?>
		<fieldset>
			<?php if ( ONLIM_BODY_OPEN ) { ?>
				<label for="onlim-settings-inclusion-header">
					<input type='radio' name='onlim_general_settings[onlim_settings_inclusion]' id="onlim-settings-inclusion-header" <?php checked( $options['onlim_settings_inclusion'], 'header' ); ?> value='header'>
					<?php printf( esc_html__( 'The widget code will be printed below the opening %s section.', 'onlim' ), '<code>&lt;body&gt;</code>'); ?>
				</label><br />
			<?php } ?>
			<label for="onlim-settings-inclusion-footer">
				<input type='radio' name='onlim_general_settings[onlim_settings_inclusion]' id="onlim-settings-inclusion-footer" <?php checked( $options['onlim_settings_inclusion'], 'footer' ); ?> value='footer'>
				<?php printf( esc_html__( 'The widget code will be printed above the closing %s section. (recommended)', 'onlim' ), '<code>&lt;/body&gt;</code>'); ?>
			</label><br />
			<label for="onlim-settings-inclusion-shortcode">
				<input type='radio' name='onlim_general_settings[onlim_settings_inclusion]' id="onlim-settings-inclusion-shortcode" <?php checked( $options['onlim_settings_inclusion'], 'shortcode' ); ?> value='shortcode'>
				<?php printf( esc_html__( 'The widget code will be printed where you include the %s shortcode.', 'onlim' ), '<code>[onlim-widget]</code>'); ?>
			</label>
		</fieldset>
    <?php
  }

  public function onlim_settings_active_render() {
  	$options = get_option( 'onlim_general_settings' );
		if ( !isset( $options['onlim_settings_active'] ) ) $options['onlim_settings_active'] = 0;
  	?>
		<fieldset>
			<label for="onlim-settings-activate">
  			<input type='checkbox' name='onlim_general_settings[onlim_settings_active]' id="onlim-settings-activate" <?php checked( $options['onlim_settings_active'], 1 ); ?> value='1'>
				<?php echo esc_html__( 'Check this, to show the widget on your website.', 'onlim' ); ?>
			</label>
		</fieldset>
  	<?php
  }

  public function onlim_settings_page() {
  	?>
    <div class="wrap">
      <h1><?php echo esc_html__( 'Onlim Settings', 'onlim' ); ?></h1>
      <form action='options.php' method='post'>
        <?php
        settings_fields( 'onlim_settings' );
        do_settings_sections( 'onlim_settings' );
        submit_button();
        ?>
      </form>
    </div>
  	<?php
  }

  /**
   * Enqueue styles.
   */
  public function onlim_admin_styles() {
    $suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		wp_register_style( 'onlim-admin', ONLIM()->plugin_url() . '/assets/css/onlim-admin' . $suffix . '.css', array(), ONLIM_VERSION );
		wp_style_add_data( 'onlim-admin', 'rtl', 'replace' );
		wp_register_style( 'onlim-admin-pages', ONLIM()->plugin_url() . '/assets/css/onlim-admin-pages' . $suffix . '.css', array(), ONLIM_VERSION );
		wp_style_add_data( 'onlim-admin-pages', 'rtl', 'replace' );
		wp_register_style( 'onlim-admin-page-main', ONLIM()->plugin_url() . '/assets/css/onlim-admin-page-main' . $suffix . '.css', array(), ONLIM_VERSION );
		wp_style_add_data( 'onlim-admin-page-main', 'rtl', 'replace' );

		/* Global admin styles. */
		wp_enqueue_style( 'onlim-admin' );

		/* Admin styles for Onlim pages only. */
		if ( in_array( $screen_id, onlim_screen_ids() ) ) {
			wp_enqueue_style( 'onlim-admin-pages' );
		}

    if ( $screen_id == 'toplevel_page_onlim' ) {
			wp_enqueue_style( 'onlim-admin-page-main' );
			do_action( 'onlim_admin_page_styles' );
    }
	}

  /**
	 * Enqueue scripts.
	 */
  public function onlim_admin_scripts() {
    $suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $screen    = get_current_screen();
    $screen_id = $screen ? $screen->id : '';

    wp_register_script( 'onlim-admin', ONLIM()->plugin_url() . '/assets/js/onlim-admin' . $suffix . '.js', array( 'jquery' ), ONLIM_VERSION );
		wp_register_script( 'onlim-admin-pages', ONLIM()->plugin_url() . '/assets/js/onlim-admin-pages' . $suffix . '.js', array( 'jquery' ), ONLIM_VERSION );
		wp_register_script( 'onlim-admin-page-main', ONLIM()->plugin_url() . '/assets/js/onlim-admin-page-main' . $suffix . '.js', array( 'jquery' ), ONLIM_VERSION );

		/* Global admin scripts. */
		wp_enqueue_script( 'onlim-admin' );

    /* Admin styles for Onlim pages only. */
		if ( in_array( $screen_id, onlim_screen_ids() ) ) {
      wp_enqueue_script( 'onlim-admin-pages' );
    }

    if ( $screen_id == 'toplevel_page_onlim' ) {
      wp_enqueue_script( 'onlim-admin-page-main' );
			do_action( 'onlim_admin_page_scripts' );
    }
  }

}

return new Onlim_Admin();
