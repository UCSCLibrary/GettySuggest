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
     * @var array $_suggestEndpoints List of suggest endpoints 
     * corresponding to controlled vocabularies
     * and authorities made available by the Getty Trust LOD project
     * 
     */
    private $_suggestEndpoints = array(
        'aat' => 'Art and Architecture Thesaurus', 
        'tgn' => 'Thesuarus of Geographic Names', 
        'ulan' => 'Union List of Artist Names', 
#        'cona' => 'Cultural Objects Name Authority'
				       );
    
    /**
     * Find a suggest record by element ID.
     * 
     * @param int|string $elementId
     * @return GettySuggest|null
     */
    public function findByElementId($elementId)
    {
        $select = $this->getSelect()->where('element_id = ?', $elementId);
        return $this->fetchObjects($select);
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
