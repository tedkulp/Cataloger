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
         $this->smarty->assign('items',$categoryItems);
		$this->smartyBasics();

		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
