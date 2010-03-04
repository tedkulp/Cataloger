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

		if (!isset($params['global_sort']) || $params['global_sort']=='date')
			{
        	usort($categoryItems,array("Cataloger", "created"));
        	}
		else
			{
		    usort($categoryItems,array("Cataloger", "chrono"));
			}

		if (isset($params['global_sort_dir']) && $params['global_sort_dir']=='asc')
			{
			$categoryItems = array_reverse($categoryItems);
			}
			
        $count = count($categoryItems);
        $start = 0;
        $end = min($count,$params['count']);
        $thisUrl = $_SERVER['REQUEST_URI'];
        $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/','',$thisUrl);
        $categoryItems = array_splice($categoryItems, $start, $end);
        $this->smarty->assign('items',$categoryItems);
 		
		$this->smartyBasics();
		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
