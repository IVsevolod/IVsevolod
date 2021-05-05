<?php
$test = "42";
$test1 = 23;
?>
test

<?php
echo $test;
$test = $test . " 24";
$test1 = $test1 + 32;
$test1 += 1;
$test = "56";
if (intval($test) === 56) {
    $test1 += 1;
} else if ($test == 56) {
    $test1 += 3;
} else {

}
var_dump($test1);
for ($i = 0; $i < 5; $i++) {
    $test1++;
}

var_dump($test1);
?>
