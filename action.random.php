<?php
		if (!isset($gCms)) exit;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
 		if (! isset($params['recurse']))
 			{
 			$params['recurse'] = 'items_all';
 		 	}
		
 		list($curPage,$categoryItems) = $this->getCatalogItemsList($params);
        $count = min(count($categoryItems),$params['count']);
        $thisUrl = $_SERVER['REQUEST_URI'];
        $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/','',$thisUrl);
        if ($count == 1)
        	{
        	$thisKey = array_rand($categoryItems,1);
         $catTmp = array();
       	array_push($catTmp,$categoryItems[$thisKey]);
        	$this->smarty->assign('items',$catTmp);
        	}
        else if ($count == 0)
        	{
        	$this->smarty->assign('items',array());
        	}
        else
        	{
        	$categoryItemKeys = array_rand($categoryItems, $count);
        	$catTmp = array();
        	foreach($categoryItemKeys as $thisKey)
        		{
        		array_push($catTmp,$categoryItems[$thisKey]);
        		}
        	$this->smarty->assign('items',$catTmp);
        	}
        
 		
		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
