<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Printful_Admin_Settings {
	
	public static $_instance;
	const CARRIER_TYPE_STANDARD = 'standard';
	const CARRIER_TYPE_EXPEDITED = 'expedited';
	const CARRIER_TYPE_DOMESTIC = 'domestic';
	const CARRIER_TYPE_INTERNATIONAL = 'international';
    const CARRIER_REGION_US = 'US';
    const CARRIER_REGION_EU = 'LV';

    /**
     * @return array
     */
	public static function getIntegrationFields()
    {
        return array(
            'printful_key' => array(
                'title' => __( 'Printful store API key', 'printful' ),
                'type' => 'text',
                'desc_tip' => true,
                'description' => __( 'Your store\'s Printful API key. Create it in the Prinful dashboard', 'printful' ),
                'default' => false,
            ),
            'calculate_tax' => array(
                'title' => __( 'Calculate sales tax', 'printful' ),
                'type' => 'checkbox',
                'label' => __( 'Calculated for all products shipped to North Carolina and California', 'printful' ),
                'default' => 'no',
            ),
            'disable_ssl' => array(
                'title' => __( 'Disable SSL', 'printful' ),
                'type' => 'checkbox',
                'label' => __( 'Use HTTP instead of HTTPS to connect to the Printful API (may be required if the plugin does not work for some hosting configurations)', 'printful' ),
                'default' => 'no',
            ),
        );
    }

	/**
	 * @return Printful_Admin_Settings
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Setup the view
	 */
	public static function view() {

		$settings = self::instance();
		$settings->render();
	}

	/**
	 * Display the view
	 */
	public function render() {

		Printful_Admin::load_template( 'header', array( 'tabs' => Printful_Admin::get_tabs() ) );

		echo '<form method="post" name="printful_settings" action="' . esc_url( admin_url( 'admin-ajax.php?action=save_printful_settings' ) ) . '">';

		//integration settings
		$integration_settings = $this->setup_integration_fields();
		Printful_Admin::load_template( 'setting-group', $integration_settings );

		Printful_Admin::load_template( 'shipping-notification' );

		//carriers settings
		Printful_Admin::load_template( 'ajax-loader', array( 'action' => 'get_printful_carriers', 'message' => 'Loading your carriers...' ) );

		Printful_Admin::load_template( 'setting-submit', array( 'nonce' => wp_create_nonce( 'printful_settings' ), 'disabled' => true ) );

		echo '</from>';

		Printful_Admin::load_template( 'footer' );
	}

    /**
     * Display the ajax content for carrier settings
     * @throws PrintfulException
     */
	public static function render_carriers_ajax() {

		$carrier_settings = self::instance()->setup_carrier_fields();
		Printful_Admin::load_template( 'setting-group', $carrier_settings );
		$enable_submit = 'Printful_Settings.enable_submit_btn();';
		Printful_Admin::load_template( 'inline-script', array( 'script' => $enable_submit ) );

		exit;
	}

	/**
	 * @return mixed
	 * @internal param $integration_settings
	 */
	public function setup_integration_fields() {

		$integration_settings = array(
			'title'       => 'Integration settings',
			'description' => '',
			'settings'    => self::getIntegrationFields(),
		);

		foreach ( $integration_settings['settings'] as $key => $setting ) {
			if ( $setting['type'] !== 'title' ) {
				$integration_settings['settings'][ $key ]['value'] = Printful_Integration::instance()->get_option( $key, $setting['default'] );
			}
		}

		return $integration_settings;
	}

    /**
     * @internal param $carrier_settings
     * @throws PrintfulException
     */
	public function setup_carrier_fields() {

		$carrier_settings = array(
			'title'       => __( 'Carriers & Services', 'printful' ),
			'description' => __( "You can specify here which shipping carriers are available for this store.\n
			 Uncheck the ones you want to disable. If you leave “Flat rate” checked it means that any order where flat rate was selected can still ship with any carrier and service.", 'printful' ),
			'settings'    => array(),
		);

		if ( ! Printful_Integration::instance()->is_connected() ) {
			$carrier_settings['description'] = __( 'You need to be connected to Printful API to edit carrier settings!', 'printful' );

			return $carrier_settings;
		}

        $carrier_regions = Printful_Carriers::instance()->carriers;

        if ( empty( $carrier_regions ) ) {
            return false;
		}

        $carrier_settings[ 'settings' ] = $this->prepare_form_data( $carrier_regions );

		return $carrier_settings;
	}

	/**
	 * Prepare carrier data for posting to Printful API
	 * @return array|bool
	 */
	public function prepare_carriers() {

        $carrier_regions = Printful_Carriers::instance()->carriers;

        if ( empty( $carrier_regions ) ) {
			return false;
		}

        $us_carriers[ self::CARRIER_REGION_US ]  = ( ( ! empty( $_POST[ self::CARRIER_REGION_US ] ) && wp_verify_nonce( $_POST['_wpnonce'], 'printful_settings' ) ) ? $_POST[ self::CARRIER_REGION_US ] : array() );
        $eu_carriers[ self::CARRIER_REGION_EU ] = ( ( ! empty( $_POST[ self::CARRIER_REGION_EU ] ) && wp_verify_nonce( $_POST['_wpnonce'], 'printful_settings' ) ) ? $_POST[ self::CARRIER_REGION_EU ] : array() );

        $parsed_carriers = $this->parse_region_carriers( $us_carriers, $eu_carriers );
        $us_carriers = $parsed_carriers['us_carriers'];
        $eu_carriers = $parsed_carriers['eu_carriers'];

        if ( empty( $us_carriers ) && empty( $eu_carriers ) ) {
			return false;
		}

        $saved_carriers = array_merge( $us_carriers, $eu_carriers );
		$request_body   = array();

        foreach ( $carrier_regions as $region => $carrier_region ) {
            foreach ( $carrier_region as $carrier_type => $carrier_methods ) {
                foreach ( $carrier_methods as $carrier_method => $carrier_data ) {
                    $is_active = false;

                    if ( in_array( $carrier_method, $saved_carriers[ $region ][ $carrier_type ] ) ) {
                        $is_active = true;
                    }

                    $request_body[ $region ][ $carrier_type ][ $carrier_method ] = array(
                        'isActive' => $is_active
                    );
                }
            }
        }

        return $request_body;
	}

    /**
     * Ajax endpoint for saving the settings
     * @throws PrintfulException
     */
	public static function save_printful_settings() {

		if ( ! empty( $_POST ) ) {

			check_admin_referer( 'printful_settings' );
			$error_msg = null;

			//save carriers first, so API key change does not affect this
			if ( Printful_Integration::instance()->is_connected(true) ) {

				//save remote carrier settings
				$request_body = Printful_Admin_Settings::instance()->prepare_carriers();
				$result = Printful_Carriers::instance()->post_carriers( $request_body );

				if ( ! $result ) {
					$error_msg = 'Error: failed to save carriers';
				} else if (isset($result['error'])) {
					$error_msg = $result['error'];
                }
			}

			$options = array();

			//build save options list
			foreach ( self::getIntegrationFields() as $key => $field ) {

				if ( $field['type'] == 'checkbox' ) {
					if ( isset( $_POST[ $key ] ) ) {
						$options[ $key ] = 'yes';
					} else {
						$options[ $key ] = 'no';
					}
				} else {
					if ( isset( $_POST[ $key ] ) ) {
						$options[ $key ] = $_POST[ $key ];
					}
				}
			}

			//save integration settings
			Printful_Integration::instance()->update_settings( $options );

			if ( $error_msg ) {
				die( $error_msg );
			}

			die('OK');
		}
	}

    /**
     * @param array $carrier_regions
     * @return array|bool
     */
    private function prepare_form_data( $carrier_regions )
    {
        $carrier_settings = array();

        foreach ( $carrier_regions as $region => $carrier_types ) {
            foreach ($carrier_types as $carrier_type => $carrier_data) {
                foreach ($carrier_data as $key => $carrier) {
                    $carrier_item = array(
                        'label' => $carrier[ 'label' ] . ' <i>' . $carrier[ 'subtitle' ] . '</i>',
                        'value' => ( $carrier[ 'isActive' ] == true ? 'yes' : 'no' ),
                    );

                    $carrier_regions[ $region ][ $carrier_type ][ $carrier['key'] ] = $carrier_item;
                }
            }
        }

        $item_array = $this->format_carrier_items( $carrier_regions );

        $carrier_settings[ self::CARRIER_REGION_US ] = array(
            'title' => __( 'International from USA', 'printful' ),
            'type'  => 'checkbox-group',
            'items' => $item_array[ self::CARRIER_REGION_US ]
        );

        $carrier_settings[ self::CARRIER_REGION_EU ] = array(
            'title' => __( 'International from EU', 'printful' ),
            'type'  => 'checkbox-group',
            'items' => $item_array[ self::CARRIER_REGION_EU ]
        );

        return $carrier_settings;
    }

    /**
     * Prepare carrier item data for form
     * @param array $carrier_regions
     * @return array
     */
    private function format_carrier_items( $carrier_regions )
    {
        $item_array = array();

        foreach ( $carrier_regions as $region => $carrier_types ) {
            foreach ( $carrier_types as $carrier_type => $carrier_data ) {
                $item_array[ $region ][ $carrier_type ] = array(
                    'subtitle' => ucfirst($carrier_type) . ' shipping',
                    'carriers' => $carrier_data
                );
            }
        }

        return array(
            self::CARRIER_REGION_US => $item_array[ self::CARRIER_REGION_US ],
            self::CARRIER_REGION_EU => $item_array[ self::CARRIER_REGION_EU ]
        );
    }

    /**
     * @param array $us_carriers
     * @param array $eu_carriers
     * @return array
     */
    private function parse_region_carriers( $us_carriers, $eu_carriers )
    {
        foreach ( $us_carriers[ self::CARRIER_REGION_US ] as $carrier_type => $carrier_methods ) {
            foreach ( $carrier_methods as $key => $carrier_method ) {
                $us_carriers[ self::CARRIER_REGION_US ][ $carrier_type ][ $key ] = str_replace( self::CARRIER_REGION_US . '_' . $carrier_type . '_', '', $carrier_method );
            }
        }

        foreach ( $eu_carriers[ self::CARRIER_REGION_EU ] as $carrier_type => $carrier_methods ) {
            foreach ( $carrier_methods as $key => $carrier_method ) {
                $eu_carriers[ self::CARRIER_REGION_EU ][ $carrier_type ][ $key ] = str_replace( self::CARRIER_REGION_EU . '_' . $carrier_type.'_', '', $carrier_method );
            }
        }

        return array(
            'eu_carriers' => $eu_carriers,
            'us_carriers' => $us_carriers
        );
    }
}