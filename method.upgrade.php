<?php
		if (!isset($gCms)) exit;
		$db =& $gCms->GetDb();
		$current_version = $oldversion;

		switch($current_version)
		{
			case "0.1":
			case "0.2":
			case "0.3":
                $this->RemovePreference('image_count');
            case "0.4":
				$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_template_type VALUES (?,?)';
            	$dbresult = $db->Execute($query,array(5, $this->Lang('catalog_short_list')));  
            case "0.5.6":
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_catalog_attr",
   				 	"is_textarea I");
				$dict->ExecuteSQLArray($sqlarray);
		        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
				$dbresult = $db->Execute($query,array($new_id, 1, 1, 'notes'));
		        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
				$dbresult = $db->Execute($query,array($new_id, 2, 1, 'notes'));
		        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
				$dbresult = $db->Execute($query,array($new_id, 3, 1, 'notes'));
				$this->AddEventHandler( 'Core', 'ContentEditPost', false );
        }

		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));
?>
