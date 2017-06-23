<?php

	$FILE_ZONE_A = "/etc/nginx/conf.d/aaaaaaaaaaaa00000000.conf";

	if(!is_file($FILE_ZONE_A))
	{
		file_put_contents($FILE_ZONE_A, "fastcgi_cache_path  /data/cache  levels=1:2    keys_zone=STATIC:2m    inactive=24h  max_size=1G;");
	}

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


	echo $USUARIO . PHP_EOL;



	echo PHP_EOL;

 ?>