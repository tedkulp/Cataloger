<?php
		if (!isset($gCms)) exit;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
			
		$hm =& $gCms->GetHierarchyManager();
		
		if (isset($gCms->variables['content_id']))
		  {
            $curPageID = $gCms->variables['content_id'];
            $curPageNode = $hm->sureGetNodeById($curPageID);
            $curPage = $curPageNode->GetContent();
          }
        else if (isset($params['alias']))
          {
            $curPageNode = $hm->sureGetNodeByAlias($params['alias']);
            $curPage = $curPageNode->GetContent();
            $curPageID = $curPage->Id();
          }
		$curHierarchy = $curPage->Hierarchy();
        $curHierLen = strlen($curHierarchy);
        $curHierDepth = substr_count($curHierarchy,'.');

		$content = $this->getSubContent($curPageID);

        $categoryItems = array();
		foreach ($content as $thisPage)
			{
            if (!$thisPage->Active())
                {
                continue;
                }
            if ($thisPage->Id() == $curPage->Id())
                {
                continue;
                }
			$type_ok = false;
			$depth_ok = false;
			if ($thisPage->Type() == 'aliasmodule')
				{
				$thisPage = $thisPage->GetAliasContent();
				}
			if ($thisPage->Type() == 'catalogitem' &&
                      ($params['recurse'] == 'items_one' ||
                       $params['recurse'] == 'items_all' ||
                       $params['recurse'] == 'mixed_one' ||
                       $params['recurse'] == 'mixed_all'))
                {
                $type_ok = true;
                }
            else if ($thisPage->Type() == 'catalogcategory' &&
                          ($params['recurse'] == 'categories_one' ||
                           $params['recurse'] == 'categories_all' ||
                           $params['recurse'] == 'mixed_one' ||
                           $params['recurse'] == 'mixed_all'))
                    {
                    $type_ok = true;
                    }
            if (! $type_ok)
                {
                continue;
                }
            if (($params['recurse'] == 'items_one' ||
                 $params['recurse'] == 'categories_one' ||
                 $params['recurse'] == 'mixed_one') &&
                 substr_count($thisPage->Hierarchy(),'.') ==
                 	($curHierDepth + 1) &&
                 substr($thisPage->Hierarchy(),0,$curHierLen) == $curHierarchy)
                {
                $depth_ok = true;
                }
            else if (($params['recurse'] == 'items_all' ||
                 $params['recurse'] == 'categories_all' ||
                 $params['recurse'] == 'mixed_all') &&
                 substr($thisPage->Hierarchy(),0,$curHierLen) == $curHierarchy)
                    {
                    $depth_ok = true;
                    }
            if (! $depth_ok)
                {
                continue;
                }
			// in the category, and approved for addition
			$catThumbSize = $this->GetPreference('category_image_size_thumbnail',90);
			$itemThumbSize = $this->GetPreference('item_image_size_category',70);
			switch ($thisPage->Type())
				{
                case 'catalogitem':
				    $thisItem['image'] = $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_s_1_'.$itemThumbSize.'.jpg';
				    break;
				case 'catalogcategory':
				    $thisItem['image'] = $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_1_'.$catThumbSize.'.jpg';
				    break;
				}
			$thisItem['link'] = $thisPage->GetUrl();
			$thisItem['title'] = $thisPage->Name();
			$thisItem['menutitle'] = $thisPage->MenuText();
			$theseAttrs = $thisPage->getAttrs();
			foreach ($theseAttrs as $thisAttr)
				{
				$safeattr = strtolower(preg_replace('/\W/','',$thisAttr));
				$thisItem[$thisAttr] = $thisPage->GetPropertyValue($thisAttr);
				}
			array_push($categoryItems,$thisItem);
			}
        if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
            {
            usort($categoryItems,array("Cataloger", "contentalpha"));
            }
            
        $count = count($categoryItems);
        if (isset($_REQUEST['start']))
        	{
        	$start = $_REQUEST['start'];
        	}
        else
        	{
        	$start = 0;
        	}
        if (isset($params['items_per_page']))
        	{
        	$end = max($params['items_per_page'],1);
        	}
        else
        	{
        	$end = max($count,1);
        	}
        $thisUrl = $_SERVER['REQUEST_URI'];
        $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/','',$thisUrl);
		if (strpos($thisUrl,'?') === false)
			{
			$delim = '?';
			}
		else
			{
			$delim = '&';
			}
        if ($start > 0)
        	{
        	$this->smarty->assign('prev','<a href="'.$thisUrl.$delim.'start='.
        		max(0,$start-$end).'">'.$this->Lang('prev').'</a>');
        	$this->smarty->assign('prevurl',$thisUrl.$delim.'start='.
        		max(0,$start-$end));
        	}
        else
        	{
        	$this->smarty->assign('prev','');
        	$this->smarty->assign('prevurl','');
        	}
        if ($start + $end < $count)
        	{
        	$this->smarty->assign('next','<a href="'.$thisUrl.$delim.'start='.
        		($start + $end).'">'.$this->Lang('next').'</a>');
        	$this->smarty->assign('nexturl',$thisUrl.$delim.'start='.
        		($start + $end));
        	}
        else
        	{
        	$this->smarty->assign('next','');
        	$this->smarty->assign('nexturl','');
        	}
        $navstr = '';
        $pageInd = 1;
       	for ($i=0;$i<$count;$i+=$end)
       		{
       		if ($i == $start)
       			{
       			$navstr .= $pageInd;
       			}
       		else
       			{
       			$navstr .= '<a href="'.$thisUrl.$delim.'start='.$i.'">'.
       				$pageInd.'</a>';
       			}
       		$navstr .= ':';
       		$pageInd++;
       		}

		$navstr = rtrim($navstr,':');
        $categoryItems = array_splice($categoryItems, $start, $end);
        $this->smarty->assign('items',$categoryItems);
        if (strlen($navstr) > 1)
        	{
        	$this->smarty->assign('navstr',$navstr);
        	$this->smarty->assign('hasnav',1);
			}
		else
			{
			$this->smarty->assign('navstr','');
        	$this->smarty->assign('hasnav',0);
			}
        $imgcount = $this->GetPreference('category_image_count', '1');
        $fullSize = $this->GetPreference('category_image_size_hero', '400');
        $thumbSize = $this->GetPreference('category_image_size_thumbnail', '90');
        $imageArray = array();
        for ($i=1;$i<=$imgcount;$i++)
            {
              // was $thisPage->Alias()
            array_push($imageArray, $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_cf_'.$i.'_'.$fullSize.'.jpg');
            array_push($imageArray, $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_ct_'.$i.'_'.$thumbSize.'.jpg');

            $this->smarty->assign('image_'.$i.'_url',$gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_cf_'.$i.'_'.$fullSize.'.jpg');
            $this->smarty->assign('image_thumb_'.$i.'_url',$gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_ct_'.$i.'_'.$thumbSize.'.jpg'
            );
            }
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
 
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
?>
