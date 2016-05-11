<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
!(defined("ENV_NAME")) ? define("ENV_NAME", "env.posix") : 0;
$START_WDIR = getcwd();



mkdir(ENV_NAME);
chdir(ENV_NAME);

mkdir("mode777");
mkdir("mode555", 0555);
touch("mode555", strtotime("1992:06:23 14:12:00"));
mkdir("dir/in/dir", 0777, true);
mkdir("mode755", 0755);


touch("mode755/A.txt");
file_put_contents("mode755/A.txt", str_repeat("A", 512));
touch("mode755/AB.txt");
file_put_contents("mode755/AB.txt", str_repeat("AB", 512));
touch("mode755/B.txt");
file_put_contents("mode755/B.txt", str_repeat("B", 5649));

link("mode755/B.txt", "mode755/HLink.B");
symlink("B.txt", "mode755/SLink.B");


$path = str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/" . str_repeat("D", 50) . "/" . str_repeat("E", 45);
mkdir($path, 0755, true);
file_put_contents($path . "/FFF", str_repeat("CONTENT_", 10));
touch($path . "/FFF", strtotime("1992:06:23 14:12:00"));










chdir($START_WDIR);
