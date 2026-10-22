<?php
$val = '14.26249875';
$prec = 6;
$adder = '0.' . str_repeat('0', $prec) . '5';
echo "Val: $val\nAdder: $adder\nRes: " . bcadd($val, $adder, $prec) . "\n";
