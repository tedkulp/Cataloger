<?php
		if (!isset($gCms)) exit;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$imageArray = array();
		$thumbArray = array();
        $imgcount = $this->GetPreference('item_image_count', '2');
        $fullSize = $this->GetPreference('item_image_size_hero', '400');
        $thumbSize = $this->GetPreference('item_image_size_thumbnail', '70');
        for ($i=1;$i<=$imgcount;$i++)
            {
            array_push($imageArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_f_'.$i.'_'.$fullSize.'.jpg');
            array_push($thumbArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_t_'.$i.'_'.$thumbSize.'.jpg');

            $this->smarty->assign('image_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_f_'.$i.'_'.$fullSize.'.jpg');
            $this->smarty->assign('image_thumb_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_t_'.$i.'_'.$thumbSize.'.jpg'
            );
            }
		$this->smarty->assign_by_ref('attrlist',$params['attrlist']);
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);	
?>