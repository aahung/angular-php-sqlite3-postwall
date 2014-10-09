<?php
session_start();
$t_user = $_SESSION['app_user_name'];
class MyDB extends SQLite3
{
  function __construct()
  {
    $dbfile = 'posts.db';
    $this->open($dbfile, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
  }
}
$db = new MyDB();
$results = $db->query("SELECT name FROM SQLITE_MASTER WHERE type = 'table'");
if (!$results->fetchArray()) {
    // create table
    $results = $db->exec('CREATE TABLE posts
                (user TEXT, content TEXT, image TEXT,
                    color TEXT, time INTEGER, removed INTEGER)');
    $results = $db->exec('CREATE TABLE likes
                (post_id INTEGER, user TEXT)');
    $results = $db->exec('CREATE TABLE comments
                (post_id INTEGER, user TEXT, content TEXT, time INTEGER)');
}
// main


if (isset($_GET['q']) && $_GET['q'] == '1') {
    echo_all_posts($db);
} elseif (isset($_GET['w']) && $_GET['w'] == '1') {
    echo $t_user;
} elseif (isset($_POST['q']) && $t_user != '') {
    if ($_POST['q'] == 'a') {
        // insert
        $content_raw = $_POST['raw'];
        $content = $content_raw;
        $img = '';
        $color = $_POST['color'];
        $img_pos = strpos($content_raw, 'img:');
        if ($img_pos !== false) {
            $content = substr($content_raw, 0, $img_pos);
            $img = substr($content_raw, $img_pos + 4);
        }
        $stmt = $db->prepare('INSERT INTO posts VALUES (:user, :content,
            :image, :color, :time, 0)');
        $stmt->bindValue(':user', $t_user, SQLITE3_TEXT);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->bindValue(':image', $img, SQLITE3_TEXT);
        $stmt->bindValue(':color', $color, SQLITE3_TEXT);
        $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
        $result = $stmt->execute();
        echo_all_posts($db);
    } elseif ($_POST['q'] == 'd') {
        // delete
        $id = $_POST['id'];
        $stmt = $db->prepare('UPDATE posts SET removed = 1 
            WHERE ROWID = :id and user = :user');
        $stmt->bindValue(':user', $t_user, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        echo_all_posts($db);
    } elseif ($_POST['q'] == 'ca') {
        // insert
        $content = $_POST['c'];
        $id = $_POST['id'];
        $stmt = $db->prepare('INSERT INTO comments VALUES (:id, :user,
            :content, :time)');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':user', $t_user, SQLITE3_TEXT);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
        $result = $stmt->execute();
        echo_all_posts($db);
    } 
}

function echo_all_posts($db) {
    date_default_timezone_set('Asia/Hong_Kong');
    $results = $db->query('SELECT ROWID, * FROM posts 
        WHERE removed = 0 ORDER BY ROWID DESC');
    $posts = array();
    while ($row = $results->fetchArray()) {
        $row['time'] = date("Y-m-d H:i:s", $row['time']);
        foreach ($row as $key => $value) {
            if (is_numeric($key)) {
                unset($row[$key]);
            }
        }
        array_push($posts, $row);
    }
    for ($i = 0; $i < count($posts); ++$i) {
        $results = $db->query('SELECT ROWID, * FROM comments 
            WHERE post_id = ' . $posts[$i]['rowid'] . ' ORDER BY ROWID DESC');
        $comments = array();
        while ($row = $results->fetchArray()) {
            foreach ($row as $key => $value) {
                if (is_numeric($key)) {
                    unset($row[$key]);
                }
            }
            array_push($comments, $row);
        }
        $posts[$i]['comments'] = $comments;
    }
    header("Content-Type: application/json");
    echo json_encode($posts);
}
?>