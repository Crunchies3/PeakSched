<?php
require_once 'config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/appointments.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/services.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/customers.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/address.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/employees.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/form_validation.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/peaksched/class/notifMessages.php";

$appointments = new Appointment();
$appointments->setConn($conn);

$serviceObj = new Services();
$serviceObj->setConn($conn);

$customerObj = new Customers();
$customerObj->setConn($conn);

$addressObj = new Address();
$addressObj->setConn($conn);

$employee = new Employees();
$employee->setConn($conn);

$validation = new Validation();

$notification = new NotifMessages();
$notification->setConn($conn);


$supervisorList = $employee->fetchEmployeeArr();

$appointmentId = "";
$supervisorErr = "";

if (isset($_GET["appointmentId"])) {
    $appointmentId = $_GET["appointmentId"];
}

$appointments->getAppointmentDetails($appointmentId);

// initial displayables
$customerId = $appointments->getCustomerId();
$customerObj->displayCurrentCustomer($customerId);
$customerName = $customerObj->getFirstname() . ' ' . $customerObj->getLastname();

$serviceId = $appointments->getServiceId();
$serviceObj->displayCurrentService($serviceId);
$service = $serviceObj->getTitle();

$addressId =  $appointments->getAddressId();
$addressObj->getAddressById($addressId);
$address = $addressObj->getStreet() . '. ' . $addressObj->getCity() . ', ' . $addressObj->getProvince() . '. ' . $addressObj->getCountry() . ', ' . $addressObj->getZip_code();

$date = $appointments->getStart();
$dateOnly = date("Y-m-d", strtotime($date));
$timeOnly = date('h:i A', strtotime($date));

$numOfFloors = $appointments->getNumOfFloors();
$numOfBeds = $appointments->getNumOfBeds();
$numOfBaths = $appointments->getNumOfBaths();

if (isset($_POST['approveApp'])) {
    $appointmentId = $_POST["appointmentId"];
    
    $appointments->getAppointmentDetails($appointmentId);

    $customerId = $appointments->getCustomerId();
    $customerObj->displayCurrentCustomer($customerId);
    $customerName = $customerObj->getFirstname() . ' ' . $customerObj->getLastname();

    $serviceId = $appointments->getServiceId();
    $serviceObj->displayCurrentService($serviceId);
    $service = $serviceObj->getTitle();

    $addressId =  $appointments->getAddressId();
    $addressObj->getAddressById($addressId);
    $address = $addressObj->getStreet() . '. ' . $addressObj->getCity() . ', ' . $addressObj->getProvince() . '. ' . $addressObj->getCountry() . ', ' . $addressObj->getZip_code();

    $date = $appointments->getStart();
    $numOfFloors = $appointments->getNumOfFloors();
    $numOfBeds = $appointments->getNumOfBeds();
    $numOfBaths = $appointments->getNumOfBaths();
    $end = $appointments->getEnd();
    $note = $appointments->getNote();
    $supervisorName = $_POST['selectedSupervisor'];
    $supervisorErr = $validation->supervisorChoose($supervisorName);
    $supervisorId = $employee->getIdFromName($supervisorName);
    $status = 'pending';

    $receiver = $customerId;
    $unread = true;
    date_default_timezone_set("America/Vancouver");
    $created_at = date("Y-m-d H:i:s");
    $message = $notification->adminToCustApproveAppointment($service);

    if(empty($supervisorErr)){
        $appointments->approveAppointment($appointmentId, $customerId, $serviceId, $addressId, $supervisorId, $numOfFloors,
        $numOfBeds, $numOfBaths, $date, $end, $note, $status);
        //updating the status to approved in customers request appointment table
        $appointments->updateApprovedAppointment($appointmentId);
        $notification->insertNotif($receiver, $unread, $created_at, $message);
        header("location: ./index.php");
    }
}

if(isset($_POST['denyRequestModal'])) {
    $appointmentId = $_POST["appointmentId"];
    $appointments->getAppointmentDetails($appointmentId);

    $customerId = $appointments->getCustomerId();

    $serviceId = $appointments->getServiceId();
    $serviceObj->displayCurrentService($serviceId);
    $service = $serviceObj->getTitle();

    $receiver = $customerId;
    $unread = true;
    date_default_timezone_set("America/Vancouver");
    $created_at = date("Y-m-d H:i:s");
    $message = $notification->adminToCustDenyAppointment($service);

    $notification->insertNotif($receiver,$unread,$created_at,$message);
    $appointments->denyCustRequest($appointmentId);


    header("location: ./index.php");
}