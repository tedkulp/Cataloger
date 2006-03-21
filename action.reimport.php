<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;
		$dir=opendir(dirname(__FILE__).'/includes');
   		$temps = array();
   		while($filespec=readdir($dir))
   			{
       		if(! preg_match('/\.tpl$/i',$filespec))
       			{
       			continue;
       			}
       		array_push($temps, $filespec);
			}        
		sort($temps);
		$query = 'INSERT INTO '. cms_db_prefix().
				'module_catalog_template (id, type_id, title, template) '.
				' VALUES (?,?,?,?)';

		foreach ($temps as $filespec)
			{
       		$file = file(dirname(__FILE__).'/includes/'.$filespec);
       		$template = implode('', $file);
       		$temp_name = preg_replace('/\.tpl$/i','',$filespec);
			$type_id = -1;
       		if (substr($temp_name,0,5) == 'Item-')
       			{
       			$type_id = 1;
       			}
       		else if (substr($temp_name,0,9) == 'Category-')
       			{
       			$type_id = 2;
       			}
       		else if (substr($temp_name,0,10) == 'Printable-')
       			{
       			$type_id = 3;
       			}
       		
    		$temp_id = $db->GenID(cms_db_prefix().
    			'module_catalog_template_seq');
			$dbresult = $db->Execute($query,
				array($temp_id,$type_id, $temp_name,$template));
       		$this->SetTemplate('catalog_'.$temp_id,$template);
       		}
	
		$params['message'] = $this->Lang('reimported');
		$this->DoAction('defaultadmin', 'catalogmodule', $params);

?>
