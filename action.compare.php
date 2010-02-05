<?php
		if (!isset($gCms)) exit;
		$itemlist = array();
		if (isset($_REQUEST['items']))
			{
			$params['items'] = $_REQUEST['items'];
			}
		if (! is_array($params['items']))
			{
			$params['items'] = explode(',',$params['items']);	
			}
		foreach ($params['items'] as $thisItem)
			{
			$page = $this->getCatalogItem($thisItem);
			array_push($itemlist,$page);	
			}
		if (count($itemlist)>0)
			{
			$vars = &$gCms->variables;
			$this->smarty->assign('attrlist',$vars['catalog_attrs']);
			}
		else
			{
			// blank array
			$this->smarty->assign('attrlist',$itemlist);
			}

 		$this->smarty->assign('items',$itemlist);
		$this->smartyBasics();
		echo $this->ProcessTemplateFromDatabase($this->getTemplateFromAlias($params['sub_template']));
?>
