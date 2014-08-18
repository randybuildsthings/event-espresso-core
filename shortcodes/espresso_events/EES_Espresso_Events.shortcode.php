<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author			Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link				http://www.eventespresso.com
 * @ version		 	4.0
 *
 * ------------------------------------------------------------------------
 *
 * ESPRESSO_EVENTS
 *
 * @package			Event Espresso
 * @subpackage		/shortcodes/
 * @author			Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
class EES_Espresso_Events  extends EES_Shortcode {

	/**
	 * 	set_hooks - for hooking into EE Core, modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks() {
	}

	/**
	 * 	set_hooks_admin - for hooking into EE Admin Core, modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks_admin() {
	}



	/**
	 *    run - initial module setup
	 *
	 * @access    public
	 * @param WP $WP
	 * @return    void
	 */
	public function run( WP $WP ) {
		if ( ! did_action( 'pre_get_posts' )) {
			// this will trigger the EED_Events_Archive module's event_list() method during the pre_get_posts hook point,
			// this allows us to initialize things, enqueue assets, etc,
			// as well, this saves an instantiation of the module in an array using 'espresso_events' as the key, so that we can retrieve it
			add_action( 'pre_get_posts', array( EED_Events_Archive::instance(), 'event_list' ));
		} else {
			EED_Events_Archive::instance()->event_list();
		}
	}



	/**
	 * 	process_shortcode - ESPRESSO_EVENTS - Returns a list of events
	 * 	[ESPRESSO_EVENTS]
	 * 	[ESPRESSO_EVENTS title="My Super Event"]
	 * 	[ESPRESSO_EVENTS limit=5]
	 * 	[ESPRESSO_EVENTS css_class="my-custom-class"]
	 * 	[ESPRESSO_EVENTS month="April 2014"]
	 * 	[ESPRESSO_EVENTS show_expired=true]
	 * 	[ESPRESSO_EVENTS category_slug="free-events"]
	 * 	[ESPRESSO_EVENTS order_by="start_date,id"]
	 * 	[ESPRESSO_EVENTS sort="ASC"]
	 *
	 *  @access 	public
	 *  @param 	array 	$attributes
	 *  @return 	string
	 */
	public function process_shortcode( $attributes = array() ) {
		$default_espresso_events_shortcode_atts = array(
			'title' => NULL,
			'limit' => 10,
			'css_class' => NULL,
			'show_expired' => FALSE,
			'month' => NULL,
			'category_slug' => NULL,
			'order_by' => 'start_date',
			'sort' => 'ASC'
		);
		// allow the defaults to be filtered
		$default_espresso_events_shortcode_atts = apply_filters( 'EES_Espresso_Events__process_shortcode__default_espresso_events_shortcode_atts', $default_espresso_events_shortcode_atts );
		// grab attributes and merge with defaults, then extract
		$attributes = shortcode_atts( $default_espresso_events_shortcode_atts, $attributes );
		// make sure we use the_excerpt()
		add_filter( 'FHEE__EES_Espresso_Events__process_shortcode__true', '__return_true' );
		// run the query
		global $wp_query;
		$wp_query = new EE_Event_List_Query( $attributes );
		//d( $wp_query );
		$template = 'loop-espresso_events.php';
		// check what template is loaded and load filters accordingly
		EED_Events_Archive::instance()->template_include( $template );
		// load our template
		$event_list = EEH_Template::locate_template( $template, array(), TRUE, TRUE );
		// now reset the query and postdata
		wp_reset_query();
		wp_reset_postdata();
		EED_Events_Archive::remove_all_events_archive_filters();
		// pull our content from the output buffer and return it
		return $event_list;
	}




}
// End of file EES_Espresso_Events.shortcode.php
// Location: /shortcodes/EES_Espresso_Events.shortcode.php