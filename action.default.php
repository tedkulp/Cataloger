<?php
		if (!isset($gCms)) exit;
		foreach ($params as $key=>$val)
			{
			$this->smarty->assign($key, $params[$key]);
			}
		$showMissing = '_'. $this->GetPreference('show_missing','1');
		$imageArray = array();
		$fileArray = array();
		$fileUrlArray = array();
		$fileTypeArray = array();
		$srcImgArray = array();
		$thumbArray = array();
        $imgcount = $this->GetPreference('item_image_count', 2);
        $filecount = $this->GetPreference('item_file_count', 0);
        $fullSize = $this->GetPreference('item_image_size_hero', '400');
        $thumbSize = $this->GetPreference('item_image_size_thumbnail', '70');
        $prunelist = ($this->GetPreference('show_extant','1') == '1');
		$actualfilecount = 0;
		if ($imgcount > 0)
			{
	        for ($i=1;$i<=$imgcount;$i++)
	            {
	            if (! $prunelist || $this->srcExists($params['alias'], $i))
	            	{
					array_push($imageArray, 
						$this->imageSpec($params['alias'], 'f', $i, $fullSize));            
					array_push($thumbArray,
						$this->imageSpec($params['alias'], 't', $i, $thumbSize));
					array_push($srcImgArray,
						$this->srcImageSpec($params['alias'], $i));
					$this->smarty->assign('image_'.$i.'_url',
						$this->imageSpec($params['alias'], 'f', $i, $fullSize));            
					$this->smarty->assign('image_thumb_'.$i.'_url',
						$this->imageSpec($params['alias'], 't', $i, $thumbSize));
					$this->smarty->assign('src_image_'.$i.'_url',
						$this->srcImageSpec($params['alias'], $i));
					}
	            }
			}
		if ($filecount > 0)
			{
			list($fileArray,$fileTypeArray) = $this->getFiles($params['alias']);

			foreach ($fileArray as $i=>$v)
			    {
					$this->smarty->assign('file_'.($i+1).'_url',
						$gCms->config['uploads_url'].$this->getAssetPath('f').'/'.$params['alias'].'/'.$fileArray[$i]);
					array_push($fileUrlArray,$gCms->config['uploads_url'].$this->getAssetPath('f').'/'.$params['alias'].'/'.$fileArray[$i]);
					$this->smarty->assign('file_'.($i+1).'_name',
						$fileArray[$i]);
					$this->smarty->assign('file_'.($i+1).'_ext',
						$fileTypeArray[$i]);
					$actualfilecount += 1;
				}

			}
		$this->smarty->assign_by_ref('attrlist',$params['attrlist']);
		$this->smarty->assign_by_ref('image_url_array',$imageArray);
		$this->smarty->assign_by_ref('file_url_array',$fileUrlArray);
		$this->smarty->assign_by_ref('file_name_array',$fileArray);
		$this->smarty->assign_by_ref('file_ext_array',$fileTypeArray);
		$this->smarty->assign('file_count',$actualfilecount);
        $this->smarty->assign_by_ref('image_thumb_url_array',$thumbArray);
        $this->smarty->assign_by_ref('src_image_url_array',$srcImgArray);
		$this->smartyBasics();
		echo $this->ProcessTemplateFromDatabase('catalog_'.$params['sub_template']);	
?>