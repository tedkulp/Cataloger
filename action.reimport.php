<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;
	
		$this->importSampleTemplates();
		$params['message'] = $this->Lang('reimported');
		$this->DoAction('defaultadmin', $id, $params);

?>
