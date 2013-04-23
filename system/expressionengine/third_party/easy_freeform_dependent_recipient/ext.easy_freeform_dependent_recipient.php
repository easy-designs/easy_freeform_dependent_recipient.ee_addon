<?php if ( ! defined('EXT')) exit('No direct script access allowed');

/**
 * Easy Freeform Dependent Recipient - Extension
 *
 * @package		Easy:Freeform Dependent Recipient
 * @author		Easy Designs, LLC
 * @copyright	Copyright (c) 2013, Easy Designs, LLC
 * @link		http://easy-designs.net
 * @license		MIT
 * @version		1.0
 * @filesource	calendar/ext.calendar.php
 */

class Easy_freeform_dependent_recipient_ext
{
	public $settings		= array();
	public $name			= 'Easy Freeform Dependent Recipient';
	public $version			= '1.0';
	public $description		= 'Allows you to set a Freeform field that determines who you notify.';
	public $settings_exist	= 'n';
	public $docs_url		= 'Somewhere';
	public $required_by 	= array();
	
	private $prefix = 'easy_dependent_recipient';


	// --------------------------------------------------------------------

	/**
	 * Extension Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();
		
		if ( session_id() == "" ) 
		{
			session_start(); 
		}
	}
	# END

	/**
	 * Capture the form extensions
	 */
	public function freeform_module_form_begin( $obj )
	{
		
		$easy_vars = array();
		
		# capture our overloaded tag data
		foreach ( $this->EE->TMPL->tag_data as $tag )
		{
			if ( strpos( $tag['tag'], 'exp:freeform:form' ) !== FALSE )
			{
				foreach ( $tag['params'] as $key => $value )
				{
					if ( strpos( $key, $this->prefix ) !== FALSE )
					{
						$easy_vars[$key] = $value;
					}
				}
			}
		}
		
		# store it in the session
		$_SESSION["{$this->prefix}_data"] = base64_encode( serialize( $easy_vars ) );

	}


	/**
	 * Now use them
	 */
	function freeform_module_user_notification( $fields, $e_id, $vars, $f_id, $obj )
	{
		# have other extensions already manipulated?
		if ($this->EE->extensions->last_call !== FALSE)
		{
			$vars = $this->EE->extensions->last_call;
		}
		
		# pull it out of the session
		$easy_vars = unserialize( base64_decode(
			$_SESSION["{$this->prefix}_data"]
		) );
		
		# do we have a field?
		if ( isset( $easy_vars["{$this->prefix}_field"] ) )
		{
			$i = 1;
			while ( isset( $easy_vars["{$this->prefix}_value{$i}"] ) )
			{
				if ( $easy_vars["{$this->prefix}_value{$i}"] == $vars['field_inputs'][$easy_vars["{$this->prefix}_field"]] )
				{
					$vars['recipients'] = array(
						$easy_vars["{$this->prefix}_email{$i}"]
					);
					unset( $_SESSION["{$this->prefix}_data"] );
					break;
				}
				$i++;
			}
		}

		# required
		return $vars;
	}   
	# END
		
   
	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		$this->EE->db->insert(
			'extensions',
			array(
				'class'    => __CLASS__,
				'method'   => 'freeform_module_user_notification',
				'hook'     => 'freeform_module_user_notification',
				'settings' => '',
				'priority' => 10,
				'version'  => $this->version,
				'enabled'  => 'y'
			)
		);
		$this->EE->db->insert(
			'extensions',
			array(
				'class'    => __CLASS__,
				'method'   => 'freeform_module_form_begin',
				'hook'     => 'freeform_module_form_begin',
				'settings' => '',
				'priority' => 10,
				'version'  => $this->version,
				'enabled'  => 'y'
			)
		);
	}

	/**
	 * Update Extension
	 */
	function update_extension($current = FALSE)
	{
		if (! $current || $current == $this->version)
		{
			return FALSE;
		}

		$this->EE->db->where( 'class', __CLASS__ );
		$this->EE->db->update(
			'extensions',
			array(
				'version' => $this->version
			)
		);
	}

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		$this->EE->db->delete(
			'exp_extensions',
			array(
				'class'	=> __CLASS__
			)
		);
	}

	// --------------------------------------------------------------------


}
// END CLASS