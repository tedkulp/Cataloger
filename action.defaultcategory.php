<?php
		if (!isset($gCms)) exit;
		$showMissing = '_'. $this->GetPreference('show_missing','1');

		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
 		
 		list($curPage,$categoryItems) = $this->getCatalogItemsList($params);
 		
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
            array_push($imageArray, $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_cf_'.$i.'_'.$fullSize.$showMissing.'.jpg');
            array_push($imageArray, $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_ct_'.$i.'_'.$thumbSize.$showMissing.'.jpg');

            $this->smarty->assign('image_'.$i.'_url',$gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_cf_'.$i.'_'.$fullSize.$showMissing.'.jpg');
            $this->smarty->assign('image_thumb_'.$i.'_url',$gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$curPage->Alias().'_ct_'.$i.'_'.$thumbSize.$showMissing.'.jpg'
            );
            }
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
 
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
?>
