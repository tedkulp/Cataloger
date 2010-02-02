<?php
		if (!isset($gCms)) exit;
		$db =& $gCms->GetDb();
		$current_version = $oldversion;
		$dict = NewDataDictionary($db);

		switch($current_version)
		{
			case "0.1":
			case "0.1.5":
			case "0.1.6":
			case "0.1.7":
			case "0.2":
			case "0.3":
                $this->RemovePreference('image_count');
         case "0.4":
				$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_template_type VALUES (?,?)';
            	$dbresult = $db->Execute($query,array(5, $this->Lang('catalog_short_list'))); 
 			case "0.4.1":
			case "0.4.2":
         case "0.5":
         case "0.5.1":
         case "0.5.2":
         case "0.5.3":
         case "0.5.4":
         case "0.5.5":
         case "0.5.6":
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_catalog_attr",
   				 	"is_textarea I");
				$dict->ExecuteSQLArray($sqlarray);
				$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_attr (id,type_id,is_textarea,attribute) VALUES (?,?,?,?)';
		        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
				$dbresult = $db->Execute($query,array($new_id, 1, 1, 'notes'));
		        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
				$dbresult = $db->Execute($query,array($new_id, 2, 1, 'notes'));
		        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
				$dbresult = $db->Execute($query,array($new_id, 3, 1, 'notes'));
				$this->AddEventHandler( 'Core', 'ContentEditPost', false );
			case "0.6":
			case "0.6.1":
			case "0.6.2":
				$res = $db->Execute("select * from ".cms_db_prefix()."module_catalog_attr");
				if ($res && $columns=$res->GetArray(1))
					{
						// haven't created the stuff above because I screwed up version numbers!
						if (! array_key_exists ('is_textarea', $columns[0]))
							{
							// so fix it!
							$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_catalog_attr",
			   				 	"is_textarea I");
							$dict->ExecuteSQLArray($sqlarray);
							$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_attr (id,type_id,is_textarea,attribute) VALUES (?,?,?,?)';
					        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
							$dbresult = $db->Execute($query,array($new_id, 1, 1, 'notes'));
					        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
							$dbresult = $db->Execute($query,array($new_id, 2, 1, 'notes'));
					        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
							$dbresult = $db->Execute($query,array($new_id, 3, 1, 'notes'));
							$this->AddEventHandler( 'Core', 'ContentEditPost', false );
							}
					}
			case "0.7":
			case "0.7.2":
			case "0.7.3":
			case "0.7.4":
			case "0.7.5":
			case "0.7.6":
			case "0.7.7":
				$catalogdirs = array('/catalogerfiles');
				foreach ($catalogdirs as $thisDir)
					{
	        		$fileDir = dirname($gCms->config['uploads_path'].$thisDir.'/index.html');
	        		if (!is_dir($fileDir))
	            		{
	            		mkdir($fileDir);
	            		}
					touch($fileDir.'/index.html');
	            	}
        }

		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));
?>
