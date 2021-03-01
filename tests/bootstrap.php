<?php declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

chdir(__DIR__ . '/..');

/** @scrutinizer ignore-deprecated */ AnnotationRegistry::registerLoader('class_exists');
