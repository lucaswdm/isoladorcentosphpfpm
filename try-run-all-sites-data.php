<?php

	$inc = 0;

	foreach(glob('/data/*/') as $dir)
	{
		if(!is_dir($dir)) continue;
		$OWNER = posix_getpwuid(fileowner($dir));
		#print_r($OWNER);
		if(strtolower($dir) != $dir) continue;

		$DOMINIO = basename($dir);

		#echo $DOMINIO . PHP_EOL;

		if($OWNER['name'] == 'data')
		{
			if(is_file($dir . 'wp-settings.php'))
			{
				$SHELL = "php " . __DIR__ . '/isolaSite.php ' . $DOMINIO . ';';
				echo $SHELL . PHP_EOL;
				system($SHELL);
				sleep(2);
				#if(++$inc % 3 == 0) echo PHP_EOL . PHP_EOL;
			}
		}
	}
 ?>
