<?php
/**
 * Created by PhpStorm.
 * User: luiscunha
 * Date: 7/3/14
 * Time: 10:59 AM
 */

require_once "config.php";


require ROOT."/app/config/dbparams.php";

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => $opts['hn'],
    'database'  => $opts['db'],
    'username'  => $opts['un'],
    'password'  => $opts['pw'],
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
));
$capsule->setAsGlobal();
$capsule->bootEloquent();
// set timezone for timestamps etc
date_default_timezone_set('UTC');




$config['database'] = array(
    'default'       => 'mysql',
    'connections'   => array(

        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => $opts['hn'],
            'database'  => $opts['db'],
            'username'  => $opts['un'],
            'password'  => $opts['pw'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'salt' => array(
            'salt' => $opts['salt']
        )
    )
);

$config["rules"] = array(
    'dna' => array(
        "individualID" => "required|in:individuals:id",
        "dnaId"=> "required|notin:dna:id", #notin Table field
        "arrivalSource"=> "required",
        "biologicalSource"=> "required",
        "concentration"=> "required|numeric",
        "volume"=> "required|numeric",
        "location"=> "required",
        "date"=> "required", #|between:1930,currentYear",
        "notes"=> null,
        "status"=> null
    ),
    'rna' =>array(
        "individualID" => "required|in:individuals:id",
        "rnaId"=> "required|notin:rna:id", #notin Table field
        "arrivalSource"=> "required",
        "biologicalSource"=> "required",
        "concentration"=> "required|numeric",
        "volume"=> "required|numeric",
        "RIN"=>null,
        "location"=> "required",
        "date"=> "required", #|between:1930,currentYear",
        "notes"=> null,
        "status"=> null
    ),
    'viablecell' =>array(
        "individualID" => "required|in:individuals:id",
        "viablecellId"=> "required|notin:viablecell:id", #notin Table field
        "arrivalSource"=> "required",
        "biologicalSource"=> "required",
        "concentration"=> "required|numeric",
        "volume"=> "required|numeric",
        "location"=> "required",
        "date"=> "required", #|between:1930,currentYear",
        "notes"=> null,
        "status"=> null
    ),
    'cellline' =>array(
        "individualID" => "required|in:individuals:id",
        "celllineId"=> "required|notin:cellline:id", #notin Table field
        "arrivalSource"=> "required",
        "biologicalSource"=> "required",
        "concentration"=> "required|numeric",
        "volume"=> "required|numeric",
        "location"=> "required",
        "date"=> "required", #|between:1930,currentYear",
        "notes"=> null,
        "status"=> null
    ),
    'patient' =>array(
        "referralDate"          => "required",
        "referralComments"      => null, #|between:1945-1-1,currentYear",
        "firstName"             => "required",
        "lastName"              => "required",
        "relationship"          => "required|relationship",
        "referralPatientPhone"  => "required",
        "referralPatientEmail"  => null,
        "medicalChartId"        => null, #|between:1930,currentYear",
        "medicalChart"          => null,
        "address"               => null,
        "dateOfBirth"           => null,
        "selfGender"            => "required",
        "selfEthnicity"         => null,
        "familySelfComments"    => null,
        "noteIfFamilyRelatives" => null,
        "diagnosisReferral"     => "required",
        "diagnosisSelf"         => null,
        "diagnosisInDb"         => null,
        "preGeneticScreening"   => null,
        "brainSurgery"          => null,
        "brainSampleInBank"     => null,
        "phoneConsented"        => null,
        "fullConsented"         => null,
        "notes"                 => null,
        "familyId"              => "required|in:family:id",
        "fatherId"              => "required",
        "motherId"              => "required"
    )
);


$config["dbtables"] = array(
  "patient"        => array("id","referralDate","referralComments","firstName","lastName","relationship","referralPatientPhone","referralPatientEmail","medicalChartId","medicalChart","address","dateOfBirth","selfGender","selfEthnicity","familySelfComments","noteIfFamilyRelatives","diagnosisReferral","diagnosisSelf","diagnosisInDb","preGeneticScreening","brainSurgery","brainSampleInBank","phoneConsented","fullConsented","notes","familyId","fatherId","motherId","created","modified"),
  "doctor"         => array("id","firstName","lastName","office","institution","city","country","created","modified"),
  "family"         => array("id", "doctorId","created", "modified"),
  "individuals" => array("id", "individualID", "arrivalDate", "project", "clinician", "originalLabel", "familyID", "gender", "dateofbirth", "relationship", "phenotype", "cyrilic", "intExt", "institute", "country", "receiverName", "alternativeID", "notes", "patientId", "created", "modified"),
  "patient_doc"        => array("id","referralDate","referralComments","firstName","lastName","relationship","referralPatientPhone","referralPatientEmail","medicalChartId","medicalChart","address","dateOfBirth","selfGender","selfEthnicity","familySelfComments","noteIfFamilyRelatives","diagnosisReferral","diagnosisSelf","diagnosisInDb","preGeneticScreening","brainSurgery","brainSampleInBank","phoneConsented","fullConsented","notes","familyId","fatherId","motherId","created","modified", "doc", "Institution"),

);

