<?php

namespace cookiebot_addons_framework\lib;

class Cookiebot_Cookie_Consent {

	/**
	 * Array of cookiebot consent states
	 *
	 * It can have 4 items:
	 * - necessary
	 * - preferences
	 * - statistics
	 * - marketing
	 *
	 * @var array  consent state
	 *
	 * @since 1.2.0
	 */
	private $states = array();

	/**
	 * @var   Cookiebot_Cookie_Consent The single instance of the class
	 *
	 * @since 1.2.0
	 */
	protected static $_instance = null;

	/**
	 * Returns instance of this class
	 *
	 * @return Cookiebot_Cookie_Consent
	 *
	 * @since 1.2.0
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Scan cookiebot cookie
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		$this->scan_cookie();
	}

	/**
	 * Scans cookiebot consent cookie and fills in $states with accepted consents.
	 *
	 * @since 1.2.0
	 */
	private function scan_cookie() {
		//default - set strictly necessary cookies
		$this->add_state( 'necessary' );

		if ( isset( $_COOKIE["CookieConsent"] ) ) {
			switch ( $_COOKIE["CookieConsent"] ) {
				case "0":
					//The user has not accepted cookies - set strictly necessary cookies only
					break;

				case "-1":
					//The user is not within a region that requires consent - all cookies are accepted
					$this->add_state( 'preferences' );
					$this->add_state( 'statistics' );
					$this->add_state( 'marketing' );
					break;

				default: //The user has accepted one or more type of cookies

					//Read current user consent in encoded JavaScript format
					$valid_php_json = preg_replace( '/\s*:\s*([a-zA-Z0-9_]+?)([}\[,])/', ':"$1"$2', preg_replace( '/([{\[,])\s*([a-zA-Z0-9_]+?):/', '$1"$2":', str_replace( "'", '"', stripslashes( $_COOKIE["CookieConsent"] ) ) ) );
					$CookieConsent  = json_decode( $valid_php_json );

					if ( filter_var( $CookieConsent->preferences, FILTER_VALIDATE_BOOLEAN ) ) {
						//Current user accepts preference cookies
						$this->add_state( 'preferences' );
					} else {
						//Current user does NOT accept preference cookies
					}

					if ( filter_var( $CookieConsent->statistics, FILTER_VALIDATE_BOOLEAN ) ) {
						//Current user accepts statistics cookies
						$this->add_state( 'statistics' );
					} else {
						//Current user does NOT accept statistics cookies
					}

					if ( filter_var( $CookieConsent->marketing, FILTER_VALIDATE_BOOLEAN ) ) {
						//Current user accepts marketing cookies
						$this->add_state( 'marketing' );
					} else {
						//Current user does NOT accept marketing cookies
					}
			}
		} else {
			//The user has not accepted cookies - set strictly necessary cookies only
		}
	}

	/**
	 * Adds state to $states variable
	 *
	 * @param $state    string  new state
	 *
	 * @since 1.2.0
	 */
	private function add_state( $state ) {
		if ( ! in_array( $state, $this->states ) ) {
			$this->states[] = $state;
		}
	}

	/**
	 * Returns cookiebot cookie consent state
	 *
	 * @return array    array   List of accepted cookiebot consents
	 *
	 * @since 1.2.0
	 */
	public function get_cookie_states() {
		if ( count( $this->states ) == 0 ) {
			$this->scan_cookie();
		}

		return $this->states;
	}

	/**
	 * Checks if the cookie state is accepted
	 *
	 * @param $state    string  Cookie state to check if it is accepted
	 *
	 * @return bool
	 */
	public function is_cookie_state_accepted( $state ) {
		return in_array( $state, $this->states );
	}
}