<?php

!(defined("ENV_NAME")) ? define("ENV_NAME", "env.gnu"):0;
$START_WDIR = getcwd();



mkdir(ENV_NAME);
chdir(ENV_NAME);

mkdir("mode777");
mkdir("mode555", 0555);
touch("mode555", strtotime("1992:06:23 14:12:00"), time());
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



mkdir(str_repeat("this_is_a_long_dir/", 4), 0777, true);

touch(str_repeat("this_is_a_long_dir/", 4)."FILE.txt", strtotime("1992:06:23 14:12:00"));
chmod(str_repeat("this_is_a_long_dir/", 4)."FILE.txt", 0755);
file_put_contents("mode755/A.txt", str_repeat("FILE_", 15));

link(str_repeat("this_is_a_long_dir/", 4)."FILE.txt", "HLink_long");
symlink(str_repeat("this_is_a_long_dir/", 4)."FILE.txt", "SLink_long");

mkdir(str_repeat("this_is_an_extreme_long_directory/", 20), 0777, true);
touch(str_repeat("this_is_an_extreme_long_directory/", 20)."Extrem_Long_File", strtotime("1992:06:23 14:12:00"));


touch("dir/in/dir/CMtime.txt");
file_put_contents("mode755/CMtime.txt", str_repeat("B", 5649));







chdir($START_WDIR);