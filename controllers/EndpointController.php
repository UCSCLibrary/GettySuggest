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
      $term = $this->getRequest()->getParam('term');

        // Get the suggest record.
        $elementId = $this->getRequest()->getParam('element-id');
        $gettySuggests = $this->_helper->db->getTable('GettySuggest')->findByElementId($elementId);

        $results = array();
        foreach($gettySuggests as $gettySuggest) {
            //create the SPARQL query
            $query = $this->_getSparql($gettySuggest['suggest_endpoint'],$term,'en');
            $fullurl = 'http://vocab.getty.edu/sparql.json?query='.urlencode($query);

            //execute the query using CURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$fullurl );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);  
            
            $json = json_decode($response);
            foreach($json->results->bindings as $result)
                $results[] = $result->prefLabel->value;
        }
        $this->_helper->json($results);
    }

    /**
     * Create a Sparql query to search the Getty LOD archive for possible 
     * autocompletions
     * 
     * @param string $vocab The name of the vocabulary to query (e.g.
     * "tgn", "aat", "ulan")
     * @param string $term The first few characters of the term to autosuggest
     * @return string
     */
    private function _getSparql($vocab, $term, $language)  {
            return(
                'select distinct ?prefLabel'.
                '{?place skos:inScheme '.$vocab.': ; '.
                'gvp:prefLabelGVP [xl:literalForm ?prefLabel; dct:language gvp_lang:'.$language.']; '.
                'FILTER regex(?prefLabel,"^'.$term.'","i")} '.
                'LIMIT '.get_option('gettyLimit')
            );
    }

}
