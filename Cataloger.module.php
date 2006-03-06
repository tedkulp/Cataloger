<?php
#-------------------------------------------------------------------------
# Module: Cataloger - build a catalog or portfolio of stuff
# Version: 0.1.7
#
# Copyright (c) 2005, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit SjG's
# module homepage at http://www.cmsmodules.com
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
class Cataloger extends CMSModule
{

	var $attrs = array();
	var $fetched = false;

	function GetName()
	{
		return 'Cataloger';
	}
	
	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetVersion()
	{
		return '0.1.7';
	}

	function GetAdminDescription()
	{
		return $this->Lang('admindescription');
	}

	function Install()
	{
		$db = &$this->cms->db;

		$dict = NewDataDictionary($db);
		$flds = "
			id I KEY,
			type_id I,
			title C(255),
			template X
		";
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_template",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$db->CreateSequence(cms_db_prefix()."module_catalog_template_seq");

		$flds = "
			type_id I KEY,
			name C(25)
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_template_type",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_template_type VALUES (?,?)';
		$dbresult = $db->Execute($query,array(1, $this->Lang('item_page')));
		$dbresult = $db->Execute($query,array(2, $this->Lang('category_page')));
		$dbresult = $db->Execute($query,array(3, $this->Lang('catalog_printable')));
		$dbresult = $db->Execute($query,array(4, $this->Lang('catalog_datasheet')));

		$flds = "
			id I KEY,
			type_id I,
			attribute C(255)
		";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_catalog_attr",
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$db->CreateSequence(cms_db_prefix()."module_catalog_attr_seq");

		$query = 'INSERT INTO '. cms_db_prefix(). 'module_catalog_attr VALUES (?,?,?)';
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Weight'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Medium/Media'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Dimensions'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'Price'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 1, 'In Stock?'));
        $new_id = $db->GenID(cms_db_prefix().'module_catalog_attr_seq');
		$dbresult = $db->Execute($query,array($new_id, 3, 'Copyright'));

		$catalogdirs = array('catalog','catalog_src');
		foreach ($catalogdirs as $thisDir)
			{
        	$fileDir = dirname($this->cms->config['uploads_path'].'/images/'.$thisDir.'/index.html');
        	if (!is_dir($fileDir))
            	{
            	mkdir($fileDir);
            	}
			touch($fileDir.'/index.html');
            }
        $this->importSampleTemplates();           
        $this->SetPreference('image_count', 2);
		$this->CreatePermission('Modify Catalog Settings', 'Modify Catalog Settings');
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function Upgrade($oldversion, $newversion)
	{
		$current_version = $oldversion;
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));
	}

	function Uninstall()
	{
		$db = &$this->cms->db;
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
	}

	function importSampleTemplates()
	{
		$db = &$this->cms->db;
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
	
	}

	function DoAction($action, $id, $params, $returnid = -1)
	{
		$db = &$this->cms->db;
		echo "<!-- $action -->\n";
		switch ($action)
		{
			case "default":
				{
				$this->DisplayItem($id, $params, $returnid);
				break;
				}
			case "defaultcategory":
				{
				$this->DisplayCategory($id, $params, $returnid);
				break;
				}
			case "defaultprintable":
				{
				$this->DisplayPrintable($id, $params, $returnid);
				break;
				}
			case "defaultadmin":
			case "listtempl":
				{
				if ($this->CheckAccess())
					{
					$this->listTemplates($id, $params, $returnid);
					}
				}
				break;
			case "reimport":
				{
				if ($this->CheckAccess())
					{
					$this->importSampleTemplates();
					$this->listTemplates($id, $params, $returnid,$this->Lang('reimported'));
					}
				}
				break;
			case "edittempl":
				{
				if ($this->CheckAccess())
					{
			        $this->initAdminNav($id, $params, $returnid);
					$this->editTemplate($id, $params, $returnid);
					}
				}
				break;
			case "submittempl":
				{
				if ($this->CheckAccess())
					{
					$this->submitTemplate($id, $params, $returnid);
					}
				}
				break;
			case "deletetempl":
				{
				if ($this->CheckAccess())
					{
					$this->delTemplate($id, $params, $returnid);
					}
				}
				break;
			case "adminattrs":
				{
				if ($this->CheckAccess())
					{
			        $this->initAdminNav($id, $params, $returnid);
			   		$this->adminAttributes($id, $params, $returnid);
					}
				}
				break;
			case "submitattrs":
				{
				if ($this->CheckAccess())
					{
			        $this->initAdminNav($id, $params, $returnid);
					$this->submitAttrs($id, $params, $returnid);
					}
				}
				break;
			case "adminprefs":
				{
				if ($this->CheckAccess())
					{
			        $this->initAdminNav($id, $params, $returnid);
			   		$this->adminPrefs($id, $params, $returnid);
					}
				}
				break;
			case "submitprefs":
				{
				if ($this->CheckAccess())
					{
			        $this->initAdminNav($id, $params, $returnid);
					$this->submitPrefs($id, $params, $returnid);
					}
				}
				break;
			case "globalops":
				{
				if ($this->CheckAccess())
					{
			        $this->initAdminNav($id, $params, $returnid);
			   		$this->globalOpsForm($id, $params, $returnid);
					}
				}
				break;
			case "globalsubmit":
				{
				if ($this->CheckAccess())
					{
					$this->globalOpsSubmit($id, $params, $returnid);
					}
				}
				break;
		}
	}

    function DisplayItem($id, &$params, $returnid)
    {
		$db = &$this->cms->db;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$imageArray = array();
		$thumbArray = array();
        $imgcount = $this->GetPreference('item_image_count', '2');
        $fullSize = $this->GetPreference('item_image_size_hero', '400');
        $thumbSize = $this->GetPreference('item_image_size_thumbnail', '70');
        for ($i=1;$i<=$imgcount;$i++)
            {
            array_push($imageArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_f_'.$i.'_'.$fullSize.'.jpg');
            array_push($thumbArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_t_'.$i.'_'.$thumbSize.'.jpg');

            $this->smarty->assign('image_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_f_'.$i.'_'.$fullSize.'.jpg');
            $this->smarty->assign('image_thumb_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$params['alias'].'_t_'.$i.'_'.$thumbSize.'.jpg'
            );
            }
		$this->smarty->assign_by_ref('attrlist',$params['attrlist']);
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
	}

    function DisplayCategory($id, &$params, $returnid)
    {
		$db = &$this->cms->db;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$content = ContentManager::GetAllContent(false);
		$curPageID = $this->cms->variables['content_id'];

		for ($i=0;$i<count($content);$i++)
			{
			if ($content[$i]->Id() == $curPageID)
				{
				$curPage = $content[$i];
				}
			}
		$curHierarchy = $curPage->Hierarchy();

        $curHierLen = strlen($curHierarchy);
        $curHierDepth = substr_count($curHierarchy,'.');
        $categoryItems = array();
        if (isset($params['sort_order']) && $params['sort_order'] == 'alpha')
            {
            usort($content,array("Cataloger", "contentalpha"));
            }
		foreach ($content as $thisPage)
			{
            if (!$thisPage->Active())
                {
                continue;
                }
            if ($thisPage->Id() == $curPage->Id())
                {
                continue;
                }
			$type_ok = false;
			$depth_ok = false;
			if ($thisPage->Type() == 'aliasmodule')
				{
				$thisPage = $thisPage->GetAliasContent();
				}
			if ($thisPage->Type() == 'catalogitem' &&
                      ($params['recurse'] == 'items_one' ||
                       $params['recurse'] == 'items_all' ||
                       $params['recurse'] == 'mixed_one' ||
                       $params['recurse'] == 'mixed_all'))
                {
                $type_ok = true;
                }
            else if ($thisPage->Type() == 'catalogcategory' &&
                          ($params['recurse'] == 'categories_one' ||
                           $params['recurse'] == 'categories_all' ||
                           $params['recurse'] == 'mixed_one' ||
                           $params['recurse'] == 'mixed_all'))
                    {
                    $type_ok = true;
                    }
            if (! $type_ok)
                {
                continue;
                }
            if (($params['recurse'] == 'items_one' ||
                 $params['recurse'] == 'categories_one' ||
                 $params['recurse'] == 'mixed_one') &&
                 substr_count($thisPage->Hierarchy(),'.') ==
                 	($curHierDepth + 1) &&
                 substr($thisPage->Hierarchy(),0,$curHierLen) == $curHierarchy)
                {
                $depth_ok = true;
                }
            else if (($params['recurse'] == 'items_all' ||
                 $params['recurse'] == 'categories_all' ||
                 $params['recurse'] == 'mixed_all') &&
                 substr($thisPage->Hierarchy(),0,$curHierLen) == $curHierarchy)
                    {
                    $depth_ok = true;
                    }
            if (! $depth_ok)
                {
                continue;
                }
			// in the category, and approved for addition
			$catThumbSize = $this->GetPreference('category_image_size_thumbnail',90);
			$itemThumbSize = $this->GetPreference('item_image_size_category',70);
			switch ($thisPage->Type())
				{
                case 'catalogitem':
				    $thisItem['image'] = $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_s_1_'.$itemThumbSize.'.jpg';
				    break;
				case 'catalogcategory':
				    $thisItem['image'] = $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_1_'.$catThumbSize.'.jpg';
				    break;
				}
			$thisItem['link'] = $thisPage->GetUrl();
			$thisItem['title'] = $thisPage->MenuText();
			array_push($categoryItems,$thisItem);
			}
            
        $count = count($categoryItems);
        if (isset($_REQUEST['start']))
        	{
        	$start = $_REQUEST['start'];
        	}
        else
        	{
        	$start = 0;
        	}
        if (isset($params['items_per_page']))
        	{
        	$end = max($params['items_per_page'],1);
        	}
        else
        	{
        	$end = max($count,1);
        	}
        $thisUrl = $_SERVER['REQUEST_URI'];
        $thisUrl = preg_replace('/(\?)*(\&)*start=\d+/','',$thisUrl);
		if (strpos($thisUrl,'?') === false)
			{
			$delim = '?';
			}
		else
			{
			$delim = '&';
			}
        if ($start > 0)
        	{
        	$this->smarty->assign('prev','<a href="'.$thisUrl.$delim.'start='.
        		max(0,$start-$end).'">'.$this->Lang('prev').'</a>');
        	$this->smarty->assign('prevurl',$thisUrl.$delim.'start='.
        		max(0,$start-$end));
        	}
        else
        	{
        	$this->smarty->assign('prev','');
        	$this->smarty->assign('prevurl','');
        	}
        if ($start + $end < $count)
        	{
        	$this->smarty->assign('next','<a href="'.$thisUrl.$delim.'start='.
        		($start + $end).'">'.$this->Lang('next').'</a>');
        	$this->smarty->assign('nexturl',$thisUrl.$delim.'start='.
        		($start + $end));
        	}
        else
        	{
        	$this->smarty->assign('next','');
        	$this->smarty->assign('nexturl','');
        	}
        $navstr = '';
        $pageInd = 1;
       	for ($i=0;$i<$count;$i+=$end)
       		{
       		if ($i == $start)
       			{
       			$navstr .= $pageInd;
       			}
       		else
       			{
       			$navstr .= '<a href="'.$thisUrl.$delim.'start='.$i.'">'.
       				$pageInd.'</a>';
       			}
       		$navstr .= ':';
       		$pageInd++;
       		}

		$navstr = rtrim($navstr,':');
        $categoryItems = array_splice($categoryItems, $start, $end);
        $this->smarty->assign('items',$categoryItems);
        if (strlen($navstr) > 1)
        	{
        	$this->smarty->assign('navstr',$navstr);
        	$this->smarty->assign('hasnav',1);
			}
		else
			{
			$this->smarty->assign('navstr','');
        	$this->smarty->assign('hasnav',0);
			}
        $imgcount = $this->GetPreference('category_image_count', '1');
        $fullSize = $this->GetPreference('category_image_size_hero', '400');
        $thumbSize = $this->GetPreference('category_image_size_thumbnail', '90');
        $imageArray = array();
        for ($i=1;$i<=$imgcount;$i++)
            {
            array_push($imageArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_cf_'.$i.'_'.$fullSize.'.jpg');
            array_push($imageArray, $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_'.$i.'_'.$thumbSize.'.jpg');

            $this->smarty->assign('image_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_cf_'.$i.'_'.$fullSize.'.jpg');
            $this->smarty->assign('image_thumb_'.$i.'_url',$this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_ct_'.$i.'_'.$thumbSize.'.jpg'
            );
            }
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
	}

    function DisplayPrintable($id, &$params, $returnid)
    {
		$db = &$this->cms->db;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$content = ContentManager::GetAllContent(false);
		$curPageID = $this->cms->variables['content_id'];
        $printableItems = array();
        $showAttrs = explode(',',$params['fieldlist']);
		$lastCat = 'none';
		foreach ($content as $thisPage)
			{
            if (!$thisPage->Active())
                {
                continue;
                }
			if ($thisPage->Type() == 'aliasmodule')
				{
				$thisPage = $thisPage->GetAliasContent();
				}
			if ($thisPage->Type() == 'catalogcategory')
				{
				$lastCat = $thisPage->MenuText();
				continue;
				}
			if ($thisPage->Type() != 'catalogitem')
                {
                continue;
                }
			// approved for viewing
			$printThumbSize = $this->GetPreference('item_image_size_catalog',100);
			$thisItem['image'] = $this->cms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thisPage->Alias().'_p_1_'.$printThumbSize.'.jpg';
			$thisItem['link'] = $thisPage->GetUrl();
			$thisItem['title'] = $thisPage->MenuText();
			$thisItem['cat'] = $lastCat;
			$theseAttrs = $thisPage->getAttrs();
			foreach ($theseAttrs as $thisAttr)
				{
error_log($thisAttr);
				if (! in_array($thisAttr,$showAttrs))
					{
					continue;
					}
error_log('!');
				$safeattr = strtolower(preg_replace('/\W/','',$thisAttr));
				$thisItem[$safeattr] = $thisPage->GetPropertyValue($thisAttr);
				}
			array_push($printableItems,$thisItem);
			}
            
        $this->smarty->assign('items',$printableItems);
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);
	}


    function contentalpha($a, $b)
    {
      return strcasecmp($a->MenuText(), $b->MenuText());
    }

    function adminAttributes($id, &$params, $returnid, $message='')
    {
        $db = &$this->cms->db;
		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitattrs', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));

        $attributes = array();
        $query = "SELECT attribute, type_id FROM ".cms_db_prefix(). "module_catalog_attr ORDER BY attribute";
        $dbresult = $db->Execute($query);
        while ($dbresult !== false && $row = $dbresult->FetchRow())
        {
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr'.$row['type_id'].'[]', $row['attribute'], 25, 255);
           $onerow->type = $row['type_id'];
	       array_push($attributes, $onerow);
        }
        for ($i=0;$i<3;$i++)
        {
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr1[]', '', 25, 255);
           $onerow->type = 1;
           array_push($attributes, $onerow);
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr2[]', '', 25, 255);
           $onerow->type = 2;
           array_push($attributes, $onerow);
	       $onerow = new stdClass();
           $onerow->input = $this->CreateInputText($id, 'attr3[]', '', 25, 255);
           $onerow->type = 3;
	       array_push($attributes, $onerow);
        }

        $this->smarty->assign('tab_headers',$this->StartTabHeaders().
            $this->SetTabHeader('item',$this->Lang('title_item_tab')).
            $this->SetTabHeader('category',$this->Lang('title_category_tab')).
            $this->SetTabHeader('catalog',$this->Lang('title_printable_tab')).
            $this->EndTabHeaders().$this->StartTabContent());
        $this->smarty->assign('end_tab',$this->EndTab());
        $this->smarty->assign('tab_footers',$this->EndTabContent());
        $this->smarty->assign('start_item_tab',$this->StartTab('item'));
        $this->smarty->assign('start_category_tab',$this->StartTab('category'));
        $this->smarty->assign('start_catalog_tab',$this->StartTab('catalog'));

        $this->smarty->assign_by_ref('message', $message);
        $this->smarty->assign('attribute_inputs', $attributes);
        $this->smarty->assign('title_item_attributes', $this->Lang('title_item_tab'));
        $this->smarty->assign('title_catalog_attributes', $this->Lang('title_printable_tab'));
        $this->smarty->assign('title_category_attributes', $this->Lang('title_category_tab'));
        $this->smarty->assign('title_item_attributes_help', $this->Lang('title_item_attributes_help'));
        $this->smarty->assign('title_catalog_attributes_help', $this->Lang('title_catalog_attributes_help'));
        $this->smarty->assign('title_category_attributes_help', $this->Lang('title_category_attributes_help'));

        $this->smarty->assign('category', $this->Lang('manageattrs'));

        #Display template
        echo $this->ProcessTemplate('adminattrs.tpl');
    }

    function submitAttrs($id, &$params, $returnid)
    {
		$db = &$this->cms->db;

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
        $this->adminAttributes($id, $params, $returnid, $this->Lang('attrsupdated'));
    }

    function adminPrefs($id, &$params, $returnid, $message='')
    {
        $db = &$this->cms->db;
		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submitprefs', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));

        $this->smarty->assign('tab_headers',$this->StartTabHeaders().
        $this->SetTabHeader('itemimage',$this->Lang('title_item_image_tab')).
        $this->SetTabHeader('categoryimage',$this->Lang('title_category_image_tab')).
        $this->SetTabHeader('printable',$this->Lang('title_printable_tab')).
        $this->SetTabHeader('aspect',$this->Lang('title_aspect_tab')).
        $this->EndTabHeaders().$this->StartTabContent());
        $this->smarty->assign('end_tab',$this->EndTab());
        $this->smarty->assign('tab_footers',$this->EndTabContent());
        $this->smarty->assign('start_item_image_tab',$this->StartTab('itemimage'));
        $this->smarty->assign('start_category_image_tab',$this->StartTab('categoryimage'));
        $this->smarty->assign('start_printable_tab',$this->StartTab('printable'));
        $this->smarty->assign('start_aspect_tab',$this->StartTab('aspect'));

        $this->smarty->assign('title_item_image_count', $this->Lang('title_item_image_count'));
        $this->smarty->assign('title_category_image_count', $this->Lang('title_category_image_count'));
        $this->smarty->assign('title_item_image_size_hero', $this->Lang('title_item_image_size_hero'));
        $this->smarty->assign('title_item_image_size_thumbnail', $this->Lang('title_item_image_size_thumbnail'));
        $this->smarty->assign('title_category_image_size_hero', $this->Lang('title_category_image_size_hero'));
        $this->smarty->assign('title_category_image_size_thumbnail', $this->Lang('title_category_image_size_thumbnail'));
        $this->smarty->assign('title_item_image_size_category', $this->Lang('title_item_image_size_category'));
        $this->smarty->assign('title_item_image_size_catalog', $this->Lang('title_item_image_size_catalog'));
        $this->smarty->assign('title_category_recurse',$this->Lang('title_category_recurse'));
        $this->smarty->assign('title_printable_sort_order',$this->Lang('title_printable_sort_order'));
        $this->smarty->assign('title_force_aspect_ratio', $this->Lang('title_force_aspect_ratio'));
        $this->smarty->assign('title_image_aspect_ratio', $this->Lang('title_image_aspect_ratio'));
        $this->smarty->assign('title_aspect_ratio_help', $this->Lang('title_aspect_ratio_help'));
        $number = array();
        for ($i=1;$i<16;$i++)
        	{
        	$number[$i]=$i;
        	}
        $this->smarty->assign('input_item_image_count', $this->CreateInputDropdown($id, 'item_image_count', $number, -1,  $this->GetPreference('image_count', '2')));
        $this->smarty->assign('input_category_image_count', $this->CreateInputDropdown($id, 'category_image_count', $number, -1,  $this->GetPreference('category_count', '1')));

		$recurse =  $this->GetPreference('category_recurse', 'mixed_one'); 
		$this->smarty->assign('input_category_recurse',
			'<input type="radio" name="'.$id.'category_recurse" value="items_all"' .
				($recurse == 'items_all'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_items_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="items_one"' .
				($recurse == 'items_one'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_items_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_all"' .
				($recurse == 'categories_all'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_categories_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_one"' .
				($recurse == 'categories_one'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_categories_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_all"' .
				($recurse == 'mixed_all'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_mixed_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_one"' .
				($recurse == 'mixed_one'?' checked="checked"':'').' />'.
				$this->Lang('title_category_recurse_mixed_one'));
        $this->smarty->assign('input_item_image_size_hero', $this->CreateInputText($id, 'item_image_size_hero', $this->GetPreference('item_image_size_hero', '400'), 10, 10));
        $this->smarty->assign('input_item_image_size_thumbnail', $this->CreateInputText($id, 'item_image_size_thumbnail', $this->GetPreference('item_image_size_thumbnail', '70'), 10, 10));
        $this->smarty->assign('input_category_image_size_hero', $this->CreateInputText($id, 'category_image_size_hero', $this->GetPreference('category_image_size_hero', '400'), 10, 10));
        $this->smarty->assign('input_category_image_size_thumbnail', $this->CreateInputText($id, 'category_image_size_thumbnail', $this->GetPreference('category_image_size_thumbnail', '90'), 10, 10));
        $this->smarty->assign('input_item_image_size_category', $this->CreateInputText($id, 'item_image_size_category', $this->GetPreference('item_image_size_category', '70'), 10, 10));
        $this->smarty->assign('input_item_image_size_catalog', $this->CreateInputText($id, 'item_image_size_catalog', $this->GetPreference('item_image_size_catalog', '100'), 10, 10));

		$this->smarty->assign('input_force_aspect_ratio',$this->CreateInputCheckbox($id, 'force_aspect_ratio', 1, $this->GetPreference('force_aspect_ratio', 0)).'&nbsp;'.
		$this->Lang('title_force_aspect_ratio_label'));
        $this->smarty->assign('input_image_aspect_ratio', $this->CreateInputText($id, 'image_aspect_ratio', $this->GetPreference('image_aspect_ratio', '4:3'), 10, 10));

        $this->smarty->assign('title_items_per_page',$this->Lang('title_items_per_page'));
        $this->smarty->assign('input_items_per_page',$this->CreateInputDropdown($id,
         		'items_per_page',
                array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',
                	'7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12',
                	'13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                	'19'=>'19','20'=>'20','24'=>'24','25'=>'25','30'=>'30','40'=>'40',
                	'50'=>'50', '1000'=>'1000'), -1, $this->GetPreference('category_items_per_page','10')));
        $this->smarty->assign('title_item_sort_order',$this->Lang('title_item_sort_order'));
		$this->smarty->assign('input_item_sort_order',$this->CreateInputDropdown($id,
		 	'item_sort_order',array($this->Lang('natural_order')=>'natural',
		 	$this->Lang('alpha_order')=>'alpha'), -1, $this->GetPreference('category_sort_order','natural')));
		$this->smarty->assign('input_printable_sort_order',$this->CreateInputDropdown($id,
		 	'printable_sort_order',array($this->Lang('natural_order')=>'natural',
		 	$this->Lang('alpha_order')=>'alpha'), -1, $this->GetPreference('printable_sort_order','natural')));

        $this->smarty->assign_by_ref('message', $message);

        $this->smarty->assign('category', $this->Lang('manageprefs'));

        #Display template
        echo $this->ProcessTemplate('adminprefs.tpl');
    }

    function globalOpsForm($id, &$params, $returnid, $message='')
    {
        $db = &$this->cms->db;
		$this->smarty->assign('startform', $this->CreateFormStart($id, 'globalsubmit', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Update Entire Catalog'));

        $this->smarty->assign('title_category_recurse',$this->Lang('title_global_category_recurse'));

		$this->smarty->assign('input_category_recurse',
			'<input type="radio" name="'.$id.'category_recurse" value="nochange" checked="checked" />'.$this->Lang('noglobalchange').'<br />' .
			'<input type="radio" name="'.$id.'category_recurse" value="items_all" />'.
				$this->Lang('title_category_recurse_items_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="items_one" />'.
				$this->Lang('title_category_recurse_items_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_all" />'.
				$this->Lang('title_category_recurse_categories_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="categories_one" />'.
				$this->Lang('title_category_recurse_categories_one').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_all" />'.
				$this->Lang('title_category_recurse_mixed_all').'<br />'.
			'<input type="radio" name="'.$id.'category_recurse" value="mixed_one" />'.
				$this->Lang('title_category_recurse_mixed_one'));
        $this->smarty->assign('title_items_per_page',$this->Lang('title_global_items_per_page'));
        $this->smarty->assign('input_items_per_page',$this->CreateInputDropdown($id,
         		'items_per_page',
                array($this->Lang('noglobalchange')=>'-1',
                	'1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',
                	'7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11',
                	'12'=>'12', '13'=>'13','14'=>'14','15'=>'15','16'=>'16',
                	'17'=>'17','18'=>'18','19'=>'19','20'=>'20','24'=>'24',
                	'25'=>'25','30'=>'30','40'=>'40',
                	'50'=>'50', '1000'=>'1000'), -1, '-1'));
        $this->smarty->assign('title_item_sort_order',$this->Lang('title_global_item_sort_order'));
		$this->smarty->assign('input_item_sort_order',$this->CreateInputDropdown($id,
		 	'item_sort_order',array($this->Lang('noglobalchange')=>'nochange',
		 	$this->Lang('natural_order')=>'natural',
		 	$this->Lang('alpha_order')=>'alpha'), -1, 'nochange'));

        $this->smarty->assign_by_ref('message', $message);

        $this->smarty->assign('category', $this->Lang('globalops'));

        #Display template
        echo $this->ProcessTemplate('adminglobalops.tpl');
    }


	function globalOpsSubmit($id, &$params, $returnid)
	{
		$db = &$this->cms->db;
		if (isset($params['item_sort_order']) &&
			$params['item_sort_order'] != 'nochange')
			{
			// update all item sort order
			$query = "update ".
				cms_db_prefix()."content_props cp, ".cms_db_prefix().
				"content c set cp.content=? where ".
				"cp.content_id=c.content_id and c.type='catalogcategory' ".
				"and cp.prop_name='sort_order'";
			$dbresult = $db->Execute($query,array($params['item_sort_order']));
			}
		if (isset($params['category_recurse']) &&
			$params['category_recurse'] != 'nochange')
			{
			// update display rules
			$query = "update ".
				cms_db_prefix()."content_props cp, ".cms_db_prefix().
				"content c set cp.content=? where ".
				"cp.content_id=c.content_id and c.type='catalogcategory' ".
				"and cp.prop_name='recurse'";
			$dbresult = $db->Execute($query,array($params['category_recurse']));
			}
		if (isset($params['items_per_page']) &&
			$params['items_per_page'] != -1)
			{
			// update display rules
			$query = "update ".
				cms_db_prefix()."content_props cp, ".cms_db_prefix().
				"content c set cp.content=? where ".
				"cp.content_id=c.content_id and c.type='catalogcategory' ".
				"and cp.prop_name='items_per_page'";
			$dbresult = $db->Execute($query,array($params['items_per_page']));
			}
		$this->globalOpsForm($id,$params,$returnid,$this->Lang('globallyupdated'));
	}


    function submitPrefs($id, &$params, $returnid)
    {
        $this->SetPreference('item_image_count', isset($params['item_image_count'])?$params['item_image_count']:2);
        $this->SetPreference('category_image_count', isset($params['category_image_count'])?$params['category_image_count']:1);
		$this->SetPreference('item_image_size_hero', isset($params['item_image_size_hero'])?$params['item_image_size_hero']:'400');
		$this->SetPreference('item_image_size_thumbnail', isset($params['item_image_size_thumbnail'])?$params['item_image_size_thumbnail']:'70');
		$this->SetPreference('category_image_size_hero', isset($params['category_image_size_hero'])?$params['category_image_size_hero']:'400');
		$this->SetPreference('category_image_size_thumbnail', isset($params['category_image_size_thumbnail'])?$params['category_image_size_thumbnail']:'90');
		$this->SetPreference('item_image_size_category', isset($params['item_image_size_category'])?$params['item_image_size_category']:'70');
		$this->SetPreference('item_image_size_catalog', isset($params['item_image_size_catalog'])?$params['item_image_size_catalog']:'100');
		$this->SetPreference('force_aspect_ratio', isset($params['force_aspect_ratio'])?$params['force_aspect_ratio']:0);
		$this->SetPreference('image_aspect_ratio', isset($params['image_aspect_ratio'])?$params['image_aspect_ratio']:'4:3');
		$this->SetPreference('category_recurse', isset($params['category_recurse'])?$params['category_recurse']:'mixed_one');
		$this->SetPreference('category_sort_order', isset($params['item_sort_order'])?$params['item_sort_order']:'natural');
		$this->SetPreference('category_items_per_page', isset($params['items_per_page'])?$params['items_per_page']:'10');

        return $this->adminPrefs($id, $params, $returnid, $this->Lang('prefsupdated'));
    }


    function listTemplates($id, &$params, $returnid, $message="")
    {
        global $gCms;
		$db = &$this->cms->db;

		$this->initAdminNav($id, $params, $returnid);

        //Load the shows
        $entryarray = array();

        $query = "SELECT t.id, t.title, tt.name as type FROM ".
        	cms_db_prefix()."module_catalog_template t, ".
        	cms_db_prefix().
        	"module_catalog_template_type tt WHERE t.type_id = tt.type_id ORDER by t.type_id, title";
        $dbresult = $db->Execute($query);

        $rowclass = 'row1';

        while ($dbresult !== false && $row = $dbresult->FetchRow())
        {
	       $onerow = new stdClass();

	       $onerow->id = $row['id'];
	       $onerow->type = $row['type'];
	       $onerow->title = $this->CreateLink($id, 'edittempl', $returnid,
	       		$row['title'], array('template_id'=>$row['id']));
	       $onerow->rowclass = $rowclass;

	       $onerow->editlink = $this->CreateLink($id, 'edittempl', $returnid,
	       		$gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif',
	       			$this->Lang('edit'),'','','systemicon'),
	       		 	array('template_id'=>$row['id']));
	       $onerow->deletelink = $this->CreateLink($id, 'deletetempl', $returnid,
	       		$gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif',
	       			$this->Lang('delete'),'','','systemicon'),
	       			array('template_id'=>$row['id']), $this->Lang('areyousure','Template'));

	       array_push($entryarray, $onerow);

	       ($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
        }

        $this->smarty->assign_by_ref('items', $entryarray);
        $this->smarty->assign('itemcount', count($entryarray));
        $this->smarty->assign('category',$this->Lang('templatelist'));
        $this->smarty->assign('title_template',$this->Lang('title_template'));
        $this->smarty->assign('title_template_type',$this->Lang('title_template_type'));
        $this->smarty->assign('notemplates',$this->Lang('notemplates'));
		if ($message != '')
			{
			$this->smarty->assign_by_ref('message',$message);
			}

        $this->smarty->assign('addlink',
            $this->CreateLink($id, 'edittempl', $returnid,
                $gCms->variables['admintheme']->DisplayImage('icons/system/newobject.gif',
                $this->Lang('addtemplate'),'','','systemicon'), array(), '', false, false, '') .' '.
            $this->CreateLink($id, 'edittempl', $returnid,
                $this->Lang('addtemplate'), array(), '', false, false, 'class="pageoptions"').
                '&nbsp;&nbsp;'.
            $this->CreateLink($id, 'reimport', $returnid,
                '<img src="'.$gCms->config['root_url'].'/modules/Cataloger/images/reload.gif" class="systemicon" alt="'.$this->Lang('reimporttemplates').'"  title="'.$this->Lang('reimporttemplates').'" />', array(), '', false, false, '') .' '.
            $this->CreateLink($id, 'reimport', $returnid,
                $this->Lang('reimporttemplates'), array(), '', false, false, 'class="pageoptions"')             
                );

        #Display template
        echo $this->ProcessTemplate('templatelist.tpl');
    }

    function editTemplate($id, &$params, $returnid)
    {
		$db = &$this->cms->db;

		$typeids = array();
		$query = 'SELECT type_id, name FROM ' .
				cms_db_prefix(). 'module_catalog_template_type';
        $dbresult = $db->Execute($query);
        while ($dbresult !== false && $row = $dbresult->FetchRow())
        {
        	$typeids[$row['name']] = $row['type_id'];
        }

		if (isset($params['template_id']))
			{
			// editing a template
			$query = 'SELECT title, template, type_id FROM ' .
				cms_db_prefix(). 'module_catalog_template WHERE id=?';
			$dbresult = $db->Execute($query,array($params['template_id']));
			$row = $dbresult->FetchRow();
			$templateid = $params['template_id'];
			$title=$row['title'];
			$template=$row['template'];
			$type_id=$row['type_id'];
			}
		else
			{
			// adding a template
			$templateid = '';
			$title='';
			$template='';
			$this->smarty->assign('op', 'Add Template');
			}
        $query = "SELECT attribute, type_id FROM ".cms_db_prefix()."module_catalog_attr";
        $dbresult = $db->Execute($query);
        $attrs = '<h3>'.$this->Lang('title_item_template_vars').'</h3>{$title}, {$notes}, ';
        $cattrs = '<h3>'.$this->Lang('title_cat_template_vars').'</h3>{$title}, {$notes}, {$prev}, {$prevurl}, {$navstr}, {$next}, {$nexturl}, {$items}, ';

        while ($dbresult !== false && $row = $dbresult->FetchRow())
        	{
            $safeattr = strtolower(preg_replace('/\W/','',$row['attribute']));
            if ($row['type_id'] == 1)
            	{
            	$attrs .= '{$'.$safeattr.'}, ';
            	}
            else if ($row['type_id'] == 2)
            	{
				$cattrs .= '{$'.$safeattr.'}, ';
				}
        	}
        $image_count = $this->GetPreference('item_image_count', '1');
        for ($i=1;$i<=$image_count;$i++)
        	{
        	$attrs .= '{$image_'.$i.'_url}, {$image_thumb_'.$i;
        	$attrs .= '_url}, ';
        	}
        $attrs .= '{$image_url_array}, ';
        $attrs .= '{$image_thumb_url_array}';
        $attrs = rtrim($attrs,', ');

        $image_count = $this->GetPreference('category_image_count', '1');
        for ($i=1;$i<=$image_count;$i++)
        	{
        	$cattrs .= '{$image_'.$i.'_url}, {$image_thumb_'.$i;
        	$cattrs .= '_url}, ';
        	}
        $cattrs .= '{$image_url_array}, ';
        $cattrs .= '{$image_thumb_url_array}';
        $cattrs = rtrim($cattrs,', ');
        $cattrs .= '<h3>$items array contents:</h3>';
        $cattrs .= '$items[].title, $items[].link, $items[].image';

        
		$this->smarty->assign('startform', $this->CreateFormStart($id, 'submittempl', $returnid));
		$this->smarty->assign('endform', $this->CreateFormEnd());
		$this->smarty->assign('hidden',$this->CreateInputHidden($id, 'template_id', $templateid));
        $this->smarty->assign('title_title',$this->Lang('title_title'));
		$this->smarty->assign('title_template',$this->Lang('title_template'));
		$this->smarty->assign('title_template_type',$this->Lang('title_template_type'));
		$this->smarty->assign('title_avail_attrs',$this->Lang('title_avail_attrs'));
		if (isset($type_id))
			{
			if ($type_id == 1)
				{
				$this->smarty->assign_by_ref('avail_attrs',$attrs);
				}
			else if ($type_id == 2)
				{
				$this->smarty->assign_by_ref('avail_attrs',$cattrs);		
				}
			}
		else
			{
			$this->smarty->assign('avail_attrs',$attrs.', '.$cattrs);
			}

//		$this->smarty->assign('title_avail_imattrs',$this->Lang('title_avail_imattrs'));
//		$this->smarty->assign_by_ref('avail_imattrs',$imattrs);
		$this->smarty->assign('input_template_type',$this->CreateInputDropdown($id, 'type_id', $typeids, -1, isset($type_id)?$type_id:''));

        $this->smarty->assign('input_title',$this->CreateInputText($id, 'title', $title, 20, 255));
        $this->smarty->assign('input_template',$this->CreateTextArea(false, $id, $template, 'templ'));

		$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
		echo $this->ProcessTemplate('edittemplate.tpl');
    }

	function submitTemplate($id, &$params, $returnid)
	{
		$db = &$this->cms->db;
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
		$this->listTemplates($id, $params, $returnid,$this->Lang('templateupdated'));

	}

	function delTemplate($id, &$params, $returnid)
	{
		$db = &$this->cms->db;
		if (! empty($params['template_id']))
			{
			$query = 'DELETE FROM '. cms_db_prefix().
				'module_catalog_template WHERE id=?';
			$dbresult = $db->Execute($query,array($params['template_id']));
			}
		$this->DeleteTemplate('glossary_'.$params['template_id']);
		$this->listTemplates($id, $params, $returnid,$this->Lang('templatedeleted'));

	}


    function initAdminNav($id, &$params, $returnid)
        {
        global $gCms;
		$this->smarty->assign('innernav',
			$this->CreateLink($id, 'listtempl', $returnid,
				$gCms->variables['admintheme']->DisplayImage('icons/topfiles/template.gif',
                $this->Lang('listtempl'),'','','systemicon'), array()) .
			$this->CreateLink($id, 'listtempl', $returnid, $this->Lang('listtempl'), array()) .
			' : ' .
			$this->CreateLink($id, 'adminattrs', $returnid,
				$gCms->variables['admintheme']->DisplayImage('icons/topfiles/images.gif',
                $this->Lang('manageattrs'),'','','systemicon'), array()) .
			$this->CreateLink($id, 'adminattrs', $returnid, $this->Lang('manageattrs'), array()) .
			' : ' .
			$this->CreateLink($id, 'globalops', $returnid,
				'<img class="systemicon" alt="'.$this->Lang('globalops').'" title="'.$this->Lang('globalops').'" src="'.$gCms->config['root_url'].'/modules/Cataloger/images/global.gif" />') .
			$this->CreateLink($id, 'globalops', $returnid, $this->Lang('globalops'), array()) .
			' : ' .
			$this->CreateLink($id, 'adminprefs', $returnid,
				$gCms->variables['admintheme']->DisplayImage('icons/topfiles/siteprefs.gif',
                $this->Lang('manageprefs'),'','','systemicon'), array()) .
			$this->CreateLink($id, 'adminprefs', $returnid, $this->Lang('manageprefs'), array()));
        }

	function GetAdminCategory()
	{
		return 'extensions';
	}

	
    function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify Catalog Settings');
    }


	function GetHelp($lang='en_US')
	{
		return $this->Lang('helptext');
	}

	function GetAuthor()
	{
		return 'SjG';
	}

	function CheckAccess($permission='Modify Catalog Settings')
	{
		$access = $this->CheckPermission($permission);
		if (!$access)
			{
			echo "<p class=\"error\">".$this->Lang('needpermission',$permission)."</p>";
			return false;
			}
		return true;
	}


	function GetAuthorEmail()
	{
		return 'sjg@cmsmodules.com';
	}

	function GetChangeLog()
	{
		return $this->Lang('changelog');
	}

    function displayError($message)
    {
        $this->smarty->assign_by_ref('error',$message);
        echo $this->ProcessTemplate('error.tpl');
    }

}

class CatalogItem extends CMSModuleContentType
{
    var $attrs;

	function SetProperties()
	{
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
        	$this->mProperties->Add('string', $thisAttr, '');
			}
		$this->mProperties->Add('string', 'notes', '');
		$this->mProperties->Add('string', 'sub_template', '');

		#Turn on preview
		$this->mPreview = true;

		#Turn off caching
		$this->mCachable = false;
	}

    function getUserAttributes()
    {
        global $gCms;
        $vars = &$gCms->variables;
        $db = &$gCms->db;
        if (isset($vars['catalog_attrs']) && is_array($vars['catalog_attrs']))
            {
            $this->attrs = &$vars['catalog_attrs'];
            }
        else
            {
            $vars['catalog_attrs'] = array();
            $query = "SELECT attribute FROM ".
        	   cms_db_prefix()."module_catalog_attr WHERE type_id=1";
            $dbresult = $db->Execute($query);
            while ($dbresult !== false && $row = $dbresult->FetchRow())
        	   {
        	   array_push($vars['catalog_attrs'],$row['attribute']);
        	   }
            $this->attrs = &$vars['catalog_attrs'];
            }
    }
    
    function &getAttrs()
    {
    	$this->getUserAttributes();
    	return $this->attrs;
    }

	function TabNames()
	{
		return array(lang('main'), 'Images', lang('options'));
	}

	function EditAsArray($adding = false, $tab = 0, $showadmin=false)
	{
		global $gCms;
		$config = &$gCms->config;
		$db = &$gCms->db;
		$ret = array();
		$stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}
		else
		{
			$defaulttemplate = TemplateOperations::LoadDefaultTemplate();
			if (isset($defaulttemplate))
			{
				$this->mTemplateId = $defaulttemplate->id;
				$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
			}
		}

        if ($tab == 0)
		{
        $query = "SELECT id, title FROM ".
        	cms_db_prefix().
        	"module_catalog_template WHERE type_id=1 ORDER by title";
        $subTemplates = array();
        $dbresult = $db->Execute($query);

        while ($dbresult !== false && $row = $dbresult->FetchRow())
        	{
        	$subTemplates[$row['title']]=$row['id'];
        	}		

		array_push($ret,array(lang('title'),
			'<input type="text" name="title" value="'.$this->mName.'">'));
		array_push($ret,array(lang('menutext'),
			'<input type="text" name="menutext" value="'.
			htmlspecialchars($this->mMenuText, ENT_QUOTES).'">'));
		if (!($config['auto_alias_content'] == true && $adding))
		{
			array_push($ret,array(lang('pagealias'),
				'<input type="text" name="alias" value="'.
				htmlspecialchars($this->mAlias, ENT_QUOTES).'">'));
		}
		array_push($ret,array(lang('parent').
			'/Category',
			ContentManager::CreateHierarchyDropdown($this->mId,
			$this->mParentId)));
		array_push($ret,array('Page '.
			lang('template'),
			TemplateOperations::TemplateDropdown('template_id',
			$this->mTemplateId)));
		array_push($ret,array('Sub '.
			lang('template'),
			CMSModule::CreateInputDropdown('',
			'sub_template', $subTemplates, -1,
			$this->GetPropertyValue('sub_template'))));
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
            $safeattr = strtolower(preg_replace('/\W/','', $thisAttr));
        	array_push($ret,array($thisAttr,
        		'<input type="text" name="'.$safeattr.'" value="'.
        		htmlspecialchars($this->GetPropertyValue($thisAttr),ENT_QUOTES).
        		'" />'));
			}

		array_push($ret,array('Notes',create_textarea(true, $this->GetPropertyValue('notes'), 'notes', '', 'notes', '', $stylesheet, 80, 10)));
		}
		if ($tab == 1)
		  {
            $imgcount = get_site_preference('Cataloger_mapi_pref_item_image_count', '2');
            $thumbsize = get_site_preference('Cataloger_mapi_pref_item_image_size_thumbnail', '70');
            $imgsrc = '<table>';
            for ($i=1; $i<= $imgcount; $i++)
                {
                $imgsrc .= '<tr><td style="vertical-align:top">Image '.$i.':</td><td style="vertical-align:top">';
				$imgsrc .= '<img src="'.$config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$this->mAlias.'_t_'.$i.'_'.$thumbsize.'.jpg" />';
                $imgsrc .= '</td><td style="vertical-align:top">&nbsp;<input type="file" name="image'.$i.'" />';
                $imgsrc .= '</td></tr>';
                }
            $imgsrc .= '</table>';
            array_push($ret,array('Images:', $imgsrc));
            }
		if ($tab == 2)
		{
        array_push($ret,array(lang('active'),'<input type="checkbox" name="active"'.($this->mActive?' checked="true"':'').'>'));
		array_push($ret,array(lang('showinmenu'),'<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="true"':'').'>'));
			if (!$adding && $showadmin)
			{
				array_push($ret, array('Owner:',@UserOperations::GenerateDropdown($this->Owner())));
			}
			if ($adding || $showadmin)
			{
				array_push($ret, $this->ShowAdditionalEditors());
			}
        }
		return $ret;
	}

	function FillParams(&$params)
	{
		global $gCms;
		$config = &$gCms->config;
		$db = &$gCms->db;

		if (isset($params))
		{
			$parameters = array('notes', 'sub_template');

			foreach ($parameters as $oneparam)
			{
				if (isset($params[$oneparam]))
				{
					$this->SetPropertyValue($oneparam, $params[$oneparam]);
				}
			}
			
			$this->getUserAttributes();
			foreach ($this->attrs as $thisAttr)
				{
				array_push($parameters,$thisAttr);
				}
            foreach ($parameters as $thisParam)
               {
               $safeattr = strtolower(preg_replace('/\W/','', $thisParam));
        	   if (isset($params[$safeattr]))
        	       {
                    $this->SetPropertyValue($thisParam, $params[$safeattr]);
                   }
               }

			if (isset($params['title']))
			{
				$this->mName = $params['title'];
			}
			if (isset($params['menutext']))
			{
				$this->mMenuText = $params['menutext'];
			}
			if (isset($params['template_id']))
			{
				$this->mTemplateId = $params['template_id'];
			}
			if (isset($params['alias']))
			{
				$this->SetAlias($params['alias']);
			}
			else
			{
				$this->SetAlias('');
			}
			if (isset($params['parent_id']))
			{
				if ($this->mParentId != $params['parent_id'])
				{
					$this->mHierarchy = '';
					$this->mItemOrder = -1;
				}
				$this->mParentId = $params['parent_id'];
			}
			if (isset($params['active']))
			{
				$this->mActive = true;
			}
			else
			{
				$this->mActive = false;
			}
			if (isset($params['showinmenu']))
			{
				$this->mShowInMenu = true;
			}
			else
			{
				$this->mShowInMenu = false;
			}
			
			// Copy and resize the image files...
            $imgcount = get_site_preference('Cataloger_mapi_pref_item_image_count', '2');
            $herosize = get_site_preference('Cataloger_mapi_pref_item_image_size_hero', '400');
            $thumbsize = get_site_preference('Cataloger_mapi_pref_item_image_size_thumbnail', '70');
            $catalogsize = get_site_preference('Cataloger_mapi_pref_item_image_size_catalog', '100');
            $categorysize = get_site_preference('Cataloger_mapi_pref_item_image_size_category', '70');
            for ($i=1; $i<= $imgcount; $i++)
                {
			    if (isset($_FILES['image'.$i]['size']) && $_FILES['image'.$i]['size']>0)
                    {
                    // keep original image, but purge old thumbnails
                    copy($_FILES['image'.$i]['tmp_name'],
                        dirname($config['uploads_path'].
                        '/images/catalog_src/index.html') .
                        '/'.$this->mAlias.'_src_'.$i.'.jpg');
                    }
                }
		}
	}

	function PopulateParams(&$params)
	{
		global $gCms;
		$config = &$gCms->config;
        $db = &$gCms->db;

		$parameters = array('notes', 'sub_template');
		foreach ($parameters as $oneparam)
		{
			$tmp = $this->GetPropertyValue($oneparam);
			if (isset($tmp) && ! empty($tmp))
			{
				$params[$oneparam] = $tmp;
			}
		}
		
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
			array_push($parameters,$thisAttr);
			}
		$safeattrlist = array();
        foreach ($parameters as $thisParam)
            {
            $safeattr = strtolower(preg_replace('/\W/','', $thisParam));
        	$tmp = $this->GetPropertyValue($thisParam);
			if (isset($tmp) && ! empty($tmp))
                {
				$params[$safeattr] = $tmp;
				if ($safeattr != 'sub_template')
					{
					$thisSafeAttr = array();
					$thisSafeAttr['name']=ucfirst($thisParam);
					$thisSafeAttr['key']='{$'.$safeattr.'}';
					array_push($safeattrlist,$thisSafeAttr);
					}
                }
            }
		$params['title'] = $this->mName;
		$params['menutext'] = $this->mMenuText;
		$params['template_id'] = $this->mTemplateId;
		$params['alias'] = $this->mAlias;
		$params['parent_id'] = $this->mParentId;
		$params['active'] = $this->mActive;
		$params['showinmenu']=$this->mShowInMenu;
		$params['attrlist'] = $safeattrlist;
	}



	function Show()
	{
		global $gCms;

		$params = array();

		$this->PopulateParams($params);

		$pf = new Cataloger();

		@ob_start();
		$pf->DoAction('default', 'catalogmodule', $params);
		$text = @ob_get_contents();
		@ob_end_clean();
		return '{literal}'.$text.'{/literal}';
	}


    function FriendlyName()
	{
		return 'Catalog Item';
	}
}

class CatalogCategory extends CMSModuleContentType
{
    var $attrs;

	function SetProperties()
	{
		global $gCms;
		$config = &$gCms->config;
        $this->getUserAttributes();
        foreach($this->attrs as $thisAttr)
            {
            $this->mProperties->Add('string', $thisAttr, '');
            }

		$this->mProperties->Add('string', 'sort_order', '');
		$this->mProperties->Add('string', 'recurse', '');
		$this->mProperties->Add('string', 'notes', '');
		$this->mProperties->Add('string', 'sub_template', '');
		$this->mProperties->Add('int', 'items_per_page', -1);
		
		#Turn on preview
		$this->mPreview = true;

		#Turn off caching
		$this->mCachable = false;
	}

    function getUserAttributes()
    {
        global $gCms;
        $vars = &$gCms->variables;
        $db = &$gCms->db;
        if (isset($vars['catalog_cat_attrs']) && is_array($vars['catalog_cat_attrs']))
            {
            $this->attrs = &$vars['catalog_cat_attrs'];
            }
        else
            {
            $vars['catalog_cat_attrs'] = array();
            $query = "SELECT attribute FROM ".
        	   cms_db_prefix()."module_catalog_attr WHERE type_id=2";
            $dbresult = $db->Execute($query);
            while ($dbresult !== false && $row = $dbresult->FetchRow())
        	   {
        	   array_push($vars['catalog_cat_attrs'],$row['attribute']);
        	   }
            $this->attrs = &$vars['catalog_cat_attrs'];
            }
    }



	function TabNames()
	{
		return array(lang('main'), 'Images', lang('options'), 'Permissions');
	}

	function EditAsArray($adding = false, $tab = 0, $showadmin=false)
	{
		global $gCms;
		$config = &$gCms->config;
		$db = &$gCms->db;
		$ret = array();
		$stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}
		else
		{
			$defaulttemplate = TemplateOperations::LoadDefaultTemplate();
			if (isset($defaulttemplate))
			{
				$this->mTemplateId = $defaulttemplate->id;
				$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
			}
		}

        if ($tab == 0)
		{
        $query = "SELECT id, title FROM ".
        	cms_db_prefix()."module_catalog_template WHERE type_id=2 ORDER by title";
        $subTemplates = array();
        $dbresult = $db->Execute($query);

        while ($dbresult !== false && $row = $dbresult->FetchRow())
        	{
        	$subTemplates[$row['title']]=$row['id'];
        	}		

		array_push($ret,array(lang('title'),'<input type="text" name="title" value="'.$this->mName.'">'));
		array_push($ret,array(lang('menutext'),'<input type="text" name="menutext" value="'.htmlspecialchars($this->mMenuText,ENT_QUOTES).'">'));
		if (!($config['auto_alias_content'] == true && $adding))
		{
			array_push($ret,array(lang('pagealias'),'<input type="text" name="alias" value="'.htmlspecialchars($this->mAlias,ENT_QUOTES).'">'));
		}
		array_push($ret,array(lang('parent').'/Category',ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId)));
		array_push($ret,array('Page '.lang('template'),TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId)));
		array_push($ret,array('Sub '.lang('template'),CMSModule::CreateInputDropdown('', 'sub_template', $subTemplates, -1, $this->GetPropertyValue('sub_template'))));
        
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
            $safeattr = strtolower(preg_replace('/\W/','', $thisAttr));
        	array_push($ret,array($thisAttr,
        		'<input type="text" name="'.$safeattr.'" value="'.
        		htmlspecialchars($this->GetPropertyValue($thisAttr),ENT_QUOTES).
        		'" />'));
			}

		array_push($ret,array('Notes',create_textarea(true, $this->GetPropertyValue('notes'), 'notes', '', 'notes', '', $stylesheet, 80, 10)));
		}
		if ($tab == 1)
		  {
            $imgcount = get_site_preference('Cataloger_mapi_pref_category_image_count', '1');
            $thumbsize = get_site_preference('Cataloger_mapi_pref_category_image_size_thumbnail', '90');
            $imgsrc = '<table>';
            for ($i=1; $i<= $imgcount; $i++)
                {
                $imgsrc .= '<tr><td style="vertical-align:top">Image '.$i.':</td><td style="vertical-align:top">';
				$imgsrc .= '<img src="'.$config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$this->mAlias.'_ct_'.$i.'_'.$thumbsize.'.jpg" />';
                $imgsrc .= '</td><td style="vertical-align:top">&nbsp;<input type="file" name="image'.$i.'" />';
                $imgsrc .= '</td></tr>';
                }
            $imgsrc .= '</table>';
            array_push($ret,array('Images:', $imgsrc));
            }
		if ($tab == 2)
		{
			$ipp = $this->GetPropertyValue('items_per_page');
			if ($ipp == -1)
				{
				$ipp = get_site_preference('Cataloger_mapi_pref_category_items_per_page', '10');
				}
			$so = $this->GetPropertyValue('sort_order');
			if ($so == '')
				{
				$so = get_site_preference('Cataloger_mapi_pref_category_sort_order', 'natural');
				}
            array_push($ret,array('Items Per Page',CMSModule::CreateInputDropdown('', 'items_per_page',
                array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6',
                	'7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12',
                	'13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                	'19'=>'19','20'=>'20','24'=>'24','25'=>'25','30'=>'30','40'=>'40',
                	'50'=>'50', '1000'=>'1000'), -1, $ipp)));

            array_push($ret,array('Item Sort Order',CMSModule::CreateInputDropdown('', 'sort_order',
                array("Navigation Order"=>'natural', "Alphabetical Order"=>'alpha'), -1, $so)));

            $recurse = $this->GetPropertyValue('recurse');
            if ($recurse == '')
                {
                $recurse = get_site_preference('Cataloger_mapi_pref_category_recurse', 'mixed_one');
                }
            array_push($ret,array('Display Behavior',
            '<table><tr><td><input type="radio" name="recurse" value="items_all" '.(($recurse=='items_all')?'checked':'').'/>&nbsp;Include all Items within this category, including items in sub-categories</td></tr>'.
            '<tr><td><input type="radio" name="recurse" value="items_one" '.(($recurse=='items_one')?'checked':'').'/>&nbsp;Include all Items immediately within this category, but not items in sub-categories</td></tr>' .
            '<tr><td><input type="radio" name="recurse" value="categories_all" '.(($recurse=='categories_all')?'checked':'').'/>&nbsp;Include all Categories within this category, including Categories in sub-Categories</td></tr>' .
            '<tr><td><input type="radio" name="recurse" value="categories_one" '.(($recurse=='categories_one')?'checked':'').'/>&nbsp;Include all Categories immediately within this category, but not Categories in sub-Categories</td></tr>' .
            '<tr><td><input type="radio" name="recurse" value="mixed_all" '.(($recurse=='mixed_all')?'checked':'').'/>&nbsp;Include all Items and Categories within this category, including items and Categories in sub-Categories</td></tr>' .
            '<tr><td><input type="radio" name="recurse" value="mixed_one" '.(($recurse=='mixed_one')?'checked':'').'/>&nbsp;Include all Items and Categories immediately within this category, but not items or Categories in sub-Categories</td></tr></table>'));
        }
	if ($tab == 3)
		{        
            array_push($ret,array(lang('active'),'<input type="checkbox" name="active"'.($this->mActive?' checked="true"':'').'>'));
		  array_push($ret,array(lang('showinmenu'),'<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="true"':'').'>'));
			if (!$adding && $showadmin)
			{
				array_push($ret, array('Owner:',@UserOperations::GenerateDropdown($this->Owner())));
			}
			if ($adding || $showadmin)
			{
				array_push($ret, $this->ShowAdditionalEditors());
			}
        }
		return $ret;
	}

	function FillParams(&$params)
	{
		global $gCms;
		$config = &$gCms->config;
		$db = &$gCms->db;

		if (isset($params))
		{
			$parameters = array('notes', 'sub_template', 'sort_order', 'recurse','items_per_page');

			foreach ($parameters as $oneparam)
			{
				if (isset($params[$oneparam]))
				{
					$this->SetPropertyValue($oneparam, $params[$oneparam]);
				}
			}
			
			$this->getUserAttributes();
			foreach ($this->attrs as $thisAttr)
				{
				array_push($parameters,$thisAttr);
				}

            foreach ($parameters as $thisParam)
               {
               $safeattr = strtolower(preg_replace('/\W/','', $thisParam));
        	   if (isset($params[$safeattr]))
        	       {
                    $this->SetPropertyValue($thisParam, $params[$safeattr]);
                   }
               }

			if (isset($params['title']))
			{
				$this->mName = $params['title'];
			}
			if (isset($params['menutext']))
			{
				$this->mMenuText = $params['menutext'];
			}
			if (isset($params['template_id']))
			{
				$this->mTemplateId = $params['template_id'];
			}
			if (isset($params['alias']))
			{
				$this->SetAlias($params['alias']);
			}
			else
			{
				$this->SetAlias('');
			}
			if (isset($params['parent_id']))
			{
				if ($this->mParentId != $params['parent_id'])
				{
					$this->mHierarchy = '';
					$this->mItemOrder = -1;
				}
				$this->mParentId = $params['parent_id'];
			}
			if (isset($params['active']))
			{
				$this->mActive = true;
			}
			else
			{
				$this->mActive = false;
			}
			if (isset($params['showinmenu']))
			{
				$this->mShowInMenu = true;
			}
			else
			{
				$this->mShowInMenu = false;
			}
			// Copy and resize the image files...
            $imgcount = get_site_preference('Cataloger_mapi_pref_category_image_count', '1');
            $herosize = get_site_preference('Cataloger_mapi_pref_category_image_size_hero', '400');
            $thumbsize = get_site_preference('Cataloger_mapi_pref_category_image_size_thumbnail', '90');
            for ($i=1; $i<= $imgcount; $i++)
                {
			    if (isset($_FILES['image'.$i]['size']) && $_FILES['image'.$i]['size']>0)
                    {
                    // we's gots us an upload!
                    // transfer it ...
					copy($_FILES['image'.$i]['tmp_name'],
                        dirname($config['uploads_path'].
                        '/images/catalog_src/index.html') .
                        '/'.$this->mAlias.'_src_'.$i.'.jpg');
                    }
                }

		}
	}


	function PopulateParams(&$params)
	{
		global $gCms;
		$config = &$gCms->config;
        $db = &$gCms->db;

		$parameters = array('notes', 'sub_template', 'sort_order', 'recurse', 'items_per_page');
		foreach ($parameters as $oneparam)
		{
			$tmp = $this->GetPropertyValue($oneparam);
			if (isset($tmp) && ! empty($tmp))
			{
				$params[$oneparam] = $tmp;
			}
		}
		
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
			array_push($parameters,$thisAttr);
			}

        foreach ($parameters as $thisParam)
            {
            $safeattr = strtolower(preg_replace('/\W/','', $thisParam));
        	$tmp = $this->GetPropertyValue($thisParam);
			if (isset($tmp) && ! empty($tmp))
                {
				$params[$safeattr] = $tmp;
                }
            }
		$params['title'] = $this->mName;
		$params['menutext'] = $this->mMenuText;
		$params['template_id'] = $this->mTemplateId;
		$params['alias'] = $this->mAlias;
		$params['parent_id'] = $this->mParentId;
		$params['active'] = $this->mActive;
		$params['showinmenu']=$this->mShowInMenu;
	}



	function Show()
	{
		global $gCms;

		$params = array();

		$this->PopulateParams($params);

		$pf = new Cataloger();

		@ob_start();
		$pf->DoAction('defaultcategory', 'catalogmodule', $params);
		$text = @ob_get_contents();
		@ob_end_clean();
		return '{literal}'.$text.'{/literal}';
	}

    function FriendlyName()
	{
		return 'Catalog Category';
	}
}

