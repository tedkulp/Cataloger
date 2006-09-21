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
  var $showMissing = '';

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
    return '0.5.3';
  }

  function MinimumCMSVersion()
  {
    return '1.0-svn';
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
    $this->RegisterContentType('CatalogItem',
			       dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.catalogitem.php',
			       'Cataloger Item');
    $this->RegisterContentType('CatalogCategory',
			       dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.catalogcategory.php',
			       'Cataloger Category');
    $this->RegisterContentType('CatalogPrintable',
			       dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.catalogprintable.php',
			       'Cataloger Printable');
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
	    $thisItem['image'] = $this->imageSpec($thispagecontent->Alias(),
	    	's', 1, $itemThumbSize);
	    break;
	  case 'catalogcategory':
	    $thisItem['image'] = $this->imageSpec($thispagecontent->Alias(),
	    	'ct', 1, $catThumbSize);
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


  function imageSpec($alias, $type, $image_number, $size, $anticache=true, $forceshowmissing=false)
  {
    global $gCms;
  	if ($this->showMissing == '')
  		{
  		$this->showMissing = $this->GetPreference('show_missing','1');
  		}
  	$extender = '';
  	if ($anticache)
  		{
  		$extender = '&ac=';
		for ($r = 0; $r < 5; $r++)
			{
			$extender .= rand(0,9);
			}
  		}

	return $gCms->config['root_url'].
			'/modules/Cataloger/Cataloger.Image.php?i='.
			$alias.'_'.$type.'_'.$image_number.
			'_'.$size.
			($forceshowmissing?'_1':'_'.$this->showMissing).
			'.jpg'.$extender;
  }

  function srcImageSpec($alias, $image_number)
  {
  	global $gCms;
  	if ($this->showMissing == '')
  		{
  		$this->showMissing = $this->GetPreference('show_missing','1');
  		}
	$srcSpec = $gCms->config['uploads_path'].'/images/catalog_src/'.$alias .
			'_src_'.$image_number.'.jpg';

	$orig = @stat($srcSpec);
	if ($orig === false)
		{
		if ($this->showMissing != '1')
			{
			return $gCms->config['root_url'].
				'/modules/Cataloger/images/trans.gif';
			}
		else
			{
			return $gCms->config['root_url'].
				'/modules/Cataloger/images/no-image.gif';
			}
		}
	else
		{
		return $gCms->config['uploads_url'].'/images/catalog_src/'.$alias .
			'_src_'.$image_number.'.jpg';
		}
  }


  function displayError($message)
  {
    $this->smarty->assign_by_ref('error',$message);
    echo $this->ProcessTemplate('error.tpl');
  }

}

# vim:ts=4 sw=4 noet
?>
