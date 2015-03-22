<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}


$lang = array_merge(
	$lang, array(
		'LOG_SYNCPOSTCOUNTER_CRON_RESYNCED'	=> '<strong>Resynced post counter</strong>',
		'LOG_SYNCPOSTCUNTER_CRON_ERROR'		=> '<strong>Error while resyncing post counter: </strong><br />%s',
		'LOG_SYNCPOSTCUNTER_CONFIG_SETTINGS'=> '<strong>Resync post counter config updated</strong>',

		'ACP_SYNCPOSTCUNTER'						=> 'Resync Post Counter',
		'ACP_SYNCPOSTCUNTER_SETTINGS'				=> 'Resync Post Counter settings',
		'ACP_SYNCPOSTCUNTER_SETTINGS_EXPLAIN' 		=> '',
		'ACP_SYNCPOSTCUNTER_GENERAL'				=> 'General',
		'ACP_SYNCPOSTCUNTER_RUN_INTERVAL'			=> 'Run interval',
		'ACP_SYNCPOSTCUNTER_RUN_INTERVAL_EXPLAIN'	=> 'Interval in minutes between cronjob runs.<br />'.
			'<strong>WARNING</strong>: Cronjob can be pretty expensive, don\'t run too often.<br />'.
			'Default is 1440 minutes = 1 once per day.<br />'.
			'Values lower than 1 disable the whole cronjob.',
	)
);
