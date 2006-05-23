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

		$this->RemovePermission('Modify Catalog Settings');
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
?>