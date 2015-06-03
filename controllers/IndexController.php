<?php
/**
 * Getty Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Getty Suggest controller.
 * 
 * @package GettySuggest
 */
class GettySuggest_IndexController extends Omeka_Controller_AbstractActionController
{

  /**
   * The default action for this plugin's admin page.
   *
   * Sets up form variables which require values from the database.
   * @return void
   */
  public function indexAction()
  {
    $csrf = new OMeka_Form_SessionCsrf;
    $this->view->csrf = $csrf;
    $this->view->form_element_options = $this->_getFormElementOptions();
    $this->view->form_suggest_options = $this->_getFormSuggestOptions();
    $this->view->assignments = $this->_getAssignments();
  }

  /**
   * Proxy for the Getty Suggest suggest endpoints, used by the 
   * autosuggest feature.
   *
   * @return void
   */
  public function suggestEndpointProxyAction()
  {
    //get the term
    $term = $this->getRequest()->getParam('term');

    // Get the suggest record.
    $elementId = $this->getRequest()->getParam('element-id');
    $gettySuggest = $this->_helper->db->getTable('GettySuggest')->findByElementId($elementId);

    //create the SPARQL query
    $query = $this->_getSparql($gettySuggest['suggest_endpoint'],$term,'en');

    $fullurl = 'http://vocab.getty.edu/sparql.json?query='.urlencode($query);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$fullurl );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);  

    $json = json_decode($response);

    $results = array();
    foreach($json->results->bindings as $result) {
      $results[] = $result->prefLabel->value;
    }
	
    $this->_helper->json($results);
  }
    
  /**
   * Get an array to be used in formSelect() containing all elements.
   * 
   * @return array
   */
  private function _getFormElementOptions()
  {
    $db = $this->_helper->db->getDb();
    $sql = "
        SELECT es.name AS element_set_name, e.id AS element_id, e.name AS element_name, 
        it.name AS item_type_name, ls.id AS lc_suggest_id 
        FROM {$db->ElementSet} es 
        JOIN {$db->Element} e ON es.id = e.element_set_id 
        LEFT JOIN {$db->ItemTypesElements} ite ON e.id = ite.element_id 
        LEFT JOIN {$db->ItemType} it ON ite.item_type_id = it.id 
        LEFT JOIN {$db->GettySuggest} ls ON e.id = ls.element_id 
        WHERE es.record_type IS NULL OR es.record_type = 'Item' 
        ORDER BY es.name, it.name, e.name";
    $elements = $db->fetchAll($sql);
    $options = array('' => __('Select Below'));
    foreach ($elements as $element) {
      $optGroup = $element['item_type_name'] 
	? __('Item Type') . ': ' . __($element['item_type_name']) 
	: __($element['element_set_name']);
      $value = __($element['element_name']);
      if ($element['lc_suggest_id']) {
	$value .= ' *';
      }
      $options[$optGroup][$element['element_id']] = $value;
    }
    return $options;
  }
    
  /**
   * Get an array to be used in formSelect() containing all sugggest endpoints.
   * 
   * @return array
   */
  private function _getFormSuggestOptions()
  {
    //print_r($this->_helper->db->getTable('GettySuggest'));
    //die();
    $suggests = $this->_helper->db->getTable('GettySuggest')->getSuggestEndpoints();
      
    $options = array('' => __('Select Below'));

    foreach ($suggests as $suggestEndpoint => $suggest) {
      $options[$suggestEndpoint] = $suggest;
    }

    return $options;
  }
    
  /**
   * Get all the current authority/vocabulary assignments.
   * 
   * @return array
   */
  private function _getAssignments()
  {
    $gettySuggestTable = $this->_helper->db->getTable('GettySuggest');
    $elementTable = $this->_helper->db->getTable('Element');
    $elementSetTable = $this->_helper->db->getTable('ElementSet');
    $itemTypeTable = $this->_helper->db->getTable('ItemType');
    $itemTypesElementsTable = $this->_helper->db->getTable('ItemTypesElements');
        
    $suggestEndpoints = $gettySuggestTable->getSuggestEndpoints();
    $assignments = array();
    foreach ($gettySuggestTable->findAll() as $gettySuggest) {
      $element = $elementTable->find($gettySuggest->element_id);
      $elementSet = $elementSetTable->find($element->element_set_id);
      $elementSetName = $elementSet->name;
      if( $itemTypesElements = $itemTypesElementsTable->findByElement($element->id)) {
	$itemTypesElement = $itemTypesElements[0];
	$itemType = $itemTypeTable->find($itemTypesElement->item_type_id);
	$elementSetName.=': '.$itemType->name;
      }

      $authorityVocabulary = $suggestEndpoints[$gettySuggest->suggest_endpoint];
      $assignments[] = array(
			     'suggest_id' => $gettySuggest->id,
			     'element_set_name' => $elementSetName, 
			     'element_name' => $element->name, 
			     'authority_vocabulary' => $authorityVocabulary,
			     'element_id' => $gettySuggest->element_id
			     );
            
    }
    return $assignments;
  }

  /**
   * Create a Sparql query to search the Getty LOD archive for possible 
   * autocompletions
   * 
   * @param string $vocab The name of the vocabulary to query (e.g.
   * "tgn", or "aat")
   * @param string $term The first few characters of the term to autosuggest
   * @return string
   */
  private function _getSparql($vocab, $term, $language)  {
    return('select ?prefLabel where '.
	   '{?concept a gvp:Concept . '.
	   '?concept skos:inScheme '.$vocab.': . '.
	   '?concept skos:prefLabel ?prefLabel . '.
	   '?concept ?b ?label . '.
	   'FILTER (?b= skos:prefLabel || ?b= skos:altLabel) . '.
	   'FILTER (lang(?label) = "'.$language.'") . '.
	   'FILTER (lang(?prefLabel) = "'.$language.'") . '.
	   'FILTER (regex(?label,"^'.$term.'","i") '.
	   ') } '.
	   'order by $prefLabel '.
	   'LIMIT 20'
	   );
  }
}
