<?php
		if (!isset($gCms)) exit;
		if (!isset($params['name']))
			{
			echo '<!-- please specify a Cataloger variable name to retrieve -->';
			return;
			}

		$pageattrs = &$gCms->variables['catalog_attrs'];
	    $manager =& $gCms->GetHierarchyManager();
		$pageinfo = &$gCms->variables['pageinfo'];
		if ($pageinfo->content_id != -1)
			{
			$node =& $manager->sureGetNodeById($pageinfo->content_id);
			$content =& $node->GetContent();
			if ($content->Type() != 'catalogitem' && $content->Type() != 'catalogcategory' && $content->Type() != 'catalogprintable')
				{
				return;
				}
			$content->PopulateParams($params);
			$found = false;
	    	foreach ($pageattrs as $thisParam)
				{
				$safeattr = strtolower(preg_replace('/\W/','', $thisParam->attr));
				if ($params['name'] == $safeattr)
					{
					$tmp = $content->GetPropertyValue($thisParam->attr);
					$found = true;
					}
				}
			}

		if (! $found)
			{
			if (isset($params['default']))
				{
				$tmp = $params['default'];
				}
			else
				{
				$tmp = '';
				}
			}
		echo $tmp;
?>