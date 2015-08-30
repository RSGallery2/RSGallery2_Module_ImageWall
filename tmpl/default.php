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
$TargetImage = imagecreate($TargetWidth, $TargetHeight);
$TargetImageName = 'ImageWall.png';
//  $fullPath_original = JPATH_ROOT.$rsgConfig->get('imgPath_original') . '/';
$RsgalleryImagePath = realpath(JPATH_ROOT.$rsgConfig->get('imgPath_original').'/../');
//echo '$RsgalleryImagePath: '.$RsgalleryImagePath.'<br>';
$RsgalleryImageTempPath =  realpath(JPATH_ROOT.$rsgConfig->get('imgPath_original').'/../tmp');
//echo '$RsgalleryImageTempPath: '.$RsgalleryImageTempPath.'<br>';
if (!file_exists($RsgalleryImageTempPath)) {
	mkdir($RsgalleryImageTempPath, 0755);
}
// Server file path
$TargetImagePath = $RsgalleryImageTempPath .'/'. $TargetImageName;
//echo '$TargetImagePath: '.$TargetImagePath.'<br>';

// URL file path
$TargetImageUrl= JRoute::_('images/rsgallery/tmp/'.$TargetImageName);
//echo '$TargetImageUrl: '.$TargetImageUrl.'<br>';

//$black = imagecolorallocate($image,0,0,0);
//$grey_shade = imagecolorallocate($image,40,40,40);
//$white = imagecolorallocate($image,255,255,255);
$bg = imagecolorallocate ( $TargetImage, 0, 127, 255 );
//$bg = imagecolorallocate ( $TargetImage, 255, 255, 255 );
//imagefilledrectangle($TargetImage,0,0,120,20,$bg);

imagealphablending($TargetImage, false);
imagesavealpha($TargetImage, true);

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

/*
		echo '$ItemIdx: ' . $ItemIdx . '<br>';

		if($ColIdx > 2){
//			break;
		}
		echo '$ColIdx: ' . $ColIdx . '<br>';
*/

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

		echo '$ColIdx: ' . $ColIdx . '<br>';
		echo '$RowIdx: ' . $RowIdx . '<br>';
		echo '$ItemIdx: ' . $ItemIdx . '<br>';

		echo '$InsertOffsetX: '.$InsertOffsetX.'<br>';
		echo '$InsertOffsetY: '.$InsertOffsetY.'<br>';

//		imagecopymerge($TargetImage, $InsertImage, $InsertOffsetX, $InsertOffsetY, 0, 0,
// 			$InsertWidth, $InsertHeight, 100);

		imagecopyresized($TargetImage, $InsertImage, $InsertOffsetX, $InsertOffsetY, 0, 0,
			$InsertWidth, $InsertHeight, $SourceWidth, $SourceHeight);



//          return GD2::createSquareThumb( $source, $target, $rsgConfig->get('thumb_width') ); 
//          return GD2::resizeImage($source, $target, $targetWidth); 
//			img.utils.php


	}
}


imagepng($TargetImage, $TargetImagePath);
echo '<img src="'.$TargetImageUrl.'">';
// imagedestroy($InsertImage);
imagedestroy($TargetImage);

/*
$Image=imagecreate($imgWidth, $imgHeight);
$colorWhite=imagecolorallocate($Image, 255, 255, 255);
$colorGrey=imagecolorallocate($Image, 192, 192, 192);
$colorBlue=imagecolorallocate($Image, 0, 0, 255);

// Create border around image
imageline($Image, 0, 0, 0, $imgHeight, $colorGrey);
imageline($Image, 0, 0, $imgWidth, 0, $colorGrey);
imageline($Image, $imgWidth-1, 0, $imgWidth-1, $imgHeight-1, $colorGrey);
imageline($Image, 0, $imgHeight-1, $imgWidth-1, $imgHeight-1, $colorGrey);

// Create grid
for ($i=1; $i<($imgWidth/$grid); $i++)
    {imageline($Image, $i*$grid, 0, $i*$grid, $imgHeight, $colorGrey);}
for ($i=1; $i<($imgHeight/$grid); $i++)
    {imageline($Image, 0, $i*$grid, $imgWidth, $i*$grid, $colorGrey);}


for ($i=0; $i<count($graphValues)-1; $i++)
    {imageline($Image, $i*$space, ($imgHeight-$graphValues[$i]), ($i+1)*$space, ($imgHeight-$graphValues[$i+1]), $colorBlue);}

// Output graph and clear image from memory
imagepng($Image);
imagedestroy($Image);


================================================
<img src="create.php?txt=some%20text" />

File create.php:

<?php
$txt = $_GET['txt'];
$im = @imagecreate(400, 300) or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 0, 0);
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 55, 55, 55,  $txt, $text_color);
header("Content-Type: image/png");
imagepng($im);
imagedestroy($im);
?>
	
*/
	



echo '</div>';

