<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/DBConfig.php');

function get_list_to_remove($id, $dbh)
{
    $result = array($id);
    $list = $dbh->query('select id from comments where answer_to = ' . $id)->fetchAll();
    foreach ($list as $item) {
        //$result[] = $item['id'];
        $result = array_merge($result, get_list_to_remove($item['id'], $dbh));
    }
    return $result;
}

switch ($_POST['act']) {
    case 'add': {
        $stmt = $pdo->prepare('INSERT INTO comments (nick, com_text, answer_to, uid) VALUES (:nick, :text, :answer_to, :uid)');
        $stmt->bindParam(':nick', $_POST['name']);
        $stmt->bindParam(':text', nl2br($_POST['text']));
        $stmt->bindParam(':answer_to', $_POST['answer_to']);
        $stmt->bindParam(':uid', $_COOKIE['uid']);
        $stmt->execute();
        $comment_html = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/templates/comment.html');
        $comment_html = str_replace('{name}', $_POST['name'], $comment_html);
        $comment_html = str_replace('{message}', nl2br($_POST['text']), $comment_html);
        $comment_html = str_replace('{id}', $pdo->lastInsertId(), $comment_html);
        $comment_html = str_replace('{hide}', '', $comment_html);
        if ($_POST['answer_to']) {
            $comment_html = str_replace('{level}', $_POST['level'] + 1, $comment_html);
        } else {
            $comment_html = str_replace('{level}', 0, $comment_html);
        }
        echo $comment_html;
        break;
    }
    case 'remove': {
        $list_to_remove = get_list_to_remove($_POST['id'], $pdo);
        $stmt = $pdo->prepare('delete from comments WHERE id = ?');
        foreach ($list_to_remove as $id)
        {
            $stmt->execute([$id]);
        }
        echo json_encode($list_to_remove);
        break;
    }
}