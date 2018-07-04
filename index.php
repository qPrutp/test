<?php
const STATE_TITLE = 'Cостояние';
const RCOMENDATION_TITLE = 'Рекомендации';
require_once ('./loader/Loader.php');
Loader::load('app.php');
Loader::load('url/iUrl.php');
Loader::load('url/Url.php');
Loader::load('robot/aRobot.php');
Loader::load('robot/aRobotForUrl.php');
Loader::load('robot/RobotForUrl.php');
Loader::load('robot/iRobot.php');
Loader::load('robot/Robot.php');
Loader::load('robot/Save.php');
Loader::load('Classes/PHPExcel.php');

$result=[];
if(array_key_exists('url', $_POST)){
    $robot = new \App\Robot\Robot($_POST['url']);
    $result = $robot->check()->getResults();
    ksort($result);
    $phpexcel = new PHPExcel();
    new Save($result, $phpexcel);
}

require_once './layouts/header.php';
require_once './layouts/test.php';
require_once './layouts/footer.php';
?>