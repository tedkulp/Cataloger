<?php
		if (!isset($gCms)) exit;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$content = ContentManager::GetAllContent(false);
		$curPageID = $this->cms->variables['content_id'];

		for ($i=0;$i<count($content);$i++)
			{
			if ($content[$i]->Id() == $curPageID)
				{
				$curPage = $content[$i];
				}
			}
		$curHierarchy = $curPage->Hierarchy();

        $curHierLen = strlen($curHierarchy);
        $curHierDepth = substr_count($curHierarchy,'.');
        $categoryItems = array();
        if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
            {
            usort($content,array("Cataloger", "contentalpha"));
            }
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
				    $thisItem['image'] = $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_s_1_'.$itemThumbSize.'.jpg';
				    break;
				case 'catalogcategory':
				    $thisItem['image'] = $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_1_'.$catThumbSize.'.jpg';
				    break;
				}
			$thisItem['link'] = $thisPage->GetUrl();
			$thisItem['title'] = $thisPage->MenuText();
			array_push($categoryItems,$thisItem);
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
            array_push($imageArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_cf_'.$i.'_'.$fullSize.'.jpg');
            array_push($imageArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_'.$i.'_'.$thumbSize.'.jpg');

            $this->smarty->assign('image_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_cf_'.$i.'_'.$fullSize.'.jpg');
            $this->smarty->assign('image_thumb_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_'.$i.'_'.$thumbSize.'.jpg'
            );
            }
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
?>