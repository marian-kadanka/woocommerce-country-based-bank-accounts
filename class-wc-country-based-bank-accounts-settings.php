<?php

/**
 * Admin settings in WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Country_Based_Bank_Accounts_Settings {

	private $id;


	public function __construct()	{
		$this->id = 'wccbba';

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 40);

		add_action( 'woocommerce_settings_tabs_' . $this->id, array( $this, 'add_section_to_tab' ) );

		add_action( 'woocommerce_update_options_' . $this->id, array( $this, 'update_options' ) );
	}

	/**
	 * Create settings tab for WooCommerce settings
	 */
	public function add_settings_tab( $tabs ) {
		$tabs[$this->id] = __( 'Country Based Bank Accounts', 'wccbba' );

		return $tabs;
	}

	/**
	 * Create input field for every available bank account
	 *
	 * @return $fields array
	 */
	public function create_fields() {
		$gateways = WC()->payment_gateways->payment_gateways();
		$bacs = $gateways['bacs'];

		$fields = array();

		foreach ( $bacs->account_details as $account ) {
			$fields[] = array(
					'title'   => implode(', ', array_filter( $account ) ),
					'type'    => 'multi_select_countries',
					// TODO no IDs on bank accounts, it's neccessary to use all fields to create a key
					'id'      => $this->id . '_' . md5( serialize( $account ) ),
				);
		}

		return $fields;
	}

	/**
	 * Create section and include input fields in section
	 *
	 * @return array
	 */
	public function create_tab_section() {
		$section = array();

		$section[] = array(
				'title' => __( 'Country Based Bank Accounts', 'wccbba' ),
				'desc'  => __( 'Select in which countries certain bank accounts will be available.', 'wccbba' ),
				'type'  => 'title',
				'id'    => $this->id,
			);

		$section = array_merge( $section, $this->create_fields() );

		$section[] = array( 'type' => 'sectionend', 'id' => $this->id );

		return $section;
	}

	/**
	 * Add section to tab
	 */
	public function add_section_to_tab() {
		woocommerce_admin_fields( $this->create_tab_section() );
	}

	/**
	 *  Update setting fields
	 */
	public function update_options() {
		woocommerce_update_options( $this->create_fields() );
	}
}
