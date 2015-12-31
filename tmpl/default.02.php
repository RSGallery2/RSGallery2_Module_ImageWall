<?php
/**
* RSGallery2 latest galleries module: shows latest galleries from the Joomla extension RSGallery2 (www.rsgallery2.nl).
* @copyright (C) 2012 RSGallery2 Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
**/

defined('_JEXEC') or die();

header('Content-Type: image/png');

echo '<div class="mod_rsgallery2_image_wall">';

$ItemIdx = 0;

echo '$RowCount: '.$RowCount.'<br>';
echo '$ColumnCount: '.$ColumnCount.'<br>';
echo 'ImagesCount: '.count($Images).'<br>';

//--- Create target image as file ----------------------------------------

$TargetImage = imagecreatetruecolor($TargetWidth, $TargetHeight);

$TargetImageName = 'ImageWall.png';
$RsgalleryImagePath = realpath(JPATH_ROOT.$rsgConfig->get('imgPath_original').'/../');
$RsgalleryImageTempPath =  realpath(JPATH_ROOT.$rsgConfig->get('imgPath_original').'/../tmp');
if (!file_exists($RsgalleryImageTempPath)) {
	mkdir($RsgalleryImageTempPath, 0755);
}
// Server file path
$TargetImagePath = $RsgalleryImageTempPath .'/'. $TargetImageName;

// URL file path
$TargetImageUrl= JRoute::_('images/rsgallery/tmp/'.$TargetImageName);

// to make background transparent
imagealphablending($TargetImage, false);
$transparency = imagecolorallocatealpha($TargetImage, 0, 0, 0, 127);
imagefill($TargetImage, 0, 0, $transparency);
/* if you want to set background color
$white = imagecolorallocate($TargetImage, 255, 255, 255);
imagefill($TargetImage, 0, 0, $white);
*/
imagesavealpha($TargetImage, true);

// imagealphablending must be true in order to correcly stack 
// the layers, but it must be false to save the image.
imagealphablending($TargetImage, true);

$InsertWidth = $TargetWidth / $ColumnCount; // + border ?
$InsertHeight = $TargetHeight / $RowCount; // + border ?

echo '$TargetWidth: '.$TargetWidth.'<br>';
echo '$TargetHeight: '.$TargetHeight.'<br>';
echo '$InsertWidth: '.$InsertWidth.'<br>';
echo '$InsertHeight: '.$InsertHeight.'<br>';


$ItemIdx=-1;
for ($RowIdx = 0; $RowIdx < $RowCount; $RowIdx++) {
//	echo '$RowIdx: ' . $RowIdx . '<br>';

//	// If there still is am image to show, start a new row
//	if (!isset($Images[$ItemIdx])) {
//		continue;
//	}

//	if($RowIdx > 1) {
//		break;
//	}

	$SrcImageUrlPath = JRoute::_('images/rsgallery/original/');

	for ($ColIdx = 0; $ColIdx < $ColumnCount; $ColIdx++) {
		// Next image
		$ItemIdx++;

		// If there still is a gallery image to show, show it, otherwise, continue
		if (!isset($Images[$ItemIdx])) {
			echo '$ColIdx: ' . $ColIdx . '<br>';
			echo 'No image'.'<br>';
			continue;
		}

		//--- path to source image ---------------------------------
		//		echo 'SourceImg: ' . $SourceImage['name'] . '<br>';
		$SourceImage = $Images[$ItemIdx];
		$SourceImageName = $SourceImage['name'];
		$SrcImageUrl = JRoute::_($SrcImageUrlPath.$SourceImageName);
		echo '<img src="'.$SrcImageUrl.'">';

		//         $url = 'images/rsgallery/original/'
		//$SourceImagePath = 'images/rsgallery/original/' . $SourceImage['name'];
		$fullPath_original = JPATH_ROOT.$rsgConfig->get('imgPath_original') . '/';
		$SourceImagePath = $fullPath_original . $SourceImage['name'];
		echo '$SourceImagePath: ' . $SourceImagePath . '<br>';

		if (!file_exists($SourceImagePath)) {
			continue;
		}

		list($SourceWidth, $SourceHeight) = getimagesize($SourceImagePath);

		//--- Merge image into target ---------------------------------
		$InsertImage = imagecreatefromjpeg($SourceImagePath);
		$InsertOffsetX = $ColIdx * $InsertWidth;
		$InsertOffsetY = $RowIdx * $InsertHeight;
/*
		echo '$ColIdx: ' . $ColIdx . '<br>';
		echo '$RowIdx: ' . $RowIdx . '<br>';
		echo '$ItemIdx: ' . $ItemIdx . '<br>';

		echo '$InsertOffsetX: '.$InsertOffsetX.'<br>';
		echo '$InsertOffsetY: '.$InsertOffsetY.'<br>';
*/
//		imagecopymerge($TargetImage, $InsertImage, $InsertOffsetX, $InsertOffsetY, 0, 0,
// 			$InsertWidth, $InsertHeight, 100);

		imagecopyresized($TargetImage, $InsertImage, $InsertOffsetX, $InsertOffsetY, 0, 0,
			$InsertWidth, $InsertHeight, $SourceWidth, $SourceHeight);

//          return GD2::createSquareThumb( $source, $target, $rsgConfig->get('thumb_width') ); 
//          return GD2::resizeImage($source, $target, $targetWidth); 
//			img.utils.php

	}
}
imagealphablending($TargetImage, false);
imagesavealpha($TargetImage, true);

imagepng($TargetImage, $TargetImagePath);
echo '<img src="'.$TargetImageUrl.'">';
// imagedestroy($InsertImage);
imagedestroy($TargetImage);

echo '</div>';

