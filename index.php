<?php
//Define Vars
$navigation = '';

if (isset($_GET['page'])) {
  if (strstr($_GET['page'], '.') || strstr($_GET['page'], '/')) {
     die('You can\'t use dots or slashes in page names.');
  }
}

include ('functions.php');

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'Home';
}

$dbh = new PDO("sqlite:data/datastore.sqlite");
$IDq = $dbh->query("SELECT * FROM pages WHERE nav = '1'");
$rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);

foreach ($rowarray as $file) {
    if ($page == $file['title']) {
        $active = 'class="active"';
    }else{
        $active = '';
    }
    $navigation.= '<li ' . $active . '><a href="/?page=' . $file['title'] . '">' . $file['title'] . '</a></li>';
    $active = '';
}

$title = get_data("conf_site_name") . ' | ' . $page;
$content = get_page_content($page);
$ipaddress = $_SERVER['REMOTE_ADDR'];

if (isset($_SERVER['HTTP_REFERER'])){
	$referrer = $_SERVER['HTTP_REFERER'];
} else {
	$referrer = 'direct/none';
}
$datetime = time();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$remotehost = getHostByAddr($ipaddress);
$logfile = fopen("data/logging/logfile.txt", "a");
$log = $ipaddress . '|' . $referrer . '|' . $datetime . '|' . $useragent . '|' . $remotehost . '|' . $page . "\n";
fwrite($logfile, $log);
fclose($logfile);

include_once ('includes/xtemplate.class.php');

$xtpl = new XTemplate('data/default.xtpl');

// Assign the data to whatever you call the {randomstuff} things.
$xtpl->assign('title', $title);
$xtpl->assign('content', $content);
$xtpl->assign('navigation', $navigation);

// Get block data and assign it.
$dbh = new PDO("sqlite:data/datastore.sqlite");
$IDq = $dbh->query("SELECT * FROM blocks");
$rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);
foreach ($rowarray as $box) {
  $xtpl->assign($box['block'].'title', $box['title']);
  $xtpl->assign($box['block'].'href', $box['link']);
  $xtpl->assign($box['block'], $box['text']);
}

$xtpl->parse('main');
$xtpl->out('main');   
?>
