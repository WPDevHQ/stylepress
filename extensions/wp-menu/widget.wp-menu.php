<?php
/**
 * WordPress Nav Menu Widget
 *
 * @package dtbaker-elementor
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}



/**
 * Creates our custom Elementor widget
 *
 * Class Widget_Dtbaker_WP_Menu
 *
 * @package Elementor
 */
class Widget_Dtbaker_WP_Menu extends Widget_Base {


	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dtbaker_wp_menu';
	}

	/**
	 * Get widgets title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'WordPress Menu', 'stylepress' );
	}

	/**
	 * Get the current icon for display on frontend.
	 * The extra 'dtbaker-elementor-widget' class is styled differently in frontend.css
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'dtbaker-stylepress-elementor-widget';
	}

	/**
	 * Get available categories for this widget. Which is our own category for page builder options.
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'dtbaker-elementor' ];
	}

	/**
	 * We always show this item in the panel.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
		return true;
	}

	/**
	 * This registers our controls for the widget. Currently there are none but we may add options down the track.
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_dtbaker_wp_menu',
			[
				'label' => __( 'WordPress Menu', 'stylepress' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => sprintf( __( 'Choose the WordPress menu to output below. To change menu items please go to the <a href="%s" target="_blank">WordPress Menu Editor</a> page.', 'stylepress' ), admin_url( 'nav-menus.php' ) ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		if ( false && ! function_exists( 'max_mega_menu_is_enabled' ) ) {

			$this->add_control(
				'megamenu',
				[
					// Translators: %s is the URL for MegaMenu plugin
					'label' => sprintf( __( 'We recommend installing the <a href="%s" target="_blank">Max Mega Menu</a> plugin to get an awesome menu layout.', 'stylepress' ), 'https://wordpress.org/plugins/megamenu/' ),
					'type' => Controls_Manager::RAW_HTML,
				]
			);

		}

		$menu_select = array(
			'' => esc_html__( ' - choose - ', 'stylepress' ),
		);

		/*if ( function_exists( 'max_mega_menu_is_enabled' ) ) {
			$menus = get_registered_nav_menus();
			foreach ( $menus as $location => $description ) {
				$menu_select[ $location ] = $description;
			}
		}*/
		// we also show a list of users menues.
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ){
		    $menu_select[$menu->term_id] = $menu->name;
        }


		$this->add_control(
			'menu_location',
			[
				'label'   => esc_html__( 'Choose Menu', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $menu_select,
			]
		);

		/*
        if(function_exists('max_mega_menu_is_enabled') && class_exists('Mega_Menu_Style_Manager')){

            $style_manager = new \Mega_Menu_Style_Manager();
            $themes = $style_manager->get_themes();

            $menu_styles = array(
                '' => esc_html__( 'Default' ),
            );
            foreach($themes as $theme_id => $theme){
                $menu_styles[$theme_id] = $theme['title'];
            }

            $this->add_control(
                'menu_style',
                [
                    'label' => __( 'Menu Menu Style', 'elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $menu_styles,
                ]
            );

        }*/

		$this->end_controls_section();

		$this->start_controls_section(
			'dtbaker_menu_logo',
			[
				'label' => __( 'Logo Image', 'elementor' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Actually its `image_size`
				'label' => __( 'Image Size', 'elementor' ),
				'default' => 'large',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_to',
			[
				'label' => __( 'Link to', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'elementor' ),
					'file' => __( 'Media File', 'elementor' ),
					'custom' => __( 'Custom URL', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link to', 'elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', 'elementor' ),
				'condition' => [
					'link_to' => 'custom',
				],
				'show_label' => false,
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon List', 'elementor' ),
			]
		);

		$this->add_control(
			'icon_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'text' => __( 'List Item #1', 'elementor' ),
						'icon' => 'fa fa-check',
					],
					[
						'text' => __( 'List Item #2', 'elementor' ),
						'icon' => 'fa fa-times',
					],
					[
						'text' => __( 'List Item #3', 'elementor' ),
						'icon' => 'fa fa-dot-circle-o',
					],
				],
				'fields' => [
					[
						'name' => 'text',
						'label' => __( 'Text', 'elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'placeholder' => __( 'List Item', 'elementor' ),
						'default' => __( 'List Item', 'elementor' ),
					],
					[
						'name' => 'icon',
						'label' => __( 'Icon', 'elementor' ),
						'type' => Controls_Manager::ICON,
						'label_block' => true,
						'default' => 'fa fa-check',
					],
					[
						'name' => 'link',
						'label' => __( 'Link', 'elementor' ),
						'type' => Controls_Manager::URL,
						'label_block' => true,
						'placeholder' => __( 'http://your-link.com', 'elementor' ),
					],
				],
				'title_field' => '<i class="{{ icon }}"></i> {{{ text }}}',
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_list',
			[
				'label' => __( 'List', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'space_between',
			[
				'label' => __( 'Space Between', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_responsive_control(
			'icon_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'prefix_class' => 'elementor-align-',
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item, {{WRAPPER}} .stylepress-menu-icons-item a' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'right' => 'flex-end',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label' => __( 'Divider', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'elementor' ),
				'label_on' => __( 'On', 'elementor' ),
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:last-child):after' => 'content: ""',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => __( 'Style', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => __( 'Solid', 'elementor' ),
					'double' => __( 'Double', 'elementor' ),
					'dotted' => __( 'Dotted', 'elementor' ),
					'dashed' => __( 'Dashed', 'elementor' ),
				],
				'default' => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:last-child):after' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label' => __( 'Weight', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:last-child):after' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label' => __( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'units' => [ '%' ],
				'default' => [
					'unit' => '%',
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-item:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-icon i' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-menu-icons-icon' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .stylepress-menu-icons-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stylepress_menu_style',
			[
				'label' => __( 'Menu Style', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'menu_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'prefix_class' => 'elementor-align-',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_background',
			[
				'label' => __( 'Background', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f8f8f8',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation, {{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul ul' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'menu_background_hover',
			[
				'label' => __( 'Background (hover)', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#eaeaea',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul li:hover a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'font_color',
			[
				'label' => __( 'Font Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation .stylepress-menu-toggle, {{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'font_color_hover',
			[
				'label' => __( 'Font Color (Hover)', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul li a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();


		do_action( 'dtbaker_wp_menu_elementor_controls', $this );

	}

	/**
	 * Render our custom menu onto the page.
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( ! empty( $settings['menu_location'] ) ) {

		    ?> 
            <div class="stylepress-nav-menu">
            <?php
			/*
            if(function_exists('max_mega_menu_is_enabled') && !empty($settings['menu_style'])) {

                // $menu_styles
                add_filter('option_megamenu_settings', function ($value, $option) use ($settings) {

                    if($value && !empty($value[$settings['menu_location']])){
                        $value[$settings['menu_location']]['theme'] = $settings['menu_style'];
                    }

                    return $value;
                }, 10, 2);
            }*/

			// if the menu is a "location" then we

			if ( false && function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled($settings['menu_location']) ){
				wp_nav_menu( array( 'theme_location' => $settings['menu_location'] ) );
            }else{
			    ob_start();

			    if ( ! empty( $settings['image']['url'] ) ) {
			        ?>
                    <div class="stylepress-nav-logo">
                    <?php
		            $link = $this->get_link_url( $settings );

                    if ( $link ) {
                        $this->add_render_attribute( 'link', 'href', $link['url'] );

                        if ( ! empty( $link['is_external'] ) ) {
                            $this->add_render_attribute( 'link', 'target', '_blank' );
                        }
                    }
                    if ( $link ) : ?>
                            <a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
                    <?php endif;
		            echo Group_Control_Image_Size::get_attachment_image_html( $settings );

		            if ( $link ) : ?>
                        </a>
                    <?php endif;
                    ?>
                    </div>
                <?php } ?>
                <nav itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" class="stylepress-main-navigation">
                    <button class="stylepress-menu-toggle" aria-controls="<?php echo $this->get_id();?>-menu" aria-expanded="false">
                        <span class="stylepress-mobile-menu"><?php esc_html_e('Menu','stylepress');?></span>
                    </button>
                    <div id="<?php echo $this->get_id();?>-menu" class="stylepress-inside-navigation">
						<?php

                        if(is_numeric($settings['menu_location'])){
	                        $nav_menu = wp_get_nav_menu_object( $settings['menu_location'] );
	                        if ( $nav_menu ){
		                        wp_nav_menu( array(
			                        'menu'        => $nav_menu,
			                        'fallback_cb' => '',
			                        'container'       => 'div',
			                        'container_class' => 'main-nav stylepress_menu',
			                        'container_id'    => 'primary-menu',
			                        'menu_class'      => '',
			                        'items_wrap'      => '<ul id="%1$s" class="%2$s ' . '">%3$s</ul>',
			                        'walker' => new \stylepress_walker_nav_menu()
		                        ) );
                            }else{
	                            echo "Menu Configuration Issue";
                            }
                        }else {
	                        wp_nav_menu(
		                        array(
			                        'theme_location'  => $settings['menu_location'],
			                        'container'       => 'div',
			                        'container_class' => 'main-nav stylepress_menu',
			                        'container_id'    => 'primary-menu',
			                        'menu_class'      => '',
			                        'items_wrap'      => '<ul id="%1$s" class="%2$s ' . '">%3$s</ul>',
			                        'walker' => new \stylepress_walker_nav_menu(),
		                        )
	                        );
                        }
						?>
                    </div><!-- .inside-navigation -->
                </nav><!-- #site-navigation -->

                <ul class="stylepress-menu-icons-items">
					<?php foreach ( $settings['icon_list'] as $item ) : ?>
                        <li class="stylepress-menu-icons-item" >
							<?php
							if ( ! empty( $item['link']['url'] ) ) {
								$target = $item['link']['is_external'] ? ' target="_blank"' : '';

								echo '<a href="' . $item['link']['url'] . '"' . $target . '>';
							}

							if ( $item['icon'] ) : ?>
                                <span class="stylepress-menu-icons-icon">
							<i class="<?php echo esc_attr( $item['icon'] ); ?>"></i>
						</span>
							<?php endif; ?>
                            <span class="stylepress-menu-icons-text"><?php echo $item['text']; ?></span>
							<?php
							if ( ! empty( $item['link']['url'] ) ) {
								echo '</a>';
							}
							?>
                        </li>
						<?php
					endforeach; ?>
                </ul>
                <?php
                echo apply_filters('stylepress_menu_output', ob_get_clean(), $settings['menu_location'], $settings );
            }
            ?>
            </div>
            <?php

		} else {
			$this->content_template();
		}

	}

	private function get_link_url( $instance ) {
		if ( 'none' === $instance['link_to'] ) {
			return false;
		}

		if ( 'custom' === $instance['link_to'] ) {
			if ( empty( $instance['link']['url'] ) ) {
				return false;
			}
			return $instance['link'];
		}

		return [
			'url' => $instance['image']['url'],
		];
	}

	/**
	 * This is outputted while rending the page.
	 */
	protected function content_template() {
		?>
		<div class="dtbaker-wp-menu-content-area">
		WordPress Menu Will Appear Here
		</div>
		<?php
	}

}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Dtbaker_WP_Menu() );