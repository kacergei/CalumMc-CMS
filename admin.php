<?php
require('functions.php');

// Create vars.
$list = '';
$body = '';

if(!isset($_GET['q'])){
	$_GET['q'] = 'home';
}
// Process login info.
if (isset($_POST['user']) && isset($_POST['pass'])) {
  if ($_POST['user'] == get_data($dbh, "admin_user_name") && $_POST['pass'] == get_data($dbh, "admin_user_pass")) {
    $_SESSION['user'] = get_data($dbh, "admin_user_name");
  } else {
    $message = 'Wrong Username/Password';
    $alerttype = 'alert-error';
  }
}
// If logged in.
if (isset($_SESSION['user'])) {
  if ($_GET['q'] == 'template' && isset($_POST['template'])) {
    $template = 'data/default.xtpl';
    $fh = fopen($template, 'w') or die("Error writing to template, you need to chmod 777.");
    $stringData = stripslashes($_POST['template']);
    fwrite($fh, $stringData);
    fclose($fh);
    $message = 'Template file has been updated';
    $alerttype = 'alert-success';
  }

  // Edit CSS
  if ($_GET['q'] == 'style' && isset($_POST['style'])) {
    $style = 'assets/style.css';
    $fh = fopen($style, 'w') or die("Error writing to style, you need to chmod 777.");
    $stringData = stripslashes($_POST['style']);
    fwrite($fh, $stringData);
    fclose($fh);
    $message = 'Style file has been updated';
    $alerttype = 'alert-success';
  }

    // Signing out
  if ($_GET['q'] == 'signout') {
    session_destroy();
    header('Location: admin.php');
  }
    // Create new page?
  if (isset($_POST['newpage'])) {
    $title = $_POST['newpage'];
        // Create navigation item for page if set.
    if ($_POST['navigation']=='true') {
      $nav = 1;
    } else {
      $nav = 0;
    }
    $dbh = new PDO("sqlite:data/datastore.sqlite");
    $qry = $dbh->prepare('INSERT INTO pages (title,text,nav) VALUES (?, ?, ?)');
    $qry->execute(array($title, '<h1>Sorry!</h1><p>No content has been added to this page yet.  Please check back later.</p>', $nav));
    $message = 'Page has been created';
    $alerttype = 'alert-success';
  }
    // Delete a page?
  if (isset($_GET['delete'])) {
    $dbh = new PDO("sqlite:data/datastore.sqlite");
    $qry = $dbh->prepare('DELETE FROM pages WHERE title = ?');
    $qry->execute(array($_GET['delete']));
    $message = 'Page has been deleted';
    $alerttype = 'alert-success';
  }
    // List pages?
  if ($_GET['q'] == 'pages') {
    $dbh = new PDO("sqlite:data/datastore.sqlite");
    $IDq = $dbh->query("SELECT * FROM pages");
    $rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);
    $list.= '<table class="table table-hover" style="width:100%;"><tr><td><b>Title</b></td> <td><b>Edit</b></td> <td><b>Delete</b></td><td><b>Navigation Item</b></td></tr>';
    foreach ($rowarray as $row) {
      if ($row['nav'] == '1') {
        $nav = 'Yes';
      } else {
        $nav = 'No';
      }
      $list.= '<tr><td>' . $row['title'] . '</td> <td><a class="btn btn-primary btn-mini" href="admin.php?q=edit&p=' . $row['title'] . '"><i class="icon-pencil"></i> Edit Page</a></td> <td><a class="btn btn-danger btn-mini" href="admin.php?q=pages&delete=' . $row['title'] . '"><i class="icon-trash"></i> Delete Page</a></td><td>' . $nav . '</td></tr>';
    }
    $list.= '</table>';
    $body = $list . '<br /><form action="admin.php?q=pages" class="well form-inline" method="post" /><p><i class="icon-plus-sign"></i> New Page: <input type="text" placeholder="Page Title" name="newpage" /> <select name="navigation"><option value="true">Show on navigation bar</option><option value="false">Do not show on navigation bar</option></select> <input type="submit" class="btn btn-primary" value="Go"></p></form>';
  }
    // Submit edited page.
  if (isset($_POST['edit'])) {
    $stringData = stripslashes($_POST['edit']);
    $dbh = new PDO("sqlite:data/datastore.sqlite");
    $qry = $dbh->prepare("UPDATE pages SET title=?, text=? WHERE title = ?");
    $qry->execute(array($_GET['p'], $stringData, $_GET['p']));
    $message = 'Page has been updated';
    $alerttype = 'alert-success';
  }
    // Edit a page.
  if ($_GET['q'] == 'edit') {
    $body = '<form action="admin.php?q=edit&p=' . $_GET["p"] . '" class="well" method="post">
    <textarea style="width:100%;" rows="20" cols="100" name="edit">' . get_page_content($dbh,$_GET["p"]) . '</textarea>
    <script type="text/javascript">
     CKEDITOR.replace("edit",{
       extraPlugins: "magicline",
       allowedContent: true
     });
</script>
<input type="submit" class="btn btn-primary" value="Save" />
</form>';
}
    // Edit the template.
