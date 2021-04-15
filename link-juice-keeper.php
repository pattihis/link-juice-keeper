<?php
/*
Plugin Name: Link Juice Keeper
Plugin URI: http://www.poradnik-webmastera.com/projekty/link_juice_keeper/
Description: This plugin helps you to keep the link juice by redirecting all non-existing URLs which normally return a 404 error to the front blog page using 301 redirect.
Author: Daniel Frużyński
Version: 1.2.3
Author URI: http://www.poradnik-webmastera.com/
License: GPL2
*/

/*  Copyright 2009,2011  Daniel Frużyński  (email : daniel [A-T] poradnik-webmastera.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( !class_exists( 'LinkJuiceKeeper' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG  ) ) {

class LinkJuiceKeeper {
	// Constructor
	function LinkJuiceKeeper() {
		
		//add_action( 'init', array( &$this, 'init' ) );
		
		add_filter( 'status_header', array( &$this, 'status_header' ), 100, 2 );
	}
	
	/*// Initialise plugin
	function init() {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'link-juice-keeper', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)) );
		}
	}*/
	
	function status_header( $status_header, $header ) {
		if ( $header == 404 ) {
			// Extract root dir from blog url
			$root = '/';
			if ( preg_match( '#^http://[^/]+(/.+)$#', get_option( 'siteurl' ), $matches ) ) {
				$root = $matches[1];
			}
			// Make sure it ends with slash
			if ( $root[ strlen($root) - 1 ] != '/' ) {
				$root .= '/';
			}
			// Check if request is not for GWT verification file
			if ( strpos( $_SERVER['REQUEST_URI'], $root.'noexist_' ) !== 0 ) {
				wp_redirect( get_bloginfo( 'siteurl' ) , 301 );
				exit();
			}
		}
		
		return $status_header;
	}
}

$wp_link_juice_keeper = new LinkJuiceKeeper();

} // END

?>