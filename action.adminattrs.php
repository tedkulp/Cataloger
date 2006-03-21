<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initAdminNav($id, $params, $returnid);

		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitattrs', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));

        $attributes = array();
        $query = "SELECT attribute, type_id FROM ".cms_db_prefix(). "module_catalog_attr ORDER BY attribute";
        $dbresult = $db->Execute($query);
        while ($dbresult !== false && $row = $dbresult->FetchRow())
        {
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr'.$row['type_id'].'[]', $row['attribute'], 25, 255);
           $onerow->type = $row['type_id'];
	       array_push($attributes, $onerow);
        }
        for ($i=0;$i<3;$i++)
        {
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr1[]', '', 25, 255);
           $onerow->type = 1;
           array_push($attributes, $onerow);
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr2[]', '', 25, 255);
           $onerow->type = 2;
           array_push($attributes, $onerow);
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr3[]', '', 25, 255);
           $onerow->type = 3;
	       array_push($attributes, $onerow);
        }

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

        $this->smarty->assign('category', $this->Lang('manageattrs'));

        // Display template
        echo $this->ProcessTemplate('adminattrs.tpl');
?>