class CatalogPrintable extends CMSModuleContentType
{
    var $attrs;

	function SetProperties()
	{
		global $gCms;
		$config = &$gCms->config;
        $this->getUserAttributes();
        foreach($this->attrs as $thisAttr)
            {
            $this->mProperties->Add('string', $thisAttr, '');
            }

		$this->mProperties->Add('string', 'sort_order', '');
		$this->mProperties->Add('string', 'sub_template', '');
		$this->mProperties->Add('string', 'notes', '');
		$this->mProperties->Add('string', 'fieldlist','');
		
		#Turn on preview
		$this->mPreview = true;

		#Turn off caching
		$this->mCachable = false;
	}

    function getUserAttributes()
    {
        global $gCms;
        $vars = &$gCms->variables;
        $db = &$gCms->db;
        if (isset($vars['catalog_print_attrs']) && is_array($vars['catalog_print_attrs']))
            {
            $this->attrs = &$vars['catalog_print_attrs'];
            }
        else
            {
            $vars['catalog_print_attrs'] = array();
            $query = "SELECT attribute FROM ".
        	   cms_db_prefix()."module_catalog_attr WHERE type_id=3";
            $dbresult = $db->Execute($query);
            while ($dbresult !== false && $row = $dbresult->FetchRow())
        	   {
        	   array_push($vars['catalog_print_attrs'],$row['attribute']);
        	   }
            $this->attrs = &$vars['catalog_print_attrs'];
            }
    }



