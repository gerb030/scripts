#!/usr/bin/php
<?php
if (count($argv) < 2) { echo "Usage: ./github_userlist.php [github_username]\n"; exit(0); }
$username = $argv[1];
echo "Enter your github password: ";
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
$url = 'https://api.github.com/orgs/spilgames/members';
$userCredentials = $username.':'.$password;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, $userCredentials);
$results = curl_exec($ch);
curl_close($ch);
// parse all that stuff
$usernames = array();
$list = json_decode($results);
$countList = count($list);
for ($n=0;$n<$countList;$n++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $list[$n]->url);    
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $userCredentials);
    $result = json_decode(curl_exec($ch));
    curl_close($ch);
    if ($result->type == 'User') {
        $usernames[$list[$n]->login] = array(
            'realname' => $result->name, 
            'email' => $result->email,
            'blog' => $result->blog
        );
    } else {
        echo 'This entity is not a user. This is most weird:';
        print_r($result);
        echo "\n";
    }
}
foreach($usernames as $user => $userData) {
    echo 'login:'.$user.'; realname:'.$userData['realname'].'; email:'.$userData['email'].'; blog:'.$userData['blog']."\n";
}
