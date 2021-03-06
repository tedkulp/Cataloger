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
define('CTEMPLATE_ITEM',1);
define('CTEMPLATE_CATEGORY',2);
define('CTEMPLATE_CATALOG',3);
define('CTEMPLATE_COMPARISON',4);
define('CTEMPLATE_FEATURE',5);

class Cataloger extends CMSModule
{
  var $attrs = array();
  var $fetched = false;
  var $showMissing = '';

  function Cataloger()
  {
    parent::CMSModule();
    $this->RegisterContentType('CatalogItem',
			       dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.catalogitem.php',
			       $this->Lang('item_page'));
    $this->RegisterContentType('CatalogCategory',
			       dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.catalogcategory.php',
			       $this->Lang('category_page'));
    $this->RegisterContentType('CatalogPrintable',
			       dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.catalogprintable.php',
			       $this->Lang('catalog_printable'));
  }

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
    return '0.8.1';
  }

  function MinimumCMSVersion()
  {
    return '1.6';
  }

  function GetAdminDescription()
  {
    return $this->Lang('admindescription');
  }

  function InstallPostMessage()
  {
    return $this->Lang('postinstall');
  }

  function SetParameters()
  { 
   $this->RegisterModulePlugin();
   $this->RestrictUnknownParams();
			   			   
  $this->SetParameterType('sub_template',CLEAN_STRING); 
  $this->SetParameterType('recent',CLEAN_STRING);
  $this->SetParameterType('count',CLEAN_INT);
  $this->SetParameterType('alias',CLEAN_STRING);
  $this->SetParameterType('random',CLEAN_STRING);
  $this->SetParameterType('action',CLEAN_STRING);
  $this->SetParameterType('recurse',CLEAN_STRING);
  $this->SetParameterType('items',CLEAN_STRING);
  $this->SetParameterType('global_sort',CLEAN_STRING);
  $this->SetParameterType('global_sort_dir',CLEAN_STRING);
  }

  function getTemplateFromAlias($alias)
  {
    $db = $this->GetDb();
    $dbresult = $db->Execute('SELECT id from '.cms_db_prefix().
			     'module_catalog_template where title=?',array($alias));
    if ($dbresult !== false && $row = $dbresult->FetchRow())
      {
	return 'catalog_'.$row['id'];
      }
    return '';	
  }

  function importSampleTemplates($onlytype='all')
  {
    $db = $this->GetDb();
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
	$type = substr($temp_name,0,strpos($temp_name,'-'));
	if ($type == 'Item' && ($onlytype=='all'||$onlytype='Item'))
	  {
	    $type_id = CTEMPLATE_ITEM;
	  }
	else if ($type == 'Category' && ($onlytype=='all'||$onlytype='Category'))
	  {
	    $type_id = CTEMPLATE_CATEGORY;
	  }
	else if ($type == 'Printable' && ($onlytype=='all'||$onlytype='Printable'))
	  {
	    $type_id = CTEMPLATE_CATALOG;
	  }
	else if ($type == 'Comparison' && ($onlytype=='all'||$onlytype='Comparison'))
	  {
	    $type_id = CTEMPLATE_COMPARISON;
	  }
	else if ($type == 'Feature' && ($onlytype=='all'||$onlytype='Feature'))
	  {
	    $type_id = CTEMPLATE_FEATURE;
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

  function created($a, $b)
  {
    if ($a['createdate'] > $b['createdate'])
      {
      	return -1;
      }
    if ($a['createdate'] < $b['createdate'])
      {
      	return 1;
      }
    return 0;
  }


  function initAdminNav($id, &$params, $returnid)
  {
    $config = $this->GetConfig();
		$vars = $this->GetVariables();
   $this->smarty->assign('innernav',
			  $this->CreateLink($id, 'defaultadmin', $returnid,
					    $vars['admintheme']->DisplayImage('icons/topfiles/template.gif',
											 $this->Lang('listtempl'),'','','systemicon'), array()) .
			  $this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('listtempl'), array()) .
			  ' : ' .
			  $this->CreateLink($id, 'adminattrs', $returnid,
					    $vars['admintheme']->DisplayImage('icons/topfiles/images.gif',
											 $this->Lang('manageattrs'),'','','systemicon'), array()) .
			  $this->CreateLink($id, 'adminattrs', $returnid, $this->Lang('manageattrs'), array()) .
			  ' : ' .
			  $this->CreateLink($id, 'globalops', $returnid,
					    '<img class="systemicon" alt="'.$this->Lang('globalops').'" title="'.$this->Lang('globalops').'" src="'.$config['root_url'].'/modules/Cataloger/images/global.gif" />') .
			  $this->CreateLink($id, 'globalops', $returnid, $this->Lang('globalops'), array()) .
			  ' : ' .
			  $this->CreateLink($id, 'adminprefs', $returnid,
					    $vars['admintheme']->DisplayImage('icons/topfiles/siteprefs.gif',
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
    return $this->ProcessTemplate("changelog.tpl");
    //$this->Lang('changelog');
  }


  function & getSubContent($startNodeId)
    {
      $gCms=cmsms();
      $content = array();
      $hm = $gCms->GetHierarchyManager();
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
      $gCms=cmsms();
      $content = array();
      $hm = $gCms->GetHierarchyManager();
		
      $rn = $hm->GetRootNode(); 
      $count = 0;
      $hm->getFlattenedChildren($rn, $content, $count);
      return $content;
    }


  function getUserAttributes($global_ref='catalog_attrs')
  {
    $db = cmsms()->GetDb();
    if (! isset($this->attrs[$global_ref]) || ! is_array($this->attrs[$global_ref]))
      {
	$this->attrs[$global_ref] = array();
	$query = "SELECT attribute, alias, defaultval, length, is_textarea, field_type, select_values FROM ".
	  cms_db_prefix()."module_catalog_attr WHERE type_id=? ORDER BY order_by ASC";
	$type_id = 1;
	if ($global_ref == 'catalog_cat_attrs')
		{
		$type_id = 2;
		}
	elseif ($global_ref == 'catalog_print_attrs')
		{
		$type_id = 3;
		}
	$dbresult = $db->Execute($query, array($type_id));
	while ($dbresult !== false && $row = $dbresult->FetchRow())
	  {
		$thisAttr = new stdClass();
		$thisAttr->attr = $row['attribute'];
		$thisAttr->alias = $row['alias'];
		$thisAttr->is_text = $row['is_textarea'];
		$thisAttr->length = $row['length'];
		$thisAttr->default = $row['defaultval'];
		$thisAttr->select_values = $row['select_values'];
		$thisAttr->field_type = $row['field_type'];
		$thisAttr->safe = strtolower(preg_replace('/\W/','',$row['attribute']));
	    array_push($this->attrs[$global_ref],$thisAttr);
		}
      }
  return $this->attrs;
}

  function &getCatalogItem($alias)
	{
	    $gCms=cmsms();

	    $hm = $gCms->GetHierarchyManager();
   	  $pageNode = $hm->sureGetNodeByAlias($alias);
   		$page = $pageNode->GetContent();
		  $node = $this->itemToArray($page, '');
   	  return $node;
	}

  function &getCatalogItemById($id)
	{
	    $gCms=cmsms();

	    $hm = $gCms->GetHierarchyManager();
   	  $pageNode = $hm->sureGetNodeById($id);
   		$page = $pageNode->GetContent();
		  $node = $this->itemToArray($page, '');
   	  return $node;
	}


  function smartyBasics()
	{
	$config = $this->GetConfig();
	$this->smarty->assign('root_url',$config['root_url']);
	$this->smarty->assign('image_root',$config['root_url'].
			'/modules/Cataloger/Cataloger.Image.php');
	}


	function getCatalogItemsIDList(&$params)
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();
		$ret = array();
		if (!isset($params['alias']) || $params['alias']=='/')
				{
				$dbresult = $db->Execute('SELECT content_id from '.cms_db_prefix().
				     'content where type=\'catalogitem\'');
				}
		else
				{
				$base_hierarchy = $db->GetOne('SELECT hierarchy from '.cms_db_prefix().
				     'content where content_alias=?',array($params['alias']));
				if (! $base_hierarchy)
					{
					return $ret;
					}
					$dbresult = $db->Execute('SELECT content_id from '.cms_db_prefix().
					     'content where type=\'catalogitem\' and hierarchy LIKE \''.$base_hierarchy.'%\'');
				}
	  while ($dbresult !== false && $row = $dbresult->FetchRow())
	      {
				array_push($ret,$row['content_id']);
	      }
		return $ret;
	}

  function getCatalogItemsList(&$params)
  {
    $gCms=cmsms();
		$vars = $this->GetVariables();

    $hm = $gCms->GetHierarchyManager();
    $lastcat = "";
    $lastcatfull = null;
		
    if ( isset($params['alias'])&& $params['alias']=='/')
      {
	  $content = $hm->getFlatList();
	  $curHierDepth = isset($params['start_depth'])?$params['start_depth']:-1;
	  $curHierarchy = '';
	  $curHierLen = 0;
	  $curPage = new ContentBase();
      }
    else
      {
   	if (isset($params['content_id']))
   	  {
   	    $curPageID = $vars[$params['content_id']];
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
	if (!$thispagecontent)
	  {
	    continue;
	  }
	if (method_exists($thispagecontent,'Active') && !$thispagecontent->Active())
	  {
	    continue;
	  }
	if ($thispagecontent->Id() == $curPage->Id())
	  {
	    continue;
	  }
	$type_ok = false;
	$depth_ok = false;
	if ($thispagecontent->Type() == 'contentalias')
	  {
         $thispagecontent = $thispagecontent->GetAliasContent();
	     $curHierLen = strlen($curHierarchy);
	     $curHierDepth = substr_count($curHierarchy,'.');
	  }
	if ($thispagecontent->Type() == 'catalogcategory')
	  {
	  $lastcat = $thispagecontent->Name();
	  //debug_display($thispagecontent);
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
	    substr($thispagecontent->Hierarchy(),0,$curHierLen+1) == $curHierarchy.'.')
	  {
	    $depth_ok = true;
	  }
	else if ((isset($params['alias']) && $params['alias'] == '/') ||
      (($params['recurse'] == 'items_all' ||
		  $params['recurse'] == 'categories_all' ||
		  $params['recurse'] == 'mixed_all') &&
                 substr($thispagecontent->Hierarchy(),0,$curHierLen+1) == $curHierarchy.'.'))
	  {
	    $depth_ok = true;
	  }

	if (! $depth_ok)
	  {
	    continue;
	  }
	// in the category, and approved for addition
	$thisItem = $this->itemToArray($thispagecontent,$lastcat);
	array_push($categoryItems,$thisItem);
      }
    return array($curPage,$categoryItems);
  }

  function itemToArray($pagecontent, $lastcat)
	{
		$thisItem = array();
		$catThumbSize = $this->GetPreference('category_image_size_thumbnail',90);
		$itemThumbSize = $this->GetPreference('item_image_size_category',70);
		$missingImage = $this->GetPreference('show_missing','1');
		switch ($pagecontent->Type())
		  {
		  case 'catalogitem':
		    $thisItem['image'] = $this->imageSpec($pagecontent->Alias(),
		    	's', 1, $itemThumbSize);
		    $thisItem['image_src'] = $this->srcImageSpec($pagecontent->Alias(),
		    	1);
		    break;
		  case 'catalogcategory':
		    $thisItem['image'] = $this->imageSpec($pagecontent->Alias(),
		    	'ct', 1, $catThumbSize);
		    break;
		  }
		$thisItem['link'] = $pagecontent->GetUrl();
		$thisItem['title'] = $pagecontent->Name();
		$thisItem['alias'] = $pagecontent->Alias();
		$thisItem['menutitle'] = $pagecontent->MenuText();
		$thisItem['modifieddate']=$pagecontent->GetModifiedDate();
		$thisItem['category']=$lastcat;
		$thisItem['cat']=$lastcat;
		$thisItem['createdate']=$pagecontent->GetCreationDate();
		$thisItem['attrs'] = array();
		$theseAttrs = $pagecontent->getAttrs();
		foreach ($theseAttrs as $thisAttr)
		  {
		    //$safeattr = strtolower(preg_replace('/\W/','',$thisAttr->attr));
		    $thisItem[$thisAttr->safe] = $pagecontent->GetPropertyValue($thisAttr->attr);
			$thisItem['attrs'][$thisAttr->safe] = $pagecontent->GetPropertyValue($thisAttr->attr);
			if (isset($thisAttr->alias) && $thisAttr->alias != '')
				{
				$thisItem[$thisAttr->alias] = $pagecontent->GetPropertyValue($thisAttr->attr);
				$thisItem['attrs'][$thisAttr->alias] = $pagecontent->GetPropertyValue($thisAttr->attr);	
				}
		  }
		return $thisItem;
		
	}


  function imageSpec($alias, $type, $image_number, $size, $anticache=true, $forceshowmissing=false)
  {
    $config = $this->GetConfig();
  	if ($this->showMissing == '')
  		{
  		$this->showMissing = $this->GetPreference('show_missing','1');
  		}
  	$extender = '';
  	if ($anticache)
  		{
  		$extender = '&amp;ac=';
		for ($r = 0; $r < 5; $r++)
			{
			$extender .= rand(0,9);
			}
  		}

	return $config['root_url'].
			'/modules/Cataloger/Cataloger.Image.php?i='.
			$alias.'_'.$type.'_'.$image_number.
			'_'.$size.
			($forceshowmissing?'_1':'_'.$this->showMissing).
			'.jpg'.$extender;
  }

  function srcImageSpec($alias, $image_number)
  {
    $config = $this->GetConfig();
  	if ($this->showMissing == '')
  		{
  		$this->showMissing = $this->GetPreference('show_missing','1');
  		}

	if (! $this->srcExists($alias, $image_number))
		{
		if ($this->showMissing != '1')
			{
			return $config['root_url'].
				'/modules/Cataloger/images/trans.gif';
			}
		else
			{
			return $config['root_url'].
				'/modules/Cataloger/images/no-image.gif';
			}
		}
	else
		{
		return $config['uploads_url'].$this->getAssetPath('s').'/'.$alias .
			'_src_'.$image_number.'.jpg';
		}
  }

  function srcExists($alias, $image_number)
  {
    $config = $this->GetConfig();
	$srcSpec = $config['uploads_path'].$this->getAssetPath('s').'/'.$alias .
			'_src_'.$image_number.'.jpg';
	return file_exists($srcSpec);
  }


  function getFiles($alias)
  {
	  $config = $this->GetConfig();
  
	$dirspec = $config['uploads_path'].$this->getAssetPath('f').'/'.$alias;
	$files = array();
	$types = array();
	if (is_dir($dirspec))
	{	
	$dh  = opendir($dirspec);
	while (false !== ($filename = readdir($dh)))
		{
		if ($filename != '.' && $filename != '..')
			{
	    	$files[] = $filename;
			if (strpos($filename,'.') !== false)
				{
				$types[] = substr($filename, strrpos($filename,'.')+1);
				}
			else
				{
				$types[] = '?';
				}
			}
		}
	}
	return array($files,$types);
  }

  function purgeAllImage($alias, $imageNumber)
  {
  	$this->purgeScaledImages($alias, $imageNumber);
  	$this->purgeSourceImage($alias, $imageNumber);  	  	
  }
  
  function purgeScaledImages($alias, $imageNumber)
  {
  	$config = $this->GetConfig();
  	$srcDir = $config['uploads_path'].$this->getAssetPath('f').'/';
  	$toDel = array();
   if ($dh = opendir($srcDir))
   		{
   		while (($file = readdir($dh)) !== false)
   			{
          	$fileParts = explode('_',$file);
          	if ($fileParts[0]==$alias && $fileParts[2]==$imageNumber)
          		{
          		array_push($toDel,$srcDir.'/'.$file);
          		}
       		}
       	closedir($dh);
   		}
   	foreach ($toDel as $thisDel)
   		{
   		unlink($thisDel);
   		}
  }

  function purgeSourceImage($alias, $imageNumber)
  {
  	$config = $this->GetConfig();
	$srcSpec = $config['uploads_path'].$this->getAssetPath('s').'/'.$alias .
			'_src_'.$imageNumber.'.jpg';
	unlink($srcSpec);
  }


  function renameImages($old, $newAlias)
  {
  	$config = $this->GetConfig();
	if ($handle = opendir($config['uploads_path'].$this->getAssetPath('s').'/'))
		{
	    while (false !== ($file = readdir($handle)))
			{
	        if (substr($file,0,strlen($old)) == $old)
				{
				$newspec = $newAlias . substr($file,strlen($old));
	            rename ($config['uploads_path'].$this->getAssetPath('s').'/'.$file,$config['uploads_path'].$this->getAssetPath('s').'/'.$newspec);
	        	}
	    	}
	    closedir($handle);
		}
  }


  function displayError($message)
  {
    $this->smarty->assign_by_ref('error',$message);
    echo $this->ProcessTemplate('error.tpl');
  }

	function HandlesEvents()
	{
		return true;
	}

  // handle alias name changes	
	function DoEvent( $originator, $eventname, &$params )
	{
		if ($originator == 'Core' && $eventname=='ContentEditPost')
			{
			$content = &$params['content'];
			if ( ($content->Type() == 'catalogitem' ||
				  $content->Type() == 'catalogcategory' ||
				  $content->Type() == 'catalogprintable'
			     ) && $content->Alias() != $content->mOldAlias)
					{
					if ($content->mOldAlias && $content->Alias())
						{
							$this->renameImages($content->mOldAlias, $content->Alias());
						}
					}
			}
	}


	// type can be "s" - source image, "i" - processed image, or "f" - file
	function getAssetPath($type="i", $default=false)
	{
		$config = $this->GetConfig();
		$uploadbase = str_replace($config['uploads_path'],$config['root_path'],'');
		if ($default)
			{
			switch ($type)
				{
				case 's':
					return $uploadbase.'/images/catalog_src';
				case 'i':
					return $uploadbase.'/images/catalog';
				case 'f':
					return $uploadbase.'/catalogerfiles';
				}
			}
			switch ($type)
				{
				case 's':
					return $this->GetPreference('image_upload_path',$uploadbase.'/images/catalog_src');
				case 'i':
					return $this->GetPreference('image_proc_path',$uploadbase.'/images/catalog');
				case 'f':
					return $this->GetPreference('file_upload_path',$uploadbase.'/catalogerfiles');
				}
		
	}
	
	function getFieldTypes()
	{
		return array('text' => $this->Lang('text'), 'select' => $this->Lang('dropdown'));
	}
	
	function get_product_info($page_id)
	{
		$gCms = cmsms();

		$hm = $gCms->GetHierarchyManager();
		$pageNode = $hm->sureGetNodeById($page_id);
		$page = $pageNode->GetContent();

		$obj = new cg_ecomm_productinfo();
		$obj->set_product_id($page_id);
		$obj->set_name($page->Name());
		
		if ($page->HasProperty("Price") &&
			$page->GetPropertyValue("Price") != '' &&
			is_numeric($page->GetPropertyValue("Price")))
		{
			$obj->set_price($page->GetPropertyValue("Price"));
		}
		else if ($page->HasProperty("price") &&
			$page->GetPropertyValue("price") != '' &&
			is_numeric($page->GetPropertyValue("price")))
		{
			$obj->set_price($page->GetPropertyValue("price"));
		}
		
		if ($page->HasProperty("Weight") &&
			$page->GetPropertyValue("Weight") != '' &&
			is_numeric($page->GetPropertyValue("Weight")))
		{
			$obj->set_weight($page->GetPropertyValue("Weight"));
		}
		else if ($page->HasProperty("weight") &&
			$page->GetPropertyValue("weight") != '' &&
			is_numeric($page->GetPropertyValue("weight")))
		{
			$obj->set_weight($page->GetPropertyValue("weight"));
		}
		
		if ($page->HasProperty("SKU") &&
			$page->GetPropertyValue("SKU") != '')
		{
			$obj->set_sku($page->GetPropertyValue("SKU"));
		}
		else if ($page->HasProperty("sku") &&
			$page->GetPropertyValue("sku") != '')
		{
			$obj->set_sku($page->GetPropertyValue("sku"));
		}
		
		$obj->set_type(cg_ecomm_productinfo::TYPE_PRODUCT);
		
		return $obj;
	}

}

# vim:ts=4 sw=4 noet
?>
