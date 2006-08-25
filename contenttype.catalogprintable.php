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
	$templateops = $gCms->GetTemplateOperations();
	$defaulttemplate = $templateoperations->LoadDefaultTemplate();
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

	$templateops = $gCms->GetTemplateOperations();
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
	$contentops = $gCms->GetContentOperations();
	array_push($ret,array(lang('parent').'/Category',$contentops->CreateHierarchyDropdown($this->mId, $this->mParentId)));
	array_push($ret,array('Page '.lang('template'),$templateops->TemplateDropdown('template_id', $this->mTemplateId)));
	$module =& $this->GetModuleInstance();
	array_push($ret,array('Sub '.lang('template'),$module->CreateInputDropdown('', 'sub_template', $subTemplates, -1, $this->GetPropertyValue('sub_template'))));
        
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
	array_push($ret,array('Item Sort Order',$module->CreateInputDropdown('', 'sort_order',
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
	    $userops = $gcms->GetUserOperations();
	    array_push($ret, array('Owner:',@$userops->GenerateDropdown($this->Owner())));
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