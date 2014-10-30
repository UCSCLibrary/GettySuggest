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

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install', 
        'uninstall', 
        'initialize', 
        'define_acl', 
    );
    
    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main', 
    );
    
    /**
     * Install the plugin.
     *
     * @return void
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
     *
     * @return void
     */
    public function hookUninstall()
    {
        $sql1 = "DROP TABLE IF EXISTS `{$this->_db->GettySuggest}`";
        $this->_db->query($sql1);
    }
    
    /**
     * Initialize the plugin.
     *
     * @return void
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
   *
   * @param array $args This array contains a reference to
   * the zend ACL under it's 'acl' key.
   * @return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('GettySuggest_Index');
    }
    
    /**
     * Add the GettySuggest link to the admin main navigation.
     * 
     * @param array $nav Array of links for admin nav section
     * @return array $nav Updated array of links for admin nav section
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
