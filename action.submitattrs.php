<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

$query = 'DELETE FROM '. cms_db_prefix().'module_catalog_attr';
$dbresult = $db->Execute($query);

foreach ($params as $thisParamKey=>$thisParamValue)
   {
   if (substr($thisParamKey,0,4) == 'attr')
       {
	   list($attr,$attrType,$attrNum) = explode('_',$thisParamKey);
	   if ((strlen($thisParamValue) > 0) &&
		(! isset($params['delete_'.$attrType.'_'.$attrNum]) || $params['delete_'.$attrType.'_'.$attrNum] == '0'))
		  {
		  $is_text = isset($params['istext_'.$attrType.'_'.$attrNum]) && ($params['istext_'.$attrType.'_'.$attrNum] == 1);
	       $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
	       $query = 'INSERT INTO '. cms_db_prefix().
	           'module_catalog_attr (id,type_id,is_textarea,attribute) VALUES (?,?,?,?)';
	       $dbresult = $db->Execute($query,array($new_id,$attrType,($is_text?1:0),$thisParamValue));
	       if ($dbresult === false)
	           {
	           return $this->displayError($db->ErrorMsg());
	           }
			}
       }
   }
$params['module_message'] = $this->Lang('attrsupdated');
$this->DoAction('adminattrs', $id, $params);

?>