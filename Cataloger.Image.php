<?php
	require_once(dirname(__FILE__).'/../../config.php');
	
	$spec = $_GET['i'];
	$debug = isset($_GET['debug']);
	$anticache = isset($_GET['ac']);
	$spec = preg_replace('/(\.\.|\/)/','',$spec);
	if (! preg_match('/\.jpg$/i',$spec))
		{
		return returnMissing($config['root_url'], true, $debug);
		}
		
	$sized = @stat($config['uploads_path'].'/images/catalog/'.$spec);

	$spec = substr($spec, 0, strrpos($spec,'.'));
	$parts = explode('_',$spec);
	$parts = array_reverse($parts);
		
	$showMissing = $parts[0] == '1';
	$size = $parts[1];
	$imgno = $parts[2];
	$type = $parts[3];
	$name = '';
	for ($j=count($parts)-1;$j>3;$j--)
		{
		$name .= $parts[$j].'_';
		}
	$srcSpec = $config['uploads_path'].'/images/catalog_src/'.$name;
	$srcSpec .= 'src_'.$imgno.'.jpg';
	if ($debug)
		{
		error_log("CatalogerImage: src image ".$srcSpec);
		}
	$orig = @stat($srcSpec);
	$newImage = false;
	if ($orig === false)
		{
		if ($debug) error_log("Can't find ".$srcSpec);
		return returnMissing($config['root_url'], $showMissing);
		}
	if (!$sized || $sized['mtime'] < $orig['mtime'])
		{
		if ($debug) error_log("newer than existant, transforming");
		// we don't have a cached version we can use
		$destSpec = $config['uploads_path'].'/images/catalog/'.$spec.'.jpg';
		// so we make one
		imageTransform($srcSpec, $destSpec, $size, $config);
		$newImage = true;
		}

	$dest = "Location: ".$config['uploads_url'].
		'/images/catalog/'.$spec.'.jpg';
	if ($newImage || $anticache)
		{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, cachehack=".time());
		header("Cache-Control: no-store, must-revalidate");
		header("Cache-Control: post-check=-1, pre-check=-1", false);
		$dest .= '?ac=';
		for ($i=0;$i<5;$i++)
			{
			$dest .= rand(0,9);
			}
		}
	if ($debug) error_log($dest);
	header($dest);
	return;


	function returnMissing($rootUrl, $showMissing, $debug=false)
	{
		// if so desired, don't 404, but send an image
		if ($debug)
			{
			error_log("CatalogerImage: no image at $rootUrl");
			}
		if (! $showMissing)
			{
			header("Location: ".$rootUrl.
				'/modules/Cataloger/images/trans.gif');
			}
		else
			{
			header("Location: ".$rootUrl.
				'/modules/Cataloger/images/no-image.gif');			
			}
		return;
	
	}

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