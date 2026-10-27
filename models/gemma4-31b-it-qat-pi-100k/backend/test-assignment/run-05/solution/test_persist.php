<?php
require 'vendor/autoload.php';
$kernel = new App\Kernel('dev', true);
$kernel->boot();
$entityManager = $kernel->getContainer()->get('doctrine'); // This might not be the right way to get EM in Symfony 8
echo "Test persist\n";
