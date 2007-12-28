<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initAdminNav($id, $params, $returnid);

		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitattrs', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));

        $attributes = array();
        $query = "SELECT id, attribute, type_id, is_textarea FROM ".cms_db_prefix(). "module_catalog_attr ORDER BY attribute";
        $dbresult = $db->Execute($query);
		$countbytype = array();
		$countbytype[1]=0;
		$countbytype[2]=0;
		$countbytype[3]=0;
        while ($dbresult !== false && $row = $dbresult->FetchRow())
        {
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr_'.$row['type_id'].'_'.$countbytype[$row['type_id']],
				$row['attribute'], 25, 255);
       	   $onerow->hidden = $this->CreateInputHidden($id, 'old_'.$row['type_id'].'_'.$countbytype[$row['type_id']],$row['id']);
           $onerow->type = $row['type_id'];
           $onerow->istext = $this->CreateInputCheckbox($id, 'istext_'.$row['type_id'].'_'.$countbytype[$row['type_id']],
				1, $row['is_textarea']);
           $onerow->delete = $this->CreateInputCheckbox($id, 'delete_'.$row['type_id'].'_'.$countbytype[$row['type_id']],
				1, 0);
		   $countbytype[$row['type_id']]++;
	       array_push($attributes, $onerow);
        }
        for ($i=0;$i<3;$i++)
        {
		   for ($j=1;$j<4;$j++)
				{
	       		$onerow = new stdClass();
           		$onerow->input = $this->CreateInputText($id, 'attr_'.$j.'_'.$countbytype[$j], '', 25, 255);
	            $onerow->istext = $this->CreateInputCheckbox($id, 'istext_'.$j.'_'.$countbytype[$j],
					1, 0);
	           	$onerow->delete = '';
				$onerow->hidden = '';
           		$onerow->type = $j;
				$countbytype[$j]++;
           		array_push($attributes, $onerow);
				}
        }
//debug_display($attributes);
        $this->smarty->assign('tab_headers',$this->StartTabHeaders().
            $this->SetTabHeader('item',$this->Lang('title_item_tab')).
            $this->SetTabHeader('category',$this->Lang('title_category_tab')).
            $this->SetTabHeader('catalog',$this->Lang('title_printable_tab')).
            $this->EndTabHeaders().$this->StartTabContent());
        $this->smarty->assign('end_tab',$this->EndTab());
        $this->smarty->assign('tab_footers',$this->EndTabContent());
        $this->smarty->assign('start_item_tab',$this->StartTab('item'));
        $this->smarty->assign('start_category_tab',$this->StartTab('category'));
        $this->smarty->assign('start_catalog_tab',$this->StartTab('catalog'));

        $this->smarty->assign('message', isset($params['message'])?$params['message']:'');
        $this->smarty->assign('attribute_inputs', $attributes);
        $this->smarty->assign('title_item_attributes', $this->Lang('title_item_tab'));
        $this->smarty->assign('title_catalog_attributes', $this->Lang('title_printable_tab'));
        $this->smarty->assign('title_category_attributes', $this->Lang('title_category_tab'));
        $this->smarty->assign('title_item_attributes_help', $this->Lang('title_item_attributes_help'));
        $this->smarty->assign('title_catalog_attributes_help', $this->Lang('title_catalog_attributes_help'));
        $this->smarty->assign('title_category_attributes_help', $this->Lang('title_category_attributes_help'));
        $this->smarty->assign('title_is_textfield', $this->Lang('title_is_textfield'));
        $this->smarty->assign('title_delete', $this->Lang('title_delete'));
        $this->smarty->assign('category', $this->Lang('manageattrs'));

        // Display template
        echo $this->ProcessTemplate('adminattrs.tpl');
?>
