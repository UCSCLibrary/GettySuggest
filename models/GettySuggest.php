<?php
/**
 * Getty Collection Suggest
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A getty_suggests row.
 * 
 * @package GettySuggest
 */
class GettySuggest extends Omeka_Record_AbstractRecord
{
    public $id;
    public $element_id;
    public $suggest_endpoint;
}

?>