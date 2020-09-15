<?php

/**
 * Google Ads for DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Terence J. Grant<tjgrant@tatewake.com>
 */

if (!defined('DOKU_INC')) {
    define('DOKU_INC', realpath(dirname(__FILE__) . '/../../') . '/');
}
if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}
require_once(DOKU_PLUGIN . 'admin.php');

include_once(DOKU_PLUGIN . 'googleads/code.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_googleads extends DokuWiki_Admin_Plugin
{
    public $state = 0;
    public $googleads = '';
    
    /**
     * Constructor
     */
    public function admin_plugin_googleads()
    {
        $this->setupLocale();
    }
        
    /**
     * handle user request
     */
    public function handle()
    {
        $this->state = 0;
        
        if (!isset($_REQUEST['cmd']) || !is_array($_REQUEST['cmd'])) {
            return;
        }

        $this->googleads = $_REQUEST['googleads'];
        
        if (is_array($this->googleads)) {
            $this->state = 1;
        }
    }
    
    /**
     * output appropriate html
     */
    public function html()
    {
        global $conf;
        global $gads_loaded, $gads_settings;
        
        if ($this->state != 0) {
            $gads_settings['code'] = $this->googleads != null && array_key_exists('code', $this->googleads) ? addslashes($this->googleads['code']) : '';
            $gads_settings['dontcountadmin'] = $this->getIsValueOn($this->googleads, 'dontcountadmin') ? 1 : 0;
            $gads_settings['dontcountmanager'] = $this->getIsValueOn($this->googleads, 'dontcountmanager') ? 1 : 0;
            $gads_settings['dontcountusers'] = $this->getIsValueOn($this->googleads, 'dontcountusers') ? 1 : 0;
            
            gads_save();
        }

        print $this->locale_xhtml('intro');
        print $this->getForm();
        print '<br/><br/>';
        print $this->locale_xhtml('outtro');
    }

    protected function getIsValueOn($map, $key)
    {
        $result = false;
        
        if ($map != null && array_key_exists($key, $map)) {
            $result = $map[$key] == 1 || $map[$key] === 'on' || $map[$key] === 'checked';
        }

        return $result;
    }

    /**
     * Create the preference form
     *
     * @return string
     */
    protected function getForm()
    {
        global $ID;
        global $gads_settings;

        $form = new \dokuwiki\Form\Form([
            'method' => 'POST',
            'action' => wl($ID, ['do' => 'admin', 'page' => $this->getPluginName(), 'cmd[googleads]' => 'true'], false, '&')
        ]);
        $form->addFieldsetOpen($this->getLang('components'));

        $ta = $form->addTextarea('googleads[code]', $this->getLang('gads_googleads_code'));

        if ($gads_settings != null && array_key_exists('code', $gads_settings)) {
            $ta->val(stripslashes($gads_settings['code']));
        }

        $cb = $form->addCheckbox("googleads[dontcountadmin]", $this->getLang('gads_dont_count_admin'))->useInput(false)->addClass('block');
        if ($this->getIsValueOn($gads_settings, 'dontcountadmin')) {
            $cb->attr('checked', 'checked');
        }

        $cb = $form->addCheckbox("googleads[dontcountmanager]", $this->getLang('gads_dont_count_manager'))->useInput(false)->addClass('block');
        if ($this->getIsValueOn($gads_settings, 'dontcountmanager')) {
            $cb->attr('checked', 'checked');
        }

        $cb = $form->addCheckbox("googleads[dontcountusers]", $this->getLang('gads_dont_count_users'))->useInput(false)->addClass('block');
        if ($this->getIsValueOn($gads_settings, 'dontcountusers')) {
            $cb->attr('checked', 'checked');
        }

        $form->addButton('save', $this->getLang('gads_save'));
        return $form->toHTML();
    }
}
