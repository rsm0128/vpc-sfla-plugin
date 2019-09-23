<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://orionorigin.com
 * @since      1.0.0
 *
 * @package    Vpc_Sfla
 * @subpackage Vpc_Sfla/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vpc_Sfla
 * @subpackage Vpc_Sfla/public
 * @author     Orion <help@orionorigin.com>
 */
class Vpc_Sfla_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vpc-sfla-public.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'vpc-sfla-popup-css', plugin_dir_url( __FILE__ ) . 'css/popup.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vpc-sfla-public.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'vpc-sfla-modal-js', plugin_dir_url( __FILE__ ) . 'js/jquery.popup.js', array( 'jquery' ), $this->version, false );

				wp_localize_script(
					$this->plugin_name,
					'myPluginVars',
					array(
						'pluginUrl' => plugin_dir_url( __FILE__ ) . 'img/',
					)
				);

	}

	function get_vpc_sfla_buttons( $buttons, $prod_id ) {
		$config = $this->get_vpc_sfla_config_data( $prod_id );
		if ( isset( $config['save-for-later'] ) && $config['save-for-later'] == 'Yes' ) {
			$save_btn = array(
				'id'         => 'vpc-save-btn',
				'label'      => __( 'Save for later', 'vpc-sfla' ),
				'class'      => '',
				'attributes' => array(
					'data-pid' => $prod_id,
				),
			);
			array_push( $buttons, $save_btn );
		}
		return $buttons;
	}


	private function get_vpc_sfla_config_data( $prod_id ) {
		$ids         = get_product_root_and_variations_ids( $prod_id );
		$config_meta = get_post_meta( $ids['product-id'], 'vpc-config', true );
		$configs     = get_proper_value( $config_meta, $prod_id, array() );
		$config_id   = get_proper_value( $configs, 'config-id', false );
		$config      = get_post_meta( $config_id, 'vpc-config', true );
		return $config;
	}

	function save_for_later_ajax() {
		$prod_id     = $_POST['pid'];
		$recap       = $_POST['recap'];
		$config_name = $_POST['config_name'];
		$metas       = array(
			'prod_id'     => $prod_id,
			'recap'       => $recap,
			'config_name' => $config_name,
		);
		echo $this->save_user_metas( $metas );
		die();
	}

	function save_user_metas( $metas, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$user_metas = get_user_meta( $user_id, 'user_configs', true );
		$id         = uniqid();
		if ( is_array( $user_metas ) ) {
			$user_metas[ $id ] = $metas;
		} else {
			$user_metas        = array();
			$user_metas[ $id ] = $metas;
		}
		update_user_meta( $user_id, 'user_configs', $user_metas );
		return $id;
	}

	function add_vpc_sfla_var( $datas ) {
		$datas['log']              = is_user_logged_in();
		$datas['login_page']       = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
		$datas['success_msg']      = __( 'Product successfully saved to your account', 'vpc-sfla' );
		$datas['modal_title']      = __( 'Your configurator name', 'vpc-sfla' );
		$datas['send_label']       = __( 'SEND', 'vpc-sfla' );
		$datas['placeholder_name'] = __( 'Name', 'vpc-sfla' );

		return $datas;
	}

	function vpc_sfla_query_vars( $vars ) {
		$vars[] = 'save_design';
		$vars[] = 'edit_config';
		return $vars;
	}

	function vpc_sfla_login_redirect( $redirect_to, $user ) {
		if ( isset( $_SESSION['user_config'] ) ) {
			$this->save_user_metas( $_SESSION['user_config'], $user->ID );
			$config_url = vpc_get_configuration_url( $_SESSION['user_config']['prod_id'] );
			unset( $_SESSION['user_config'] );
			$redirect_to = $config_url;
		}
		return $redirect_to;
	}

	function vpc_sfla_registration_redirect( $redirect_to ) {
		$user_id = get_current_user_id();
		if ( isset( $_SESSION['user_config'] ) ) {
			$this->save_user_metas( $_SESSION['user_config'], $user_id );
			$config_url = vpc_get_configuration_url( $_SESSION['user_config']['prod_id'] );
			unset( $_SESSION['user_config'] );
			$redirect_to = $config_url;
		}
		return $redirect_to;
	}

	function save_in_cookies_ajax() {
		$prod_id                 = $_POST['pid'];
		$recap                   = $_POST['recap'];
		$_SESSION['user_config'] = array(
			'prod_id'     => $prod_id,
			'recap'       => $recap,
			'config_name' => $_POST['config_name'],
		);
		die();
	}
	function vpc_sfla_init_sessions() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	function add_after_component( $config, $prod_id ) {
		if ( is_user_logged_in() && isset( $config['save-for-later'] ) && $config['save-for-later'] == 'Yes' ) {
			$metas = get_user_meta( get_current_user_id(), 'user_configs', true );
			if ( is_array( $metas ) ) {
					$metas = array_filter(
						$metas,
						function( $ar ) use ( $prod_id ) {
							return ( $ar['prod_id'] === $prod_id );
						}
					);

				if ( sizeof( $metas ) > 0 ) {

					?><!--fermeture php 1-->
				</div>
			</div>
							<div class="o-col xl-1-3 lg-1-3 md-1-1 sm-1-1 saved_panel">
								<div class='title'><?php esc_html_e( 'My Saved', 'vpc-sfla' ); ?></div>
								<div>

							<?php
							// ouverture php
							foreach ( $metas as $id => $meta ) {
									$config_url = vpc_get_configuration_url( $meta['prod_id'] );
								if ( get_option( 'permalink_structure' ) ) {
									$edit_url = $config_url . "?edit_config=$id";
								} else {
									$edit_url = $config_url . "&edit_config=$id";
								}
									$config_name = $meta['config_name'];
								if ( empty( $config_name ) ) {
									$config_name = $id;
								}
									echo '<div class="saved_bloc"><a class="save_later" href="' . $edit_url . '">' . $config_name . '</a><span id="delete_saved" data-id=' . $id . '>x</span></div>';
							}
				}
			}
		}
	}

	public function load_vpc_sfla_config_data( $datas ) {
		global $wp_query;
		$metas = get_user_meta( get_current_user_id(), 'user_configs', true );
		if ( isset( $wp_query->query_vars['edit_config'] ) ) {
			$config_id = $wp_query->query_vars['edit_config'];
			if ( isset( $metas[ $config_id ]['recap'] ) ) {
				$datas = $metas[ $config_id ]['recap'];
			}
		}
		return $datas;
	}

	public function delete_config_ajax() {
		$id    = $_POST['id'];
		$metas = get_user_meta( get_current_user_id(), 'user_configs', true );
		unset( $metas[ $id ] );
		update_user_meta( get_current_user_id(), 'user_configs', $metas );
		die();
	}

	public function add_saved_design_menu( $menu_links ) {

		$menu = array( 'config-saved' => 'My Saved Configurations' );

		$menu_links = array_slice( $menu_links, 0, -1, true ) + $menu + array_slice( $menu_links, -1, null, true );

		return $menu_links;
	}

	public function add_saved_design() {
		if ( class_exists( 'Vpc_Sfla' ) ) {
			$metas = get_user_meta( get_current_user_id(), 'user_configs', true );
			if ( ( is_array( $metas ) ) && ( sizeof( $metas ) > 0 ) ) {
				?>
						<div class="o-col xl-1-3 lg-1-3 md-1-1 sm-1-1 saved_panel">
							<div class='title'><?php esc_html_e( 'My Saved Configurations', 'vpc-sfla' ); ?></div>
						<div>
						<?php
						$metas = $this->filter_saved_config_arr($metas);
						foreach ( $metas as $prod_id => $meta ) {
								?>
							<div class='product_name'><?php esc_html_e( get_the_title( $prod_id ), 'vpc-sfla' ); ?>
							</div>
								<?php
								$config_url = vpc_get_configuration_url( $prod_id );
							foreach($meta as $id => $config_name){
							if ( get_option( 'permalink_structure' ) ) {
								$edit_url = $config_url . "?edit_config=$id";
							} else {
								$edit_url = $config_url . "&edit_config=$id";
							}
							if ( empty( $config_name ) ) {
								$config_name = $id;
							}
								echo '<div class="saved_bloc"><a class="save_later" href="' . $edit_url . '">' . $config_name . '</a><span id="delete_saved" data-id=' . $id . '>x</span></div>';
						}
				}
			}
			?>
				</div>
			</div>
			<?php
		}
	}

	public function add_saved_design_endpoint() {
		add_rewrite_endpoint( 'config-saved', EP_PAGES );
		flush_rewrite_rules();
	}

	public function filter_saved_config_arr($array){
		$new_metas = [];
		$filtered_arr = [];
		foreach($array as $id => $meta){
				$filtered_arr = array( $id => $meta['config_name']);
				if(isset($new_metas[$meta['prod_id']])){
					$new_metas[$meta['prod_id']][$id] = $meta['config_name'];
				}else{
					$new_metas[$meta['prod_id']] = $filtered_arr;
				}
		}
		return $new_metas;
	}
}
