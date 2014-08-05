 jQuery(document).ready(function($) {

	var  eeThnx = {

		// set current TXN's status
		prev_txn_status : '',
		// data object sent from the server
		data : [],
		// JSON array  of data to be sent to the server when polling
		return : {
			'reg_url_link' : eei18n.reg_url_link,
			'initial_access' : eei18n.initial_access,
			'txn_status' : this.prev_txn_status,
			'get_payments_since' : 0
		},
		// ajax loading animation
		spinner : '',
		// polling_time
		polling_time : 5,


		/**
		*	init
		*/
		init : function() {
			this.console_log( 'init' );
			this.display_spinner();
			this.set_up_wp_heartbeat();
		},

		/**
		*	display_spinner
		*/
		display_spinner : function() {
			this.console_log( 'display_spinner' );
			this.spinner = $('#espresso-ajax-loading');
			$('#espresso-ajax-loading').remove();
			$('#ee-ajax-loading-dv').after( this.spinner );
			$( this.spinner ).css({ 'position' : 'relative', 'top' : '-5px', 'left' : 0, 'margin-left' : '.5em', 'font-size' : '18px', 'float' : 'left' }).show();
		},

		/**
		*	set_up_wp_heartbeat
		*/
		set_up_wp_heartbeat : function() {
			this.console_log( 'set_up_wp_heartbeat' );
			// Show debug info ?
			wp.heartbeat.debug = eei18n.wp_debug === '1' ? true : false;
			// set initial beat to fast
			wp.heartbeat.interval( this.polling_time );
			wp.heartbeat.enqueue( 'espresso_thank_you_page', this.return, false );
			wp.heartbeat.connectNow();

		},

		/**
		*	process_heartbeat_response
		*/
		process_heartbeat_response : function( data ) {
			this.console_log( 'process_heartbeat_response' );
			// check heartbeat for thank you page data
			if ( typeof data.espresso_thank_you_page === 'undefined' ) {
				this.console_log( 'espresso_thank_you_page undefined' );
				return;
			}
			// store it
			this.data = data.espresso_thank_you_page;
			// and log to console if debugging
			this.console_log_obj( 'this.data', this.data );
			// set return txn status to incoming txn status
			if ( typeof this.data.txn_status !== 'undefined') {
				this.return.txn_status = this.data.txn_status;
			}
			// set return get_payments_since to incoming get_payments_since which
			if ( typeof this.data.get_payments_since !== 'undefined') {
				this.return.get_payments_since = this.data.get_payments_since;
			}
			// handle errors
			if ( typeof this.data.errors !== 'undefined') {
				this.display_errors( this.data.errors );
				this.stop_heartbeat();
			// slow IPN
			} else if ( typeof this.data.still_waiting !== 'undefined') {
				this.process_wait_time();
			// server sent back data
			} else {
				this.process_server_data();
			}
		},

		/**
		*	process_wait_time
		*/
		process_wait_time : function() {
			this.console_log( 'process_wait_time' );
			// has wait time exceeded exceptable limit?
			if ( this.data.still_waiting > eei18n.IPN_wait_time ) {
				// waited tooooo long
				this.wait_time_exceeded();
				this.stop_heartbeat();
			} else {
				// keep waiting
				this.set_wait_time();
				this.restart_heartbeat();
			}
		},

		/**
		*	process_server_data
		*/
		process_server_data : function() {
			this.console_log( 'process_server_data' );
			// received new payments AND updated transaction ?
			if ( typeof this.data.new_payments !== 'undefined' ) {
				this.update_transaction_details();
				this.display_new_payments();
				this.start_stop_heartbeat();
			// received transaction AND payment details ?
			} else if ( typeof this.data.transaction_details !== 'undefined' && typeof this.data.payment_details !== 'undefined' ) {
				this.display_transaction_details();
				this.display_payment_details();
				this.hide_loading_message();
				this.start_stop_heartbeat();
			// received transaction details only ?
			} else if ( typeof this.data.transaction_details !== 'undefined' ) {
				this.display_transaction_details();
				this.update_loading_message();
				this.start_stop_heartbeat();
			// received payment details
			} else if ( typeof this.data.payment_details !== 'undefined' ) {
				this.display_payment_details();
				this.hide_loading_message();
				this.start_stop_heartbeat();
			} else {
				this.start_stop_heartbeat();
			}
		},

		/**
		*	display_errors
		*/
		display_errors : function() {
			this.console_log( 'display_errors' );
			$('#espresso-thank-you-page-ajax-content-dv').hide().html( this.errors ).slideDown();
		},

		/**
		*	display_transaction_details
		*/
		display_transaction_details : function() {
			this.console_log( 'display_transaction_details' );
			// has the TXN status changed ?
			if ( this.return.txn_status !== this.prev_txn_status ) {
				this.prev_txn_status = this.return.txn_status;
			}
			$('#espresso-thank-you-page-ajax-transaction-dv').hide().html( this.data.transaction_details ).slideDown();
		},

		/**
		*	update_transaction_details
		*/
		update_transaction_details : function() {
			this.console_log( 'update_transaction_details' );
			$('#espresso-thank-you-page-ajax-transaction-dv').html( this.data.transaction_details );
			// has the TXN status changed ?
			if ( this.return.txn_status !== this.prev_txn_status ) {
				this.prev_txn_status = this.return.txn_status;
			}
		},

		/**
		*	display_payment_details
		*/
		display_payment_details : function() {
			this.console_log( 'display_payment_details' );
			$('#espresso-thank-you-page-ajax-payment-dv').hide().html( this.data.payment_details ).slideDown();
		},

		/**
		*	display_new_payments
		*/
		display_new_payments : function() {
			this.console_log( 'display_new_payments' );
			$('#espresso-thank-you-page-payment-details-dv table tbody').append( this.data.new_payments );
		},

		/**
		*	update_loading_message
		*/
		update_loading_message : function() {
			this.console_log( 'update_loading_message' );
			$('#ee-ajax-loading-msg-spn').html( eei18n.loading_payment_info );
		},

		/**
		*	hide_loading_message
		*/
		hide_loading_message : function() {
			this.console_log( 'hide_loading_message' );
			$('#espresso-thank-you-page-ajax-loading-dv').hide();
		},

		/**
		*	checking_for_new_payments_message
		*/
//		checking_for_new_payments_message : function() {
//			this.console_log( 'checking_for_new_payments_message' );
//			$('#ee-ajax-loading-pg').hide();
//			$('#ee-ajax-loading-dv').removeClass('lt-blue-text').addClass('lt-grey-text');
////			var since = new Date( null, null, null, null, null, this.data.get_payments_since ).toTimeString();   + ' ' + since
//			$('#ee-ajax-loading-msg-spn').html( eei18n.checking_for_new_payments );
//			$('#espresso-ajax-loading').css({ 'font-size' : '12px', 'top' : 0 }).addClass('lt-grey-text');
//			$('#espresso-thank-you-page-ajax-loading-dv').hide(0).addClass('small-text').delay( ( this.polling_time - 1 ) * 1000 ).show(0);
//		},

		/**
		*	set_wait_time
		*/
		set_wait_time : function() {
			this.console_log( 'set_wait_time' );
			var waitTime = new Date( null, null, null, null, null, this.data.still_waiting ).toTimeString().match(/\d{2}:\d{2}:\d{2}/)[0];
			$('#espresso-thank-you-page-ajax-time-dv').html( waitTime );
		},

		/**
		*	wait_time_exceeded
		*/
		wait_time_exceeded : function() {
			this.console_log( 'wait_time_exceeded' );
			$('#espresso-thank-you-page-ajax-content-dv').hide().html( eei18n.slow_IPN ).slideDown();
		},

		/**
		*	start_stop_heartbeat
		*/
		start_stop_heartbeat : function() {
			this.console_log( 'start_stop_heartbeat' );
			if (this.return.txn_status === eei18n.TXN_incomplete) {
				this.restart_heartbeat();
//				this.checking_for_new_payments_message();
			} else {
				this.stop_heartbeat();
				this.hide_loading_message();
			}
		},

		/**
		*	restart_heartbeat
		*/
		restart_heartbeat : function() {
			this.console_log( 'restart_heartbeat' );
			this.console_log_obj( 'this.return', this.return );
			wp.heartbeat.enqueue( 'espresso_thank_you_page', this.return, true );
		},

		/**
		*	stop_heartbeat
		*/
		stop_heartbeat : function() {
			this.console_log( 'stop_heartbeat' );
			wp.heartbeat.dequeue( 'espresso_thank_you_page' );
		},

		/**
		*	log
		*/
		console_log : function( key, value ) {
			if ( eei18n.wp_debug && typeof key !== 'undefined' && typeof value !== 'undefined' ) {
				console.log( JSON.stringify( key + ': ' + value, null, 4 ));
			} else if ( eei18n.wp_debug && typeof key !== 'undefined' ) {
				console.log( key );
			}
		},

		/**
		*	log
		*/
		console_log_obj : function( obj_name, obj ) {
			if ( eei18n.wp_debug && typeof obj_name !== 'undefined' ) {
				console.log( JSON.stringify( obj_name, null, 4 ));
			}
			if ( eei18n.wp_debug && typeof obj !== 'undefined' ) {
				for ( var key in obj ) {
					if ( typeof key !== 'undefined' && typeof obj[ key ] !== 'undefined' ) {
					console.log( JSON.stringify( '    ' + key + ': ' + obj[ key ], null, 4 ));
				}
				}
			}
		}

	};
	// end of eeThnx object
	eeThnx.init();
	// setup listener
	$(document).on( 'heartbeat-tick.espresso_thank_you_page', function( event, data ) {
		eeThnx.process_heartbeat_response( data );
	});

});
