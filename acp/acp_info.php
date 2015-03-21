<?php

namespace fh3095\syncpostcounter\acp;

class acp_info
{
	function module()
	{
		return array(
				'filename'    => 'fh3095\syncpostcounter\acp\acp_module',
				'title'        => 'ACP_SYNCPOSTCUNTER',
				'version'    => '1.0.0',
				'modes'        => array(
						'settings'                => array(
								'title' => 'ACP_SYNCPOSTCUNTER',
								'auth'  => 'acl_a_server',
								'cat'   => array('ACP_CAT_MAINTENANCE')
						),
				),
		);
	}
}
