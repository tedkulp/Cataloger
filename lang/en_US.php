<?php
$lang['needpermission']='You need %s set to do that.';
$lang['friendlyname']='Cataloger';
$lang['admindescription']='Manage Catalog settings';
$lang['areyousure']='Are you sure you want to delete this %s?';
$lang['uniquecode']='Unique Code';
$lang['templatelist']='Sub-templates';
$lang['listtempl']='Manage Sub-templates';
$lang['title_item_tab']='Item Attributes';
$lang['title_category_tab']='Category Attributes';
$lang['title_printable_tab']='Printable Catalog Attributes';
$lang['title_item_image_tab']='Item Image Sizes';
$lang['title_category_image_tab']='Category Page Settings';
$lang['title_printable_tab']='Printable Catalog Settings';
$lang['title_aspect_tab']='Image Aspect Ratios';
$lang['title_item_template_vars'] = 'Item Template Variables';
$lang['title_cat_template_vars'] = 'Category Template Variables';

$lang['item_page'] = 'Item Page';
$lang['category_page'] = 'Category Page';
$lang['catalog_printable'] = 'Printable Catalog';
$lang['catalog_datasheet'] = 'Catalog Datasheet';

$lang['title_global_item_sort_order'] = 'Set item sort order to';
$lang['title_global_items_per_page']= 'Set number of items per page to';
$lang['title_global_category_recurse'] = 'Set Category Display Behavior to';

$lang['title_item_sort_order'] = 'Default item sort order';
$lang['title_printable_sort_order'] = 'Default printable catalog sort order';
$lang['title_items_per_page']= 'Default number of items per page';
$lang['title_avail_attrs'] = 'Item attributes available for the template';
$lang['title_avail_imattrs'] = 'Item images available for the template';
$lang['title_item_image_size_hero'] = 'Image size for Item Page (long axis in pixels)';
$lang['title_item_image_size_thumbnail'] = 'Thumbail image size for Item Page (long axis in pixels)';
$lang['title_item_image_size_category'] = 'Thumbnail image size for Item on Category Page (long axis in pixels)';
$lang['title_item_image_size_catalog'] = 'Thumbnail image size for Item on Catalog Page (long axis in pixels)';
$lang['title_category_image_size_hero'] = 'Image size for Category Page (long axis in pixels)';
$lang['title_category_image_size_thumbnail'] = 'Thumbail image size for Category Page (long axis in pixels)';
$lang['title_template']='Template';
$lang['title_template_type']='Template Type';
$lang['title_title']='Title';
$lang['title_item_image_count']='Maximum number of views for each Item';
$lang['title_category_image_count']='Maximum number of Banner images for each Category Page';
$lang['title_item_attributes_help']='Each attribute you enter here will be the title of a field that you can use to describe an individual item, and will be displayed with that item. You may have to update your template to reflect these attributes. Blank out or type over an existing attribute to remove or replace it.';
$lang['title_category_attributes_help']='Each attribute you enter here will be the title of a field that you can use to describe an item category, and will be displayed on that category page. You may have to update your template to reflect these attributes. Blank out or type over an existing attribute to remove or replace it.';
$lang['title_catalog_attributes_help']='Each attribute you enter here will be the title of a field that you can use to describe the entire catalog, and will be displayed on the printable catalog page. You may have to update your template to reflect these attributes. Blank out or type over an existing attribute to remove or replace it.';
$lang['title_force_aspect_ratio'] = 'Force images to a specified Aspect ratio?';
$lang['title_force_aspect_ratio_label'] = 'Force aspect ratio (not yet implemented)';
$lang['title_image_aspect_ratio'] = 'Aspect ratio (specify as "4:3" or "1:2.5")';
$lang['title_aspect_ratio_help'] = 'If you force an image to a specified aspect ratio, it will be scaled to fit exactly that aspect ratio (or its inverse, so you can have both portrait and landscape formats). Otherwise, images will preserve their aspect ratio, and be scaled so their long axis is the size specified. <b>this is not yet implemented!</b>';
$lang['title_category_sort'] = 'Category Page Item-list sort order';
$lang['title_category_recurse'] = 'Default Category Display Behavior';
$lang['title_category_recurse_items_all'] = 'Include all Items within this category, including items in sub-categories';
$lang['title_category_recurse_items_one'] = 'Include all Items immediately within this category, but not items in sub-categories';
$lang['title_category_recurse_categories_all'] = 'Include all Categories within this category, including categories in sub-categories';
$lang['title_category_recurse_categories_one'] = 'Include all Categories immediately within this category, but not categories in sub-categories';
$lang['title_category_recurse_mixed_all'] = 'Include all Items and Categories within this category, including items and categories in sub-categories';
$lang['title_category_recurse_mixed_one'] = 'Include all Items and Categories immediately within this category, but not items or categories in sub-categories';
$lang['natural_order'] = 'Navigation Order';
$lang['alpha_order'] = 'Alphabetical Order';

