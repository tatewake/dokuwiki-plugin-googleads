<?php
/**
 * Google Ads for DokuWiki
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Bernd Zeimetz <bernd@bzed.de>, based on code by Terence J. Grant<tjgrant@tatewake.com>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');

//--- Exported code
include_once(DOKU_PLUGIN.'googleads/code.php');
//--- Exported code

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_googleads extends DokuWiki_Admin_Plugin
{
var $state = 0;
var $googleads = '';

	/**
	 * Constructor
	 */
	function admin_plugin_googleads()
	{
		$this->setupLocale();
	}

	/**
	 * return some info
	 */
	function getInfo()
	{
		return array(
			'author' => 'Bernd Zeimetz',
			'email'  => 'bernd@bzed.de',
			'date'   => '2007-03-14',
			'name'   => 'Google Adsense Plugin',
			'desc'   => 'Plugin to embed your Google Adsense code in your site.',
			'url'    => 'http://bzed.de/code/dokuwiki/googleads',
		);
	}

	/**
	 * return sort order for position in admin menu
	 */
	function getMenuSort()
	{
		return 999;
	}
	

	/**
	 * handle user request
	 */
	function handle()
	{
		$this->state = 0;
	
		if (!isset($_REQUEST['cmd'])) return;   // first time - nothing to do

		if (!is_array($_REQUEST['cmd'])) return;

		$this->googleads = $_REQUEST['googleads'];

		if (is_array($this->googleads))
		{
			$this->state = 1;
		}
	}

	/**
	 * output appropriate html
	 */
	function html()
	{
		global $conf;
		global $gads_loaded, $gads_settings;

		if ($this->state != 0)	//If we are to save now...
		{
			$gads_settings['code'] = addslashes($this->googleads['code']);
			$gads_settings['dontcountadmin'] = $this->googleads['dontcountadmin'] == 'on' ? 'checked' : '';
			$gads_settings['dontcountusers'] = $this->googleads['dontcountusers'] == 'on' ? 'checked' : '';

			gads_save();
		}

		print $this->locale_xhtml('intro');

		ptln("<form action=\"".wl($ID)."\" method=\"post\">");
		ptln('  <input type="hidden" name="do"   value="admin" />');
		ptln('  <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
		ptln('  <input type="hidden" name="cmd[googleads]" value="true" />');
		print '<center><table class="inline">';
		print '	<tr><th> '.$this->getLang('gads_item_type').' </th><th> '.$this->getLang('gads_item_option').' </th></tr>';
		print '	<tr><td> '.$this->getLang('gads_googleads_code').' </td><td><TEXTAREA rows="15" cols="40" name="googleads[code]">' . stripslashes($gads_settings['code']) . '</TEXTAREA></td></tr>';
		print '	<tr><td> '.$this->getLang('gads_dont_count_admin').' </td><td><input type="checkbox" name="googleads[dontcountadmin]" '.$gads_settings['dontcountadmin'].'/></td></tr>';
		print '	<tr><td> '.$this->getLang('gads_dont_count_users').' </td><td><input type="checkbox" name="googleads[dontcountusers]" '.$gads_settings['dontcountusers'].'/></td></tr>';
		print '</table>';
		print '<br />';
		print '<p><input type="submit" value="'.$this->getLang('gads_save').'"></p></center>';
		print '</form>';
	}
}

