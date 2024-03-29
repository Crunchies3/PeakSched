<?php
require_once 'config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/employee_client.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/employee_account.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/notifMessages.php";

$notification = new NotifMessages();
$notification->setConn($conn);

$employeeAcc = unserialize($_SESSION["employeeUser"]);
$supervisorId = $employeeAcc->getId();

$employeeClient = new Employee_Client();
$employeeClient->setConn($conn);

$appointmentId = "";

if (isset($_GET["appointmentId"])) {
    $appointmentId = $_GET["appointmentId"];

$employeeClient->fetchUnassignedAppointmentWorkers($appointmentId,$supervisorId);
$result = $employeeClient->getUnassignedWorkers();
}
if(isset($_POST['AssignWorkerModal'])){
    $workerId = $_POST["workerId"];
    $appointmentId = $_POST["appointmentId"];

    $employeeClient->displayCurrentAppointmentAssigned($appointmentId);
    $notification->setsenderUserType($supervisorId);
    $notification->setreceiverUserType($workerId);

    $supervisorId = $employeeAcc->getId();
    $appointmentName = $employeeClient->getServicetitle();
    $message = $notification->supToWorkerAssign($appointmentName);
    $receiver = $notification->getreceiverUserType();
    $unread = true;
    date_default_timezone_set("America/Vancouver");
    $date = date("Y-m-d H:i:s");


    //para marefresh ang table
    $employeeClient->addWorkerToAppointment($appointmentId,$workerId);
    $notification->insertNotif($receiver,$unread,$date,$message);

    $employeeClient->fetchUnassignedAppointmentWorkers($appointmentId,$supervisorId);
    $result = $employeeClient->getUnassignedWorkers();

}
