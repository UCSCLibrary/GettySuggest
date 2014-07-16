<?php
/**
 * Getty Collection Suggest
 * 
 * @copyright Copyright 2007-2012 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The getty_suggests table.
 * 
 * @package Omeka\Plugins\GettySuggest
 */
class Table_GettySuggest extends Omeka_Db_Table
{
    /**
     * List of suggest endpoints made available by the Library of Congress 
     * Authorities and Vocabularies service.
     * 
     * The keys are URLs to the authority/vocabulary suggest endpoints. The 
     * values are arrays containing the authority/vocabulary name and the URL to 
     * the authority/vocabulary description page.
     * 
     * These authorities and vocabularies have been selected due to their large 
     * size and suitability to the autosuggest feature. Vocabularies not 
     * explicitly included here may be redundant or better suited as a full list 
     * controlled vocabulary.
     * 
     * @see http://id.loc.gov/
     */
    private $_suggestEndpoints = array(
        'aat' => 'Art and Architecture Thesaurus', 
        'tgn' => 'Thesuarus of Geographic Names', 
        'ulan' => 'Union List of Artist Names', 
        'cona' => 'Cultural Objects Name Authority'
				       );
    
    /**
     * Find a suggest record by element ID.
     * 
     * @param int|string $elementId
     * @return LcSuggest|null
     */
    public function findByElementId($elementId)
    {
        $select = $this->getSelect()->where('element_id = ?', $elementId);
        return $this->fetchObject($select);
    }
    
    /**
     * Get the suggest endpoints.
     * 
     * @return array
     */
    public function getSuggestEndpoints()
    {
        return $this->_suggestEndpoints;
    }
}
