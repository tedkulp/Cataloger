<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		if (! empty($params['template_id']))
			{
			// updating a template
			$query = 'UPDATE '. cms_db_prefix().
				'module_catalog_template set title=?, template=?, type_id=? WHERE id=?';
			$dbresult = $db->Execute($query,array($params['title'],$params['templ'],
				$params['type_id'], $params['template_id']));
			$template_id = $params['template_id'];
			}
		else
			{
			// creating a template
			$query = 'INSERT INTO '. cms_db_prefix().
				'module_catalog_template (id, title, type_id, template) VALUES (?,?,?,?)';
			$template_id = $db->GenID(cms_db_prefix().'module_catalog_template_seq');
			$dbresult = $db->Execute($query,array($template_id,$params['title'],$params['type_id'],$params['templ']));
			}
		$this->SetTemplate('catalog_'.$template_id,$params['templ']);
		
		$params['message'] = $this->Lang('templateupdated');
		$this->DoAction('defaultadmin', $id, $params);

?>
