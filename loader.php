<?php
/*
Plugin Name: BuddyPress TimeZones
Description: A BuddyPress plugin. Allows a member to set their own timezone.
Version: 1.0
Author: PhiloPress, shanebp
Author URI: http://philopress.com/
License: GPLv2
*/


if ( !defined( 'ABSPATH' ) ) exit;


function bp_timezones_include() {
    require( dirname( __FILE__ ) . '/bp-timezones.php' );
}
add_action( 'bp_include', 'bp_timezones_include' );


function bp_timezones_install() {
	bp_add_timezone_list();
}
register_activation_hook( __FILE__, 'bp_timezones_install' );


function bp_add_timezone_list() {

    $field_group_id = 1;

	// add on delete
	// xprofile_delete_field(xprofile_get_field_id_from_name('Time Zone'));

	if ( !xprofile_get_field_id_from_name('Time Zone') ) {

		$timezone_list_args = array(
		       'field_group_id'  => $field_group_id,
		       'name'            => 'Time Zone',
		       'description'	 => 'Please select your time zone',
		       'can_delete'      => true,
		       'field_order' 	 => 2,
		       'is_required'     => false,
		       'type'            => 'selectbox',
		       'order_by'	     => 'custom'
		);

		$timezone_list_id = xprofile_insert_field( $timezone_list_args );

		if ( $timezone_list_id ) {

			$timezones = bp_timezones_array();

			$i = 0;

			foreach (  $timezones as $timezone ) {

				xprofile_insert_field( array(
					'field_group_id'	=> $field_group_id,
					'parent_id'		    => $timezone_list_id,
					'type'			    => 'option',
					'name'			    => $timezone,
					'option_order'      => $i++
				));

			}
		}
	}
}

function bp_timezones_array() {

	static $regions = array(
		DateTimeZone::AFRICA,
		DateTimeZone::AMERICA,
		DateTimeZone::ANTARCTICA,
		DateTimeZone::ASIA,
		DateTimeZone::ATLANTIC,
		DateTimeZone::AUSTRALIA,
		DateTimeZone::EUROPE,
		DateTimeZone::INDIAN,
		DateTimeZone::PACIFIC,
	);

	$timezones = array();
	foreach( $regions as $region )
	{
		$timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
	}

	$timezone_offsets = array();
	foreach( $timezones as $timezone )
	{
		$tz = new DateTimeZone($timezone);
		$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
	}

	// sort timezone by offset
	asort($timezone_offsets);

	$timezone_list = array();
	foreach( $timezone_offsets as $timezone => $offset )
	{
		$offset_prefix = $offset < 0 ? '-' : '+';
		$offset_formatted = gmdate( 'H:i', abs($offset) );

		$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

		$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
	}

	return $timezone_list;
}
