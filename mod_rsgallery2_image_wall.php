<?php

/**
* RSGallery2 image wall module:
* Module to show a collection of images from RSGallery2 side by side (www.rsgallery2.nl).
* @copyright (C) 2015-2015 RSGallery2 Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @version 4.0.0
**/

defined('_JEXEC') or die();

// Initialise RSGallery2 and other variables
require_once(JPATH_BASE.'/administrator/components/com_rsgallery2/init.rsgallery2.php');

// Add styling
$document = JFactory::getDocument();
$url = JURI::base().'modules/mod_rsgallery2_image_wall/css/mod_rsgallery2_image_wall.css';
$document->addStyleSheet($url);

global $rsgConfig;

//--- Parameters --------------------------------------------------------------
// Number of  latest galleries to display = number of rows times the number of columns
$RowCount			= (int) $params->get('RowCount', 		'3');
$ColumnCount		= (int) $params->get('ColumnCount',	'4');
$ImageCount			= $RowCount * $ColumnCount;

$TargetWidth 	= (int) $params->get('TargetWidth', 		'800');
$TargetHeight	= (int) $params->get('TargetHeight', 		'350');

$HasOuterBorder	= (int) $params->get('HasOuterBorder', 		'0');
$OuterBorderSize	= (int) $params->get('OuterBorderSize', 		'5');
//$OuterBorderColorRGB	= (int) $params->get('OuterBorderColorRGB', 		'0xff');
$InnerBorderType	= (int) $params->get('InnerBorderType', 		'1');
$InnerBorderSize	= (int) $params->get('InnerBorderSize', 		'10');
//$InnerBorderColorRGB	= (int) $params->get('InnerBorderColorRGB', 		'0x0');


//--- Select images -----------------------------



// ToDo: Export image selection to single file (s)


//--- Query latest images -----------------------------------------------------

// Query to get limited ($ImageCount) number of latest images
//$query = "SELECT * FROM #__rsgallery2_files $list ORDER BY date DESC LIMIT $ImageCount";
$result = Null;

$database = JFactory::getDbo();
$query = $database->getQuery(true);
$query->select('*')
    ->from('#__rsgallery2_files')
    ->where('published = 1');
/*	NOTE TODO Access should be checked for galleries, not for images
// If user is not a Super Admin then use View Access Levels
if (!$superAdmin) { // No View Access check for Super Administrators
	$query->where('access IN ('.$groupsIN.')'); //@todo use trash state: published=-2
}
*/

$query->order('date DESC');
$database->setQuery($query, 0, $ImageCount);	//$ImageCount is the number of results to return

$Images = $database->loadAssocList();
if(!$Images){
	// Error handling
	// ToDo: Ask module admin if a message is required (?debug) and to provide this error message
	// enque message
}

//--- Output ------------------------------------------------------------------

// Let's display what we've gathered: get the layout
require JModuleHelper::getLayoutPath('mod_rsgallery2_image_wall', $params->get('layout', 'default'));

