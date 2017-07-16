<?php
final class Backup_Cronjob extends GWF_MethodCronjob
{
	public function run()
	{
		$module = Module_Backup::instance();
		$last = $module->cfgLastDate();
		$curr = date('Ymd');
		if ($last !== $curr)
		{
			$this->logNotice("Doing backup for $curr");
			$this->doBackup();
			$module->saveConfigVar('backup_lastdate', $curr);
		}
	}
	
	private function doBackup()
	{
		
	}
}
