<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initAdminNav($id, $params, $returnid);

		$typeids = array();
		$query = 'SELECT type_id, name FROM ' .
				cms_db_prefix(). 'module_catalog_template_type';
        $dbresult = $db->Execute($query);
        while ($dbresult !== false && $row = $dbresult->FetchRow())
        {
        	$typeids[$row['name']] = $row['type_id'];
        }

		if (isset($params['template_id']))
			{
			// editing a template
			$query = 'SELECT title, template, type_id FROM ' .
				cms_db_prefix(). 'module_catalog_template WHERE id=?';
			$dbresult = $db->Execute($query,array($params['template_id']));
			$row = $dbresult->FetchRow();
			$templateid = $params['template_id'];
			$title=$row['title'];
			$template=$row['template'];
			$type_id=$row['type_id'];
			$this->smarty->assign('op', $this->Lang('edittemplate'));
			}
		else
			{
			// adding a template
			$templateid = '';
			$title='';
			$template='';
			$this->smarty->assign('op', $this->Lang('addtemplate'));
			}
        $query = "SELECT attribute, type_id FROM ".cms_db_prefix()."module_catalog_attr";
        $dbresult = $db->Execute($query);
        $attrs = '<h3>'.$this->Lang('title_item_template_vars').'</h3>{$title}, {$notes}, ';
        $cattrs = '<h3>'.$this->Lang('title_cat_template_vars').'</h3>{$title}, {$notes}, {$prev}, {$prevurl}, {$navstr}, {$next}, {$nexturl}, {$items}, ';
        $pcattrs = '<h3>'.$this->Lang('title_catalog_template_vars').'</h3>{$items}, {$attrlist}';
        $compattrs = '<h3>'.$this->Lang('title_compare_template_vars').'</h3>{$items}, {$attrlist}';
        $feattrs = '<h3>'.$this->Lang('title_feature_template_vars').'</h3>{$items}';

        while ($dbresult !== false && $row = $dbresult->FetchRow())
        	{
            $safeattr = strtolower(preg_replace('/\W/','',$row['attribute']));
            if ($row['type_id'] == CTEMPLATE_ITEM)
            	{
            	$attrs .= '{$'.$safeattr.'}, ';
            	}
            else if ($row['type_id'] == CTEMPLATE_CATEGORY)
            	{
				$cattrs .= '{$'.$safeattr.'}, ';
				}
        	}
        $image_count = $this->GetPreference('item_image_count', '1');
        for ($i=1;$i<=$image_count;$i++)
        	{
        	$attrs .= '{$image_'.$i.'_url}, {$image_thumb_'.$i;
        	$attrs .= '_url}, {$src_image_'.$i.'_url}, ';
        	}
        $attrs .= '{$image_url_array}, ';
        $attrs .= '{$src_image_url_array}, ';
        $attrs .= '{$image_thumb_url_array}';
        $file_count = $this->GetPreference('item_file_count', 0);
        for ($i=1;$i<=$file_count;$i++)
        	{
        	$attrs .= '{$file_count}, {$file_'.$i.'_name}, {$file_'.$i.'_url}, {$file_'.$i;
        	$attrs .= '_ext}, ';
        	}
        $attrs .= '{$file_url_array}, {$file_name_array}, ';
        $attrs .= '{$file_ext_array}';
        $attrs = rtrim($attrs,', ');

        $image_count = $this->GetPreference('category_image_count', '1');
        for ($i=1;$i<=$image_count;$i++)
        	{
        	$cattrs .= '{$image_'.$i.'_url}, {$image_thumb_'.$i;
        	$cattrs .= '_url}, {$src_image_'.$i.'_url}, ';
        	}
        $cattrs .= '{$image_url_array}, ';
        $cattrs .= '{$src_image_url_array}, ';
        $cattrs .= '{$image_thumb_url_array}';
        $cattrs = rtrim($cattrs,', ');
        $cattrs .= '<h3>$items array contents:</h3>';
        $cattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attrname</i>';
        $pcattrs .= '<h3>$items array contents:</h3>';
        $pcattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attrname</i>';
        $pcattrs .= '<h3>$attrlist array contents:</h3>';
        $pcattrs .= '$attrlist[]->attr, $attrlist[]->safe';
        $compattrs .= '<h3>$items array contents:</h3>';
        $compattrs .= '$items[].title, $items[].link, $items[].image, $items[].<i>attrname</i>';
        $compattrs .= '<h3>$attrlist array contents:</h3>';
        $compattrs .= '$attrlist[]->attr, $attrlist[]->safe';
        $feattrs .= '<h3>$items array contents:</h3>';
        $feattrs .= '$items[].title, $items[].link, $items[].image, $items[].cat, $items[].<i>attrname</i>';

        
		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submittempl', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('hidden',$this->CreateInputHidden($id, 'template_id', $templateid));
        $this->smarty->assign('title_title',$this->Lang('title_title'));
		$this->smarty->assign('title_template',$this->Lang('title_template'));
		$this->smarty->assign('title_template_type',$this->Lang('title_template_type'));
		$this->smarty->assign('title_avail_attrs',$this->Lang('title_avail_attrs'));
		if (isset($type_id))
			{
			if ($type_id == CTEMPLATE_ITEM)
				{
				$this->smarty->assign_by_ref('avail_attrs',$attrs);
				}
			else if ($type_id == CTEMPLATE_CATEGORY)
				{
				$this->smarty->assign_by_ref('avail_attrs',$cattrs);		
				}
			else if ($type_id == CTEMPLATE_CATALOG)
				{
				$this->smarty->assign_by_ref('avail_attrs',$pcattrs);		
				}
			else if ($type_id == CTEMPLATE_COMPARISON)
				{
				$this->smarty->assign_by_ref('avail_attrs',$compattrs);		
				}
			else if ($type_id == CTEMPLATE_FEATURE)
				{
				$this->smarty->assign_by_ref('avail_attrs',$feattrs);		
				}
			}
		else
			{
			$this->smarty->assign('avail_attrs',$attrs.', '.$cattrs.', '.$feattrs);
			}

//		$this->smarty->assign('title_avail_imattrs',$this->Lang('title_avail_imattrs'));
//		$this->smarty->assign_by_ref('avail_imattrs',$imattrs);
		$this->smarty->assign('input_template_type',$this->CreateInputDropdown($id, 'type_id', $typeids, -1, isset($type_id)?$type_id:''));

        $this->smarty->assign('input_title',$this->CreateInputText($id, 'title', $title, 20, 255));
        $this->smarty->assign('input_template',$this->CreateTextArea(false, $id, $template, 'templ'));

		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
		echo $this->ProcessTemplate('edittemplate.tpl');
?>