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
		$dbresult = $db->Execute($query,array(1, $this->Lang('item_page')));
		$dbresult = $db->Execute($query,array(2, $this->Lang('category_page')));
		$dbresult = $db->Execute($query,array(3, $this->Lang('catalog_printable')));
		$dbresult = $db->Execute($query,array(4, $this->Lang('catalog_datasheet')));

		$flds = "
			id I KEY,
			type_id I,
			attribute C(255)
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_attr",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$db->CreateSequence(cms_db_prefix()."module_catalog_attr_seq");

		$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_attr VALUES (?,?,?)';
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Weight'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Medium/Media'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Dimensions'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Price'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'In Stock?'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 3, 'Copyright'));

		$catalogdirs = array('catalog','catalog_src');
		foreach ($catalogdirs as $thisDir)
			{
        	$fileDir = dirname($gCms->config['uploads_path'].'/images/'.$thisDir.'/index.html');
        	if (!is_dir($fileDir))
            	{
            	mkdir($fileDir);
            	}
			touch($fileDir.'/index.html');
            }
        $this->importSampleTemplates();           
        $this->SetPreference('item_image_count', 2);
		$this->CreatePermission('Modify Catalog Settings', 'Modify Catalog Settings');
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
?>
