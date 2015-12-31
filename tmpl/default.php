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
/*
echo '$RowCount: '.$RowCount.'<br>';
echo '$ColumnCount: '.$ColumnCount.'<br>';
echo '$ImagesCount: '.count($Images).'<br>';
*/
//--- Create target image as file ----------------------------------------

$TargetImage = imagecreatetruecolor($TargetWidth, $TargetHeight);

$TargetImageDebug = imagecreatetruecolor($TargetWidth, $TargetHeight);

$TargetImageName = 'ImageWall.png';
$TargetImageDebugName = 'ImageWallDebug.png';
$RsgalleryImagePath = realpath(JPATH_ROOT.$rsgConfig->get('imgPath_original').'/../');
$RsgalleryImageTempPath =  realpath(JPATH_ROOT.$rsgConfig->get('imgPath_original').'/../tmp');
if (!file_exists($RsgalleryImageTempPath)) {
	mkdir($RsgalleryImageTempPath, 0755);
}
// Server file path
$TargetImagePath = $RsgalleryImageTempPath .'/'. $TargetImageName;
$TargetImageDebugPath = $RsgalleryImageTempPath .'/'. $TargetImageDebugName;

// URL file path
$TargetImageUrl= JRoute::_('images/rsgallery/tmp/'.$TargetImageName);
$TargetImageDebugUrl= JRoute::_('images/rsgallery/tmp/'.$TargetImageDebugName);

/*
// to make background transparent
imagealphablending($TargetImage, false);
$transparency = imagecolorallocatealpha($TargetImage, 0, 0, 0, 127);
imagefill($TargetImage, 0, 0, $transparency);
/**/
/* if you want to set background color *
$white = imagecolorallocate($TargetImage, 255, 255, 255);
$black = imagecolorallocate($TargetImage, 0, 0, 0);
//imagefill($TargetImage, 0, 0, $white);
imagefill($TargetImage, 0, 0, $black);
/**/
imagesavealpha($TargetImage, true);

// imagealphablending must be true in order to correcly stack 
// the layers, but it must be false to save the image.
imagealphablending($TargetImage, true);

//--------------------------------------------------------------------------
/**/
// to make background transparent
imagealphablending($TargetImageDebug, false);
$transparency = imagecolorallocatealpha($TargetImageDebug, 0, 0, 0, 127);
imagefill($TargetImageDebug, 0, 0, $transparency);
/**/
/* if you want to set background color *
$white = imagecolorallocate($TargetImageDebug, 255, 255, 255);
imagefill($TargetImageDebug, 0, 0, $white);
/**/
imagesavealpha($TargetImageDebug, true);

// imagealphablending must be true in order to correcly stack 
// the layers, but it must be false to save the image.
imagealphablending($TargetImageDebug, true);


//---  ----------------------------------------

$InsertBaseWidth = $TargetWidth / $ColumnCount; // + border ?
$InsertBaseHeight = $TargetHeight / $RowCount; // + border ?

// "0" > InnerBorderType None
// "1" > InnerBorderType Merge
// "2" > InnerBorderType Space

if ($InnerBorderType > 0)
{
	// Will make every image longer so it merges with the next
	$InsertWidth = $InsertBaseWidth + $InnerBorderSize;
	$InsertHeight = $InsertBaseHeight + $InnerBorderSize; 
}
/*
echo '$TargetWidth: '.$TargetWidth.'<br>';
echo '$TargetHeight: '.$TargetHeight.'<br>';
echo '$InsertWidth: '.$InsertWidth.'<br>';
echo '$InsertHeight: '.$InsertHeight.'<br>';
*/

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
//		echo '<img src="'.$SrcImageUrl.'">';

		//         $url = 'images/rsgallery/original/'
		//$SourceImagePath = 'images/rsgallery/original/' . $SourceImage['name'];
		$FullPath_original = JPATH_ROOT.$rsgConfig->get('imgPath_original') . '/';
		$SourceImagePath = $FullPath_original . $SourceImage['name'];
//		echo '$SourceImagePath: ' . $SourceImagePath . '<br>';

		if (!file_exists($SourceImagePath)) {
			continue;
		}

		list($SourceWidth, $SourceHeight) = getimagesize($SourceImagePath);
		$SourceImage = imagecreatefromjpeg($SourceImagePath);

		// image with new size
		$ImgWithNewSize = ImageCreateTrueColor($InsertWidth, $InsertHeight);
		imagecopyResampled ($ImgWithNewSize, $SourceImage, 0, 0, 0, 0, 
			$InsertWidth, $InsertHeight, $SourceWidth, $SourceHeight);

		//--- Merge image into target ---------------------------------
		$InsertOffsetX = $ColIdx * $InsertBaseWidth;
		$InsertOffsetY = $RowIdx * $InsertBaseHeight;
/*
		echo '$ColIdx: ' . $ColIdx . '<br>';
		echo '$RowIdx: ' . $RowIdx . '<br>';
		echo '$ItemIdx: ' . $ItemIdx . '<br>';

		echo '$InsertOffsetX: '.$InsertOffsetX.'<br>';
		echo '$InsertOffsetY: '.$InsertOffsetY.'<br>';
*/

//          return GD2::createSquareThumb( $source, $target, $rsgConfig->get('thumb_width') ); 
//          return GD2::resizeImage($source, $target, $targetWidth); 
//			img.utils.php

		imagecopymerge($TargetImage, $ImgWithNewSize, $InsertOffsetX, $InsertOffsetY, 0, 0,
 			$InsertWidth, $InsertHeight, 99);

		imagedestroy($ImgWithNewSize);
		/* working but no merging ;_)
                imagecopyresized($TargetImage, $InsertImage, $InsertOffsetX, $InsertOffsetY, 0, 0,
                    $InsertWidth, $InsertHeight, $SourceWidth, $SourceHeight);
        */

		// Debug 
		imagecopyresized($TargetImageDebug, $SourceImage, $InsertOffsetX, $InsertOffsetY, 0, 0,
			$InsertWidth, $InsertHeight, $SourceWidth, $SourceHeight);
	}
}

imagealphablending($TargetImage, false);
imagesavealpha($TargetImage, true);

imagepng($TargetImage, $TargetImagePath);
echo '<img src="'.$TargetImageUrl.'">';
// imagedestroy($InsertImage);
imagedestroy($TargetImage);


// Debug
imagealphablending($TargetImageDebug, false);
imagesavealpha($TargetImageDebug, true);


echo "<br>";
echo "99"."<br>";
echo "<br>";
//echo "\$TargetImageDebug".$TargetImageDebug;
//echo "\$TargetImageDebugUrl".$TargetImageDebugUrl;

imagepng($TargetImageDebug, $TargetImageDebugPath);
echo '<img src="'.$TargetImageDebugUrl.'">';
// imagedestroy($InsertImage);
imagedestroy($TargetImageDebug);





echo '</div>';
echo "<br>";

