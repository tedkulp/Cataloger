<?php
if (!isset($gCms)) exit;

		$showMissing = '_'. $this->GetPreference('show_missing','1');

		foreach ($params as $key=>$val)
			{
			if (isset($params[$key]))
				{
				$this->smarty->assign($key, $params[$key]);
				}
			else
				{
				$this->smarty->assign($key,'');
				}
			}

		$content = $this->getAllContent();

        $printableItems = array();
        $showAttrs = explode(',',$params['fieldlist']);
    	$this->smarty->assign('attrlist',$showAttrs);
		$lastCat = 'none';
		foreach ($content as $thisPage)
			{
            if (!$thisPage->Active())
                {
                continue;
                }
			if ($thisPage->Type() == 'aliasmodule')
				{
				$thisPage = $thisPage->GetAliasContent();
				}
			if ($thisPage->Type() == 'catalogcategory')
				{
				$lastCat = $thisPage->MenuText();
				continue;
				}
			if ($thisPage->Type() != 'catalogitem')
                {
                continue;
                }
			// approved for viewing
			$printThumbSize = $this->GetPreference('item_image_size_catalog',100);
			$thisItem['image'] = $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_p_1_'.$printThumbSize.$showMissing.'.jpg';
			$thisItem['link'] = $thisPage->GetUrl();
			$thisItem['title'] = $thisPage->MenuText();
			$thisItem['cat'] = $lastCat;
			$theseAttrs = $thisPage->getAttrs();
			foreach ($theseAttrs as $thisAttr)
				{
				if (! in_array($thisAttr,$showAttrs))
					{
					continue;
					}
				$safeattr = strtolower(preg_replace('/\W/','',$thisAttr));
				$thisItem[$thisAttr] = $thisPage->GetPropertyValue($thisAttr);
				}
			array_push($printableItems,$thisItem);
			}
            
        if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
            {
            usort($printableItems,array("Cataloger", "contentalpha"));
            }
         
        $this->smarty->assign('items',$printableItems);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
?>
