<?php

namespace fh3095\syncpostcounter\acp;


class acp_module {
	public $u_action;
	public $page_title;
	public $tpl_name;

	function main($id, $mode)
	{
		global $user, $template, $request;
		global $config, $phpbb_log;

		$user->add_lang('acp/board');

		$is_submit = isset($_POST['submit']);

		$form_key = 'acp_syncpostcounter';
		add_form_key($form_key);

		$display_vars = array(
			'title'	=> 'ACP_SYNCPOSTCUNTER_SETTINGS',
			'vars'	=> array(
				'legend1'				=> 'ACP_SYNCPOSTCUNTER_GENERAL',
				'recountposts_syncpostcounter_run_interval'	=>
					array('lang' => 'ACP_SYNCPOSTCUNTER_RUN_INTERVAL',
						'validate' => 'int',
						'type' => 'number:0:527040',
						'explain' => true,
						'append' => ' ' . $user->lang['MINUTES']),

				'legend4'				=> 'ACP_SUBMIT_CHANGES',
			),
		);

		// Copy relevant configuration entries, either from current config or from submitted config
		$new_config = array();
		foreach($display_vars['vars'] AS $config_name => $null)
		{
			if (strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			if(isset($_REQUEST['config']) && isset($_REQUEST['config'][$config_name]))
			{
				$new_config[$config_name] = utf8_normalize_nfc($_REQUEST['config'][$config_name]);
			}
			else
			{
				$new_config[$config_name] = $config[$config_name];
			}
		}
		
		// check for error
		$error = array();
		validate_config_vars($display_vars['vars'], $new_config, $error);

		if ($is_submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		if (sizeof($error))
		{
			$is_submit = false;
		}

		// Save the new values now
		if($is_submit)
		{
			foreach($new_config AS $config_key => $config_value)
			{
				$config->set($config_key, $config_value);
			}

			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_SYNCPOSTCUNTER_CONFIG_SETTINGS', time());

			$message = $user->lang('CONFIG_UPDATED');
			$message_type = E_USER_NOTICE;

			trigger_error($message . adm_back_link($this->u_action), $message_type);
		}

		// assign variables to the template
		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action,
		));

		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			// Simple strings are already assigned
			if (!is_array($vars))
			{
				continue;
			}


			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($user->lang[$vars['lang'] . '_EXPLAIN']))
			{
				$l_explain =  $user->lang[$vars['lang'] . '_EXPLAIN'];
			}

			$content = build_cfg_template($type, $config_key, $new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,

			));
		}
	}
}