if ($_GET['q'] == 'template') {
  $body = '<form action="admin.php?q=template" class="well" method="post" class="well">
  <h2>Edit Raw Template:</h2>
  <textarea style="width:100%;" rows="20" cols="100" name="template">' . htmlentities(file_get_contents("data/default.xtpl")) . '</textarea>
  <input type="submit" class="btn btn-primary" value="Save" />
</form>';
}

if ($_GET['q'] == 'style') {
  $body = '<form action="admin.php?q=style" method="post" class="well">
  <h2>Edit Raw Stylesheet:</h2>
  <textarea style="width:100%;" rows="20" cols="100" name="style">' . htmlentities(file_get_contents("assets/style.css")) . '</textarea>
  <input type="submit" class="btn btn-primary" value="Save" />
</form>';
}

    // Edit Blocks
if ($_GET['q'] == 'blocks') {

  $body = '<form action="admin.php?q=blocks" method="post"><input type="hidden" name="blocksubmit" value="true">';
  $dbh = new PDO("sqlite:data/datastore.sqlite");
  $IDq = $dbh->query("SELECT * FROM blocks");
  $rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);
  foreach ($rowarray as $box) {
    $body .= '
    <div class="well" style="width:100%; float:left; margin:5px;">
      <h2>'.$box['block'].'</h2>
      <input style="width:100%" id="'.$box['block'].'title" placeholder="title" type="text" value="'.$box['title'].'"><br>
      <input style="width:100%" id="'.$box['block'].'url" placeholder="link URL" type="text" value="'.$box['link'].'"><br>
      <textarea style="width:100%; height:200px;" id="'.$box['block'].'text" name="'.$box['block'].'text">'.$box['text'].'</textarea>

    </div>
    <script type="text/javascript">
     CKEDITOR.replace("'.$box['block'].'text",{
       customConfig: "minimal_config.js"
     });
</script>
';
}
$body .= '<input type="submit" class="btn btn-primary" value="Save All"></form>';
}
    // Submit blocks
if (isset($_POST['blocksubmit'])) {
  $dbh = new PDO("sqlite:data/datastore.sqlite");
  $IDq = $dbh->query("SELECT * FROM blocks");
  $rowarray = $IDq->fetchall(PDO::FETCH_ASSOC);
  foreach ($rowarray as $box) {
    $qry = $dbh->prepare("UPDATE blocks SET title=?, link=? text=? WHERE block = ?");
    $block = $box['block'];
    $text = stripslashes($_POST[$block.'text']);
    $qry->execute(array($_POST[$block.'title'], $_POST[$block.'url'],$text,$box['block']));
    $message = 'Blocks have been updated';
    $alerttype = 'alert-success';
  }
}    
//End if logged in.
}
?>



<!DOCTYPE html>
<html>
<head>
  <title>
    <?php echo get_data($dbh,"conf_site_name"); ?> Admin Panel
  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript" src="./admin/assets/admin.js"></script>
  <script src="includes/ckeditor/ckeditor.js"></script>
</head>
     <body>
    <?php if (isset($_SESSION['user'])) { ?>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=
          ".nav-collapse"></a> <a class="brand" href="#"><img src="includes/admin_logo.png" alt="McAlinden" width="200"></a>
          <div class="nav-collapse">
            <ul class="nav">
              <li>
                <a href="admin.php?q=pages"><i class="icon-pencil"></i> Pages</a>
              </li>
              <li>
                <a href="admin.php?q=template"><i class="icon-th-list"></i> Template</a>
              </li>
              <li>
                <a href="admin.php?q=style"><i class="icon-file"></i> Style</a>
              </li>
              <li>
                <a href="admin.php?q=blocks"><i class="icon-th-large"></i> Blocks</a>
              </li>
              <li>
                <a href="admin.php?q=files"><i class="icon-folder-open"></i> Files</a>
              </li>
              <li>
                <a href="index.php"><i class="icon-home"></i> Visit Website</a>
              </li>
              <li>
                <a href="admin.php?q=signout"><i class="icon-off"></i> Sign Out</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <?php
  } ?>
  <div class="container">
    <div style="width:100%; height:42px;">
      <div id="popup" class="alert <?php echo $alerttype; ?>">
       <?php
// Any popups?
       echo $message; ?>
     </div>
   </div>
   <?php
// If not logged in, show login box.
   if (!isset($_SESSION['user'])) { ?>
   <p class="login-logo"><img src="includes/admin_logo.png" alt="McAlinden" width="200"></p>
   <form class="well" action="admin.php" method="post">
    <label>Admin Username</label>
    <input type="text" name="user" placeholder="Username">
    <span class="help-block">Please type your admin username.</span>
    <label>Admin Password</label>
    <input type="password" name="pass" placeholder="Password"> 
    <span class="help-block">Please type your admin password.</span>
    <input type="submit" class="btn btn-primary" value="Sign In">
  </form>
  <?php
    // If logged in, display the body that has been produced.

} else {
  echo $body;
  if(isset($xtpl)){
    echo '<form class="form-horizontal">';
    $xtpl->out('main');
    echo '</form>';
  }
} ?>
</div>
</body>
</html>
