<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

$types=array('','catalogitem','catalogcategory','catalogprintable');

foreach ($params as $thisParamKey=>$thisParamValue)
   {
   if (substr($thisParamKey,0,4) == 'attr')
       {
	   list($attr,$attrType,$attrNum) = explode('_',$thisParamKey);
	   if ((strlen($thisParamValue) > 0) &&
		(! isset($params['delete_'.$attrType.'_'.$attrNum]) || $params['delete_'.$attrType.'_'.$attrNum] == '0'))
		  {
		  $is_text = isset($params['istext_'.$attrType.'_'.$attrNum]) && ($params['istext_'.$attrType.'_'.$attrNum] == 1);
	      // new attribute or updating old?
		  if (isset($params['old_'.$attrType.'_'.$attrNum]))
			  {
			  // it's an old record
			  // get old value to update content table
			  $query = "SELECT attribute FROM ".cms_db_prefix(). "module_catalog_attr where id=?";
	          $oldval = $db->GetOne($query,array($params['old_'.$attrType.'_'.$attrNum]));
	       
			  // write new value
			  $query = "UPDATE ".cms_db_prefix().'module_catalog_attr SET is_textarea=?, attribute=?, alias=?, order_by=?, defaultval=?, length=? where id=?';
	       	  $dbresult = $db->Execute($query,array(
					($is_text?1:0),
					$thisParamValue,
					$params['alias_'.$attrType.'_'.$attrNum],
					$params['orderby_'.$attrType.'_'.$attrNum],			
					$params['default_'.$attrType.'_'.$attrNum],			
					$params['len_'.$attrType.'_'.$attrNum],			
					$params['old_'.$attrType.'_'.$attrNum]));
	       	  if ($dbresult === false)
				  {
	           	  return $this->displayError($db->ErrorMsg());
	           	  }
			  // update content table
			  if ($oldval != $thisParamValue)
			     {
				  $query = 'SELECT c.content_id from '.cms_db_prefix().'content c, '.cms_db_prefix().
					'content_props p where c.type=? and p.prop_name=? and c.content_id=p.content_id';
		       	  $dbresult = $db->Execute($query,array($types[$attrType],$oldval));
		          $clist = array();
				  while ($dbresult !== false && $row = $dbresult->FetchRow())
		        	 {
					 $clist[] = $row['content_id'];
				 	 }
				  if (count($clist) > 0)
					 {
					  $query = 'UPDATE '.cms_db_prefix().'content_props SET prop_name=? where prop_name=? and content_id in ('.
						implode(',',$clist).')';
			       	$dbresult = $db->Execute($query,array($thisParamValue,$oldval));
					if ($dbresult === false)
						  {
			           	  return $this->displayError($db->ErrorMsg());
			           	  }
		      		}
                }
			  }
		  else
		      {
	       	  $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
	       	  $query = 'INSERT INTO '. cms_db_prefix().
	           'module_catalog_attr (id,type_id,is_textarea,attribute,alias,defaultval,length,order_by) VALUES (?,?,?,?,?,?,?,?)';
	       	  $dbresult = $db->Execute($query,
					array($new_id,$attrType,($is_text?1:0),$thisParamValue,
						$params['alias_'.$attrType.'_'.$attrNum],
						$params['default_'.$attrType.'_'.$attrNum],			
						$params['len_'.$attrType.'_'.$attrNum],			
						$params['orderby_'.$attrType.'_'.$attrNum]));
	       	  if ($dbresult === false)
				  {
	           	  return $this->displayError($db->ErrorMsg());
	           	  }
			  }
			}
		else if (isset($params['delete_'.$attrType.'_'.$attrNum]) && $params['delete_'.$attrType.'_'.$attrNum] == '1')
			{
        		// handle deletes explicitly now.
			$query = 'DELETE FROM '.cms_db_prefix().'module_catalog_attr where id=?';
			$dbresult = $db->Execute($query, array($params['old_'.$attrType.'_'.$attrNum]));
			}
		}
	}
$params['module_message'] = $this->Lang('attrsupdated');
$this->DoAction('adminattrs', $id, $params);

?>