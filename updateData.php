<?php
// read the JSON data from the file
$string = file_get_contents("data.json");
$data = json_decode($string, true);

// find the row that needs to be updated based on the "name" attribute
$base = $_POST['base'];
$name = $_POST['name'];
$manager = $_POST['manager'];

foreach ($data[$base]['rows'] as &$row) {
  if ($row['c'][0]['v'] === $name) {
    $row['c'][1]['v'] = $manager;
    break;
  }
}

// write the updated JSON data back to the file
file_put_contents("data.json", json_encode($data));

echo "Data updated successfully!";
?>
