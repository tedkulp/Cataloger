<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initAdminNav($id, $params, $returnid);

		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitprefs', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));

        $this->smarty->assign('tab_headers',$this->StartTabHeaders().
        $this->SetTabHeader('itemimage',$this->Lang('title_item_image_tab')).
        $this->SetTabHeader('file',$this->Lang('title_file_tab')).
        $this->SetTabHeader('categoryimage',$this->Lang('title_category_image_tab')).
        $this->SetTabHeader('printable',$this->Lang('title_printable_tab')).
        $this->SetTabHeader('image',$this->Lang('title_image_tab')).
        $this->SetTabHeader('path',$this->Lang('title_path_tab')).
        $this->EndTabHeaders().$this->StartTabContent());
        $this->smarty->assign('end_tab',$this->EndTab());
        $this->smarty->assign('tab_footers',$this->EndTabContent());
        $this->smarty->assign('start_item_image_tab',$this->StartTab('itemimage'));
        $this->smarty->assign('start_category_image_tab',$this->StartTab('categoryimage'));
        $this->smarty->assign('start_printable_tab',$this->StartTab('printable'));
        $this->smarty->assign('start_file_tab',$this->StartTab('file'));
        $this->smarty->assign('start_path_tab',$this->StartTab('path'));
	$this->smarty->assign('start_image_tab',$this->StartTab('image'));

        $this->smarty->assign('title_item_image_count', $this->Lang('title_item_image_count'));
        $this->smarty->assign('title_item_file_count', $this->Lang('title_item_file_count'));
       $this->smarty->assign('title_item_file_types', $this->Lang('title_item_file_types'));
        $this->smarty->assign('title_flush_cats', $this->Lang('title_flush_cats'));
        $this->smarty->assign('title_show_only_existing_images',
        	$this->Lang('title_show_only_existing_images'));
        $this->smarty->assign('title_show_missing_images',
        	$this->Lang('title_show_missing_images'));
        $this->smarty->assign('title_category_image_count', $this->Lang('title_category_image_count'));
        $this->smarty->assign('title_item_image_size_hero', $this->Lang('title_item_image_size_hero'));
        $this->smarty->assign('title_item_image_size_thumbnail', $this->Lang('title_item_image_size_thumbnail'));
        $this->smarty->assign('title_category_image_size_hero', $this->Lang('title_category_image_size_hero'));
        $this->smarty->assign('title_category_image_size_thumbnail', $this->Lang('title_category_image_size_thumbnail'));
        $this->smarty->assign('title_item_image_size_category', $this->Lang('title_item_image_size_category'));
        $this->smarty->assign('title_item_image_size_catalog', $this->Lang('title_item_image_size_catalog'));
        $this->smarty->assign('title_category_recurse',$this->Lang('title_category_recurse'));
        $this->smarty->assign('title_printable_sort_order',$this->Lang('title_printable_sort_order'));

        $this->smarty->assign('title_image_upload_path',$this->Lang('title_image_upload_path'));
        $this->smarty->assign('title_file_upload_path',$this->Lang('title_file_upload_path'));
        $this->smarty->assign('title_image_proc_path',$this->Lang('title_image_proc_path'));
        $this->smarty->assign('path_help',$this->Lang('path_help'));

        $number = array();
        for ($i=0;$i<16;$i++)
        	{
        	$number[$i]=$i;
        	}
        $this->smarty->assign('input_item_image_count', $this->CreateInputDropdown($id, 'item_image_count', $number, -1,  $this->GetPreference('item_image_count', '2')));
        $this->smarty->assign('input_category_image_count', $this->CreateInputDropdown($id, 'category_image_count', $number, -1,  $this->GetPreference('category_image_count', '1')));
        $this->smarty->assign('input_item_file_count', $this->CreateInputDropdown($id, 'item_file_count', $number, -1,  $this->GetPreference('item_file_count', '0')));
        $this->smarty->assign('input_item_file_types', $this->CreateInputText($id, 'item_file_types',$this->GetPreference('item_file_types', 'pdf,swf,flv,doc,odt,ods,xls')));

		
       $this->smarty->assign('input_image_upload_path', $this->CreateInputText($id, 'image_upload_path',$this->GetPreference('image_upload_path', $this->getAssetPath('s',true))));
       $this->smarty->assign('input_image_proc_path', $this->CreateInputText($id, 'image_proc_path',$this->GetPreference('image_proc_path', $this->getAssetPath('i',true))));
       $this->smarty->assign('input_file_upload_path', $this->CreateInputText($id, 'file_upload_path',$this->GetPreference('file_upload_path', $this->getAssetPath('f',true))));


		$recurse =  $this->GetPreference('category_recurse', 'mixed_one'); 
		$this->smarty->assign('input_category_recurse',
			'<input type="radio" name="'.$id.'category_recurse" value="items_all"' .
				($recurse == 'items_all'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_items_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="items_one"' .
				($recurse == 'items_one'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_items_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_all"' .
				($recurse == 'categories_all'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_categories_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_one"' .
				($recurse == 'categories_one'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_categories_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_all"' .
				($recurse == 'mixed_all'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_mixed_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_one"' .
				($recurse == 'mixed_one'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_mixed_one'));
        $this->smarty->assign('input_item_image_size_hero', $this->CreateInputText($id, 'item_image_size_hero', $this->GetPreference('item_image_size_hero', '400'), 10, 10));
        $this->smarty->assign('input_item_image_size_thumbnail', $this->CreateInputText($id, 'item_image_size_thumbnail', $this->GetPreference('item_image_size_thumbnail', '70'), 10, 10));
        $this->smarty->assign('input_category_image_size_hero', $this->CreateInputText($id, 'category_image_size_hero', $this->GetPreference('category_image_size_hero', '400'), 10, 10));
        $this->smarty->assign('input_category_image_size_thumbnail', $this->CreateInputText($id, 'category_image_size_thumbnail', $this->GetPreference('category_image_size_thumbnail', '90'), 10, 10));
        $this->smarty->assign('input_item_image_size_category', $this->CreateInputText($id, 'item_image_size_category', $this->GetPreference('item_image_size_category', '70'), 10, 10));
        $this->smarty->assign('input_item_image_size_catalog', $this->CreateInputText($id, 'item_image_size_catalog', $this->GetPreference('item_image_size_catalog', '100'), 10, 10));

$this->smarty->assign('input_show_missing_images',$this->CreateInputHidden($id,'show_missing','0').$this->CreateInputCheckbox($id, 'show_missing', 1, $this->GetPreference('show_missing','1')). $this->Lang('title_show_missing_images_long'));		

$this->smarty->assign('input_show_only_existing_images',$this->CreateInputHidden($id,'show_extant','0').$this->CreateInputCheckbox($id, 'show_extant', 1, $this->GetPreference('show_extant','1')). $this->Lang('title_show_only_existing_images_help'));		

$this->smarty->assign('input_flush_cats',$this->CreateInputHidden($id,'flush_cats','0').$this->CreateInputCheckbox($id, 'flush_cats', 1, $this->GetPreference('flush_cats','0')). $this->Lang('title_flush_cats_help'));		

        $this->smarty->assign('title_items_per_page',$this->Lang('title_items_per_page'));
        $this->smarty->assign('input_items_per_page',$this->CreateInputDropdown($id,
         		'items_per_page',
                array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',
                	'7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12',
                	'13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                	'19'=>'19','20'=>'20','24'=>'24','25'=>'25','30'=>'30','40'=>'40',
                	'50'=>'50', '1000'=>'1000'), -1, $this->GetPreference('category_items_per_page','10')));
        $this->smarty->assign('title_item_sort_order',$this->Lang('title_item_sort_order'));
		$this->smarty->assign('input_item_sort_order',$this->CreateInputDropdown($id,
		 	'item_sort_order',array($this->Lang('natural_order')=>'natural',
		 	$this->Lang('alpha_order')=>'alpha'), -1, $this->GetPreference('category_sort_order','natural')));
		$this->smarty->assign('input_printable_sort_order',$this->CreateInputDropdown($id,
		 	'printable_sort_order',array($this->Lang('natural_order')=>'natural',
		 	$this->Lang('alpha_order')=>'alpha'), -1, $this->GetPreference('printable_sort_order','natural')));

        $this->smarty->assign('message',isset($params['message'])?$params['message']:'');

        $this->smarty->assign('category', $this->Lang('manageprefs'));

        #Display template
        echo $this->ProcessTemplate('adminprefs.tpl');
?>
