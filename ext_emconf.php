<?php

########################################################################
# Extension Manager/Repository config file for ext: "t3designkit"
#
# Auto generated 15-04-2009 04:59
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TYPO3 Design Kit',
	'description' => 'Create a website within just a few minutes by importing it from a set of predefined dummy page tree. Combine it with lots of designs available for download at t3.designkit.org and import and modify them using a sophisticated backend user interface.',
	'category' => 'module',
	'author' => 'JoH asenau',
	'author_email' => 'jh@eqony.com',
	'shy' => '',
	'dependencies' => 'cms,css_styled_content',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1,mod2,mod3',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_t3designkit/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'css_styled_content' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:91:{s:9:"ChangeLog";s:4:"05e3";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"ce7a";s:14:"ext_tables.php";s:4:"a797";s:19:"doc/wizard_form.dat";s:4:"e700";s:20:"doc/wizard_form.html";s:4:"5b06";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"a009";s:22:"mod1/locallang_mod.xml";s:4:"1c7d";s:19:"mod1/moduleicon.gif";s:4:"ce7a";s:13:"mod3/conf.php";s:4:"a91f";s:14:"mod3/index.php";s:4:"830e";s:18:"mod3/locallang.xml";s:4:"51cd";s:22:"mod3/locallang_mod.xml";s:4:"3d66";s:19:"mod3/moduleicon.gif";s:4:"ce7a";s:18:"mod3/icons/add.gif";s:4:"408a";s:26:"mod3/icons/button_down.gif";s:4:"fa54";s:26:"mod3/icons/button_left.gif";s:4:"cdec";s:27:"mod3/icons/button_right.gif";s:4:"5573";s:24:"mod3/icons/button_up.gif";s:4:"0cc7";s:20:"mod3/icons/clear.gif";s:4:"cc11";s:28:"mod3/icons/delete_record.gif";s:4:"e31a";s:19:"mod3/icons/down.gif";s:4:"b8a8";s:21:"mod3/icons/level1.gif";s:4:"443d";s:21:"mod3/icons/level2.gif";s:4:"d7a2";s:21:"mod3/icons/level3.gif";s:4:"47d3";s:21:"mod3/icons/level4.gif";s:4:"2271";s:20:"mod3/icons/minus.gif";s:4:"de77";s:26:"mod3/icons/minusbottom.gif";s:4:"5f7a";s:24:"mod3/icons/minusonly.gif";s:4:"362b";s:23:"mod3/icons/minustop.gif";s:4:"f47e";s:21:"mod3/icons/new_el.gif";s:4:"591c";s:24:"mod3/icons/new_level.gif";s:4:"7fcf";s:19:"mod3/icons/plus.gif";s:4:"d67c";s:25:"mod3/icons/plusbottom.gif";s:4:"9791";s:23:"mod3/icons/plusonly.gif";s:4:"f127";s:22:"mod3/icons/plustop.gif";s:4:"6d51";s:23:"mod3/icons/shortcut.gif";s:4:"7546";s:17:"mod3/icons/up.gif";s:4:"822e";s:36:"mod3/preset/small_corporate_site.xml";s:4:"872c";s:27:"mod3/images/background1.png";s:4:"b1a8";s:31:"mod3/images/buttongreen_off.png";s:4:"89af";s:30:"mod3/images/buttongreen_on.png";s:4:"edeb";s:29:"mod3/images/buttonred_off.png";s:4:"1918";s:28:"mod3/images/buttonred_on.png";s:4:"b24b";s:26:"mod3/images/screenback.png";s:4:"4ba3";s:28:"mod3/images/submitbutton.png";s:4:"51a7";s:24:"mod3/css/kickstarter.css";s:4:"26c7";s:28:"mod3/js/pagetreeFunctions.js";s:4:"91e1";s:27:"mod3/js/tabMenuFunctions.js";s:4:"a692";s:13:"mod2/conf.php";s:4:"f86c";s:14:"mod2/index.php";s:4:"9343";s:18:"mod2/locallang.xml";s:4:"51cd";s:22:"mod2/locallang_mod.xml";s:4:"f7e4";s:19:"mod2/moduleicon.gif";s:4:"dfe0";s:18:"mod2/icons/add.gif";s:4:"408a";s:26:"mod2/icons/button_down.gif";s:4:"fa54";s:26:"mod2/icons/button_left.gif";s:4:"cdec";s:27:"mod2/icons/button_right.gif";s:4:"5573";s:24:"mod2/icons/button_up.gif";s:4:"0cc7";s:20:"mod2/icons/clear.gif";s:4:"cc11";s:19:"mod2/icons/down.gif";s:4:"b8a8";s:22:"mod2/icons/garbage.gif";s:4:"7fbe";s:21:"mod2/icons/level1.gif";s:4:"443d";s:21:"mod2/icons/level2.gif";s:4:"d7a2";s:21:"mod2/icons/level3.gif";s:4:"47d3";s:21:"mod2/icons/level4.gif";s:4:"2271";s:20:"mod2/icons/minus.gif";s:4:"de77";s:26:"mod2/icons/minusbottom.gif";s:4:"5f7a";s:24:"mod2/icons/minusonly.gif";s:4:"362b";s:23:"mod2/icons/minustop.gif";s:4:"f47e";s:21:"mod2/icons/new_el.gif";s:4:"591c";s:24:"mod2/icons/new_level.gif";s:4:"7fcf";s:19:"mod2/icons/plus.gif";s:4:"d67c";s:25:"mod2/icons/plusbottom.gif";s:4:"9791";s:23:"mod2/icons/plusonly.gif";s:4:"f127";s:22:"mod2/icons/plustop.gif";s:4:"6d51";s:23:"mod2/icons/shortcut.gif";s:4:"7546";s:17:"mod2/icons/up.gif";s:4:"822e";s:36:"mod2/preset/small_corporate_site.xml";s:4:"872c";s:27:"mod2/images/background1.png";s:4:"b1a8";s:31:"mod2/images/buttongreen_off.png";s:4:"89af";s:30:"mod2/images/buttongreen_on.png";s:4:"edeb";s:29:"mod2/images/buttonred_off.png";s:4:"1918";s:28:"mod2/images/buttonred_on.png";s:4:"b24b";s:26:"mod2/images/screenback.png";s:4:"4ba3";s:28:"mod2/images/submitbutton.png";s:4:"51a7";s:24:"mod2/css/kickstarter.css";s:4:"2013";s:28:"mod2/js/pagetreeFunctions.js";s:4:"91e1";s:27:"mod2/js/tabMenuFunctions.js";s:4:"a692";s:27:"skin/images/background1.png";s:4:"c5de";}',
	'suggests' => array(
	),
);

?>