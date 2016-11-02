<?php
/**
 * Created by PhpStorm.
 * User: Тарас
 * Date: 02.11.2016
 *
 * Object oriented version of contact form with one ContactForm class
 *
 */

class ContactForm
{
    private $commentsFile = '';
    private $formDataMsg = '* - required fields';
    private $uname, $umail, $umsg = '';
    private $formErrors = [];

    public function __construct()
    {
        $this->setCommentsFileName();
        $this->processPostData();
    }

    /**
     * @return mixed
     */
    public function getUname()
    {
        return $this->uname;
    }

    /**
     * @return mixed
     */
    public function getUmail()
    {
        return $this->umail;
    }

    /**
     * @return string
     */
    public function getUmsg()
    {
        return $this->umsg;
    }

    /**
     * @return string
     */
    public function getFormDataMsg()
    {
        return $this->formDataMsg;
    }

    public function displayComments()
    {
        $userComments = '';
        $aComm = $this->getComments();
        if (!empty($aComm)) {
            foreach ($aComm as $comm) {
                if (!empty($comm)) {
                    $userComments .= '<dl>' .
                        '<dl>Username: </dl><dd>' . $comm['uname'] . '</dd>' .
                        '<dl>Email: </dl><dd>' . $comm['umail'] . '</dd>' .
                        '<dl>Message: </dl><dd>' . $this->filterBadWords($comm['umsg']) . '</dd>' .
                        '<dl>Added on: </dl><dd>' . $comm['datetime'] . '</dd>' .
                        '</dl><br>' . PHP_EOL;
                }
            }
        }
        return $userComments;
    }

    private function processPostData()
    {
        if (!empty($_POST)) {
            $this->formDataMsg .= '<br>' . 'Form data: ' . var_export($_POST, 1) . '<br>';
            if (empty($_POST['uname'])) {
                $this->formErrors[] = 'Username can`t be empty!';
            } else {
                $this->uname = $_POST['uname'];
            }
            if (empty($_POST['umail'])) {
                $this->formErrors[] = 'Email can`t be empty!';
            } else {
                $this->umail = $_POST['umail'];
            }
            if (!filter_var($_POST['umail'], FILTER_VALIDATE_EMAIL)) {
                $this->formErrors[] = 'Email has wrong format!';
            }
            if (empty($_POST['umsg'])) {
                $this->formErrors[] = 'Message can`t be empty!';
            } else {
                $this->umsg = $_POST['umsg'];
            }
            if (!empty($this->formErrors)) {
                $this->formDataMsg .= 'Can`t add your comment because filled form has validation errors: ' . '<br>' . PHP_EOL;
                foreach ($this->formErrors as $err) {
                    $this->formDataMsg .= $err . '<br>' . PHP_EOL;
                }
            } else {
                $this->commentData = $_POST;
                $this->commentData['datetime'] = date('Y-m-d H:i:s');
                $this->addComment($this->commentData);
                header('Location: ' . $_SERVER['PHP_SELF'] . '?commentAdded' . time());
            }
        }
    }

    private function addComment($aComm) {
        $allComments = $this->getComments();
        $allComments[] = $aComm;
        $sAllComm = serialize($allComments);
        file_put_contents($this->commentsFile, $sAllComm);
    }

    private function getComments() {
        $aComm = [];
        if (file_exists($this->commentsFile)) {
            $sComm = file_get_contents($this->commentsFile);
            $aComm = unserialize($sComm);
        }
        return $aComm;
    }

    private function filterBadWords($str) {
        $badWords = ['fuck', 'sex', 'sheet'];
        $goodWords = ['f**k', 's*x', 'sh**t'];
        $str = str_ireplace($badWords, $goodWords, $str);
        return $str;
    }

    private function setCommentsFileName()
    {
        $this->commentsFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'comments.dat';
    }
}

$contactForm = new ContactForm();

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
            <div><label for="uname">Username *: </label><input id="uname" type="text" name="uname" value="<?=$contactForm->getUname()?>"></div>
            <div><label for="umail">Email *: </label><input id="umail" type="email" name="umail" value="<?=$contactForm->getUmail()?>"></div>
            <div><label for="umsg">Message *: </label><textarea id="umsg" name="umsg"></textarea></div>
            <div><input type="submit" value="Add comment"></div>
        </form>
        <div><?=$contactForm->getFormDataMsg()?></div>
        <hr>
        <div>
            <h2>Added comments:</h2>
            <div><?=$contactForm->displayComments()?></div>
        </div>
    </div>
</body>
</html>
