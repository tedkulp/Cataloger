<?php
#-------------------------------------------------------------------------
# Module: Cataloger - build a catalog or portfolio of stuff
# Version: 0.4
#
# Copyright (c) 2006, Samuel Goldstein <sjg@cmsmodules.com>
# For Information, Support, Bug Reports, etc, please visit the
# CMS Made Simple Forge at http://dev.cmsmadesimple.org
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
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
		return '0.5';
	}

	function MinimumCMSVersion()
	{
		return '0.13';
	}

	function GetAdminDescription()
	{
		return $this->Lang('admindescription');
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function getTemplateFromAlias($alias)
	{
        global $gCms;
		$db =& $gCms->GetDb();
       	$dbresult = $db->Execute('SELECT id from '.cms_db_prefix().
       		'module_catalog_template where title=?',array($alias));
        if ($dbresult !== false && $row = $dbresult->FetchRow())
       		{
			return 'catalog_'.$row['id'];
       		}
       	return '';	
	}

	function importSampleTemplates()
	{
        global $gCms;
		$db =& $gCms->GetDb();
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
       		// check if it already exists
       		$excheck = 'SELECT id from '.cms_db_prefix().'module_catalog_template where title=?';
       		$dbcount = $db->Execute($excheck,array($temp_name));
       		while ($dbcount && $dbcount->RecordCount() > 0)
       			{
       			$temp_name .='_new';
       			$dbcount = $db->Execute($excheck,array($temp_name));
       			}
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
       		else if (substr($temp_name,0,8) == 'Feature-')
       			{
       			$type_id = 5;
       			}
       		
    		$temp_id = $db->GenID(cms_db_prefix().
    			'module_catalog_template_seq');
			$dbresult = $db->Execute($query,
				array($temp_id,$type_id, $temp_name,$template));
       		$this->SetTemplate('catalog_'.$temp_id,$template);
       		}
	
	}

    function contentalpha($a, $b)
    {
      return strcasecmp($a['title'], $b['title']);
    }

    function chrono($a, $b)
    {
      if ($a['modifieddate'] > $b['modifieddate'])
      	{
      	return -1;
      	}
      if ($a['modifieddate'] < $b['modifieddate'])
      	{
      	return 1;
      	}
      return 0;
    }

    function initAdminNav($id, &$params, $returnid)
        {
        global $gCms;
		$this->smarty->assign('innernav',
			$this->CreateLink($id, 'defaultadmin', $returnid,
				$gCms->variables['admintheme']->DisplayImage('icons/topfiles/template.gif',
                $this->Lang('listtempl'),'','','systemicon'), array()) .
			$this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('listtempl'), array()) .
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


	function & getSubContent($startNodeId)
	{
		global $gCms;
		$content = array();
		$hm =& $gCms->GetHierarchyManager();
		/* Works with new addition to Tree, but getFlatList is default
		$rn = $hm->sureGetNodeById($startNodeId); 
		$count = 0;
		$hm->getFlattenedChildren($rn, $content, $count);
		*/
		$content = $hm->getFlatList();
		return $content;
	}

	function & getAllContent()
	{
		global $gCms;
		$content = array();
		$hm =& $gCms->GetHierarchyManager();
		
		$rn = $hm->GetRootNode(); 
		$count = 0;
		$hm->getFlattenedChildren($rn, $content, $count);
		return $content;
	}


	function getCatalogItemsList(&$params)
	{
		global $gCms;
		$hm =& $gCms->GetHierarchyManager();
		
		if (isset($params['alias']) && $params['alias'] == '/')
			{
			$content = $hm->getFlatList();
			$curHierDepth = 0;
			$curHierarchy = '';
			$curHierLen = 0;
			$curPage = new ContentBase();
			}
		else
			{
			if (isset($params['content_id']))
			  {
				$curPageID = $gCms->variables[$params['content_id']];
				$curPageNode = $hm->sureGetNodeById($curPageID);
				$curPage = $curPageNode->GetContent();
			  }
			else if (isset($params['alias']))
			  {
				$curPageNode = $hm->sureGetNodeByAlias($params['alias']);
				$curPage = $curPageNode->GetContent();
				$curPageID = $curPage->Id();
			  }
			else if (isset($gCms->variables['content_id']))
			  {
				$curPageID = $gCms->variables['content_id'];
				$curPageNode = $hm->sureGetNodeById($curPageID);
				$curPage = $curPageNode->GetContent();
			  }
			$curHierarchy = $curPage->Hierarchy();
			$curHierLen = strlen($curHierarchy);
			$curHierDepth = substr_count($curHierarchy,'.');
	
			$content = $this->getSubContent($curPageID);
			}
		$categoryItems = array();
		foreach ($content as $thisPage)
			{
			$thispagecontent = $thisPage->GetContent();
            if (!$thispagecontent->Active())
                {
                continue;
                }
            if ($thispagecontent->Id() == $curPage->Id())
                {
                continue;
                }
			$type_ok = false;
			$depth_ok = false;
			if ($thispagecontent->Type() == 'aliasmodule')
				{
				$thisPage = $thispagecontent->GetAliasContent();
				}
			if ($thispagecontent->Type() == 'catalogitem' &&
                      ($params['recurse'] == 'items_one' ||
                       $params['recurse'] == 'items_all' ||
                       $params['recurse'] == 'mixed_one' ||
                       $params['recurse'] == 'mixed_all'))
                {
                $type_ok = true;
                }
            else if ($thispagecontent->Type() == 'catalogcategory' &&
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
                 substr_count($thispagecontent->Hierarchy(),'.') ==
                 	($curHierDepth + 1) &&
                 substr($thispagecontent->Hierarchy(),0,$curHierLen) == $curHierarchy)
                {
                $depth_ok = true;
                }
            else if (($params['recurse'] == 'items_all' ||
                 $params['recurse'] == 'categories_all' ||
                 $params['recurse'] == 'mixed_all') &&
                 substr($thispagecontent->Hierarchy(),0,$curHierLen) == $curHierarchy)
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
			$missingImage = $this->GetPreference('show_missing','1');
			switch ($thispagecontent->Type())
				{
                case 'catalogitem':
				    $thisItem['image'] = $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thispagecontent->Alias().'_s_1_'.$itemThumbSize.$showMissing.'.jpg';
				    break;
				case 'catalogcategory':
				    $thisItem['image'] = $gCms->config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$thispagecontent->Alias().'_ct_1_'.$catThumbSize.$showMissing.'.jpg';
				    break;
				}
			$thisItem['link'] = $thispagecontent->GetUrl();
			$thisItem['title'] = $thispagecontent->Name();
			$thisItem['menutitle'] = $thispagecontent->MenuText();
			$thisItem['modifieddate']=$thispagecontent->GetModifiedDate();
			$thisItem['createdate']=$thispagecontent->GetCreationDate();
			$theseAttrs = $thispagecontent->getAttrs();
			foreach ($theseAttrs as $thisAttr)
				{
				$safeattr = strtolower(preg_replace('/\W/','',$thisAttr));
				$thisItem[$safeattr] = $thispagecontent->GetPropertyValue($thisAttr);
				}
			array_push($categoryItems,$thisItem);
			}
		return array($curPage,$categoryItems);
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

	function GetCreationDate()
	{
		return $this->mCreationDate;
	}
	
	function GetModifiedDate()
	{
		return $this->mModifiedDate;
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
				$imgsrc .= '<img src="'.$config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$this->mAlias.'_t_'.$i.'_'.$thumbsize.'_1.jpg" />';
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

    function &getAttrs()
    {
    	$this->getUserAttributes();
    	return $this->attrs;
    }

	function GetCreationDate()
	{
		return $this->mCreationDate;
	}
	
	function GetModifiedDate()
	{
		return $this->mModifiedDate;
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
				$imgsrc .= '<img src="'.$config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$this->mAlias.'_ct_'.$i.'_'.$thumbsize.'_1.jpg" />';
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

	function GetCreationDate()
	{
		return $this->mCreationDate;
	}
	
	function GetModifiedDate()
	{
		return $this->mModifiedDate;
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
            else
            	{
            	$params[$safeattr] = '';
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
