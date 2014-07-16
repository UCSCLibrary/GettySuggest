<?php
/**
 * Getty Controlled Vocabulary Suggest
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Getty Controlled Vocabulary Suggest plugin.
 * 
 * @package GettySuggest
 */
class GettySuggestPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install', 
        'uninstall', 
        'initialize', 
        'define_acl', 
    );
    
    protected $_filters = array(
        'admin_navigation_main', 
    );
    
    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $sql1 = "
        CREATE TABLE `{$this->_db->GettySuggest}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `element_id` int(10) unsigned NOT NULL,
            `suggest_endpoint` tinytext COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $this->_db->query($sql1);
    }
    
    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $sql1 = "DROP TABLE IF EXISTS `{$this->_db->GettySuggest}`";
        $this->_db->query($sql1);
    }
    
    /**
     * Initialize the plugin.
     */
    public function hookInitialize()
    {
        // Register the SelectFilter controller plugin.
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new GettySuggest_Controller_Plugin_Autosuggest);
        
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    /**
     * Define the plugin's access control list.
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('GettySuggest_Index');
    }
    
    /**
     * Add the LC Suggest page to the admin navigation.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Getty Suggest'), 
            'uri' => url('getty-suggest'), 
            'resource' => 'GettySuggest_Index', 
            'privilege' => 'index', 
        );
        return $nav;
    }
}
