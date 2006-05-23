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
        }

		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));
?>