$lang['manageprefs']='Manage Preferences';
$lang['manageattrs']='Manage User-Defined Attributes';
$lang['globalops']='Global Catalog Operations';
$lang['addtemplate']='Add a new Sub-template';
$lang['reimporttemplates']='Re-import example sub-templates';
$lang['reimported'] = 'Sample sub-templates have been imported.';
$lang['templateupdated']='Template added/updated.';
$lang['templatedeleted']='Template deleted.';
$lang['prefsupdated']='Preferences updated.';
$lang['installed'] = 'Module version %s installed.';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['uninstalled'] = 'Module Uninstalled.';
$lang['attrsupdated'] = 'Attributes updated.';
$lang['noglobalchange'] = 'Do not change';
$lang['globallyupdated'] = 'Changes propagated globally.';
$lang['next'] = ':&#187;';
$lang['prev'] = '&#171;:';

$lang['notemplates']='No Sub-templates are installed!';
$lang['helptext']='
<h3>What Does This Do?</h3>
<p>This module lets you create online catalogs or portfolios. Catalogs consist of "Catalog Items" which could be products, works of art, or the like, and "Catalog Categories" which could be item categories or other natural divisions of the catalog.</p>
<p>The module has built-in support for "Content Aliases" (a module available at <a href="http://www.cmsmodules.com/ContentAliases.html">CMSModules.com</a>), which allows you to place a Catalog Item into multiple Catalog Categories.</p>
<h3>How Do I Use It</h3>
<p>When you install this module, it creates two new Content Types: Catalog Item and Catalog Category. When you\'re in your site administration category, you add Catalog Items and Catalog Categories just as you would any other kind of page. Select Content &gt; Pages &gt; Add Content, and then select "Catalog Item" or "Catalog Category" from the Content Type pulldown.</p>
<h4>Catalog Items</h4>
<p>Adding a Catalog Item is similar to adding an ordinary page to your site. The data fields for the Item are not exactly the same, however. Also note that in the "Images" tab, you can select multiple images to upload for the item. When you upload images, the system will size them appropriately for the catalog, create thumbnails for use in the Item\'s page, create thumbnails for any Category pages, and so on. This requires that you have GD or a similar image library installed. Currently, only jpeg format images are supported.</p>
<h4>Catalog Categories</h4>
<p>A Catalog Category is used for organizing your catalog items into categories. It provides a page that lists the Catalog Items that are contained by it. A Catalog Item is considered to be part of a Catalog Category if it is below that Category in the Site Hierarchy. Categories can similarly include other Categories.</p>
<p>Catalog Categories have a number of settings to determine how they should display the Items and Categories they contain: if you look at the "Options" tab, you can choose how many items and/or categories to show, what order to show them in, whether to display only items or only categories or both, and how many levels of the hierarchy to display below the category page.</p>
<h3>Customization and Advanced Topics</h3>
<h4>Catalog Item Attributes</h4>
<p>The default item attributes are typical for a catalog of products or artworks, but by going into Extensions &gt; Cataloger &gt; Manage User-Defined Attributes. 
<h4>Catalog Category Attributes</h4>
<h4>Custom Templates</h4>
<p></p>
<h3>Support</h3>
<p>This module does not include commercial support. However, there are a number of resources available to help you with it:</p>
<ul>
<li>For the latest version of this module, FAQs, or to file a Bug Report or buy commercial support, please visit SjG\'s
module homepage at <a href="http://www.cmsmodules.com">CMSModules.com</a>.</li>
<li>Additional discussion of this module may also be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>.</li>
<li>The author, SjG, can often be found in the <a href="irc://irc.freenode.net/#cms">CMS IRC Channel</a>.</li>
<li>Lastly, you may have some success emailing the author directly.</li>  
</ul>
<p>As per the GPL, this software is provided as-is. Please read the text
of the license for the full disclaimer.</p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2005, Samuel Goldstein <a href="mailto:sjg@cmsmodules.com">&lt;sjg@cmsmodules.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>';
$lang['changelog']='
<li>Version 0.1 - 19 March 2005. Initial release.</li>
</ul>';
$lang['postinstall']='Make sure to set the "Modify Catalog Settings" permission on users who will be administering the catalog.';
?>