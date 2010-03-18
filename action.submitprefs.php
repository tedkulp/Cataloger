<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

        $this->SetPreference('item_image_count', isset($params['item_image_count'])?$params['item_image_count']:2);
        $this->SetPreference('item_file_count', isset($params['item_file_count'])?$params['item_file_count']:2);
        $this->SetPreference('item_file_types', isset($params['item_file_types'])?$params['item_file_types']:'pdf,swf,flv,doc,odt,ods,xls');
        $this->SetPreference('category_image_count', isset($params['category_image_count'])?$params['category_image_count']:1);
		$this->SetPreference('item_image_size_hero', isset($params['item_image_size_hero'])?$params['item_image_size_hero']:'400');
		$this->SetPreference('item_image_size_thumbnail', isset($params['item_image_size_thumbnail'])?$params['item_image_size_thumbnail']:'70');
		$this->SetPreference('category_image_size_hero', isset($params['category_image_size_hero'])?$params['category_image_size_hero']:'400');
		$this->SetPreference('category_image_size_thumbnail', isset($params['category_image_size_thumbnail'])?$params['category_image_size_thumbnail']:'90');
		$this->SetPreference('item_image_size_category', isset($params['item_image_size_category'])?$params['item_image_size_category']:'70');
		$this->SetPreference('item_image_size_catalog', isset($params['item_image_size_catalog'])?$params['item_image_size_catalog']:'100');
		$this->SetPreference('force_aspect_ratio', isset($params['force_aspect_ratio'])?$params['force_aspect_ratio']:0);
		$this->SetPreference('image_aspect_ratio', isset($params['image_aspect_ratio'])?$params['image_aspect_ratio']:'4:3');
		$this->SetPreference('category_recurse', isset($params['category_recurse'])?$params['category_recurse']:'mixed_one');
		$this->SetPreference('category_sort_order', isset($params['item_sort_order'])?$params['item_sort_order']:'natural');
		$this->SetPreference('category_items_per_page', isset($params['items_per_page'])?$params['items_per_page']:'10');
		$this->SetPreference('show_extant', isset($params['show_extant'])?$params['show_extant']:'1');
		$this->SetPreference('flush_cats', isset($params['flush_cats'])?$params['flush_cats']:'0');
		$this->SetPreference('image_upload_path',isset($params['image_upload_path'])?$params['image_upload_path']:$this->getAssetPath('s',true));
		$this->SetPreference('file_upload_path',isset($params['file_upload_path'])?$params['file_upload_path']:$this->getAssetPath('f',true));
		$this->SetPreference('image_proc_path',isset($params['image_proc_path'])?$params['image_proc_path']:$this->getAssetPath('i',true));
		
	
	$this->SetPreference('show_missing',
isset($params['show_missing'])?$params['show_missing']:0);


		$params['message'] = $this->Lang('prefsupdated');
        $this->DoAction('adminprefs', $id, $params);

?>