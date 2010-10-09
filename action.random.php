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

			$list = $this->getCatalogItemsIDList($params);

        $count = min(count($list),$params['count']);
        $thisUrl = $_SERVER['REQUEST_URI'];
        $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/','',$thisUrl);
        if ($count == 0)
        	{
        	$this->smarty->assign('items',array());
        	}
        else
        	{
        	$categoryItemKeys = array_rand($list, $count);
					if (!is_array($categoryItemKeys))
						{
						$categoryItemKeys = array($categoryItemKeys);
						}
        	$catTmp = array();
        	foreach($categoryItemKeys as $thisKey)
        		{
						$node = $this->getCatalogItemById($list[$thisKey]);
        		array_push($catTmp,$node);
        		}
        	$this->smarty->assign('items',$catTmp);
        	}
        
 		$this->smartyBasics();
		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
