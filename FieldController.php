<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/10/2019
 * Time: 6:33 PM
 */

require "includes/config.inc.php";
require "FieldFactory.php";
require "HealthClinicField.php";
require "ConnectionClass.php";

//TODO get values into fields array

$fields = array(
    'date'      => '2019-11-13',
    'startTime' => '08:00',
    'endTime'   => '09:30',
    'treatment'   => 'Acupuncture',
    'therapist' => 'John Doe',
    'reserved'  => 'false'
);

$field = FieldFactory::Create('HealthClinicField', $fields);
$r = $field->create($fields);

    print_r ($r);

