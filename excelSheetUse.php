<?php

	include("excelwriter.inc.php");
	
	$excel=new ExcelWriter();
	
	if($excel==false)	
		echo $excel->error;
		
	$myArr=array("Name","Last Name","Address","Age");
	$excel->writeLine($myArr);

	$myArr=array("Osama","Aziz","Lahore",28);
	$excel->writeLine($myArr);
	
	$excel->writeRow();
	$excel->writeCol("Mian");
	$excel->writeCol("G");
	$excel->writeCol("21 Balakot");
	$excel->writeCol(24);
	
	$excel->writeRow();
	$excel->writeCol("Harish");
	$excel->writeCol("Chauhan");
	$excel->writeCol("115 Shyam Park Main");
	$excel->writeCol(22);

	$myArr=array("Sir","Mansoor","Risalpur",40);
	$excel->writeLine($myArr);
	
	$excel->close();
	echo "data is write into myXls.xls Successfully.";
?>