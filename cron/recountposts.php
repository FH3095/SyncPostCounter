<?php

namespace fh3095\syncpostcounter\cron;

if(!defined('IN_PHPBB'))
{
	exit;
}

class recountposts extends \phpbb\cron\task\base {

	private $config;
	private $auth;
	private $db;
	private $log;

	public function __construct(\phpbb\config\config $config, \phpbb\auth\auth $auth,
								\phpbb\db\driver\driver_interface $db, \phpbb\log\log $log)
	{
		$this->config   = $config;
		$this->auth		= $auth;
		$this->db       = $db;
		$this->log      = $log;
	}

	public function run()
	{
		$log_uid = $this->user->data['user_id'];
		if (!$uuid)
		{
			$log_uid = ANONYMOUS; // fall back to anonymous
		}
		$log_ip = $this->user->ip;
		if (!$log_ip)
		{
			$log_ip = '127.0.0.1';
		}

		try
		{
			$this->update_countable_posts();
			$this->update_user_post_count();

			// Special name, so that cron status extension can read that time
			$this->config->set('recountposts_last_gc', time(), false);
		}
		catch(\Exception $e)
		{
			$this->log->add('admin', $log_uid, $log_ip, 'LOG_SYNCPOSTCUNTER_CRON_ERROR', time(), array($e->__toString()));
		}


		$this->log->add('admin', $log_uid, $log_ip, 'LOG_SYNCPOSTCOUNTER_CRON_RESYNCED', time());
	}

	private function update_countable_posts()
	{
		$postcountRights = $this->auth->acl_get_list(false, 'f_postcount', false);


		$this->db->sql_transaction('begin');
		$sql = 'UPDATE ' . POSTS_TABLE . ' SET post_postcount = 0';
		$this->db->sql_query($sql);

		foreach($postcountRights AS $forum_id => $rights)
		{
			foreach($rights AS $right => $users)
			{
				$sql = 'UPDATE ' . POSTS_TABLE . ' SET post_postcount = 1 ' .
					' WHERE forum_id = ' . (int) $forum_id . ' AND ' .
						$this->db->sql_in_set('poster_id',$users);
				$this->db->sql_query($sql);
			}
		}
		$this->db->sql_transaction('commit');
	}

	private function update_user_post_count()
	{
		$this->db->sql_transaction('begin');
		$sql = 'UPDATE ' . USERS_TABLE . ' SET user_posts = 0';
		$this->db->sql_query($sql);

		$sql = 'SELECT COUNT(post_id) AS num_posts, poster_id ' .
			'FROM ' . POSTS_TABLE . ' ' .
			'WHERE post_postcount = 1 AND post_visibility = ' . ITEM_APPROVED . ' ' .
			'GROUP BY poster_id';
		$result = $this->db->sql_query($sql);

		if($row = $this->db->sql_fetchrow($result))
		{
			do
			{
				$sql = 'UPDATE ' . USERS_TABLE . ' SET user_posts = ' . (int) $row['num_posts'] . ' ' .
					'WHERE user_id = ' . (int) $row['poster_id'];
				$this->db->sql_query($sql);
			}
			while($row = $this->db->sql_fetchrow($result));
		}
		$this->db->sql_transaction('commit');
	}

	public function is_runnable()
	{
		return ((int)$this->config['recountposts_syncpostcounter_run_interval']) >= 1;
	}

	public function should_run()
	{
		return (int) $this->config['recountposts_last_gc'] <= strtotime('-' . (int) $this->config['recountposts_syncpostcounter_run_interval']  . ' minutes');
	}
}
