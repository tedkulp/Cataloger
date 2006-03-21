<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

		if (! empty($params['template_id']))
			{
			$query = 'DELETE FROM '. cms_db_prefix().
				'module_catalog_template WHERE id=?';
			$dbresult = $db->Execute($query,array($params['template_id']));
			}
		$this->DeleteTemplate('glossary_'.$params['template_id']);

		$params['message'] = $this->Lang('templatedeleted');
		$this->DoAction('defaultadmin', $id, $params);

?>
