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
	
	private function tempDir()
	{
	    return GWF_PATH . 'temp/backup/';
	}
	
	private function doBackup()
	{
	    GWF_File::removeDir($this->tempDir());
	    GWF_File::createDir($this->tempDir());
	    $this->doMysqlDump();
	    $this->doFilesDump();
	    $this->createArchive();
	    GWF_File::removeDir($this->tempDir());
	}
	
	private function doMysqlDump()
	{
	    $username = GWF_DB_USER;
	    $password = GWF_DB_PASS;
	    $database = GWF_DB_NAME;
	    $sitename = $this->getSiteName();
	    $today = date('Ymd');
	    $path = $this->tempDir() . "$sitename.$today.sql.gz";
	    $command = "mysqldump -u $username -p$password $database | gzip > $path";
	    $output = null; $return_val = null;
	    exec($command, $output, $return_val);
	    if ($return_val !== 0)
	    {
	        $this->logError("Could not create sql backup");
	    }
	}

	private function doFilesDump()
	{
	    $src = GWF_PATH . 'dbimg/files';
	    $sitename = $this->getSiteName();
	    $today = date('Ymd');
	    $path = $this->tempDir() . "$sitename.$today.files.zip";
	    $command = "zip -r9 $path $src";
	    $output = null; $return_val = null;
	    exec($command, $output, $return_val);
	    if ($return_val !== 0)
	    {
	        $this->logError("Could not create files backup");
	    }
	}

	private function createArchive()
	{
	    $src = $this->tempDir();
	    $backupPath = GWF_PATH . "protected/backup/";
	    $sitename = $this->getSiteName();
	    $today = date('Ymd');
	    GWF_File::createDir($backupPath);
	    $path = "$backupPath$sitename.$today.zip";
	    $command = "zip -r0 $path $src";
	    $output = null; $return_val = null;
	    exec($command, $output, $return_val);
	    if ($return_val !== 0)
	    {
	        $this->logError("Could not create archive");
	    }
	    
	}
}	
