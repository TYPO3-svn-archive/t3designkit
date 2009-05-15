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
	 
	$LANG->includeLLFile('EXT:t3designkit/mod2/locallang.xml');
	require_once(PATH_t3lib.'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF, 1); // This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
	 
	 
	 
	/**
	* Module 'Page Tree Wizard' for the 't3designkit' extension.
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
				 
				// load the GET/POST values of all variable used with the page tree wizard
				$this->t3dkptw = t3lib_div::_GP('t3dkptw');
				 
				// if a preset has been selected override the values fo the array with the values taken from the XML structure
				if (is_array($this->t3dkptw['preset'])) {
					$filePath = 'preset/'.key($this->t3dkptw['preset']).'/'.key($this->t3dkptw['preset'][key($this->t3dkptw['preset'])]).'/structure.xml';
					if (is_file($filePath)) {
						$file = utf8_encode(file_get_contents($filePath));
						$this->t3dkptw = t3lib_div::xml2Array($file);
						$this->t3dkptw['gotoStep3'] = 1;
					}
				}	
				// if the user doesn't want to create a special tree just deliver the dummy and set the save-switch to 1
				else if($this->t3dkptw['dontCare']) {
					$filePath = 'preset/dummy.xml';
					if (is_file($filePath)) {
						$file = utf8_encode(file_get_contents($filePath));
						$this->t3dkptw = t3lib_div::xml2Array($file);
						$this->t3dkptw['save'] = 1;
					}
				}
								
				// create the backend page and get some CSS and JS for the header
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->JScode = '<link rel="stylesheet" type="text/css" href="css/pagetreewizard.css" />';
				$this->doc->JScode .= '
					<script type="text/javascript" src="js/pagetreeFunctions.js"><!--PAGETREE--></script>';
				$this->doc->JScode .= '
					<script type="text/javascript" src="js/tabMenuFunctions.js"><!--TABMENU--></script>';
				$this->doc->form = '<form id="pagetreewizard_form" action="index.php" method="POST">';
				 
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				
				
				// if the user clicked on save, call the savePageTree function and then jump to web layout to watch the new tree
				if ($this->t3dkptw['save']) {
					//Save Pagetree
					$this->savePageTree();
					$this->content .= '<script type="text/javascript">top.goToModule(\'web_layout\');</script>';
					$this->printContent();
					exit;
				}
				// if the user just wants to create a new preset, save it and go to step 1 again
				else if ($this->t3dkptw['saveXML'] && $this->t3dkptw['xmlFileName'] && $this->t3dkptw['xmlFileDesc']) {
					// Render content:
					$this->saveXML();
					// Render content:
					$this->moduleContentDynTabs();
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
		* This function creates the directory tree for all available presets 
		* so that the user can select an appropriate preset tree with just one click
		*
		* @return [HTML]  A formatted list of directories
		*/
		function makePresetDirectoryTree() {
			// the starting directory will always be the preset subdirectory of this extension
			$masterDirectory = opendir('preset');
			
			// find subdirectories
			while (false !== ($dirName = readdir($masterDirectory))) {
				if (
				$dirName != '.' && $dirName != '..' && is_dir('preset/'.$dirName)
				) {
					$availableDirectories[$dirName] = array();
				}
			}
			closedir($masterDirectory);
			
			// find XML files and descriptions in all directories
			foreach($availableDirectories as $dirName => $subDir) {
				if (is_dir('preset/'.$dirName)) {
					$subDirectory = opendir('preset/'.$dirName);
					while (false !== ($subDirName = readdir($subDirectory))) {
						if (
						$subDirName != '.' && $subDirName != '..' && is_dir('preset/'.$dirName.'/'.$subDirName) && is_file('preset/'.$dirName.'/'.$subDirName.'/structure.xml') && is_file('preset/'.$dirName.'/'.$subDirName.'/description.txt')
						) {
							$availableDirectories[$dirName][$subDirName] = file_get_contents('preset/'.$dirName.'/'.$subDirName.'/description.txt');
						}
					}
					closedir($subDirectory);
				}
			}
			
			// create the tree of all available directories containing description and structure files
			if (is_array($availableDirectories)) {
				$sortArray = array(
					0 => 'small',
					1 => 'medium',
					2 => 'large',
					3 => 'custom',
					);
				$content = '<dir id="presetTree"><li>preset/</li>';
				$content .= '<li><dir>';
				foreach($sortArray as $level1Name) {
					$content .= '<li>';
					$content .= $level1Name.'/';
					if (is_array($availableDirectories[$level1Name])) {
						ksort($availableDirectories[$level1Name]);
						foreach($availableDirectories[$level1Name] as $level2Name => $description) {
							$content .= '<dl>';
							$content .= '<dt><input name="t3dkptw[preset]['.$level1Name.']['.$level2Name.']" type="submit" value=">>" title="Use this preset" /> '.$level2Name.'</dt>';
							$content .= '<dd>'.$description.'</dd>';
							$content .= '</dl>';
						}
					}
					$content .= '</li>';
				};
				$content .= '</dir>
					</li></dir>';
			}
			return $content;
		}
		 
		 
		/**
		* Saves the page tree and additional records like templates and domains to the DB
		*
		* @return [void]  ...
		*/
		function savePageTree() {
			$timeNow = time();
			$userId = $GLOBALS['BE_USER']->user['uid'];
			$pageTree = $this->t3dkptw['page'];
			$parentId = $this->t3dkptw['parentPage'];
			if (is_array($pageTree)) {
				 
				$saveData = array(
					'pid' => intval($parentId),
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
				$this->TScontainerID = $GLOBALS['TYPO3_DB']->sql_insert_id();
				$this->savePageTreeLevel($pageTree, $parentId, $userId, $timeNow);
			}
		}
		 
		/**
		* Saves on level of a page tree after being called by the savePageTree function
		* calls itself recursively for subpages and their subpages
		*
		* @param [array]  $pageTree: the branch of the page tree that is currently processed
		* @param [integer]  $parentId: the uid of the parent page of the branch that is currently processed
		* @param [integer]  $userId: the uid of the backend user that triggered the process
		* @param [integer]  $timeNow: a timestamp
		* @param [integer]  $level: the level of the page tree that is currently processed
		* @return [void]  ...
		*/
		function savePageTreeLevel($pageTree, $parentId = 0, $userId, $timeNow, $level = 0) {
			global $LANG;
			//this counter makes sure that the pages will be sorted in the right order after creation
			$this->sortingCounter++;
			$selectedLanguage = $LANG->lang;
			foreach($pageTree as $pageNumber => $valueArray) {
				// title and type have to be unset to assign only the subpages to the recursive call of this function
				// if there are different languages available use the one of the backend user
				if ($valueArray['title']) {
					$title = $valueArray['title']['lang'][$selectedLanguage];
					unset($valueArray['title']);
				}

				// title and type have to be unset to assign only the subpages to the recursive call of this function
				if ($valueArray['type']) {
					$type = $valueArray['type'];
					unset($valueArray['type']);
				}
				
				// fill the saveData Array with data from the page Array as well as some default values
				$saveData = array(
					'pid' => intval($parentId),
					'title' => $title,
					'doktype' => intval($type),
					'shortcut_mode' => intval($type) == 4 ? 1 : 0,
					'crdate' => $timeNow,
					'tstamp' => $timeNow,
					'cruser_id' => $userId,
					'perms_userid' => $userId,
					'perms_user' => 31,
					'perms_group' => 27,
					'perms_everybody' => 27,
					'is_siteroot' => $level ? 0 : 1,
					'SYS_LASTCHANGED' => $timeNow,
					'sorting' => $this->sortingCounter );
				
				// Save it to the DB and put the uid of the newly created record into a variable
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $saveData);
				$newParentId = $GLOBALS['TYPO3_DB']->sql_insert_id();
				
				// if we are on level 0 we have to assign the TS template to the root page
				// if there is more than one root page we have to check for domain names as well
				if ($level == 0) {
					$this->createBasicTypoScript($newParentId, $timeNow, $userId);
					if (isset($this->t3dkptw['domains'][$pageNumber])) {
						$domainNames = t3lib_div::trimExplode(',', $this->t3dkptw['domains'][$pageNumber]);
						foreach($domainNames as $domainName) {
							$this->createDomainRecords($domainName, $newParentId, $timeNow, $userId);
						}
					}
				}
				 
				// if there are subpages to the page currently processed, call the function recursively
				if (is_array($valueArray['1'])) {
					$this->savePageTreeLevel($valueArray, $newParentId, $userId, $timeNow, $level+1);
				}
			}
		}
		 
		/**
		* Creates the desired domain records for each root page if there is more than one root page
		*
		* @param [string]  $domainName:  The domain
		* @param [integer]  $parentId: The uid of the parent page to create the domain records for
		* @param [integer]  $timeNow:  a timestamp
		* @param [integer]  $userId: uid of the backend user that triggered the process
		* @return [void]  ...
		*/
		function createDomainRecords($domainName, $parentId, $timeNow, $userId) {
			$saveData = array(
			'pid' => intval($parentId),
				'tstamp' => $timeNow,
				'crdate' => $timeNow,
				'cruser_id' => $userId,
				'domainName' => $domainName );
			 
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_domain', $saveData);
		}
		 
		/**
		* Creats TypoScript templates for both the TS Template folder and the root pages
		*
		* @param [integer]  $parentId: The uid of the parent page to create this template for
		* @param [integer]  $timeNow: a timestamp
		* @param [integer]  $userId: uid of the backend user that triggered the process
		* @return [void]  ...
		*/
		function createBasicTypoScript($parentId, $timeNow, $userId) {
		
			// first we save the main template into the TS templates folder	
			$saveData = array(
			'pid' => intval($this->TScontainerID),
				'title' => 'Maintemplate '.$parentId,
				'root' => 1,
				'clear' => 3,
				'tstamp' => $timeNow,
				'crdate' => $timeNow,
				'cruser_id' => $userId,
				'config' => 'page = PAGE
				page.10 = TEXT
				page.10.value = HELLO WORLD' );
			 
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_template', $saveData);
			
			// now we get the uid of the newly created record
			$newTSTemplateID = $GLOBALS['TYPO3_DB']->sql_insert_id();
			 
			// and save a Mastertemplate to the root page that includes the one created before as a basis		
			$saveData = array(
			'pid' => intval($parentId),
				'title' => 'Mastertemplate '.$parentId,
				'basedOn' => $newTSTemplateID,
				'tstamp' => $timeNow,
				'crdate' => $timeNow,
				'cruser_id' => $userId );
			 
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_template', $saveData);
			 
		}
		 
		/**
		* Creates XML data out of the GP-Array and saves them into the preset/custom/ folder
		*
		* @return [void]  ...
		*/
		function saveXML() {
			$xmlArray = $this->t3dkptw;
			$xmlDescription = $this->t3dkptw['xmlFileDesc'];
			unset($xmlArray['createNew']);
			unset($xmlArray['gotoStep3']);
			unset($xmlArray['gotoStep4']);
			unset($xmlArray['parentPage']);
			unset($xmlArray['xmlContent']);
			unset($xmlArray['xmlFileName']);
			unset($xmlArray['xmlFileDesc']);
			unset($xmlArray['saveXML']);
			unset($xmlArray['preset']);
			$xmlTree .= t3lib_div::array2xml($xmlArray);
			if (!file_exists('preset/custom/'.$this->t3dkptw['xmlFileName'])) {
				mkdir('preset/custom/'.$this->t3dkptw['xmlFileName'], 0744);
			}
			if (!file_exists('preset/custom/'.$this->t3dkptw['xmlFileName'].'/structure.xml')) {
				$xmlFile = fopen('preset/custom/'.$this->t3dkptw['xmlFileName'].'/structure.xml', 'x');
				fwrite($xmlFile, $xmlTree);
			} else {
				$xmlFile = fopen('preset/custom/'.$this->t3dkptw['xmlFileName'].'/structure.xml', 'w');
				fwrite($xmlFile, $xmlTree);
			}
			fclose($xmlFile);
			if (!file_exists('preset/custom/'.$this->t3dkptw['xmlFileName'].'/description.txt')) {
				$descFile = fopen('preset/custom/'.$this->t3dkptw['xmlFileName'].'/description.txt', 'x');
				fwrite($descFile, $xmlDescription);
			} else {
				$descFile = fopen('preset/custom/'.$this->t3dkptw['xmlFileName'].'/description.txt', 'w');
				fwrite($descFile, $xmlDescription);
			}
			fclose($descFile);
		}
		 
		/**
		* Creates the Dynamic Tabs containing the different steps of the page tree wizard
		*
		* @return [void]  ...
		*/
		function moduleContentDynTabs() {
			 
			$this->content .= '<div id="topcontainer"><ul id="tabmenu">';
			$this->content .= '<li id="tabitem1" class="'.($this->t3dkptw['pagetree'][0] ? 'greenbutton1' : 'redbutton1').'"><a href="#" onclick="triggerTab(this,1);return false;">'.$GLOBALS['LANG']->getLL('TabLabel1').'</a></li>';
			$this->content .= '<li id="tabitem2" class="'.(($this->t3dkptw['createNew'] && $this->t3dkptw['pagetree'][0]) || $this->t3dkptw['page'][1] ? 'greenbutton2' : 'redbutton2').'">'.($this->t3dkptw['page'][1] || $this->t3dkptw['createNew'] && $this->t3dkptw['pagetree'][0] ? ('<a href="#" onclick="triggerTab(this,2);return false;">'.$GLOBALS['LANG']->getLL('TabLabel2').'</a>') : $GLOBALS['LANG']->getLL('TabLabel2')).'</li>';
			$this->content .= '<li id="tabitem3" class="'.($this->t3dkptw['gotoStep3'] || $this->t3dkptw['page'][1] ? 'greenbutton3' : 'redbutton3').'">'.($this->t3dkptw['page'][1] || $this->t3dkptw['createNew'] && $this->t3dkptw['pagetree'][0] ? ('<a href="#" onclick="triggerTab(this,3);return false;">'.$GLOBALS['LANG']->getLL('TabLabel3').'</a>') : $GLOBALS['LANG']->getLL('TabLabel3')).'</li>';
			$this->content .= '<li id="tabitem4" class="redbutton4">'.($this->t3dkptw['page'][1] ? ('<a href="#" onclick="triggerTab(this,4);return false;">'.$GLOBALS['LANG']->getLL('TabLabel4').'</a>') : $GLOBALS['LANG']->getLL('TabLabel4')).'</li>';
			$this->content .= '</ul></div>
				<div id="tabcontent1" class="'.(($this->t3dkptw['createNew'] && $this->t3dkptw['pagetree'][0]) || $this->t3dkptw['gotoStep3'] || $this->t3dkptw['gotoStep4'] ? 'tabcontent_off' : 'tabcontent_on').'">
				'.$this->moduleContentTab1().'
				</div>
				<div id="tabcontent2" class="'.($this->t3dkptw['createNew'] && $this->t3dkptw['pagetree'][0] ? 'tabcontent_on' : 'tabcontent_off').'">
				'.$this->moduleContentTab2().'
				</div>
				<div id="tabcontent3" class="'.($this->t3dkptw['gotoStep3'] ? 'tabcontent_on' : 'tabcontent_off').'">
				'.$this->moduleContentTab3().'
				</div>
				<div id="tabcontent4" class="'.($this->t3dkptw['gotoStep4'] ? 'tabcontent_on' : 'tabcontent_off').'">
				'.$this->moduleContentTab4().'
				</div>
				<div id="bottomcontainer"><!--BOTTOM--></div>
				';
			 
		}
		 
		/**
		* Prints out the module HTML
		*
		* @return void
		*/
		function printContent() {
			 
			$this->content .= $this->doc->endPage();
			echo $this->content;
		}
		 
		/**
		* Generates the module content for step 1
		*
		* @return [HTML]
		*/
		function moduleContentTab1() {
			$this->tab1Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><div class="tabcontent tabscreen_left">
				'.$this->doc->header('Select from a list of predefined page trees').'<hr />';
			if (is_dir('preset')) {
				$this->tab1Content .= $this->makePresetDirectoryTree();
			}
			$this->tab1Content .= '</div>';
			$this->tab1Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div><img class="counterimage" src="icons/level1.gif" /><div class="tabcontent tabscreen_right">
				'.$this->doc->header('Or create a new dummy page tree');
			$this->tab1Content .= '<hr /><p>If you can\'t wait, just click here:</p>
				<div class="stepsubmit">
				<input id="t3dkptw_createNew" type="submit" class="submit" name="t3dkptw[dontCare]" value="Hurry up, just create a dummy!" />
				</div>
				<fieldset style="clear:both;">
				<hr /><p>Otherwise set the number of pages per level</p>
				<fieldset class="levelselectors '.($this->t3dkptw['pagetree'][0] ? 'greenback' : 'redback').'">
				<div class="rightaligned">
				';
			$this->makeLevelSelector(0);
			if (is_array($this->t3dkptw['pagetree'])) {
				foreach($this->t3dkptw['pagetree'] as $level => $numberOfPages) {
					if (intval($numberOfPages) > 0) {
						$overallPagesArray[$level] = $overallPagesArray[$level-1] ? ($overallPagesArray[$level-1] * $numberOfPages) :
						$numberOfPages;
						$overallPages += $overallPagesArray[$level];
						if ($overallPages > 500) {
							break;
						} else {
							$this->makeLevelSelector(intval($level)+1);
						}
					} else {
						break;
					}
				}
			}
			$this->tab1Content .= '</div>
				</fieldset>
				';
			if ($overallPages > 100) {
				$this->tab1Content .= '<fieldset class="level1SelectorWarning">
					This will create <strong>'.$overallPages.'</strong> new pages! - Are you sure you want to do that?';
				if ($overallPages > 500) {
					$this->tab1Content .= '<br /><strong>The maximum number of pages guaranteed to be creatable in one go has been exceeded. The rendering of the HTML forms and the following database queries may run into timeouts and memory overflows. So go on at your own risk!</strong>';
				}
				$this->tab1Content .= '</fieldset>';
			}
			$this->tab1Content .= '<br /><strong>This is just kind of a kickstarter!</strong><br />
				Submitting any new settings here will override your current preview with a completely new dummy tree!
				<div class="stepsubmit">
				<input id="t3dkptw_createNew" type="submit" class="submit" name="t3dkptw[createNew]" value="Start a new dummy tree" />
				</div>
				</fieldset><hr />';
			$this->tab1Content .= '</div>';
			return $this->tab1Content;
		}
		 
		/**
		* Generates the module content for step 2
		*
		* @return [HTML]
		*/
		function moduleContentTab2() {
			if (($this->t3dkptw['page']['1'] && !$this->t3dkptw['createNew']) || $this->t3dkptw['createNew']) {
				$this->tab2Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><img class="counterimage" src="icons/level2.gif" /><div class="tabcontent tabscreen_left">
					'.$this->doc->header('Parent page for the new tree').'<hr />';
				$this->listAvailablePages();
				$this->tab2Content .= $this->doc->header('Set the type of your pages').'<hr />';
				$checked = $this->t3dkptw['setPageType'] ? ' checked="checked" ' :
				'';
				$this->tab2Content .= '<fieldset style="padding:0.2em 0.5em;margin:0.5em 0 1em 0;">
					<fieldset class="optionalsettings"><input style="border:none;" type="checkbox" id="t3dkptw_setPageType" name="t3dkptw[setPageType]" value="1"'.$checked.' />
					<label for="t3dkptw_setPageType">Set basic page types as well, else <em>Standard</em> will be default.</label>
					</fieldset>
					<p>After checking the box, the preview will change so that you can select from 3 basic page types.</p><br />
					<p><dfn>Shortcut</dfn> will redirect to it\'s first subpage by default so it can\'t be selected for pages at the last level, <dfn>SysFolder</dfn> is a container that won\'t be visible in the frontend).</p>
					</fieldset>
					</div>';
				$this->tab2Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div>
					<div class="tabcontent tabscreen_right">'.$this->doc->header('These are optional settings').'<hr />
					<div class="stepsubmit">
					<input id="t3dkptw_gotoStep3" type="submit" class="submit" name="t3dkptw[gotoStep3]" value="Save your settings" />
					</div>
					</div>';
			}
			return $this->tab2Content;
		}
		 
		/**
		* Generates the module content for step 3
		*
		* @return [HTML]
		*/
		function moduleContentTab3() {
			if (($this->t3dkptw['page']['1'] && !$this->t3dkptw['createNew']) || $this->t3dkptw['createNew']) {
				$this->tab3Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><img class="counterimage" src="icons/level3.gif" /><div class="tabcontent tabscreen_left">
					'.$this->doc->header('Modify the structure according to your website').'<hr />';
				$this->tab3Content .= '<script type="text/javascript" src="t3dkptw_pageTreeFunctions.js"><!--//PAGETREEFUNCTIONS//--></script>';
				if ($this->t3dkptw['createNew']) {
					$this->makeFormFields(0, intval($this->t3dkptw['pagetree'][0]), 'new');
				}
				else if ($this->t3dkptw['page']['1']) {
					$this->makeFormFields(0, $this->t3dkptw['page'], 'current');
				}
				$this->tab3Content .= '</div>';
				$this->tab3Content .= '<div class="tabscreenback2"><!--BACKGROUND--></div>
					<div class="tabcontent tabscreen_right"><fieldset class="previewlegend">
					<ul>
					<li>You can use the <dfn><img src="icons/plusonly.gif" title="plus icon" />/<img src="icons/minusonly.gif" title="minus icon"/>&nbsp;icons</dfn> to un-/fold higher levels of the page tree.</li>
					<li>Move the pages using the<br /><dfn><img src="icons/up.gif" title="arrow up"/>&nbsp;up&nbsp;and&nbsp;<img src="icons/down.gif" title="arrow down"/>&nbsp;down arrows</dfn>.</li>
					<li>Add a new page by clicking on this <dfn><img src="icons/new_el.gif" title="page icon"/>&nbsp;page&nbsp;icon</dfn> at the right side. A double click will clone the page and all available subpages.</li>
					<li>Add a new subpage to a page that hasn\'t got subpages yet by clicking on this<br /><dfn><img src="icons/new_level.gif" title="arrow icon"/>&nbsp;arrow&nbsp;icon</dfn> at the left side.</li>
					<li>If you klick on this <dfn><img src="icons/garbage.gif" title="garbage icon"/>&nbsp;garbage&nbsp;icon</dfn>, this page and all it\'s subpages will be removed from the page tree.</li>
					<li>Additionally the Site Generator will always create a SysFolder called <em>TS&nbsp;Templates</em> as the last page.</li>
					</ul>
					<p><strong>As long as you don\'t create the tree in step 4, anything you do will happen just virtually and nothing will be transferred to the TYPO3 database.</strong></p><br /></fieldset>';
				$this->tab3Content .= '<div class="stepsubmit">
					<input id="t3dkptw_gotoStep4" type="submit" class="submit" name="t3dkptw[gotoStep4]" value="Save this tree" />
					</div>
					</div>';
				 
			}
			return $this->tab3Content;
		}
		 
		/**
		* Generates the module content for step 4
		*
		* @return [HTML]
		*/
		function moduleContentTab4() {
			if (($this->t3dkptw['page']['1'] && !$this->t3dkptw['createNew']) || $this->t3dkptw['createNew']) {
				$xmlArray = $this->t3dkptw;
				unset($xmlArray['createNew']);
				unset($xmlArray['gotoStep3']);
				unset($xmlArray['gotoStep4']);
				unset($xmlArray['parentPage']);
				unset($xmlArray['xmlContent']);
				$this->tab4Content .= '<div class="tabscreenback1"><!--BACKGROUND--></div><div class="tabcontent tabscreen_left">
					'.$this->doc->header('Save this tree as a new custom preset').'<hr />
					<p><label>Label for this custom preset:</label>
					<input class="xmlFileName" type="text" name="t3dkptw[xmlFileName]" /></p><hr />
					<p><label>Description for this custom preset:</label></p>
					<textarea class="description" name="t3dkptw[xmlFileDesc]"></textarea><hr />
					<p><label>Preview:</label></p>
					<textarea class="xmlcontent" name="t3dkptw[xmlContent]" readonly="readonly">'.str_replace(chr(9), '    ', t3lib_div::array2xml($xmlArray)).'</textarea>';
				$this->tab4Content .= '
					<div class="stepsubmit">
					<input type="submit" class="submit" name="t3dkptw[saveXML]" value="Save a new custom preset" />
					</div>
					</div>';
				$this->tab4Content .= '<img class="counterimage" src="icons/level4.gif" />
					<div class="tabscreenback2"><!--BACKGROUND--></div><div class="tabcontent tabscreen_right">
					'.$this->doc->header('Insert the tree into the Database').'<hr />';
				$this->makeStatistics($xmlArray['page']);
				if (is_array($this->pageTypeCounter)) {
					$allPages = intval(array_sum($this->pageTypeCounter))+1+count($xmlArray['page']);
				}
				$this->tab4Content .= $this->pageTypeCounter[1] ? '
					<table class="t3dkptw_statistics">
					<tr>
					<th colspan="2">Statistics</th>
					</tr>
					<tr>
					<th>Root-Pages:</th><td>'.intval(count($xmlArray['page'])).'/'.$allPages.'</td>
					</tr>
					<tr>
					<th>Standard-Pages:</th><td>'.intval($this->pageTypeCounter[1]).'/'.$allPages.'</td>
					</tr>
					<tr>
					<th>Shortcut-Pages:</th><td>'.intval($this->pageTypeCounter[4]).'/'.$allPages.'</td>
					</tr>
					<tr>
					<th>SysFolders:</th><td>'.intval($this->pageTypeCounter[254]+1).'/'.$allPages.'</td>
					</tr>
					<tr>
					<th>Levels:</th><td>'.count($this->levelArray).'</td>
					</tr>
					</table>' :
				'';
				 
				if (intval(count($xmlArray['page']) > 1)) {
					$this->tab4Content .= '<hr /><p>You are going to create more than one root site. If you want, you can enter the domain(s) for each of them here, separated by comma, and TYPO3 will create the necessary domain records for you:</p><hr />
						<dir id="domainrecords">
						';
					$language = $GLOBALS['LANG']->lang;
					foreach($xmlArray['page'] as $rootPageNumber => $rootPageArray) {
						$this->tab4Content .= '<li><label>'.$rootPageArray['title']['lang'][$language].'</label><br /><input type="text" name="t3dkptw[domains]['.$rootPageNumber.']" /></li>';
					}
					$this->tab4Content .= '</dir>
						';
				}
				 
				$this->tab4Content .= '
					<div class="stepsubmit">
					<input type="submit" class="submit" name="t3dkptw[save]" value="Create this tree now" />
					</div>
					</div>';
			}
			return $this->tab4Content;
		}
		 
		/**
		* Helper function to create the level select boxes for step 1
		*
		* @param [integer]  $level: The level this box should be ceated for
		* @return void
		*/
		function makeLevelSelector($level) {
			$this->tab1Content .= '
				<label for="t3dkptw_pagetree_level'.$level.'_selector">'.($level == 0 ? 'Root-Sites' : 'Level ').($level == 0 ? '' : $level).':</label>&nbsp;<select id="t3dkptw_pagetree_level'.$level.'_selector" name="t3dkptw[pagetree]['.$level.']" onchange="submit();">';
			$this->tab1Content .= '<option value="">--</option>';
			for($i = 1; $i <= 20; $i++) {
				if ($i == $this->t3dkptw['pagetree'][$level]) {
					$this->tab1Content .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
				} else {
					$this->tab1Content .= '<option value="'.$i.'"">'.$i.'</option>';
				}
			}
			$this->tab1Content .= '</select><br />';
		}
		 
		/**
		* The base function for the JS driven page tree form
		* will be called recursively for children
		*
		* @param [integer]  $level: The level that is currently processed
		* @param [array]  $pages: The branch of the page tree that is currently processed
		* @param [switch]  $currentNew: can be 'current' or 'new' to check if the form field has been newly created or available before
		* @param [integer]  $parent: The ID of the parent entry to create unique id attributes for each item and it's children
		* @return [void]  ...
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
		* Creates a single element of the page tree with all necessary buttons
		*
		* @param [integer]  $i: counter for the children of a particular parent
		* @param [integer]  $key: the parent key
		* @param [array]  $pages: the branch of the page tree that is currently processed
		* @param [integer]  $level: the level that is currenlty processed
		* @param [switch]  $currentNew: can be 'current' for existing elements and 'new' for dynamically created elements
		* @param [integer]  $parent: the HTML id of the parent element
		* @param [integer]  $numberOfPages: A counter to decide if the up and down arrows have to be rendered or not
		* @return [void]  ...
		*/
		function makeListLine($i, $key, $pages, $level, $currentNew, $parent, $numberOfPages) {
			//t3lib_div::debug($pages);
			global $LANG;
			$this->counter[$level]++;
			$this->tab3Content .= '<li id="t3dkptw'.$level.$this->counter[$level].'">';
			if (($this->t3dkptw['pagetree'][$level+1] && $currentNew == 'new') || $pages[1]) {
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
				<input tabindex="'.$this->tabIndexCounter.'" type="text" class="dark" style="vertical-align:middle;"
				onmouseover="this.onfocus();" onmouseout="this.onblur();" onfocus="dimmer(this,\'light\');" onblur="dimmer(this,\'dark\');"
				size="'.($this->t3dkptw['setPageType'] ? 30 : 43).'" name="t3dkptw[page]['.$parentForName.$key.'][title][lang]['.$selectedLanguage.']"
				value="'.(isset($pages['title']['lang'][$selectedLanguage]) ? $pages['title']['lang'][$selectedLanguage] : (isset($pages['title']['lang']['default']) ? $pages['title']['lang']['default'] : (($level == 0 ? 'Root' : $LANG->getLL('page')).' '.$parent.$i))).'" />';
			if ($this->t3dkptw['setPageType']) {
				$this->tab3Content .= '<select class="dark" onmouseover="this.onfocus();" onmouseout="this.onblur();" onfocus="dimmer(this,\'light\');" onblur="dimmer(this,\'dark\');" name="t3dkptw[page]['.$parentForName.$i.'][type]" style="vertical-align:middle;">';
				$selected = intval($pages['type']) == 1 ? ' selected="selected"' :
				'';
				$this->tab3Content .= '<option value="1"'.$selected.'>Standard</option>';
				if ($this->t3dkptw['pagetree'][$level+1] || $pages[1]) {
					$selected = intval($pages['type']) == 4 ? ' selected="selected"' :
					'';
					$this->tab3Content .= '<option value="4"'.$selected.'>Shortcut</option>';
				}
				$selected = intval($pages['type']) == 254 ? ' selected="selected"' :
				'';
				$this->tab3Content .= '<option value="254"'.$selected.'>SysFolder</option>
					</select>
					';
			}
			$this->tab3Content .= '<a href="#" onclick="addItem(this);" ondblclick="cloneItem(this);"><img src="icons/new_el.gif" title="Add new page after this one" /></a><a href="#;" onclick="removeItem(this);"><img src="icons/garbage.gif" title="Remove this page and all it\'s subpages"/></a>';
			$this->tab3Content .= '<a id="t3dkptw'.$level.$this->counter[$level].'up" href="#;" onclick="moveItem(this,\'-1\');" style="display:'.($i > 1 ? 'inline' : 'none').';"><img src="icons/up.gif" title="Move this page up by one step"/></a>';
			$this->tab3Content .= '<a id="t3dkptw'.$level.$this->counter[$level].'down" href="#;" onclick="moveItem(this,\'1\');" style="display:'.($i < $numberOfPages ? 'inline' : 'none').';"><img src="icons/down.gif" title="Move this page down by one step" /></a>';
			if (($this->t3dkptw['pagetree'][$level+1] && $currentNew == 'new') || $pages[1]) {
				if ($currentNew == 'new') {
					$this->makeFormFields($level+1, intval($this->t3dkptw['pagetree'][$level+1]), 'new', $parent.$i.'.');
				} else {
					$this->makeFormFields($level+1, $pages, 'current', $parent.$key.'.');
				}
			}
			$this->tab3Content .= '</li>';
		}
		 
		/**
		* Lists all the pages that ar already available for this system
		* to let the user select them, if he wants to create a new branch for an existing tree
		*
		* @return [void]  ...
		*/
		function listAvailablePages() {
			$availablePages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,title', 'pages', 'NOT deleted');
			$this->tab2Content .= '<fieldset style="padding:0.2em 0.5em;"><label for="t3dkptw_parentPage">Levels will be calculated relatively to the level of this page.</label>
				<fieldset class="optionalsettings"><select id="t3dkptw_parentPage"  name="t3dkptw[parentPage]">
				<option value="0">-- Absolute Root --</option>';
			if (is_array($availablePages)) {
				foreach($availablePages as $valueSet) {
					$selected = ($valueSet['uid'] == $this->t3dkptw['parentPage']) ? ' selected="selected"' :
					'';
					$this->tab2Content .= '<option value="'.$valueSet['uid'].'"'.$selected.'>['.$valueSet['uid'].'] '.$valueSet['title'].'</option>';
				}
			}
			$this->tab2Content .= '</select></fieldset></fieldset>';
		}
		 
		/**
		* Statistics bout pages and types
		* to be shown before the page tree gets saved to the DB
		* calls itself recursively
		*
		* @param [array]  $pageArray: The whole page tree
		* @param [integer]  $level: The level that is currently processed
		* @return [type]  ...
		*/
		function makeStatistics($pageArray, $level = 0) {
			if ($this->arrayCounter == 1) {
				$level++;
				$this->levelArray[$level]++;
			}
			if ($this->arrayCounter) {
				$this->pageTypeCounter[1]++;
			}
			$this->arrayCounter = 1;
			if (is_array($pageArray)) {
				foreach($pageArray as $subPageArray) {
					unset($subPageArray['title']);
					if ($this->arrayCounter && $subPageArray['type']) {
						$this->pageTypeCounter[$subPageArray['type']]++;
						$this->pageTypeCounter[1]--;
						unset($subPageArray['type']);
					}
					$this->makeStatistics($subPageArray, $level);
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
