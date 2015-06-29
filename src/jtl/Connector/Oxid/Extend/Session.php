<?php
class Session extends session_parent
{
	public function start()
    {
		if (isset($_REQUEST['jtlrpc'])) {
			return;
		} else {
			parent::start();
		}
    }
}
