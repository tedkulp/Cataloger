<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		$this->initAdminNav($id, $params, $returnid);

		$this->smarty->assign('startform', $this->CreateFormStart($id, 'globalsubmit', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Update Entire Catalog'));

        $this->smarty->assign('title_category_recurse',$this->Lang('title_global_category_recurse'));

		$this->smarty->assign('input_category_recurse',
			'<input type="radio" name="'.$id.'category_recurse" value="nochange" checked="checked" />'.$this->Lang('noglobalchange').'<br />' .
			'<input type="radio" name="'.$id.'category_recurse" value="items_all" />'.
				$this->Lang('title_category_recurse_items_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="items_one" />'.
				$this->Lang('title_category_recurse_items_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_all" />'.
				$this->Lang('title_category_recurse_categories_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_one" />'.
				$this->Lang('title_category_recurse_categories_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_all" />'.
				$this->Lang('title_category_recurse_mixed_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_one" />'.
				$this->Lang('title_category_recurse_mixed_one'));
        $this->smarty->assign('title_items_per_page',$this->Lang('title_global_items_per_page'));
        $this->smarty->assign('input_items_per_page',$this->CreateInputDropdown($id,
         		'items_per_page',
                array($this->Lang('noglobalchange')=>'-1',
                	'1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',
                	'7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11',
                	'12'=>'12', '13'=>'13','14'=>'14','15'=>'15','16'=>'16',
                	'17'=>'17','18'=>'18','19'=>'19','20'=>'20','24'=>'24',
                	'25'=>'25','30'=>'30','40'=>'40',
                	'50'=>'50', '1000'=>'1000'), -1, '-1'));
        $this->smarty->assign('title_item_sort_order',$this->Lang('title_global_item_sort_order'));
		$this->smarty->assign('input_item_sort_order',$this->CreateInputDropdown($id,
		 	'item_sort_order',array($this->Lang('noglobalchange')=>'nochange',
		 	$this->Lang('natural_order')=>'natural',
		 	$this->Lang('alpha_order')=>'alpha'), -1, 'nochange'));

        $this->smarty->assign('message', isset($params['message'])?$params['message']:'');

        $this->smarty->assign('category', $this->Lang('globalops'));

        #Display template
        echo $this->ProcessTemplate('adminglobalops.tpl');
?>
