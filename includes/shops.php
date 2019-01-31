<?php

# FileName="Connection_php_mysql.htm"

# Type="MYSQL"

# HTTP="true"

$hostname_shops = "localhost";

$database_shops = "flea3sc_413xa";

$username_shops = "flea3sc_sik21";

$password_shops = "#@(%kf)@(k20k(@KF)_)9";

$shops = mysql_pconnect($hostname_shops, $username_shops, $password_shops) or trigger_error(mysql_error(),E_USER_ERROR); 

?>