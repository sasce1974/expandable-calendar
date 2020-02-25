<?php
/**
 * Created by PhpStorm.
 * User: Saso
 * Date: 11/10/2019
 * Time: 4:24 PM
 */

//namespace abstractFieldClass;


abstract class FieldFactory
{
    /*public $startTime = null;
    public $endTime = null;
    public $date = null;*/

    static function Create($type, array $fields){
        switch ($type){
            case 'HealthClinicField' :
                return new HealthClinicField($fields);
            case 'SomeOtherField' :
                return new SomeOtherField ($fields);
            default:
                return new DefaultField ($fields);
        }
    }

}