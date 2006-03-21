<?php
if (!isset($gCms)) exit;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$content = ContentManager::GetAllContent(false);
		$curPageID = $this->cms->variables['content_id'];
        $printableItems = array();
        $showAttrs = explode(',',$params['fieldlist']);
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
			$thisItem['image'] = $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_p_1_'.$printThumbSize.'.jpg';
			$thisItem['link'] = $thisPage->GetUrl();
			$thisItem['title'] = $thisPage->MenuText();
			$thisItem['cat'] = $lastCat;
			$theseAttrs = $thisPage->getAttrs();
			foreach ($theseAttrs as $thisAttr)
				{
error_log($thisAttr);
				if (! in_array($thisAttr,$showAttrs))
					{
					continue;
					}
error_log('!');
				$safeattr = strtolower(preg_replace('/\W/','',$thisAttr));
				$thisItem[$safeattr] = $thisPage->GetPropertyValue($thisAttr);
				}
			array_push($printableItems,$thisItem);
			}
            
        $this->smarty->assign('items',$printableItems);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
?>
