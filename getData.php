<?php 
$string = file_get_contents("data.json");
$data = json_decode($string, true);
echo json_encode($data[$_REQUEST['base']]);
//echo json_encode($data['Risalpur']);
?>
