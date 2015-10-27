<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;

// replace with file to your own project bootstrap
require_once 'bootstrap.php';

$helperSet = new HelperSet(array(
    'em' => new EntityManagerHelper($entityManager),
    'conn' => new ConnectionHelper($entityManager->getConnection())
));

return ConsoleRunner::createHelperSet($entityManager);
