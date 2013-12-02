<?php
require_once('functions.php');
require_once('includes/xtemplate.class.php');


// Define Vars
$navigation = '';

// Ensure the user isn't trying to get at anything eles.
if (isset($_GET['page'])) {
  if (strstr($_GET['page'], '.') || strstr($_GET['page'], '/')) {
     die('You can\'t use dots or slashes in page names.');
  }
}

// If no page then home.
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'Home';
}

// Fetch pages from datastore where nav is true
$dbh = new PDO("sqlite:data/datastore.sqlite");
$IDq = $dbh->query("SELECT * FROM pages WHERE nav = '1'");
$rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);

// Loop through and create nav option
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

// Spit it out.
$xtpl->parse('main');
$xtpl->out('main');   
?>
