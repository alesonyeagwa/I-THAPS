<?php

// if (!defined("PHP_INT_MAX")) { define("PHP_INT_MAX", 2147483647); }

// function fix_integer_overflow($size) {
//     if ($size < 0) {
//         $size += 2.0 * (PHP_INT_MAX + 1);
//     }
//     return $size;
// }

// // $a = "<script>alert('hello')</script>";
// // try {
// //     $soln = 1 / $a;
// // } catch (Exception $th) {
// //     echo var_dump($th);
// // }


// // echo $soln;

// // file inclusion functions
// $SINK_FILE_INCLUDE = array(
//     'fgets'							, #Param1
//     'fgetss'						, #Param1
//     'fread'							, #Param1
//     'parsekit_compile_file'			, #Param1: parsekit_compile_file ( string $filename [, array &$errors [, int $options = PARSEKIT_QUIET ]] )
//     'set_include_path' 				, #Param1: set_include_path ( string $new_include_path )
//     'virtual' 						, #Param1: virtual ( string $filename )
// );

// //$gg = $_POST['lang'];
// $handle = @fopen("testOtherIncludes.php", "r");
// if ($handle) {
//     while (($buffer = fgets($handle, 4096)) !== false) {
//         echo $buffer;
//     }
//     if (!feof($handle)) {
//         echo "Error: unexpected fgets() fail\n";
//     }
//     fclose($handle);
// }

// $names = explode("|", $_POST["names"]);
// //$_POST["name"] = "<script></script>";
// //printf("%s", $_POST["name"]);
print_r($names);

// $object = new stdClass();
// $object->Property1 = $_POST["name"];
// $object->Property2 = 'Value 2';
// vprintf('%-20s %-20s', $names);

//die($_POST["name"]);