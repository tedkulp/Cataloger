<?php
	require_once(dirname(__FILE__).'/../../config.php');
	
	$spec = $_GET['i'];
	$spec = preg_replace('/(\.\.|\/)/','',$spec);
	if (! preg_match('/\.jpg$/i',$spec))
		{
		// not found, but instead of a 404, we'll return a "no-image" image
		header("Location: ".$config['root_url'].
			'/modules/Cataloger/images/no-image.gif');
		return;
		}
	$sized = @stat($config['uploads_path'].'/images/catalog/'.$spec);
	$spec = substr($spec, 0, strrpos($spec,'.'));
	$parts = explode('_',$spec);
	$parts = array_reverse($parts);
		
	$size = $parts[0];
	$imgno = $parts[1];
	$type = $parts[2];
	$name = '';
	for ($j=count($parts)-1;$j>2;$j--)
		{
		$name .= $parts[$j].'_';
		}
	$srcSpec = $config['uploads_path'].'/images/catalog_src/'.$name;
	$srcSpec .= 'src_'.$imgno.'.jpg';
	$orig = @stat($srcSpec);
	if ($orig === false)
		{
		// once again, 404 is subverted
		header("Location: ".$config['root_url'].
			'/modules/Cataloger/images/no-image.gif');
		return;
		}
	if (!$sized || $sized['mtime'] < $orig['mtime'])
		{
		// we don't have a cached version we can use
		$destSpec = $config['uploads_path'].'/images/catalog/'.$spec.'.jpg';
		// so we make one
		imageTransform($srcSpec, $destSpec, $size, $config);
		}

	header("Location: ".$config['uploads_url'].
		'/images/catalog/'.$spec.'.jpg');
	return;


    function imageTransform($srcSpec, $destSpec, $size, &$config, $aspect_ratio='')
    {
        require_once(dirname(__FILE__).'/../../lib/filemanager/ImageManager/Classes/Transform.php');

        $it = new Image_Transform;
        $img = $it->factory($config['image_manipulation_prog']);
        $img->load($srcSpec);
        if ($img->img_x < $img->img_y)
            {
            $long_axis = $img->img_y;
            }
        else
            {
            $long_axis = $img->img_x;
            }

        if ($long_axis > $size)
            {
            $img->scaleByLength($size);
            $img->save($destSpec, 'jpeg');
            }
        else
            {
            $img->save($destSpec, 'jpeg');
            }
        $img->free();
    }
?>