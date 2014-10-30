<?php
/**
 * Getty Collection Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A getty_suggests row linking an element to a Getty vocabulary.
 * 
 * @package GettySuggest
 */
class GettySuggest extends Omeka_Record_AbstractRecord
{
    /**
     * @var int $id The record ID of the GettySuggest record
     */
    public $id;
    
    /**
     * @var int $element_id The record ID of the Element to be linked
     */
    public $element_id;
    
    /**
     * @var string $suggest_endpoint The url of the endpoint 
     * to which we are linking the given element
     */
    public $suggest_endpoint;
}

?>