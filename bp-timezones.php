<?php

function bp_custom_message_date( $bp_time ) {
	global $messages_template;

	$member_zone = bp_get_member_profile_data('field=Time Zone');

	if ( $member_zone != '' ) {
		$timezone = bp_timezones_lookup( $member_zone );
		$timestamp = '@' . strtotime($messages_template->thread->last_message_date);
		$dt = new DateTime( $timestamp );
		$dt->setTimeZone( new DateTimeZone($timezone) );
		return $dt->format('M j, Y, g:i a'); /* format the displayed timestamp however you want */
	}
	else
		return $bp_time;

}
add_filter( 'bp_get_message_thread_last_post_date', 'bp_custom_message_date', 11, 1 );

// customize buddy boss time sent in to user local timezone if set
function bb_custom_chat_date($bp_time) {
	global $thread_template;

	$member_zone = bp_get_member_profile_data('field=Time Zone');
	if ( $member_zone != '' ) {
		$timezone = bp_timezones_lookup( $member_zone );
		$dt = new DateTime( $thread_template->message->date_sent );
		$dt->setTimeZone( new DateTimeZone($timezone) );
		return $dt->format('M j, Y, g:i a');
	}

	return $bp_time;
}
add_filter( 'bb_get_the_thread_message_sent_time', 'bb_custom_chat_date', 11, 1 );


function bp_timezones_lookup( $member_zone ) {

	$timezones =  bp_timezones_array();

	return array_search($member_zone, $timezones);
}
