<?php
if (!isset($gCms)) exit;
if (! $this->CheckAccess()) exit;

        $query = 'DELETE FROM '. cms_db_prefix().'module_catalog_attr';
        $dbresult = $db->Execute($query);
            
        if (! is_array($params['attr1']))
            {
            $params['attr1'] = array($params['attr1']);
            }
        if (! is_array($params['attr2']))
            {
            $params['attr2'] = array($params['attr2']);
            }
        if (! is_array($params['attr3']))
            {
            $params['attr3'] = array($params['attr3']);
            }
        for ($i=1;$i<4;$i++)
            {
            $attrname = 'attr'.$i;
            foreach ($params[$attrname] as $thisAttr)
                {
                if (strlen($thisAttr) > 0)
                    {
                    $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
                    $query = 'INSERT INTO '. cms_db_prefix().
                        'module_catalog_attr VALUES (?,?,?)';
                    $dbresult = $db->Execute($query,array($new_id,$i,$thisAttr));
                    if ($dbresult === false)
                        {
                        return $this->displayError($db->ErrorMsg());
                        }
                    }
                }
            }
		$params['message'] = $this->Lang('attrsupdated');
        $this->DoAction('adminattrs', 'catalogmodule', $params);

?>