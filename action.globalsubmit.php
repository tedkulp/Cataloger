<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		if (isset($params['item_sort_order']) &&
			$params['item_sort_order'] != 'nochange')
			{
			// update all item sort order
			$query = "update ".
				cms_db_prefix()."content_props cp, ".cms_db_prefix().
				"content c set cp.content=? where ".
				"cp.content_id=c.content_id and c.type='catalogcategory' ".
				"and cp.prop_name='sort_order'";
			$dbresult = $db->Execute($query,array($params['item_sort_order']));
			}
		if (isset($params['category_recurse']) &&
			$params['category_recurse'] != 'nochange')
			{
			// update display rules
			$query = "update ".
				cms_db_prefix()."content_props cp, ".cms_db_prefix().
				"content c set cp.content=? where ".
				"cp.content_id=c.content_id and c.type='catalogcategory' ".
				"and cp.prop_name='recurse'";
			$dbresult = $db->Execute($query,array($params['category_recurse']));
			
			}
		if (isset($params['items_per_page']) &&
			$params['items_per_page'] != -1)
			{
			// update display rules
			$query = "update ".
				cms_db_prefix()."content_props cp, ".cms_db_prefix().
				"content c set cp.content=? where ".
				"cp.content_id=c.content_id and c.type='catalogcategory' ".
				"and cp.prop_name='items_per_page'";
			$dbresult = $db->Execute($query,array($params['items_per_page']));
			}

	//	$params['message'] = $this->Lang('globallyupdated');
	$params['message'] = $query.' '.$params['category_recurse'];
        $this->DoAction('globalops', $id, $params);

?>
