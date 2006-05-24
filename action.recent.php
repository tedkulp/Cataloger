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
        usort($categoryItems,array("Cataloger", "chrono"));
            
        $count = count($categoryItems);
        $start = 0;
        $end = min($count,$params['count']);
        $thisUrl = $_SERVER['REQUEST_URI'];
        $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/','',$thisUrl);
        $categoryItems = array_splice($categoryItems, $start, $end);
        $this->smarty->assign('items',$categoryItems);
 		
		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
