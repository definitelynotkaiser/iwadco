<?php
include("db_connect.php");

$id = intval($_POST['id']);
$request = $_POST['request'] === 'approve' ? 'approve' : 'reject';

$update = $conn->prepare("UPDATE applications SET request=? WHERE id=?");
$update->bind_param("si",$request,$id);
if($update->execute()){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
