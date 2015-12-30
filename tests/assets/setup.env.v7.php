<?php

define("ENV_NAME", "env.v7");
define("START_WDIR", getcwd());



mkdir(ENV_NAME);
chdir(ENV_NAME);

mkdir("mode777");
mkdir("mode644", 0555);
mkdir("dir/in/dir", 0777, true);
mkdir("mode755", 0755);


touch("mode755/A.txt");
file_put_contents("mode755/A.txt", str_repeat("A", 512));
touch("mode755/AB.txt");
file_put_contents("mode755/AB.txt", str_repeat("AB", 512));
touch("mode755/B.txt");
file_put_contents("mode755/B.txt", str_repeat("B", 5649));

link("mode755/B.txt", "mode755/HLink.B");

symlink("mode755/B.txt", "mode755/SLink.B");



mkdir(str_repeat("this_is_a_long_dir/", 4), 0777, true);

touch(str_repeat("this_is_a_long_dir/", 4)."FILE.txt");
file_put_contents("mode755/A.txt", str_repeat("FILE_", 15));

link(str_repeat("this_is_a_long_dir/", 4)."FILE.txt", "HLink_long");
symlink(str_repeat("this_is_a_long_dir/", 4)."FILE.txt", "SLink_long");



touch("dir/in/dir/CMtime.txt");
file_put_contents("mode755/CMtime.txt", str_repeat("B", 5649));







chdir(START_WDIR);