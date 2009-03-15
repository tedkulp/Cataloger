<?php
#-------------------------------------------------------------------------
# Module: Cataloger - build a catalog or portfolio of stuff
# Version: 0.6
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

class CatalogItem extends CMSModuleContentType
{
  var $attrs;

  function ModuleName()
  {
    return 'Cataloger';
  }

  function IsCopyable()
  {
  return true;
  }

  function SetProperties()
  {
    $this->getUserAttributes();
    foreach ($this->attrs as $thisAttr)
      {
	$this->mProperties->Add('string', $thisAttr->attr, '');
      }
    $this->mProperties->Add('string', 'sub_template', '');

#Turn on preview
    $this->mPreview = true;

#Turn off caching
    $this->mCachable = false;
  }

  function getUserAttributes()
  {
	global $gCms;
	Cataloger::getUserAttributes('catalog_attrs');
    $vars = &$gCms->variables;
	$this->attrs = &$vars['catalog_attrs'];
	
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
    return array(lang('main'), $this->lang('nameimages'), lang('options'));
  }

  function EditAsArray($adding = false, $tab = 0, $showadmin=false)
  {
    global $gCms;
    $config = &$gCms->config;
    $module =& $this->GetModuleInstance();
    $db = &$gCms->db;
    $wysiwyg = (strlen(get_preference(get_userid(), 'wysiwyg')) > 0);
    $ret = array();
    $stylesheet = '';
    if ($this->TemplateId() > 0)
      {
	$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
      }
    else
      {
	$templateops = $gCms->GetTemplateOperations();
	$defaulttemplate = $templateops->LoadDefaultTemplate();
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

	$ret[] = array(lang('title'),
			     '<input type="text" name="title" value="'.htmlspecialchars($this->mName,ENT_QUOTES).'" />');
	$ret[] = array(lang('menutext'),
			      '<input type="text" name="menutext" value="'.
			      htmlspecialchars($this->mMenuText, ENT_QUOTES).'" />');
	if (!($config['auto_alias_content'] == true && $adding))
	  {
	    $ret[] = array(lang('pagealias'),
				  '<input type="text" name="alias" value="'.
				  htmlspecialchars($this->mAlias, ENT_QUOTES).'" />');
	  }
	$contentops = $gCms->GetContentOperations();
	$ret[] = array(lang('parent').
			      '/'.$this->Lang('category_page'),
			      $contentops->CreateHierarchyDropdown($this->mId,
								      $this->mParentId));
	$templateops = $gCms->GetTemplateOperations();
	$ret[] = array($this->Lang('namepage').' '.
			      lang('template'),
			      $templateops->TemplateDropdown('template_id',
							     $this->mTemplateId));
	$val = $this->GetPropertyValue('sub_template');
	$ret[] = array($this->Lang('Sub').' '.
			      lang('template'),
			      $module->CreateInputDropdown('',
							   'sub_template', $subTemplates, -1, $val));
	$this->getUserAttributes();
	foreach ($this->attrs as $thisAttr)
	  {
            $safeattr = strtolower(preg_replace('/\W/','', $thisAttr->attr));
			if ($thisAttr->is_text)
				{
				$ret[] = array($thisAttr->attr,
					create_textarea($wysiwyg, $this->GetPropertyValue($thisAttr->attr), $safeattr, '', $thisAttr->attr, '', $stylesheet, 80, 10));	
				}
			else
				{
	    		$ret[] = array($thisAttr->attr,
			   		'<input type="text" name="'.$safeattr.'" value="'.
			   		htmlspecialchars($this->GetPropertyValue($thisAttr->attr),ENT_QUOTES).
			   		'" />');
				}
	  }

//	
      }
    if ($tab == 1)
      {
	$imgcount = get_site_preference('Cataloger_mapi_pref_item_image_count', '2');
	$thumbsize = get_site_preference('Cataloger_mapi_pref_item_image_size_thumbnail', '70');
	if ($imgcount != 0){ // check if is not 0
	$imgsrc = '<table>';
	for ($i=1; $i<= $imgcount; $i++)
	  {
	    $imgsrc .= '<tr><td style="vertical-align:top;">'.$this->lang('nameimages').' '.$i.':</td><td style="vertical-align:top;">';
	    $imgsrc .= '<img alt="'.$this->lang('nameimages').'" title="'.$this->lang('nameimages').'" src="'.
$config['root_url'].'/modules/Cataloger/Cataloger.Image.php?i='.$this->mAlias.'_t_'.$i.'_'.$thumbsize.'_1.jpg&amp;ac='.rand(0,9).rand(0,9).rand(0,9).'" />';
	    $imgsrc .= '</td><td style="vertical-align:top;">&nbsp;<input type="file" name="image'.$i.'" />';
	    $imgsrc .= '<input type="checkbox" id="rm_image_'.$this->mAlias.
	    	'_'.$i.'" name="rm_image_'.$this->mAlias.
	    	'_'.$i.'" /><label for="rm_image_'.$this->mAlias.
	    	'_'.$i.'">'.$this->lang('deleteimage').'</label>';

	    $imgsrc .= '</td></tr>';
	  }
	$imgsrc .= '</table>';
	$ret[] = array($this->lang('nameimages').':', $imgsrc);
	 }// end  if ($imgcount != 0)  
	 else{
	echo '<div class="pagetext"><img alt="'.$this->lang('nameimages').'" title="'.$this->lang('nameimages').'" src="/modules/Cataloger/images/no-image.gif" /></div>'; 
	  }// end else
	
      }
    if ($tab == 2)
      {
        $ret[] = array(lang('active'),'<input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' />');
	$ret[] = array(lang('showinmenu'),'<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');

			array_push($ret, array(lang('metadata').':',create_textarea(false, $this->Metadata(), 'metadata', 'pagesmalltextarea', 'metadata', '', '', '80', '6')));
			array_push($ret, array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" value="'.cms_htmlentities($this->mTitleAttribute).'" />'));
			array_push($ret, array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="10" value="'.cms_htmlentities($this->mTabIndex).'" />'));
			array_push($ret, array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />'));

	if (!$adding && $showadmin)
	  {
	    $userops = $gCms->GetUserOperations();
	    $ret[] = array($this->lang('Owner').':',@$userops->GenerateDropdown($this->Owner()));
	  }
	if ($adding || $showadmin)
	  {
	    $ret[] = $this->ShowAdditionalEditors();
	  }
	if ($tab == 3)
	{
		$ret[] = array('Hi','Foo');
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
	$parameters = array('sub_template');

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
	    array_push($parameters,$thisAttr->attr);
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

			if (isset($params['metadata']))
				$this->mMetadata = $params['metadata'];
			if (isset($params['accesskey']))
				$this->mAccessKey = $params['accesskey'];
			if (isset($params['titleattribute']))
				$this->mTitleAttribute = $params['titleattribute'];
			if (isset($params['tabindex']))
				$this->mTabIndex = $params['tabindex'];

			
	// Copy the image files...
	$imgcount = get_site_preference('Cataloger_mapi_pref_item_image_count', '2');
	$herosize = get_site_preference('Cataloger_mapi_pref_item_image_size_hero', '400');
	$thumbsize = get_site_preference('Cataloger_mapi_pref_item_image_size_thumbnail', '70');
	$catalogsize = get_site_preference('Cataloger_mapi_pref_item_image_size_catalog', '100');
	$categorysize = get_site_preference('Cataloger_mapi_pref_item_image_size_category', '70');
    $pf = new Cataloger();

	for ($i=1; $i<= $imgcount; $i++)
	  {
	    if (isset($_FILES['image'.$i]['size']) && $_FILES['image'.$i]['size']>0)
	      {
		// keep original image
		copy($_FILES['image'.$i]['tmp_name'],
		     dirname($config['uploads_path'].
			     '/images/catalog_src/index.html') .
		     '/'.$this->mAlias.'_src_'.$i.'.jpg');
	      
	      }
	  }
	  foreach ($params as $thisParam=>$thisParamVal)
	  {
	  	if (substr($thisParam,0,9) == 'rm_image_')
	  		{
	  		$imageSpecParts = explode('_',$thisParam);
	  		$pf->purgeAllImage($imageSpecParts[2],$imageSpecParts[3]);
	  		}
	  }
	  
      }
  }

  function PopulateParams(&$params)
  {
    global $gCms;
    $config = &$gCms->config;
    $db = &$gCms->db;

    $parameters = array('sub_template');
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
	array_push($parameters,$thisAttr->attr);
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



  function Show($param='')
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
    return $this->Lang('item_page');
  }
}

# vim:ts=4 sw=4 noet
?>
