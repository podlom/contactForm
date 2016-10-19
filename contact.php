<?php
/**
 * Created by PhpStorm.
 * User: Тарас
 * Date: 19.10.2016
 */

    $commentsFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'comments.dat';
    $formDataMsg = '* - required fields';
    $uname = $umail = $umsg = '';
    $formErrors = [];
    if (!empty($_POST)) {
        $formDataMsg .= '<br>' . 'Form data: ' . var_export($_POST, 1) . '<br>';
        if (empty($_POST['uname'])) {
            $formErrors[] = 'Username can`t be empty!';
        } else {
            $uname = $_POST['uname'];
        }
        if (empty($_POST['umail'])) {
            $formErrors[] = 'Email can`t be empty!';
        } else {
            $umail = $_POST['umail'];
        }
        if (!filter_var($_POST['umail'], FILTER_VALIDATE_EMAIL)) {
            $formErrors[] = 'Email has wrong format!';
        }
        if (empty($_POST['umsg'])) {
            $formErrors[] = 'Message can`t be empty!';
        } else {
            $umsg = $_POST['umsg'];
        }
        if (!empty($formErrors)) {
            $formDataMsg .= 'Can`t add your comment because filled form has validation errors: ' . '<br>' . PHP_EOL;
            foreach ($formErrors as $err) {
                $formDataMsg .= $err . '<br>' . PHP_EOL;
            }
        } else {
            $commentData = $_POST;
            $commentData['datetime'] = date('Y-m-d H:i:s');
            addComment($commentData);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?commentAdded' . time());
        }
    }

    function addComment($aComm) {
        global $commentsFile;
        $allComments = getComments();
        $allComments[] = $aComm;
        $sAllComm = serialize($allComments);
        file_put_contents($commentsFile, $sAllComm);
    }

    function getComments() {
        global $commentsFile;
        $aComm = [];
        if (file_exists($commentsFile)) {
            $sComm = file_get_contents($commentsFile);
            $aComm = unserialize($sComm);
        }
        return $aComm;
    }

    function filterBadWords($str) {
        $badWords = ['fuck', 'sex', 'sheet'];
        $goodWords = ['f**k', 's*x', 'sh**t'];
        $str = str_ireplace($badWords, $goodWords, $str);
        return $str;
    }

    $userComments = '';
    $aComm = getComments();
    if (!empty($aComm)) {
        foreach ($aComm as $comm) {
            if (!empty($comm)) {
                $userComments .= '<dl>' .
                    '<dl>Username: </dl><dd>' . $comm['uname'] . '</dd>' .
                    '<dl>Email: </dl><dd>' . $comm['umail'] . '</dd>' .
                    '<dl>Message: </dl><dd>' . filterBadWords($comm['umsg']) . '</dd>' .
                    '<dl>Added on: </dl><dd>' . $comm['datetime'] . '</dd>' .
                    '</dl><br>' . PHP_EOL;
            }
        }
    }

?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Contact</title>
    <meta name="description" content="The HTML5 Herald">
    <meta name="author" content="SitePoint">
</head>

<body>
    <div>
        <h1>Contact</h1>
        <form action="" method="post">
            <div><label for="uname">Username *: </label><input id="uname" type="text" name="uname" value="<?=$uname?>"></div>
            <div><label for="umail">Email *: </label><input id="umail" type="email" name="umail" value="<?=$umail?>"></div>
            <div><label for="umsg">Message *: </label><textarea id="umsg" name="umsg"><?=$umsg?></textarea></div>
            <div><input type="submit" value="Add comment"></div>
        </form>
        <div><?=$formDataMsg?></div>
        <hr>
        <div>
            <h2>Added comments:</h2>
            <div><?=$userComments?></div>
        </div>
    </div>
</body>
</html>
