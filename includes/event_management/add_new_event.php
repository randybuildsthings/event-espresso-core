<?php
if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

function add_new_event() {
	global $wpdb, $org_options, $espresso_premium, $current_user;
	get_currentuserinfo();
	do_action('action_hook_espresso_log', __FILE__, __FUNCTION__, '');
	$event = new stdClass();
	$event->id = 0;
	$event->event_name = '';
	$event->event_desc = '';
	$event->phone = '';
	$event->externalURL = '';
	$event->early_disc = '';
	$event->early_disc_date = '';
	$event->early_disc_percentage = '';
	$event->event_identifier = '';
	$event->start_date = '';
	$event->end_date = '';
	$event->start_time = '';
	$event->end_time = '';
	$event->registration_start = '';
	$event->registration_start = '';
	$event->status = array('display' => 'OPEN');
	$event->address = '';
	$event->address2 = '';
	$event->city = '';
	$event->state = '';
	$event->zip = '';
	$event->country = '';
	$event->virtual_url = '';
	$event->virtual_phone = '';
	$event->payment_email_id = 0;
	$event->confirmation_email_id = 1;
	$event->submitted = '';
	$event->google_map_link = espresso_google_map_link(array('address' => $event->address, 'city' => $event->city, 'state' => $event->state, 'zip' => $event->zip, 'country' => $event->country));
	$event->question_groups = array();
	$event->event_meta = array('additional_attendee_reg_info' => 1, 'default_payment_status' => '', 'add_attendee_question_groups' => array('1'), 'originally_submitted_by' => $current_user->ID);
	$event->wp_user = $current_user->ID;
		$sql = "SELECT qg.* FROM " . EVENTS_QST_GROUP_TABLE . " qg JOIN " . EVENTS_QST_GROUP_REL_TABLE . " qgr ON qg.id = qgr.group_id ";
	$sql2 = apply_filters('filter_hook_espresso_event_editor_question_groups_sql', " WHERE wp_user = '0' OR wp_user = '1' ", $event->id);
	$sql .= $sql2 . " GROUP BY qg.id ORDER BY qg.group_order";
	$sql = apply_filters('filter_hook_espresso_question_group_sql', $sql);
	//Debug:
	//echo $sql;
	$event->q_groups = $wpdb->get_results($sql);
	$event->num_rows = $wpdb->num_rows;
	$event->reg_limit = 999;
	$event->allow_multiple = false;
	$event->additional_limit = 0;
	$event->is_active = true;
	$event->event_status = 'A';
	$event->display_desc = true;
	$event->display_reg_form = true;
	$event->alt_email = '';
	$event->require_pre_approval = false;
	$event->member_only = false;
	$event->ticket_id = 0;
	$event->certificate_id = 0;
	$event->post_id = '';
	$event->slug = '';
	$event->recurrence_id = 0;
	$event = apply_filters('filter_hook_espresso_new_event_template', $event);

	espresso_display_add_event($event);
	do_action('action_hook_espresso_event_editor_footer');
}

function espresso_display_add_event($event) {
	$uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '&action=add_new_event'));
	?>

	<!--New event display-->
	<div class="wrap columns-2">
		<div id="icon-options-event" class="icon32"> </div>
		<h2>
			<?php _e('New Event', 'event_espresso'); ?>
		</h2>
		<form name="form" method="post" action="<?php echo $uri; ?>">
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="side-info-column" class="inner-sidebar">
					<?php do_meta_boxes('toplevel_page_events', 'side', $event); ?>
				</div>
				<div id="post-body">
					<div id="post-body-content">
						<?php do_meta_boxes('toplevel_page_events', 'normal', $event); ?>
						<?php do_meta_boxes('toplevel_page_events', 'advanced', $event); ?>
						<input type="hidden" name="action" value="add" />
						<input type="hidden" name="event_status" value="A" />
					</div>
				</div>
			</div>
		</form>
	</div>

	<?php
}

function espresso_new_event_publishing_action() {
	?>
	<div id="publishing-action">
		<?php wp_nonce_field('espresso_form_check', 'ee_add_new_event'); ?>
		<input class="button-primary" type="submit" name="Submit" value="<?php _e('Submit New Event', 'event_espresso'); ?>" id="add_new_event" />
	</div>
	<?php
}

add_action('action_hook_espresso_event_editor_publishing_action', 'espresso_new_event_publishing_action');