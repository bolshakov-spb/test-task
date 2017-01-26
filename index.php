<?php
function insert_child_comment($comment, $list)
{
    for ($i = 0; $i < count($list); $i++) {
        if ($list[$i]['id'] == $comment['answer_to']) {
            array_splice($list, $i + 1, 0, array($comment));
            break;
        }
    }
    return $list;
}

function calc_level($comment, $list, $start_level)
{
    $level = $start_level;
    //$id = $comment['id'];
    for ($i = 0; $i < count($list); $i++) {
        if ($list[$i]['id'] == $comment['answer_to']) {
            ++$level;
            $level = calc_level($list[$i], $list, $level);
            break;
        }
    }
    return $level;
}

function generateUID($length = 32)
{
    $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
    $numChars = strlen($chars);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, $numChars) - 1, 1);
    }
    return $string;
}

if (!$_COOKIE['uid']) {
    SetCookie('uid', generateUID(), time() + 60 * 60 * 24 * 365);
}
$uid = $_COOKIE['uid'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/DBConfig.php');

$tpl_dir = $_SERVER['DOCUMENT_ROOT'] . '/templates/';
$tpl_main = file_get_contents($tpl_dir . 'main.html');
$tpl_comment = file_get_contents($tpl_dir . 'comment.html');

$all_comments = $pdo->query('select * from comments where answer_to = 0 group by id')->fetchAll();
$child_comments = $pdo->query('select * from comments where answer_to <> 0 group by id')->fetchAll();
$comments_html = '';

foreach ($child_comments as $comment) {
    $all_comments = insert_child_comment($comment, $all_comments);
}

foreach ($all_comments as $comment) {
    $comment['level'] = calc_level($comment, $all_comments, 0);
    $temp = str_replace('{name}', $comment['nick'], $tpl_comment);
    $temp = str_replace('{message}', $comment['com_text'], $temp);
    $temp = str_replace('{level}', $comment['level'], $temp);
    $temp = str_replace('{id}', $comment['id'], $temp);
    ($comment['uid']==$_COOKIE['uid']) ? $temp = str_replace('{hide}', '', $temp) : $temp = str_replace('{hide}', 'hidden', $temp);
    $comments_html .= $temp;
}

echo str_replace('{comments}', $comments_html, $tpl_main);