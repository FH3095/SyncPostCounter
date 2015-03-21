<?php

namespace fh3095\syncpostcounter\migrations;


class v1_0_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_SYNCPOSTCUNTER')),
			array('module.add', array(
				'acp', 'ACP_SYNCPOSTCUNTER', array(
					'module_basename'	=> '\fh3095\syncpostcounter\acp\acp_module',
					'modes'				=> array('settings'),
				),
			)),
			array('config.add', array('recountposts_syncpostcounter_run_interval', 1440)),
		);
	}
}
