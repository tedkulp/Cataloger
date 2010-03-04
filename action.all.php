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

        if (isset($params['global_sort']))
 			{
			if ($params['global_sort'] == 'alpha')
            	{
            	usort($categoryItems,array("Cataloger", "contentalpha"));
            	}
			else if ($params['global_sort'] == 'date')
				{
	            usort($categoryItems,array("Cataloger", "created"));
				}
			else if ($params['global_sort'] == 'mdate')
				{
	            usort($categoryItems,array("Cataloger", "chrono"));
				}
			}
		if (isset($params['global_sort_dir']) && $params['global_sort_dir']=='asc')
			{
			$categoryItems = array_reverse($categoryItems);
			}

        $this->smarty->assign('items',$categoryItems);
		$this->smartyBasics();

		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
