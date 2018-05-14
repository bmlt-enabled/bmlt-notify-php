# bmlt-notify-php
Sends an email with all BMLT changes in x amount of days

# install
download the repository and edit the config.php then put on a server and create cron job. the config $homanydays should be set to the same amount of time you set your cron job for.

# example
example cron job calls, this would set to call the script at 6pm on sunday
0 18 * * 0 wget -q -O - https://someserver.org/bmltnotify.php >/dev/null 
0 18 * * 0 curl --silent https://someserver.org/bmltnotify.php >/dev/null