<?php
	require_once(dirname(__FILE__).'/../../config.php');
	
	$spec = $_GET['i'];
	$spec = preg_replace('/(\.\.|\/)/','',$spec);
	if (! preg_match('/\.jpg$/i',$spec))
		{
		header("Status: 404 Not Found");
		return;
		}
	if (file_exists($config['uploads_path'].'/images/catalog/'.$spec))
		{
		header("Location: ".$config['uploads_url'].'/images/catalog/'.$spec);
		}
	else
		{
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
		if (strlen($type) > 1)
			{
			$srcSpec .= 'cat_';
			}
		$srcSpec .= 'src_'.$imgno.'.jpg';
		if (!file_exists($srcSpec))
			{
			header("Status: 404 Not Found");
			return;
			}
		

		$destSpec = $config['uploads_path'].'/images/catalog/'.$spec.'.jpg';
		
		imageTransform($srcSpec, $destSpec, $size, $config);
		header("Location: ".$config['uploads_url'].
			'/images/catalog/'.$spec.'.jpg');
		}


    function imageTransform($srcSpec, $destSpec, $size, &$config, $aspect_ratio='')
    {
        // skip the require until we need it
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