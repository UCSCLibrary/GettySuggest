<?php
/**
 * Getty Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Getty Suggest Assignment controller.
 * 
 * @package GettySuggest
 */
class GettySuggest_SuggestController extends Omeka_Controller_AbstractActionController
{

    public function deleteAction()
    {   
        if(version_compare(OMEKA_VERSION,'2.2.1') >= 0)
            $this->_validatePost();
        $suggestId = $this->getRequest()->getParam('suggest_id');
        $gettySuggest = $this->_helper->db->getTable('GettySuggest')->find($suggestId);
        $gettySuggest->delete();
        $this->_helper->flashMessenger(__('Successfully disabled the element\'s suggest feature.'), 'success');
        $this->_helper->redirector('index','index');

    }

    public function editAction()
    {   
        $this->_validatePost();
        $suggestId = $this->getRequest()->getParam('suggest_id');
        $elementId = $this->getRequest()->getParam('element_id');
        $suggestEndpoint = $this->getRequest()->getParam('suggest_endpoint');

        // Don't process an invalid suggest endpoint.
        if (!$this->_suggestEndpointExists($suggestEndpoint)) {
            $this->_helper->flashMessenger(__('Invalid suggest endpoint. No changes have been made.'), 'error');
            
            $this->_helper->redirector('index','index');
        }
        
        $gettySuggest = $this->_helper->db->getTable('GettySuggest')->find($suggestId);
        $gettySuggest->element_id = $elementId;
        $gettySuggest->suggest_endpoint = $suggestEndpoint;
        $gettySuggest->save();
        $this->_helper->flashMessenger(__('Successfully edited the element\'s suggest feature.'), 'success');
        if($gettySuggest->suggest_endpoint == "tgn")
            $this->_helper->flashMessenger(__('Warning: the suggest feature for the Thesaurus of Geographic Names is currently very slow. It often takes many seconds for autosuggest results to appear. The other vocabularies are faster.'), 'error');
        $this->_helper->redirector('index','index');
    }

    /**
     * Adds a connection between an element and a vocabulary
     *
     * Overwrites existing connection for that element, if one exists
     *
     * @return void
     */
    public function addAction()
    {
        $this->_validatePost();
        $elementId = $this->getRequest()->getParam('element_id');
        $suggestEndpoint = $this->getRequest()->getParam('suggest_endpoint');
        
        // Don't process empty select options.
        if ('' == $elementId) {
	    $this->_helper->flashMessenger(__('Please select an element to assign'), 'success');
	    $this->_helper->redirector('index','index');
        }
        
        if (!$this->_suggestEndpointExists($suggestEndpoint)) {
	    $this->_helper->flashMessenger(__('Invalid suggest endpoint. No changes have been made.'), 'error');
            
	    $this->_helper->redirector('index','index');
        }
        
        $gettySuggest = new GettySuggest;
        $gettySuggest->element_id = $elementId;
        $gettySuggest->suggest_endpoint = $suggestEndpoint;
        $this->_helper->flashMessenger(__('Successfully enabled the element\'s suggest feature.'), 'success');
        if($gettySuggest->suggest_endpoint == "tgn")
            $this->_helper->flashMessenger(__('Warning: the suggest feature for the Thesaurus of Geographic Names is currently very slow. It often takes many seconds for autosuggest results to appear. The other vocabularies are faster.'), 'error');
                
        $gettySuggest->save();	
        $this->_helper->redirector('index','index');
    }


    /**
     * Check if the specified suggest endpoint exists.
     * 
     * @param string $suggestEndpoint An endpoint url 
     * which may or may not exist in the database
     * @return bool True if the endpoint exists, false otherwise
     */
    private function _suggestEndpointExists($suggestEndpoint)
    {
        $suggestEndpoints = $this->_helper->db->getTable('GettySuggest')->getSuggestEndpoints();
        if (!array_key_exists($suggestEndpoint, $suggestEndpoints)) {
            return false;
        }
        return true;
    }
    
    
    private function _validatePost(){
        $csrf = new Omeka_Form_SessionCsrf;
        if(!$csrf->isValid($_POST))
            die("ERROR!");
        return true;
    }
}


