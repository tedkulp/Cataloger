<?php
		if (!isset($gCms)) exit;
		$db =& $gCms->GetDb();
		$dict = NewDataDictionary( $db );

		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_catalog_template" );
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_catalog_template_type" );
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_catalog_attr" );
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence( cms_db_prefix()."module_catalog_template_seq" );
		$db->DropSequence( cms_db_prefix()."module_catalog_attr_seq" );

        $this->RemovePreference('show_extant');
        $this->RemovePreference('category_image_count');
        $this->RemovePreference('category_image_size_hero');
        $this->RemovePreference('category_image_size_thumbnail');
        $this->RemovePreference('item_image_count');
        $this->RemovePreference('show_missing');
        $this->RemovePreference('item_image_size_thumbnail');
        $this->RemovePreference('item_image_size_hero');
        $this->RemovePreference('item_image_count');
        $this->RemovePreference('image_aspect_ratio');
        $this->RemovePreference('category_sort_order');
        $this->RemovePreference('printable_sort_order');
        $this->RemovePreference('force_aspect_ratio');
        $this->RemovePreference('flush_cats');
        

		$this->RemovePermission('Modify Catalog Settings');
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
?>