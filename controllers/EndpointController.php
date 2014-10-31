<?php
/**
 * Getty Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Getty Suggest Endpoint controller.
 * 
 * @package GettySuggest
 */
class GettySuggest_EndpointController extends Omeka_Controller_AbstractActionController
{

    /**
     * Proxy for the Getty Suggest suggest endpoints, used by the 
     * autosuggest feature.
     *
     * @return void
     */
    public function proxyAction()
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