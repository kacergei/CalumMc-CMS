<?php
// User defined constants
define(DATABASE_LOCATION, "data/datastore.sqlite");

// Start Session
session_start();

// Create PDO object
$dbh = new PDO("sqlite:" + DATABASE_LOCATION);

function get_data($dbh,$data) {
    $IDq = $dbh->query("SELECT value FROM settings WHERE option = " . $dbh->quote($data));
    $IDq->setFetchMode(PDO::FETCH_ASSOC);
    $IDf = $IDq->fetch();
    $output = $IDf['value'];
    return $output;
}
function get_page_content($dbh,$data) {
    $IDq = $dbh->query("SELECT * FROM pages WHERE title = " . $dbh->quote($data));
    $IDq->setFetchMode(PDO::FETCH_ASSOC);
    $IDf = $IDq->fetch();
    $output = $IDf['text'];
    return $output;
}

function get_blocks($dbh) {
    $IDq = $dbh->query("SELECT * FROM blocks");
    $rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);
    return $rowarray;
}

?>
