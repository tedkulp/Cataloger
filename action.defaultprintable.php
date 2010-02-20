<?php
		if (!isset($gCms)) exit;

		if ($this->GetPreference('flush_cats','0') == '1')
			{
			while (ob_get_level() >0)
    			{
    			ob_end_flush();
    			}
			}

		$showMissing = '_'. $this->GetPreference('show_missing','1');
		$params['alias']='/';
		$params['recurse'] = 'items_all';
		
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
 		
 		list($curPage,$pageItems) = $this->getCatalogItemsList($params);

        if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
            {
            usort($pageItems,array("Cataloger", "contentalpha"));
            }
            
        $count = count($pageItems);
        $fldlist = explode(',',$params['fieldlist']);
        foreach($fldlist as $tk=>$tv)
            {
            $fldlist[$tk] = strtolower(preg_replace('/\W/','', $tv));
            }
        $this->smarty->assign('items',$pageItems);
        $fullSize = $this->GetPreference('item_image_size_catalog', '100');
        $imageArray = array();
        $srcImgArray = array();
        for ($i=1;$i<=$imgcount;$i++)
            {
            array_push($imageArray, 
            	$this->imageSpec($curPage->Alias(), 'ctf', $i, $fullSize));
			array_push($srcImgArray,
				$this->srcImageSpec($curPage->Alias(), $i));

            $this->smarty->assign('image_'.$i.'_url',
            	$this->imageSpec($curPage->Alias(), 'ctf', $i, $fullSize));
			$this->smarty->assign('src_image_'.$i.'_url',
				$this->srcImageSpec($params['alias'], $i));

            }
		$this->smarty->assign('attrlist',$fldlist);
		$this->smarty->assign('attrlistf',$params['attrlist']);

		$this->smarty->assign_by_ref('image_url_array',$imageArray);
		$this->smarty->assign_by_ref('src_image_url_array',$srcImgArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
		$this->smartyBasics();
 
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
?>
