<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Winans Creative 2009
 * @author     Fred Bliss <fred@winanscreative.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class ModuleProductLister extends ModuleIsotopeBase
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_productlist';

	protected $strOrderBySQL = 'sorting';
	
	protected $strFilterSQL;
	
	protected $strSearchSQL;
	
	protected $arrParams;		     
	
	/**
	 * The ids of all pages we take care of. this is what should later be used eg. for filter data.
	 */
	protected $arrCategories = array();
        
        
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{		
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ISOTOPE PRODUCT LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;
			
			return $objTemplate->parse();
		}

		global $objPage;
		
		$this->arrCategories = $this->setCategories($this->iso_category_scope, $objPage->rootId, $objPage->id);	
	
		if (!count($this->arrCategories))
			return '';

		return parent::generate();
	}
	
	
	/**
	 * Generate module
	 */
	protected function compile()
	{	
		global $objPage;
			
		if($this->getRequestData('clear'))
		{
			$arrFilters = array();
		}
		else
		{
			$arrFilters = array('for'=>$this->getRequestData('for'),'per_page'=>$this->getRequestData('per_page'),'page'=>$this->getRequestData('page'),'order_by'=>$this->getRequestData('order_by'));	
		
			/*$arrFilterFields = implode(',', $this->Input->get('filters'));	//get the names of filters we are using
	
			foreach($arrFilterFields as $field)
			{
				if($this->Input->get($field))
				{
					$arrFilters[$field] = $this->Input->get($field);
				}
			}*/
						
			$this->perPage = ($this->getRequestData('per_page') ? $this->getRequestData('per_page') : $this->perPage);
							
			$this->setFilterSQL($arrFilters);
		}

		if($this->strOrderBySQL=='sorting')
		{

			if($this->iso_listingSortField)
			{
				$this->setFilterSQL(array('order_by' => ($this->iso_listingSortField.'-'.$this->iso_listingSortDirection)));
		    }
		}
				
		$objProductIds = $this->Database->prepare("SELECT DISTINCT p.* FROM tl_product_categories c, tl_product_data p WHERE p.id=c.pid AND published='1'" . ($this->strFilterSQL ? " AND (" . $this->strFilterSQL . ")" : "") . " AND c.page_id IN (" . implode(',', $this->arrCategories) . ")" . ($this->strSearchSQL ? " AND (" . $this->strSearchSQL . ")" : "") . ($this->strOrderBySQL ? " ORDER BY " . $this->strOrderBySQL : ""));
		
		
		// Add pagination
		if ($this->perPage > 0)
		{
			$total = $objProductIds->execute($this->arrParams)->numRows;
			$page = $this->getRequestData('page') ? $this->getRequestData('page') : 1;
			$offset = ($page - 1) * $this->perPage;

			$objPagination = new Pagination($total, $this->perPage);
			$this->Template->pagination = $objPagination->generate("\n  ");
			
			$objProductIds->limit($this->perPage, $offset);
		}
		
		$arrProducts = $this->getProducts($objProductIds->execute($this->arrParams)->fetchEach('id'));
			
		if (!is_array($arrProducts) || !count($arrProducts))
		{
			$this->Template = new FrontendTemplate('mod_message');
			$this->Template->type = 'empty';
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['noProducts'];
			return;
		}
		
		$arrBuffer = array();
		
		foreach( $arrProducts as $i => $objProduct )
		{
			$arrBuffer[] = array
			(
				'clear'	    => ($this->iso_list_format=='grid' && $blnSetClear ? true : false),
				'class'		=> ('product' . ($i == 0 ? ' product_first' : '')),
				'html'		=> $objProduct->generate((strlen($this->iso_list_layout) ? $this->iso_list_layout : $objProduct->list_template), $this),
			);
			
			$blnSetClear = (($i+1) % $this->columns==0 ? true : false);
		}
	
		// Add "product_last" css class
		if (count($arrBuffer))
		{
			$arrBuffer[count($arrBuffer)-1]['class'] .= ' product_last';
		}

		if(!$this->iso_disableFilterAjax)
		{
			$objScriptTemplate = new FrontendTemplate('js_lister');
	
			$objScriptTemplate->ajaxParams = 'id=' . $this->id . '&pid=' . $objPage->id . '&rid=' . $objPage->rootId;
		
			$GLOBALS['TL_MOOTOOLS'][] = $objScriptTemplate->parse();
		}
		
		$this->Template->products = $arrBuffer;
	}
	
	
	protected function setFilterSQL($arrFilters)
	{
		$arrFilterClauses = array();
		$arrSearchClauses = array();
		$arrOrderByClauses = array();
		$arrFilterChunks = array();
		$arrOrderBySQLWithParentTable = array();
		
		foreach($arrFilters as $filter=>$value)
		{
			if($value)
			{
				switch($filter)
				{
					case 'order_by':
						$arrOrderByClauses[] = explode('-', $value);
						break;
						
					case 'per_page':
						//prepare per-page limit
						$this->perPage = $value;
						break;
						
					case 'page':
						$this->currentPage = $value;
						break;
						
					case 'for':
						//prepare clause for text search. TODO:  need to add filter for each std. search field plus any additional user-defined.
						$arrSearchFields = array('name','description');
						
						foreach($arrSearchFields as $field)
						{
							$arrSearchClauses[] = $this->addFilter($value, $field, 'search');
						}
						break;
						
					default:
						$arrFilterClauses[] = $this->addFilter($value, $filter, 'filter');
						break;
				}
			}						
		}
		
		if(count($arrFilterClauses[0]))
		{			
			foreach($arrFilterClauses as $param)
			{
				$arrFilterChunks[] = $param['sql'];
				$this->arrParams[] = $param['value'];
			}
		}	

		if(count($arrSearchClauses[0]))
		{
			foreach($arrSearchClauses as $param)
			{
				$arrSearchChunks[] = $param['sql'];
				$this->arrParams[] = $param['value'];
			}
		}	

		if(count($arrOrderByClauses[0]))
		{
			foreach($arrOrderByClauses as $row)
			{
				$arrOrderBySQL[] = implode(" ", $row);
			}
			
			foreach($arrOrderBySQL as $row)
			{
				if(strlen($row))
				{
					$arrRow = explode(" ", $row);
					
					switch($arrRow[0])
					{
						case 'price':		//Workaround to deal with price field being VARCHAR... check on this with Andreas... should be field type decimal.
							$arrOrderBySQLWithParentTable[] = "CAST(p." . $arrRow[0] . " AS decimal) " . $arrRow[1];
							break;
							
						default:
							$arrOrderBySQLWithParentTable[] = "p." . $row;
							break;
					}
				}
			}
			
			$this->strOrderBySQL = implode(', ', $arrOrderBySQLWithParentTable);
		}
		
		$this->strFilterSQL = (count($arrFilterChunks) ? implode(" AND ", $arrFilterChunks) : NULL);
		$this->strSearchSQL = (count($arrSearchChunks) ? implode(" OR ", $arrSearchChunks) : NULL);
	}
	
	
	protected function setCategories($strScope, $intRootId = 0, $intPageId = 0)
	{
		$arrCategories = array();
		
		//Determine category scope
		switch($strScope)
		{
			case 'global':
				 $arrCategories = array_merge($this->getChildRecords($intRootId, 'tl_page'), array($intRootId));
				break;
				
			case 'parent_and_first_child':
				$arrCategories = array_merge($this->Database->prepare("SELECT id FROM tl_page WHERE pid=?")->execute($intPageId)->fetchEach('id'), array($intPageId));
				break;
				
			case 'parent_and_all_children':
				$arrCategories = array_merge($this->getChildRecords($intPageId, 'tl_page'), array($intPageId));				
				break;
				
			default:
			case 'current_category':
				$arrCategories = array($intPageId);
				break;		
		}
		
		$i = 0;
		
		foreach($arrCategories as $row)
		{
			if(!$row)
			{
				unset($arrCategories[$i]);
			}
			
			$i++;
		}
		
		return $arrCategories;
	}
	
	
	public function generateAjax()
	{
		if($this->Input->get('clear'))
		{
			$arrFilters = array();
		} 
		else
		{			
			//get the default params
			$arrFilters = array('for'=>$this->Input->get('for'),'per_page'=>$this->Input->get('per_page'),'page'=>$this->Input->get('page'),'order_by'=>$this->Input->get('order_by'));	
			
			
			/*$arrFilterFields = implode(',', $this->Input->get('filters'));	//get the names of filters we are using
	
			foreach($arrFilterFields as $field)
			{
				if($this->Input->get($field))
				{
					$arrFilters[$field] = $this->Input->get($field);
				}
			}*/	
		}

		if(!count($arrFilters['order_by']))
		{	
			if($this->iso_listingSortField)
			{
				$arrFilters = array('order_by' => ($this->iso_listingSortField.'-'.$this->iso_listingSortDirection));
			}
		}
		
		$strHtml = $this->generateAJAXListing($arrFilters);

		return $strHtml;
	}


	/**
	 * Generate the listing template in html to update the listing results
	 * @var array $arrFilters
	 * @return string
	 */
	protected function generateAJAXListing($arrFilters)
	{
		$objTemplate = new FrontendTemplate($this->strTemplate);
		
		$this->arrCategories = $this->setCategories($this->iso_category_scope, $this->getRequestData('rid'), $this->getRequestData('pid'));		
		
		$this->setFilterSQL($arrFilters);
		
		//$strParams = (count($arrParams) ? implode(",", $arrParams) : NULL);
		
		$objProductIds = $this->Database->prepare("SELECT DISTINCT p.* FROM tl_product_categories c, tl_product_data p WHERE p.id=c.pid" . ($this->strFilterSQL ? " AND (" . $this->strFilterSQL . ")" : "") . " AND c.page_id IN (" . implode(',', $this->arrCategories) . ")" . ($this->strSearchSQL ? " AND (" . $this->strSearchSQL . ")" : "") . ($this->strOrderBySQL ? " ORDER BY " . $this->strOrderBySQL : ""));
		
		// Add pagination
		if ($this->perPage > 0)
		{
			$total = $objProductIds->execute($this->arrParams)->numRows;
			$page = $this->currentPage ? $this->currentPage : 1;
			$offset = ($page - 1) * $this->perPage;

			$objPagination = new Pagination($total, $this->perPage);
			$objTemplate->pagination = $objPagination->generate("\n  ");
			
			$objProductIds->limit($this->perPage, $offset);
		}
		
		$arrProducts = $this->getProducts($objProductIds->execute($this->arrParams)->fetchEach('id'));
			
		if (!is_array($arrProducts) || !count($arrProducts))
		{
			$objTemplate = new FrontendTemplate('mod_message');
			$objTemplate->type = 'empty';
			$objTemplate->message = $GLOBALS['TL_LANG']['MSC']['noProducts'];
			return;
		}
			
		$arrBuffer = array();
		
		foreach( $arrProducts as $i => $objProduct )
		{
			$arrBuffer[] = array
			(
				'clear'	    => ($this->iso_list_format=='grid' && $blnSetClear ? true : false),
				'class'		=> ('product' . ($i == 0 ? ' product_first' : '')),
				'html'		=> $objProduct->generate((strlen($this->iso_list_layout) ? $this->iso_list_layout : $objProduct->list_template), $this),
			);

			$blnSetClear = (($i+1) % $this->columns==0 ? true : false);
		}
		
		// Add "product_last" css class
		if (count($arrBuffer))
		{
			$arrBuffer[count($arrBuffer)-1]['class'] .= ' product_last';
		}

		$objTemplate->products = $arrBuffer;
		
		return $objTemplate->parse();
	}
	
	
	/** 
	 * Gather SQL clause components to be added into the sql query for pulling product data
	 *
	 * @param variant $varValue
	 * @param string $strKey
	 * @param string $strType
	 * @return array
	 */
	protected function addFilter($varValue, $strKey, $strType)
	{
		$arrReturn = array();
		
		if($varValue)
		{
			switch($strType)
			{
				case 'search':
					$arrReturn['sql'] 		= "p." . $strKey . " LIKE ?";
					$strValue = str_replace('%', '', $varValue);
		
					$arrReturn['value'] 	= "%%" . $strValue . "%";	//double wildcard necessary to get around vsprintf bug.				
					break;
				case 'filter':
					$arrReturn['sql']		= "p." . $strKey . "=?";
					$arrReturn['value']		= $varValue;
					break;
				default:
					break;
			}
		}		
		
		return $arrReturn;
	}
}

