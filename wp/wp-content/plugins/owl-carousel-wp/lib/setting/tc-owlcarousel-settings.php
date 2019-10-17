<?php
/**
 * WordPress settings API demo class
 *
 *
 */
if ( !class_exists('TC_Owlcarousel_Settings_API_Test' ) ):
class TC_Owlcarousel_Settings_API_Test {

    private $settings_api;

    function __construct() {
        $this->settings_api = new TC_Owlcarousel_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'sub_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'edit.php?post_type=ocw_carousel', 'Settings API', 'delete_posts', 'settings_api_test', array($this, 'plugin_page') );
    }

     function sub_menu()
    {
      add_submenu_page( 'edit.php?post_type=ocw_carousel','Carousel Settings','Carousel Settings', 'manage_options','carousel-settings',array($this, 'plugin_page'));
    }

    function my_custom_submenu_page_callback() {

    	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    		echo '<h2>My Custom Submenu Page</h2>';
    	echo '</div>';

    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'tc-owlcarousel_basics',
                'title' => __( 'Basic Settings', 'owl-carousel-wp' )
            ),
            array(
                'id' => 'tc-owlcarousel_advanced',
                'title' => __( 'Advanced Settings', 'owl-carousel-wp' )
            ),
            array(
                'id' => 'tc-owlcarousel_others',
                'title' => __( 'General Styling', 'owl-carousel-wp' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'tc-owlcarousel_basics' => array(

              array(
                  'name'    => 'auto-play',
                  'label'   => __( 'Auto Play', 'owl-carousel-wp' ),
                  'desc'    => __( 'By default  Auto Play is active.', 'owl-carousel-wp' ),
                  'type'    => 'select',
                  'default' => 'true',
                  'options' => array(
                      'true' => 'Yes',
                      'false'  => 'No'
                  )
              ),
              array(
                  'name'    => 'auto_play_timeout',
                  'label'   => __( 'Auto Play Timeout', 'owl-carousel-wp' ),
                  'desc'    => __( 'Set autoplay Timeout', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 1000,
                  'sanitize_callback' => 'intval'
              ),
            array(
                  'name'    => 'auto_play_speed',
                  'label'   => __( 'Auto Play Speed', 'owl-carousel-wp' ),
                  'desc'    => __( 'Set autoplay Speed', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 3000,
                  'sanitize_callback' => 'intval'
              ),

          array(
                  'name'    => 'nav_speed',
                  'label'   => __( 'Navigation Speed', 'owl-carousel-wp' ),
                  'desc'    => __( 'Navigation Speed', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 3000,
                  'sanitize_callback' => 'intval'
              ),
             array(
                  'name'    => 'pagination_speed',
                  'label'   => __( 'Pagination Speed', 'owl-carousel-wp' ),
                  'desc'    => __( 'Pagination/Dots Speed', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 3000,
                  'sanitize_callback' => 'intval'
              ),
              array(
                  'name'    => 'stop-onhover',
                  'label'   => __( 'Stop On Hover', 'owl-carousel-wp' ),
                  'desc'    => __( 'By default  Stop On Hover is active.', 'owl-carousel-wp' ),
                  'type'    => 'select',
                  'default' => 'true',
                  'options' => array(
                      'true' => 'Yes',
                      'false'  => 'No'
                  )
              ),
              array(
                  'name'    => 'loop',
                  'label'   => __( 'Carousel Loop', 'owl-carousel-wp' ),
                  'desc'    => __( 'By default Loop is active.', 'owl-carousel-wp' ),
                  'type'    => 'select',
                  'default' => 'true',
                  'options' => array(
                      'true' => 'Yes',
                      'false'  => 'No'
                  )
              ),
             array(
                  'name'              => 'slideBy',
                  'label'             => __( 'slideBy Items Number', 'owl-carousel-wp' ),
                  'desc'              => __( 'Number of Items Slide once', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 1,
                  'sanitize_callback' => 'intval'
              ),


              array(
                  'name'              => 'medium-desktops',
                  'label'             => __( 'Items Number ( Desktop )', 'owl-carousel-wp' ),
                  'desc'              => __( 'Any Numaric value. 4 is recomended', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 4,
                  'sanitize_callback' => 'intval'
              ),

              array(
                  'name'              => 'items-tablet-val',
                  'label'             => __( 'Items Number ( Tablet )', 'owl-carousel-wp' ),
                  'desc'              => __( 'Any Numaric value. 2 is recomended', 'owl-carousel-wp' ),
                  'type'              => 'text',
                  'default'           => 2,
                  'sanitize_callback' => 'intval'
              )


            ),
            'tc-owlcarousel_advanced' => array(

                array(
                    'name'    => 'nav-val',
                    'label'   => __( 'Navigation ', 'owl-carousel-wp' ),
                    'desc'    => __( 'DroEnable/Disable Navigation', 'owl-carousel-wp' ),
                    'type'    => 'select',
                    'default' => 'true',
                    'options' => array(
                        'true' => 'Yes',
                        'false'  => 'No'
                    )
                ),
                array(
                    'name'    => 'dots-val',
                    'label'   => __( 'Dots ', 'owl-carousel-wp' ),
                    'desc'    => __( 'Enable/Disable Dots', 'owl-carousel-wp' ),
                    'type'    => 'select',
                    'default' => 'true',
                    'options' => array(
                        'true' => 'Yes',
                        'false'  => 'No'
                    )
                ),
                array(
                    'name'    => 'autoheight',
                    'label'   => __( 'Auto Height', 'owl-carousel-wp' ),
                    'desc'    => __( 'Enable/Disable Auto Height', 'owl-carousel-wp' ),
                    'type'    => 'select',
                    'default' => 'false',
                    'options' => array(
                        'true' => 'Yes',
                        'false'  => 'No'
                    )
                ),
                array(
                    'name'    => 'auto-width',
                    'label'   => __( 'Auto Width', 'owl-carousel-wp' ),
                    'desc'    => __( 'Image width will be automatic', 'owl-carousel-wp' ),
                    'type'    => 'select',
                    'default' => 'false',
                    'options' => array(
                        'true' => 'Yes',
                        'false'  => 'No'
                    )
                ),
                array(
                    'name'    => 'center',
                    'label'   => __( 'Center Images', 'owl-carousel-wp' ),
                    'desc'    => __( 'Center the carousel Image.', 'owl-carousel-wp' ),
                    'type'    => 'select',
                    'default' => 'false',
                    'options' => array(
                        'true' => 'Yes',
                        'false'  => 'No'
                    )
                ),
                array(
                    'name'    => 'rtl-val',
                    'label'   => __( 'Right To Left', 'owl-carousel-wp' ),
                    'desc'    => __( 'Right To Left', 'owl-carousel-wp' ),
                    'type'    => 'select',
                    'default' => 'false',
                    'options' => array(
                        'true' => 'Yes',
                        'false'  => 'No'
                    )
                ),

                array(
                    'name'              => 'stage-padding',
                    'label'             => __( 'Stage Padding', 'owl-carousel-wp' ),
                    'desc'              => __( 'Any Numaric value. 2 is recomended', 'owl-carousel-wp' ),
                    'type'              => 'text',
                    'default'           => 0,
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'margin-val',
                    'label'             => __( 'Margin', 'owl-carousel-wp' ),
                    'desc'              => __( 'Any Numaric value.', 'owl-carousel-wp' ),
                    'type'              => 'text',
                    'default'           => 5,
                    'sanitize_callback' => 'intval'
                )

            ),
            'tc-owlcarousel_others' => array(

              array(
                  'name'    => 'navigation-color',
                  'label'   => __( 'Navigation Color', 'owl-carousel-wp' ),
                  'desc'    => __( 'navigation Button Color', 'owl-carousel-wp' ),
                  'type'    => 'color',
                  'default' => '#282830'
              ),
              array(
                  'name'    => 'navigation-hover-color',
                  'label'   => __( 'Navigation Hover Color', 'owl-carousel-wp' ),
                  'desc'    => __( 'Navigation Hover Color', 'owl-carousel-wp' ),
                  'type'    => 'color',
                  'default' => '#60646D'
              ),
              array(
                  'name'    => 'dots-color',
                  'label'   => __( 'Dots Color', 'owl-carousel-wp' ),
                  'desc'    => __( 'Dots Button Color', 'owl-carousel-wp' ),
                  'type'    => 'color',
                  'default' => '#000000'
              ),
              array(
                  'name'    => 'dots-hover-color',
                  'label'   => __( 'Dots Hover Color', 'owl-carousel-wp' ),
                  'desc'    => __( 'Dots Hover Color', 'owl-carousel-wp' ),
                  'type'    => 'color',
                  'default' => '#343434'
              )
            )

        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap-setting-carousel">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;
