<?php
/**
 * Registration Form Elementor custom widget.
 *
 * @link       https://themeisle.com
 * @since      1.0.0
 *
 * @package    ThemeIsle\ContentForms
 */

namespace ThemeIsle\ContentForms;

use Elementor\Controls_Manager;
use Exception;

/**
 * Class Elementor_Registration_Widget
 * @package ThemeIsle\ContentForms
 */
class Elementor_Registration_Widget extends ElementorWidget {

	/**
	 * Elementor_Registration_Widget constructor.
	 *
	 * @param array $data
	 * @param null $args
	 *
	 * @throws Exception
	 */
	public function __construct( $data = [], $args = null ) {
		parent::setup_attributes();
		try {
			parent::__construct( $data, $args );
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @return string Widget name.
	 * @since 1.0.1
	 * @access public
	 *
	 */
	public function get_name() {
		return 'content_form_registration';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.1
	 * @access public
	 *
	 */
	public function get_title() {
		return esc_html__( 'Registration Form', 'themeisle-companion' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-text-align-left';
	}

	/**
	 * Set form type.
	 *
	 * @return void
	 * @since 1.0.1
	 * @access protected
	 */
	function set_form_type() {
		$this->form_type = 'registration';
	}

	/**
	 * Set form configuration.
	 *
	 * @return void
	 * @since 1.0.1
	 * @access protected
	 */
	protected function set_form_configuration() {
		$this->forms_config = array(
			'fields' => array(
				'username' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'User Name', 'themeisle-companion' ),
					'default'     => esc_html__( 'User Name', 'themeisle-companion' ),
					'placeholder' => esc_html__( 'User Name', 'themeisle-companion' ),
					'require'     => 'required',
					'validation'  => ''// name a function which should allow only letters and numbers
				),
				'email'    => array(
					'type'        => 'email',
					'label'       => esc_html__( 'Email', 'themeisle-companion' ),
					'default'     => esc_html__( 'Email', 'themeisle-companion' ),
					'placeholder' => esc_html__( 'Email', 'themeisle-companion' ),
					'require'     => 'required'
				),
				'password' => array(
					'type'        => 'password',
					'label'       => esc_html__( 'Password', 'themeisle-companion' ),
					'default'     => esc_html__( 'Password', 'themeisle-companion' ),
					'placeholder' => esc_html__( 'Password', 'themeisle-companion' ),
					'require'     => 'required'
				)
			),

			'controls' => array(
				'submit_label' => array(
					'type'        => 'text',
					'label'       => esc_html__( 'Submit', 'themeisle-companion' ),
					'default'     => esc_html__( 'Register', 'themeisle-companion' ),
					'description' => esc_html__( 'The Call To Action label', 'themeisle-companion' )
				)
			)
		);
	}

	/**
	 * Add widget specific controls.
	 *
	 * @return bool|void
	 * @since 1.0.1
	 * @access protected
	 */
	protected function add_widget_specific_controls() {
		$this->add_submit_button_align();
	}

	/**
	 * Add alignment control for button
	 *
	 * @access private
	 * @return void
	 * @since 1.0.1
	 */
	private function add_submit_button_align() {
		$this->add_responsive_control(
			'align_submit',
			[
				'label'     => __( 'Alignment', 'elementor-addon-widgets', 'themeisle-companion' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'default'   => 'left',
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'elementor-addon-widgets', 'themeisle-companion' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor-addon-widgets', 'themeisle-companion' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'elementor-addon-widgets', 'themeisle-companion' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content-form .submit-form' => 'text-align: {{VALUE}};',
				],
			]
		);
	}
}
