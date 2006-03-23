<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		if (isset($params['item_sort_order']) &&
			$params['item_sort_order'] != 'nochange')
			{
			// update all item sort order
			$query = "update ".
				cms_db_prefix()."content_props set content=? where ".
				cms_db_prefix()."content_props.content_id=".
				cms_db_prefix()."content.content_id and ".
				cms_db_prefix()."content.type='catalogcategory' and ".
				cms_db_prefix()."content_props.prop_name='sort_order'";
			$dbresult = $db->Execute($query,array($params['item_sort_order']));
			}
		if (isset($params['category_recurse']) &&
			$params['category_recurse'] != 'nochange')
			{
			// update display rules
			$query = "update ".
				cms_db_prefix()."content_props  set content=? where ".
				cms_db_prefix()."content_props.content_id=".
				cms_db_prefix()."content.content_id and ".
				cms_db_prefix()."content.type='catalogcategory' and ".
				cms_db_prefix()."content_props.prop_name='recurse'";
			$dbresult = $db->Execute($query,array($params['category_recurse']));
			
			}
		if (isset($params['items_per_page']) &&
			$params['items_per_page'] != -1)
			{
			// update display rules
			$query = "update ".
				cms_db_prefix()."content_props  set content=? where ".
				cms_db_prefix()."content_props.content_id=".
				cms_db_prefix()."content.content_id and ".
				cms_db_prefix()."content.type='catalogcategory' and ".
				cms_db_prefix()."content_props.prop_name='items_per_page'";
			$dbresult = $db->Execute($query,array($params['items_per_page']));
			}


		$params['message'] = $this->Lang('globallyupdated');
        $this->DoAction('globalops', $id, $params);

?>