	function TabNames()
	{
		return array(lang('main'), lang('options'), 'Permissions');
	}

	function EditAsArray($adding = false, $tab = 0, $showadmin=false)
	{
		global $gCms;
		$config = &$gCms->config;
		$db = &$gCms->db;
		$ret = array();
		$stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}
		else
		{
			$defaulttemplate = TemplateOperations::LoadDefaultTemplate();
			if (isset($defaulttemplate))
			{
				$this->mTemplateId = $defaulttemplate->id;
				$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
			}
		}

        if ($tab == 0)
		{
        $query = "SELECT id, title FROM ".
        	cms_db_prefix()."module_catalog_template WHERE type_id=3 ORDER by title";
        $subTemplates = array();
        $dbresult = $db->Execute($query);

        while ($dbresult !== false && $row = $dbresult->FetchRow())
        	{
        	$subTemplates[$row['title']]=$row['id'];
        	}		

		array_push($ret,array(lang('title'),'<input type="text" name="title" value="'.$this->mName.'" />'));
		array_push($ret,array(lang('menutext'),'<input type="text" name="menutext" value="'.htmlspecialchars($this->mMenuText,ENT_QUOTES).'" />'));
		if (!($config['auto_alias_content'] == true && $adding))
		{
			array_push($ret,array(lang('pagealias'),'<input type="text" name="alias" value="'.htmlspecialchars($this->mAlias,ENT_QUOTES).'" />'));
		}
		array_push($ret,array(lang('parent').'/Category',ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId)));
		array_push($ret,array('Page '.lang('template'),TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId)));
		array_push($ret,array('Sub '.lang('template'),CMSModule::CreateInputDropdown('', 'sub_template', $subTemplates, -1, $this->GetPropertyValue('sub_template'))));
        
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
            $safeattr = strtolower(preg_replace('/\W/','', $thisAttr));
        	array_push($ret,array($thisAttr,
        		'<input type="text" name="'.$safeattr.'" value="'.
        		htmlspecialchars($this->GetPropertyValue($thisAttr),ENT_QUOTES).
        		'" />'));
			}

		array_push($ret,array('Notes',create_textarea(true, $this->GetPropertyValue('notes'), 'notes', '', 'notes', '', $stylesheet, 80, 10)));
		}
		if ($tab == 1)
		{
			$so = $this->GetPropertyValue('sort_order');
			if ($so == '')
				{
				$so = get_site_preference('Cataloger_mapi_pref_printable_sort_order', 'natural');
				}
            array_push($ret,array('Item Sort Order',CMSModule::CreateInputDropdown('', 'sort_order',
                array("Navigation/Category Order"=>'natural', "Alphabetical Order"=>'alpha'), -1, $so)));
		  $item = new CatalogItem();
		  $itemAttrs = $item->getAttrs();
		  $attrPick = '';
		  $selAttrs = explode(',',$this->GetPropertyValue('fieldlist'));
		  foreach ($itemAttrs as $thisAttr)
		  	{
		  	$attrPick .= '<input type="checkbox" name="fieldlist[]" value="'.$thisAttr.'" ';
			if (in_array($thisAttr,$selAttrs))
		  		{
		  		$attrPick .= ' checked="checked"';
		  		}
		  	$attrPick .= ' />&nbsp;'.$thisAttr.'<br />';
		  	}
		  array_push($ret,array('Which attributes should be shown in catalog',$attrPick));
        }
	if ($tab == 2)
		{        
            array_push($ret,array(lang('active'),'<input type="checkbox" name="active"'.($this->mActive?' checked="true"':'').'>'));
		  array_push($ret,array(lang('showinmenu'),'<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="true"':'').'>'));
			if (!$adding && $showadmin)
			{
				array_push($ret, array('Owner:',@UserOperations::GenerateDropdown($this->Owner())));
			}
			if ($adding || $showadmin)
			{
				array_push($ret, $this->ShowAdditionalEditors());
			}
        }
		return $ret;
	}

	function FillParams(&$params)
	{
		global $gCms;
		$config = &$gCms->config;
		$db = &$gCms->db;

		if (isset($params))
		{
			$parameters = array('notes', 'sub_template', 'sort_order');

			foreach ($parameters as $oneparam)
			{
				if (isset($params[$oneparam]))
				{
					$this->SetPropertyValue($oneparam, $params[$oneparam]);
				}
			}
			
			$this->getUserAttributes();
			foreach ($this->attrs as $thisAttr)
				{
				array_push($parameters,$thisAttr);
				}

            foreach ($parameters as $thisParam)
               {
               $safeattr = strtolower(preg_replace('/\W/','', $thisParam));
        	   if (isset($params[$safeattr]))
        	       {
                    $this->SetPropertyValue($thisParam, $params[$safeattr]);
                   }
               }

			if (isset($params['title']))
			{
				$this->mName = $params['title'];
			}
			if (isset($params['menutext']))
			{
				$this->mMenuText = $params['menutext'];
			}
			if (isset($params['template_id']))
			{
				$this->mTemplateId = $params['template_id'];
			}
			if (isset($params['alias']))
			{
				$this->SetAlias($params['alias']);
			}
			else
			{
				$this->SetAlias('');
			}
			if (isset($params['parent_id']))
			{
				if ($this->mParentId != $params['parent_id'])
				{
					$this->mHierarchy = '';
					$this->mItemOrder = -1;
				}
				$this->mParentId = $params['parent_id'];
			}
			if (isset($params['active']))
			{
				$this->mActive = true;
			}
			else
			{
				$this->mActive = false;
			}
			if (isset($params['showinmenu']))
			{
				$this->mShowInMenu = true;
			}
			else
			{
				$this->mShowInMenu = false;
			}
			if (isset($params['fieldlist']))
				{
				if (! is_array($params['fieldlist']))
					{
					$params['fieldlist'] = array($params['fieldlist']);
					}
				$fl = '';
				foreach ($params['fieldlist'] as $thisField)
					{
					$fl .= $thisField.',';
					}
				rtrim($fl,',');
				$this->SetPropertyValue('fieldlist', $fl);
				}
		}
	}


	function PopulateParams(&$params)
	{
		global $gCms;
		$config = &$gCms->config;
        $db = &$gCms->db;

		$parameters = array('notes', 'sub_template', 'sort_order','fieldlist');
		foreach ($parameters as $oneparam)
		{
			$tmp = $this->GetPropertyValue($oneparam);
			if (isset($tmp) && ! empty($tmp))
			{
				$params[$oneparam] = $tmp;
			}
		}
		
		$this->getUserAttributes();
		foreach ($this->attrs as $thisAttr)
			{
			array_push($parameters,$thisAttr);
			}

        foreach ($parameters as $thisParam)
            {
            $safeattr = strtolower(preg_replace('/\W/','', $thisParam));
        	$tmp = $this->GetPropertyValue($thisParam);
			if (isset($tmp) && ! empty($tmp))
                {
				$params[$safeattr] = $tmp;
                }
            }
		$params['title'] = $this->mName;
		$params['menutext'] = $this->mMenuText;
		$params['template_id'] = $this->mTemplateId;
		$params['alias'] = $this->mAlias;
		$params['parent_id'] = $this->mParentId;
		$params['active'] = $this->mActive;
		$params['showinmenu']=$this->mShowInMenu;
	}



	function Show()
	{
		global $gCms;

		$params = array();

		$this->PopulateParams($params);

		$pf = new Cataloger();

		@ob_start();
		$pf->DoAction('defaultprintable', 'catalogmodule', $params);
		$text = @ob_get_contents();
		@ob_end_clean();
		return '{literal}'.$text.'{/literal}';
	}

    function FriendlyName()
	{
		return 'Catalog (Printable)';
	}
}

# vim:ts=4 sw=4 noet
?>
