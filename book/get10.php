<?php
session_start();
$username=$_SESSION['rainbow_username'];
$role=$_SESSION['rainbow_role'];
$id=$_SESSION['rainbow_uid'];
include('database_connection.php');
$column = array("SNo","RegistrationNumber","Name","CourseCode","Course","CourseType","Credits","Grade","Faculty");
if($role=="student"){
$column = array("CourseCode","Course","CourseType","Credits","Grade","Faculty");
$query = "SELECT * FROM coursedetails where RegistrationNumber like '%$id%'";
}
else{
$query = "SELECT * FROM coursedetails";
}
if(isset($_POST["search"]["value"]))
{
$query .= '
having RegistrationNumber LIKE "%'.$_POST["search"]["value"].'%"
OR CourseCode LIKE "%'.$_POST["search"]["value"].'%"
OR Course LIKE "%'.$_POST["search"]["value"].'%"
OR CourseType LIKE "%'.$_POST["search"]["value"].'%"
OR Credits LIKE "%'.$_POST["search"]["value"].'%"
OR Grade LIKE "%'.$_POST["search"]["value"].'%"
OR Faculty LIKE "%'.$_POST["search"]["value"].'%"
';
}
if(isset($_POST["order"]))
{
$query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
$query .= 'ORDER BY SNo ';
}
$query1 = '';
if($_POST["length"] != -1)
{
$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
$statement = $connect->prepare($query);
$statement->execute();
$number_filter_row = $statement->rowCount();
$statement = $connect->prepare($query . $query1);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
foreach($result as $row)
{
$sub_array = array();
if($role=="user"){
$sub_array[] = $row['SNo'];
$sub_array[] = $row['RegistrationNumber'];
$sub_array[] = $row['Name'];
}
$sub_array[] = $row['CourseCode'];
$sub_array[] = $row['Course'];
$sub_array[] = $row['CourseType'];
$sub_array[] = $row['Credits'];
$sub_array[] = $row['Grade'];
$sub_array[] = $row['Faculty'];
$data[] = $sub_array;
}
function count_all_data($connect)
{
$query = "SELECT * FROM coursedetails";
$statement = $connect->prepare($query);
$statement->execute();
return $statement->rowCount();
}
$output = array(
'draw'   => intval($_POST['draw']),
'recordsTotal' => count_all_data($connect),
'recordsFiltered' => $number_filter_row,
'data'   => $data
);
echo json_encode($output);
?>
