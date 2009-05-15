<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2009 JoH asenau <jh@eqony.com>
	*  All rights reserved
	*
	*  This script is part of the TYPO3 project. The TYPO3 project is
	*  free software; you can redistribute it and/or modify
	*  it under the terms of the GNU General Public License as published by
	*  the Free Software Foundation; either version 2 of the License, or
	*  (at your option) any later version.
	*
	*  The GNU General Public License can be found at
	*  http://www.gnu.org/copyleft/gpl.html.
	*
	*  This script is distributed in the hope that it will be useful,
	*  but WITHOUT ANY WARRANTY; without even the implied warranty of
	*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*  GNU General Public License for more details.
	*
	*  This copyright notice MUST APPEAR in all copies of the script!
	***************************************************************/
	 
	 
	// DEFAULT initialization of a module [BEGIN]
	unset($MCONF);
	require_once('conf.php');
	require_once($BACK_PATH.'init.php');
	require_once($BACK_PATH.'template.php');
	 
	$LANG->includeLLFile('EXT:t3designkit/mod3/locallang.xml');
	require_once(PATH_t3lib.'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF, 1); // This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
	 
	 
	 
	/**
	* Module 'Site Generator' for the 't3designkit' extension.
	*
	* @author JoH asenau <jh@eqony.com>
	* @package TYPO3
	* @subpackage tx_t3designkit
	*/
	class tx_t3designkit_module2 extends t3lib_SCbase {
		var $pageinfo;
		 
		/**
		* Initializes the Module
		*
		* @return void
		*/
		function init() {
			global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
			 
			parent::init();
			 
			/*
			if (t3lib_div::_GP('clear_all_cache')) {
			$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
			}
			*/
		}
		 
		/**
		* Adds items to the->MOD_MENU array. Used for the function menu selector.
		*
		* @return void
		*/
		function menuConfig() {
			global $LANG;
			$this->MOD_MENU = Array (
			'function' => Array (
			'1' => $LANG->getLL('function1'),
				'2' => $LANG->getLL('function2'),
				'3' => $LANG->getLL('function3'),
				)
			);
			parent::menuConfig();
		}
		 
		/**
		* Main function of the module. Write the content to $this->content
		* If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
		*
		* @return [type]  ...
		*/
		function main() {
			global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
			 
			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			 
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
				
				$this->t3dksk = t3lib_div::_GP('t3dksk');
			 
				// Draw the header.
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->JScode = '
				<link rel="stylesheet" type="text/css" href="css/designmanager.css" />
				<link rel="stylesheet" type="text/css" href="/typo3/contrib/extjs/resources/css/ext-all.css" />
				';
				$this->doc->JScode .= '
				<script type="text/javascript" src="/typo3/contrib/prototype/prototype.js"><!--PROTOTYPE--></script>
				<script type="text/javascript" src="/typo3/contrib/scriptaculous/scriptaculous.js?load=builder,effects,controls,dragdrop"><!--SCRIPTACULOUS--></script>
				<script type="text/javascript" src="/typo3/js/iecompatibility.js"><!--EXTJS--></script>
				<script type="text/javascript" src="/typo3/contrib/extjs/adapter/prototype/ext-prototype-adapter.js"><!--PROTOTYPE ADAPTER--></script>
				';
				/*$this->doc->JScode .= '
				<script type="text/javascript" src="/typo3/contrib/extjs/adapter/ext/ext-base.js"><!--EXT BASE--></script>';*/
				$this->doc->JScode .= '				
				<script type="text/javascript" src="/typo3/contrib/extjs/ext-all-debug.js"><!--EXTJS--></script>
				<script type="text/javascript" src="js/pagetreeFunctions.js"><!--PAGETREE--></script>
				';
				$this->doc->JScode .= '
				<script type="text/javascript" src="js/tabMenuFunctions.js"><!--TABMENU--></script>				
				<script type="text/javascript" src="js/interface.js"><!--TABMENU--></script>				
				';
				$this->doc->form = '<form id="designmanager_form" action="index.php" method="POST">
				';				 
				$this->content .= $this->doc->startPage($LANG->getLL('title'));

				if ($this->t3dksk['save']) {
					//Save Pagetree
					$this->savePageTree();
				} else {
					// Render content:
					$this->moduleContentDynTabs();
				}
				 
				 
				// ShortCut
				if ($BE_USER->mayMakeShortcut()) {
					$this->content .= '<div id="shortcuticon">'.$this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name'])).'</div>';
				}
				 
			} else {
				// If no access or if ID == zero
				 
				$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->doc->backPath = $BACK_PATH;
				 
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->spacer(10);
			}
		}
		 
		/**
		* [Describe function...]
		*
		* @return [type]  ...
		*/
		function moduleContentDynTabs() {
		
		    $this->content .= '<div id="topcontainer"><ul id="tabmenu">';
		    $this->content .= '<li id="tabitem1" class="'.($this->t3dksk['pagetree'][0] ? 'greenbutton' : 'redbutton').'"><a href="#" onclick="triggerTab(this,1);return false;">'.$GLOBALS['LANG']->getLL('TabLabel1').'</a></li>';
		    $this->content .= '<li id="tabitem2" class="'.(($this->t3dksk['createNew'] && $this->t3dksk['pagetree'][0]) || $this->t3dksk['page'][1] ? 'greenbutton' : 'redbutton').'">'.($this->t3dksk['page'][1] || $this->t3dksk['createNew'] && $this->t3dksk['pagetree'][0]  ? ('<a href="#" onclick="triggerTab(this,2);return false;">'.$GLOBALS['LANG']->getLL('TabLabel2').'</a>') : $GLOBALS['LANG']->getLL('TabLabel2')).'</li>';
		    $this->content .= '<li id="tabitem3" class="'.($this->t3dksk['gotoStep3'] || $this->t3dksk['page'][1] ? 'greenbutton' : 'redbutton').'">'.($this->t3dksk['page'][1] || $this->t3dksk['createNew'] && $this->t3dksk['pagetree'][0] ? ('<a href="#" onclick="triggerTab(this,3);return false;">'.$GLOBALS['LANG']->getLL('TabLabel3').'</a>') : $GLOBALS['LANG']->getLL('TabLabel3')).'</li>';
		    $this->content .= '<li id="tabitem4" class="redbutton">'.($this->t3dksk['page'][1] ? ('<a href="#" onclick="triggerTab(this,4);return false;">'.$GLOBALS['LANG']->getLL('TabLabel4').'</a>') : $GLOBALS['LANG']->getLL('TabLabel4')).'</li>';
		    $this->content .= '</ul></div>
		    <div id="tabcontent1" class="'.(($this->t3dksk['createNew'] && $this->t3dksk['pagetree'][0]) || $this->t3dksk['gotoStep3'] || $this->t3dksk['gotoStep4'] ? 'tabcontent_off' : 'tabcontent_on').'">
			'.$this->moduleContentTab1().'
		    </div>
		    <div id="tabcontent2" class="'.($this->t3dksk['createNew'] && $this->t3dksk['pagetree'][0] ? 'tabcontent_on' : 'tabcontent_off').'">
			'.$this->moduleContentTab2().'
		    </div>
		    <div id="tabcontent3" class="'.($this->t3dksk['gotoStep3'] ? 'tabcontent_on' : 'tabcontent_off').'">
			'.$this->moduleContentTab3().'
		    </div>
		    <div id="tabcontent4" class="'.($this->t3dksk['gotoStep4'] ? 'tabcontent_on' : 'tabcontent_off').'">
			'.$this->moduleContentTab4().'
		    </div>
		    <div id="bottomcontainer"><!--BOTTOM--></div>
		    ';
		    
		}
		 
		/**
		* [Describe function...]
		*
		* @return [type]  ...
		*/
		function savePageTree() {
			$pageTree = $this->t3dksk['page'];
			$parentId = $this->t3dksk['parentPage'];
			if (is_array($pageTree)) {
				 
				$saveData = array(
				'pid' => $parentId,
					'title' => 'TS Templates',
					'doktype' => 254,
					'crdate' => $timeNow,
					'tstamp' => $timeNow,
					'cruser_id' => $userId,
					'perms_userid' => $userId,
					'perms_user' => 31,
					'perms_group' => 27,
					'SYS_LASTCHANGED' => $timeNow,
					'sorting' => 100000000 );
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $saveData);
				 
				$this->savePageTreeLevel($pageTree, $parentId, $GLOBALS['BE_USER']->user['uid'], time());
			}
		}
		 
		/**
		* [Describe function...]
		*
		* @param [type]  $pageTree: ...
		* @param [type]  $parentId: ...
		* @param [type]  $userId: ...
		* @param [type]  $timeNow: ...
		* @return [type]  ...
		*/
		function savePageTreeLevel($pageTree, $parentId = 0, $userId, $timeNow) {
			global $LANG;
			$this->sortingCounter++;
			$selectedLanguage = $LANG->lang;
			foreach($pageTree as $valueArray) {
				if ($valueArray['title']) {
					$title = $valueArray['title']['lang'][$selectedLanguage];
					unset($valueArray['title']);
				}
				if ($valueArray['type']) {
					$type = $valueArray['type'];
					unset($valueArray['type']);
				}
				$saveData = array(
				'pid' => $parentId,
					'title' => $title,
					'doktype' => intval($type),
					'shortcut_mode' => intval($type) == 4 ? 1 :
				0,
					'crdate' => $timeNow,
					'tstamp' => $timeNow,
					'cruser_id' => $userId,
					'perms_userid' => $userId,
					'perms_user' => 31,
					'perms_group' => 27,
					'SYS_LASTCHANGED' => $timeNow,
					'sorting' => $this->sortingCounter );
				 
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $saveData);
				$newParentId = $GLOBALS['TYPO3_DB']->sql_insert_id();
				 
				if (is_array($valueArray['1'])) {
					$this->savePageTreeLevel($valueArray, $newParentId, $userId, $timeNow);
				}
			}
		}
		 
		/**
		* Prints out the module HTML
		*
		* @return void
		*/
		function printContent() {
			$this->content .= '<script type="text/javascript" src="js/tabMenuFunctions.js"><!--TABMENU--></script>';
			$this->content .= $this->doc->endPage();
			echo $this->content;
		}
		 
		/**
		* Generates the module content
		*
		* @return void
		*/
		function moduleContentTab1() {
			$this->tab1Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><div class="tabcontent tabscreen_left">'.$this->doc->header('Select from a list of predefined page trees').'</div>';
			$this->tab1Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div><img class="counterimage" src="icons/level1.gif" /><div class="tabcontent tabscreen_right">'.$this->doc->header('Create a dummy page tree based on the following settings');			
			$this->tab1Content .= '<div id="colorboxes">
			<div class="colorbox"><div class="color" style="background-color:#FFFFFF;"><!--//COLORFIELD//--></div><input class="originalinput" type="text" name="color1" value="#FFFFFF" /></div>
			<hr />
			<div class="colorbox"><div class="color" style="background-color:#FFCC99;"><!--//COLORFIELD//--></div><input class="originalinput" type="text" name="color2" value="#FFCC99" /></div>
			<hr />
			<div class="colorbox"><div class="color" style="background-color:#F3C462;"><!--//COLORFIELD//--></div><input class="originalinput" type="text" name="color3" value="#F3C462" /></div>
			<hr />
			<div class="colorbox"><div class="color" style="background-color:#999999;"><!--//COLORFIELD//--></div><input class="originalinput" type="text" name="color4" value="#999999" /></div>
			</div><br />';
			$this->tab1Content .= '</div>';
			return $this->tab1Content;
		}
		 
		/**
		* [Describe function...]
		*
		* @return [type]  ...
		*/
		function moduleContentTab2() {
			if (($this->t3dksk['page']['1'] && !$this->t3dksk['createNew']) || $this->t3dksk['createNew']) {
				$this->tab2Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><div class="tabcontent tabscreen_left">'.$this->doc->header('These are optional settings').'</div>';
				$this->tab2Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div><img class="counterimage" src="icons/level2.gif" /><div class="tabcontent tabscreen_right">'.$this->doc->header('Parent page for the new tree');
				$this->listAvailablePages();
				$this->tab2Content .= $this->doc->header('Set the type of your pages');
				$checked = $this->t3dksk['setPageType'] ? ' checked="checked" ' :
				'';
				$this->tab2Content .= '<fieldset style="padding:0.2em 0.5em;margin:0.5em 0 1em 0;">
					<fieldset class="optionalsettings"><input style="border:none;" type="checkbox" id="t3dksk_setPageType" name="t3dksk[setPageType]" value="1"'.$checked.' /> <label for="t3dksk_setPageType">Set basic page types as well, else <em>Standard</em> will be default.</label></fieldset>
					<p>After checking the box, the preview will change so that you can select from 3 basic page types.</p><br /><p><dfn>Shortcut</dfn> will redirect to it\'s first subpage by default so it can\'t be selected for pages at the last level,
					<dfn>SysFolder</dfn> is a container that won\'t be visible in the frontend).</p>
					<div class="stepsubmit">
					    <input id="t3dksk_gotoStep3" type="submit" class="submit" name="t3dksk[gotoStep3]" value="Save your settings" />
					</div>
					</fieldset>
					</div>';
			}
			return $this->tab2Content;
		}
		 
		/**
		* [Describe function...]
		*
		* @return [type]  ...
		*/
		function moduleContentTab3() {
			if (($this->t3dksk['page']['1'] && !$this->t3dksk['createNew']) || $this->t3dksk['createNew']) {
				$this->tab3Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><div class="tabcontent tabscreen_left"><fieldset class="previewlegend"><ul>
					<li>You can use the <dfn><img src="icons/plusonly.gif" title="plus icon" />/<img src="icons/minusonly.gif" title="minus icon"/>&nbsp;icons</dfn> to un-/fold higher levels of the page tree.</li>
					<li>Move the pages using the <dfn><img src="icons/up.gif" title="arrow up"/>up&nbsp;and&nbsp;<img src="icons/down.gif" title="arrow down"/>&nbsp;down arrows</dfn>.</li>
					<li>Add a new page by clicking on this <dfn><img src="icons/new_el.gif" title="page icon"/>&nbsp;page&nbsp;icon</dfn> at the right side. A double click will clone the page and all available subpages.</li>
					<li>Add a new subpage to a page without pages on the next level by clicking on this <dfn><img src="icons/new_level.gif" title="arrow icon"/>&nbsp;arrow&nbsp;icon</dfn> at the left side.</li>
					<li>If you klick on this <dfn><img src="icons/delete_record.gif" title="garbage icon"/>&nbsp;garbage&nbsp;icon</dfn>, this page and all it\'s subpages will be removed from the page tree.</li>
					</ul>
					<p>Additionally the Site Generator will always create a SysFolder called <em>TS&nbsp;Templates</em> as the last page that will be referred to by the Design Importer later on.</p><br />
					<p><strong>As long as you don\'t create the tree in step 4, anything you do will happen just virtually and nothing will be transferred to the TYPO3 database.</strong></p><br /></fieldset>';
				$this->tab3Content .= '<div class="stepsubmit">
				    <input id="t3dksk_gotoStep4" type="submit" class="submit" name="t3dksk[gotoStep4]" value="Save this tree" />
				    </div>
				</div>';
					
				$this->tab3Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div><img class="counterimage" src="icons/level3.gif" /><div class="tabcontent tabscreen_right">
				'.$this->doc->header('Modify the structure according to your website');
				$this->tab3Content .= '<script type="text/javascript" src="t3dksk_pageTreeFunctions.js"><!--//PAGETREEFUNCTIONS//--></script>';
				if ($this->t3dksk['createNew']) {
					$this->makeFormFields(0, intval($this->t3dksk['pagetree'][0]), 'new');
				}
				else if ($this->t3dksk['page']['1']) {
					$this->makeFormFields(0, $this->t3dksk['page'], 'current');
				}
				$this->tab3Content .= '</div>';
			}
			return $this->tab3Content;
		}
		 
		/**
		* [Describe function...]
		*
		* @return [type]  ...
		*/
		function moduleContentTab4() {
			if (($this->t3dksk['page']['1'] && !$this->t3dksk['createNew']) || $this->t3dksk['createNew']) {
				$xmlArray = $this->t3dksk;
				unset($xmlArray['createNew']);
				unset($xmlArray['gotoStep3']);
				unset($xmlArray['gotoStep4']);
				unset($xmlArray['parentPage']);
				unset($xmlArray['xmlContent']);
				$this->tab4Content .= '<img class="counterimage" src="icons/level4.gif" />
				<div class="tabscreenback1"><!--BACKGROUND--></div><div class="tabcontent tabscreen_left">';
				$this->makeStatistics($xmlArray['page']);
				if(is_array($this->pageTypeCounter)) {
				    $allPages = intval(array_sum($this->pageTypeCounter));
				}
				$this->tab4Content .= $this->pageTypeCounter[1] ? '
				<table>
				    <tr>
					<th colspan="2">Statistics</th>
				    </tr>
				    <tr>
					<th>
					    Root-Pages:
					</th>
					<td>
					    '.intval(count($xmlArray['page'])).'/'.$allPages.'
					</td>
				    </tr>
				    <tr>
					<th>
					    Content-Pages:
					</th>
					<td>
					    '.intval($this->pageTypeCounter[1]).'/'.$allPages.'
					</td>
				    </tr>
				    <tr>
					<th>
					    Shortcut-Pages:
					</th>
					<td>
					    '.intval($this->pageTypeCounter[4]).'/'.$allPages.'
					</td>
				    </tr>
				    <tr>
					<th>
					    SysFolders:
					</th>
					<td>
					    '.intval($this->pageTypeCounter[254]).'/'.$allPages.'
					</td>
				    </tr>
				    <tr>
					<th>
					    Levels:
					</th>
					<td>
					    '.count($this->levelArray).'
					</td>
				    </tr>
				</table>' : '';
				$this->tab4Content .= '
				<div class="stepsubmit">				    
				    <input type="submit" class="submit" name="t3dksk[save]" value="Create this tree now" />
				</div>
				</div>';
				$this->tab4Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div><div class="tabcontent tabscreen_right">
				<textarea style="width:435px; height:290px;" name="t3dksk[xmlContent]">'.str_replace(chr(9), '    ', t3lib_div::array2xml($xmlArray)).'</textarea>';
				$this->tab4Content .= '
				<div class="stepsubmit">
				    <input type="submit" class="submit" name="t3dksk[saveXML]" value="Save the tree as XML" />
				</div>
				</div>';
			}
			return $this->tab4Content;
		}
		 
		/**
		* [Describe function...]
		*
		* @param [type]  $level: ...
		* @return [type]  ...
		*/
		function makeLevelSelector($level) {
			$this->tab1Content .= '
				<label for="t3dksk_pagetree_level'.$level.'_selector">'.($level == 0 ? 'Root-Level' : 'Level ').($level == 0 ? '' : $level).':</label>&nbsp;<select id="t3dksk_pagetree_level'.$level.'_selector" name="t3dksk[pagetree]['.$level.']" onchange="submit();">';
			$this->tab1Content .= '<option value="">--</option>';
			for($i = 1; $i <= 20; $i++) {
				if ($i == $this->t3dksk['pagetree'][$level]) {
					$this->tab1Content .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
				} else {
					$this->tab1Content .= '<option value="'.$i.'"">'.$i.'</option>';
				}
			}
			$this->tab1Content .= '</select><br />';
		}
		 
		/**
		* [Describe function...]
		*
		* @param [type]  $level: ...
		* @param [type]  $pages: ...
		* @param [type]  $currentNew: ...
		* @param [type]  $parent: ...
		* @return [type]  ...
		*/
		function makeFormFields($level, $pages, $currentNew, $parent = '') {
			if ($currentNew == 'current') {
				$pagesCountArray = $pages;
				unset($pagesCountArray['title']);
				unset($pagesCountArray['type']);
				$numberOfPages = count($pagesCountArray);
			} else {
				$numberOfPages = $pages;
			}
			global $LANG;
			$this->tab3Content .= '<ul id="pagetreePreview" style="display:'.($level < 2? 'block' : 'none').';list-style-type:none;margin-left:'.($level == 0 ? '0' : '1.2em').';padding-left:'.($level == 0 ? '0' : '1em').';">';
			if ($currentNew == 'new') {
				for($i = 1; $i <= $numberOfPages; $i++) {
					$this->makeListLine($i, $i, array(), $level, $currentNew, $parent, $numberOfPages);
				}
			} else {
				if (is_array($pagesCountArray)) {
					$i = 0;
					foreach($pagesCountArray as $key => $val) {
						$i++;
						$this->makeListLine($i, $key, $val, $level, $currentNew, $parent, $numberOfPages);
					}
				}
			}
			$this->tab3Content .= '</ul>';
		}
		 
		/**
		* [Describe function...]
		*
		* @param [type]  $i: ...
		* @param [type]  $key: ...
		* @param [type]  $pages: ...
		* @param [type]  $level: ...
		* @param [type]  $currentNew: ...
		* @param [type]  $parent: ...
		* @param [type]  $numberOfPages: ...
		* @return [type]  ...
		*/
		function makeListLine($i, $key, $pages, $level, $currentNew, $parent, $numberOfPages) {
			global $LANG;
			$this->counter[$level]++;
			$this->tab3Content .= '<li id="t3dksk'.$level.$this->counter[$level].'">';
			if (($this->t3dksk['pagetree'][$level+1] && $currentNew == 'new') || $pages[1]) {
				$this->tab3Content .= '
					<a href="#" onclick="switchVisibility(this);" style="vertical-align:middle;"><img src="icons/'.($level == 0 ? 'minus' : 'plus').'only.gif" title="'.($level == 0 ? 'F' : 'Unf').'old this part of the page tree"/></a>';
			} else {
				$this->tab3Content .= '
					<a href="#" onclick="addSubItem(this);" style="vertical-align:middle;"><img src="icons/new_level.gif" title="Create a subpage on next level"/></a>';
			}
			$parentForName = str_replace('.', '][', $parent);
			if ($currentNew == 'current') {
				$parentForArray = array();
				$parentForArray = t3lib_div::trimExplode('.', $parent);
			}
			$selectedLanguage = $LANG->lang;
			$this->tabIndexCounter ++;
			$this->tab3Content .= '
				<input tabindex="'.$this->tabIndexCounter.'" type="text" class="dark" style="vertical-align:middle;" onmouseover="this.onfocus();" onmouseout="this.onblur();" onfocus="dimmer(this,\'light\');" onblur="dimmer(this,\'dark\');" size="'.($this->t3dksk['setPageType'] ? 30 : 43).'" name="t3dksk[page]['.$parentForName.$key.'][title][lang]['.$selectedLanguage.']" value="'.(isset($pages['title']['lang'][$selectedLanguage]) ? $pages['title']['lang'][$selectedLanguage] : (($level==0 ? 'Root' : $LANG->getLL('page')).' '.$parent.$i)).'" />';
			if ($this->t3dksk['setPageType']) {
				$this->tab3Content .= '<select class="dark" onmouseover="this.onfocus();" onmouseout="this.onblur();" onfocus="dimmer(this,\'light\');" onblur="dimmer(this,\'dark\');" name="t3dksk[page]['.$parentForName.$i.'][type]" style="vertical-align:middle;">';
				$selected = intval($currentValue['type']) == 1 ? ' selected="selected"' :
				'';
				$this->tab3Content .= '<option value="1"'.$selected.'>Standard</option>';
				if ($this->t3dksk['pagetree'][$level+1] || $pages[1]) {
					$selected = intval($currentValue['type']) == 4 ? ' selected="selected"' :
					'';
					$this->tab3Content .= '<option value="4"'.$selected.'>Shortcut</option>';
				}
				$selected = intval($currentValue['type']) == 254 ? ' selected="selected"' :
				'';
				$this->tab3Content .= '<option value="254"'.$selected.'>SysFolder</option>
					</select>
					';
			}
			$this->tab3Content .= '<a href="#" onclick="addItem(this);" ondblclick="cloneItem(this);"><img src="icons/new_el.gif" title="Add new page after this one" /></a><a href="#;" onclick="removeItem(this);"><img src="icons/delete_record.gif" title="Remove this page and all it\'s subpages"/></a>';
			$this->tab3Content .= '<a id="t3dksk'.$level.$this->counter[$level].'up" href="#;" onclick="moveItem(this,\'-1\');" style="display:'.($i > 1 ? 'inline' : 'none').';"><img src="icons/up.gif" title="Move this page up by one step"/></a>';
			$this->tab3Content .= '<a id="t3dksk'.$level.$this->counter[$level].'down" href="#;" onclick="moveItem(this,\'1\');" style="display:'.($i < $numberOfPages ? 'inline' : 'none').';"><img src="icons/down.gif" title="Move this page down by one step" /></a>';
			if (($this->t3dksk['pagetree'][$level+1] && $currentNew == 'new') || $pages[1]) {
				if ($currentNew == 'new') {
					$this->makeFormFields($level+1, intval($this->t3dksk['pagetree'][$level+1]), 'new', $parent.$i.'.');
				} else {
					$this->makeFormFields($level+1, $pages, 'current', $parent.$key.'.');
				}
			}
			$this->tab3Content .= '</li>';
		}
		 
		/**
		* [Describe function...]
		*
		* @return [type]  ...
		*/
		function listAvailablePages() {
			$availablePages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,title', 'pages', 'NOT deleted');
			$this->tab2Content .= '<fieldset style="padding:0.2em 0.5em;"><label for="t3dksk_parentPage">Levels will be calculated relatively to the level of this page.</label>
				<fieldset class="optionalsettings"><select id="t3dksk_parentPage"  name="t3dksk[parentPage]">
				<option value="0">-- Absolute Root --</option>';
			if (is_array($availablePages)) {
				foreach($availablePages as $valueSet) {
					$selected = ($valueSet['uid'] == $this->t3dksk['parentPage']) ? ' selected="selected"' :
					'';
					$this->tab2Content .= '<option value="'.$valueSet['uid'].'"'.$selected.'>['.$valueSet['uid'].'] '.$valueSet['title'].'</option>';
				}
			}
			$this->tab2Content .= '</select></fieldset></fieldset>';
		}
		
		function makeStatistics($pageArray,$level=0) {
		    if($this->arrayCounter == 1) {
			$level++;
			$this->levelArray[$level]++;
		    }
		    if($pageArray['type'] && $this->arrayCounter) {
			$this->pageTypeCounter[$pageArray['type']]++;
		    } else if ($this->arrayCounter){
			$this->pageTypeCounter[1]++;
		    }
		    $this->arrayCounter = 1;
		    if(is_array($pageArray)) {
			foreach($pageArray as $subPageArray) {
	    		    unset($subPageArray['title']);
			    unset($subPageArray['type']);
			    $this->makeStatistics($subPageArray,$level);
			}
		    }
		}
	}
	 
	 
	 
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3designkit/mod2/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3designkit/mod2/index.php']);
	}
	 
	 
	 
	 
	// Make instance:
	$SOBE = t3lib_div::makeInstance('tx_t3designkit_module2');
	$SOBE->init();
	 
	// Include files?
	foreach($SOBE->include_once as $INC_FILE) include_once($INC_FILE);
	 
	$SOBE->main();
	$SOBE->printContent();
	 
?>
