<?php

	$FILE_ZONE_A = "/etc/nginx/conf.d/aaaaaaaaaaaa00000000.conf";

	function randomstr($qtde = 10)
	{
		$characters = '0123456789abcdefghi#jklmnopqrstu@v#wxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.#@';
		$randstring = '';
		for ($i = 0; $i < $qtde; $i++) {
			$randstring .= $characters{rand(0, (strlen($characters)-1))};
		}
		return $randstring;
	}

	if(!is_file($FILE_ZONE_A))
	{
		file_put_contents($FILE_ZONE_A, "fastcgi_cache_path  /data/cache  levels=1:2    keys_zone=STATIC:2m    inactive=24h  max_size=1G;");
	}

	function validaDominio($domain_name)
	{
	    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
	            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
	            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
	}

	if($argv[1] == "all")
	{
		foreach(glob(__DIR__ . '/*/') as $dir)
		{
			if(is_dir($dir))
			{
				$BASENAME = basename($dir);
				if($BASENAME == strtolower($BASENAME) && strpos($BASENAME, '.') && validaDominio($BASENAME) && !filter_var($BASENAME, FILTER_VALIDATE_IP))
				{
					echo "php isolaSite.php " . $BASENAME . '; ' . PHP_EOL;
				}
			}
		}
	}

	#exit;

	$DOMINIO = preg_replace("/[^0-9a-z\.\-]/", "", mb_strtolower(trim($argv[1])));

	function usuariza($x)
    {
            $x = preg_replace("/[^0-9a-z]/", "_", mb_strtolower(trim($x)));
            return substr($x,0,8) . (abs(crc32($x)) % 999);
    }


	function geraErro($x)
	{
		echo PHP_EOL;
		echo "--------------------------------------------" . PHP_EOL;
		echo $x . PHP_EOL;
		echo "--------------------------------------------" . PHP_EOL;
		echo PHP_EOL;
		exit;
	}

	echo PHP_EOL;
	echo $DOMINIO . PHP_EOL;

	$DIR = '/data/' . $DOMINIO . '/';

	if(!is_dir($DIR) || strlen($DOMINIO) < 4) geraErro("DIRETORIO " . $DIR . " #404");

	$OWNER = posix_getpwuid(fileowner($DIR));

	if($OWNER['name'] != 'data') geraErro('OWNER != data');

	$USUARIO = usuariza($DOMINIO);

	$SHELL = "useradd -d " . $DIR . " " . $USUARIO;
	echo $SHELL . PHP_EOL;
	system($SHELL);

	$NGINXCONF = file_get_contents(__DIR__ . '/nginx-model.conf');
	$NGINXCONF = str_replace('{DOMINIO}', $DOMINIO, $NGINXCONF);
	$NGINXCONF = str_replace('{USUARIO}', $USUARIO, $NGINXCONF);
	file_put_contents('/etc/nginx/conf.d/' . $DOMINIO . '.conf', $NGINXCONF);



	$PHPFPMCONF = file_get_contents(__DIR__ . '/php-fpm.d-model.conf');
	$PHPFPMCONF = str_replace('{DOMINIO}', $DOMINIO, $PHPFPMCONF);
	$PHPFPMCONF = str_replace('{USUARIO}', $USUARIO, $PHPFPMCONF);
	file_put_contents('/etc/php-fpm.d/' . $USUARIO . '.conf', $PHPFPMCONF);

	copy(__DIR__ . '/__prepend.inc.php', $DIR . '__prepend.inc.php');

	$SHELL = "chown -R " . $USUARIO . ":" . $USUARIO . " " . $DIR;
	echo $SHELL . PHP_EOL;
	system($SHELL);

	system("service nginx restart");
	system("service php-fpm restart");


	#echo $USUARIO . PHP_EOL;

	$password = randomstr(20);
	echo shell_exec('echo -e "'.$password.'\n'.$password.'" | passwd ' . $USUARIO);

	echo PHP_EOL . "---------------------------------" . PHP_EOL;
	echo "USER: " . $USUARIO . PHP_EOL;
	echo "PASS: " . $password . PHP_EOL;
	echo PHP_EOL . "---------------------------------" . PHP_EOL;



	echo PHP_EOL;

 ?>
