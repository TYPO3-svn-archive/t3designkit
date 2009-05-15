<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{
		
	t3lib_extMgm::addModule('txt3designkitM1','','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	t3lib_extMgm::addModule('txt3designkitM1','txt3designkitM2','top',t3lib_extMgm::extPath($_EXTKEY).'mod2/');
	t3lib_extMgm::addModule('txt3designkitM1','txt3designkitM3','',t3lib_extMgm::extPath($_EXTKEY).'mod3/');
}
?>