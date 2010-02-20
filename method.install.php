<?php
		if (!isset($gCms)) exit;
		$db =& $gCms->GetDb();

		$dict = NewDataDictionary($db);
		$flds = "
			id I KEY,
			type_id I,
			title C(255),
			template X
		";
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_template",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$db->CreateSequence(cms_db_prefix()."module_catalog_template_seq");

		$flds = "
			type_id I KEY,
			name C(25)
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_template_type",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_template_type VALUES (?,?)';
		$dbresult = $db->Execute($query,array(CTEMPLATE_ITEM, $this->Lang('item_page')));
		$dbresult = $db->Execute($query,array(CTEMPLATE_CATEGORY, $this->Lang('category_page')));
		$dbresult = $db->Execute($query,array(CTEMPLATE_CATALOG, $this->Lang('catalog_printable')));
		$dbresult = $db->Execute($query,array(CTEMPLATE_COMPARISON, $this->Lang('item_comparison')));
		$dbresult = $db->Execute($query,array(CTEMPLATE_FEATURE, $this->Lang('catalog_short_list')));

		$flds = "
			id I KEY,
			type_id I,
			is_textarea I,
			attribute C(255),
			order_by I,
			alias C(60),
			length I,
			defaultval X
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_attr",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$db->CreateSequence(cms_db_prefix()."module_catalog_attr_seq");

		$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_attr (id,type_id,is_textarea,attribute) VALUES (?,?,?,?)';
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 1, 'Item Notes'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 2, 1, 'Category Notes'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 3, 1, 'Catalog Notes'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 0, 'Weight'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 0, 'Medium/Media'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 0, 'Dimensions'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 0, 'Price'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 0, 'In Stock?'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 3, 0, 'Copyright'));

		$catalogdirs = array('/images/catalog','/images/catalog_src','/catalogerfiles');
		foreach ($catalogdirs as $thisDir)
			{
        	$fileDir = dirname($gCms->config['uploads_path'].$thisDir.'/index.html');
        	if (!is_dir($fileDir))
            	{
            	mkdir($fileDir);
            	}
			touch($fileDir.'/index.html');
            }
        $this->importSampleTemplates();           
		$this->AddEventHandler( 'Core', 'ContentEditPost', false );
        $this->SetPreference('item_image_count', 2);
		$this->CreatePermission('Modify Catalog Settings', 'Modify Catalog Settings');
		
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
?>
