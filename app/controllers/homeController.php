<?php


class homeController extends baseController {

    public function __construct() {
        parent::__construct();
        $this->model="patientModel";
        $this->view = "patientsView.twig";
        $this->dbh = $this->app->db;
        $this->table=$this->env["dbtables"];
    }


    public function show(){

        $query = "SELECT doc, COUNT( * )  as count FROM  `patient_doc` GROUP BY  `doc`;";
        $sth=$this->dbh->prepare($query);
            $sth->execute();
            $patientDoc = array();
            while ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
                    array_push($patientDoc, $row);
                }

        $query = "SELECT Institution, COUNT( * )  as count FROM  `patient_doc` GROUP BY  `Institution`;";
        $sth=$this->dbh->prepare($query);
        $sth->execute();
        $patientIns = array();
        while ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
            array_push($patientIns, $row);
        }



        $query = "SELECT doc, COUNT( * )  as count FROM  `patient_doc` WHERE `relationship` = '01' GROUP BY  `doc`;";
        $sth=$this->dbh->prepare($query);
        $pbd = array();
        while ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
            var_dump($row);
            array_push($pbd, $row);
        }
        echo $query;
        var_dump($pbd);

        $probandInst = array();
        $query = "SELECT Institution, COUNT( * )  as count FROM  `patient_doc` WHERE `relationship` = '01' GROUP BY  `Institution`;";
        $sth=$this->dbh->prepare($query);
        $sth->execute();
        while ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
            array_push($probandInst, $row);
        }







        $this->app->render('home.twig', array("docCount" => $patientDoc, "Institution"=>$patientIns, "probandInst"=>$probandInst, "probandDoc"=>$pbd, "user"=>$_SESSION['user']));

    }




}

