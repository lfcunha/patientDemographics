<?php

use \individuals;

#ini_set('memory_limit', '512M');
//include_once "./app/controllers/doctorController.php";

ChromePhp::log("Chrome logger is working");


$authenticate = function ($app) {

    return function () use ($app) {
        /*  Destroy the session if 20min of inactivity have passed     */
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1200)) {
            // last request was more than 20 minutes ago
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in storage
            $app->redirect('/demographicsDB/login');
        }
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/demographicsDB/login');
        }

    };
};


$app->get('/', $authenticate($app), function() use ($app){
    $env = $app->environment();
    $salt=$env['salt'];
    $app->render('home.twig', array("user"=>$_SESSION['user']));
});

$app->get('/login', function()  use ($app){
    $app->render('login.twig');
});



$app->post('/login', function() use ($app){

    $req = $app->request();
    $logininfo=$req->post();
    $env = $app->environment();
    $salt=$env['salt'];
    $dbh=$app->db;
    $sth=$dbh->prepare('SELECT * FROM `users` WHERE `user` LIKE :user AND `pass` like AES_ENCRYPT(:pass, SHA2(:salt,512)) AND `DP` like 1');
    $sth->bindParam(":user", $logininfo['user']);
    $sth->bindParam(":pass", $logininfo['pass'] );
    $sth->bindParam(":salt", $salt);
    $sth->execute();
    $result=$sth->fetchAll();

    $log = $app->log;


    if ( $result ) {
        $_SESSION['user'] = $logininfo['user'];
         $app->view->setData("user",$_SESSION['user']);
        //$_SESSION['admin'] = $result[0]["admin"];
        $log->info($_SESSION['user'] . " - " . $req->getIp() . " - logged in ");
        $app->redirect("/demographicsDB");
    }
    else {
        $log->warn($logininfo['user'] . " - " . $req->getIp() . " - login failed");
        $app->redirect("/demographicsDB/login");
    }
});

$app->get("/logout", function () use ($app) {
    $req = $app->request();
    $log = $app->log;
    $log->info($_SESSION['user'] . " - " . $req->getIp() . " - logout ");
    unset($_SESSION['user']);
    $app->view()->setData('user', null);
    $app->view()->setData('logstatus', "login");
    $app->redirect('/demographicsDB');
});

$app->get("/register", function () use ($app) {
    $app->render('register.twig');
});

$app->post("/register", function () use ($app) {
    $req = $app->request();
    $data=$req->post();
#    $log = $app->log;
#    $log->info($_SESSION['user'] . " - " . $req->getIp() . " - logout ");
    $c=new registrationController();
    $c->register($data);
});


//Individuals
$app->get('/individuals/', $authenticate($app), function () use ($app) {
    $c= new individualsController();
    $ajax = $app->request()->get('ajax');
    $length = $app->request()->get('length')?$app->request()->get('length'):10;
    $offset = $app->request()->get('offset')?$app->request()->get('offset'):0;
    $column=$app->request()->get('column')?$app->request()->get('column'):"id";
    $order= $app->request()->get('order')? $app->request()->get('order'):"asc";
    $data=$c->getIndividuals($column, $order, $offset, $length, $ajax);
    echo $data;
});



$app->post('/individuals', $authenticate($app),  function () use ($app) { #insert
        $req = $app->request();
        $value=$req->put("val");
        $col=$req->put("col");
        $id=$req->put("id");
        if($value=="")$value=NULL;
        $c= new individualsController();
        $result=$c->editIndividuals(array("column"=>$col,"value"=>$value,"id"=>$id));
        echo(json_encode($result));
        //echo 1;
});

$app->get('/fixindividuals', $authenticate($app),  function () use ($app) { #insert
    $req = $app->request();
    $value=$req->get("data");
    $this->app->render("fixIndividuals.twig", array("data"=>$value)); #auxiliary is required
});


$app->put('/individuals/:id',  $authenticate($app), function () { #edit
    echo "Hello123";
});

$app->delete('/individuals/:id',  $authenticate($app), function () { #edit
    echo "Hello123";
});

//DNA
$app->get('/DNA/',  $authenticate($app), function () use ($app) {
    $c= new DNAController();
    $ajax = $app->request()->get('ajax');
    $length = $app->request()->get('length')?$app->request()->get('length'):10;
    $offset = $app->request()->get('offset')?$app->request()->get('offset'):0;
    $column=$app->request()->get('column')?$app->request()->get('column'):"id";
    $order= $app->request()->get('order')? $app->request()->get('order'):"asc";
    $data=$c->getDNA($column, $order, $offset, $length, $ajax);
    echo $data;
});

$app->put('/DNA',  $authenticate($app), function () { #insert
    echo "Hello123";
});

$app->post('/DNA',  $authenticate($app), function () use ($app) { #insert
    $req = $app->request();
    $value=$req->put("val");
    $col=$req->put("col");
    $id=$req->put("id");
    if($value=="")$value=NULL;
    $c= new DNAController();
    $result=$c->editDNA(array("column"=>$col,"value"=>$value,"id"=>$id));
    echo(json_encode($result));
    //echo 1;
});

$app->delete('/DNA/:id',  $authenticate($app), function () { #edit
    echo "Hello123";
});





$app->get('/readExcel', $authenticate($app),  $authenticate($app), function() use ($app){
    //echo "Please use <a href='http://cripdb.mssm.edu/upload'><strong>http://cripdb.mssm.edu/upload </strong></a> to upload your excel file.";
    ChromePhp::log("read excel");
    $app->render('upload.twig');
});

$app->post('/readExcel', $authenticate($app), function()  use ($app) {
    ChromePhp::log("read excel");
    $req = $app->request();
    $table=$req->post("table");
    if (!$_FILES["file"]["error"] > 0) {
        $c= new readExcelController($_FILES["file"]["tmp_name"]);
        $c->init($table);
    }
    else {
        echo "Error uploading file. Did you select a file?</br> If problem persists, please contact us.";
    }
});




$app->get('/patients',  $authenticate($app), function()  use ($app){
    ChromePhp::log("getPatients0");
    $c      = new patientController();
    $ajax   = $app->request()->get('ajax')?$app->request()->get('ajax'):0;
    $length = $app->request()->get('length')?$app->request()->get('length'):10;
    $offset = $app->request()->get('offset')?$app->request()->get('offset'):0;
    $column = $app->request()->get('column')?$app->request()->get('column'):"id";
    $order  = $app->request()->get('order')? $app->request()->get('order'):"asc";
    ChromePhp::log("getPatients0: " . $ajax . " " . $length . " " . $offset . " " . $column . " " . $order );
    $data   =  $c->getPatients($column, $order, $offset, $length, $ajax);
    echo $data;


    #$app->render('patientsView.twig', array("table"=>"patient"));
});

$app->get('/addpatients', $authenticate($app),  function()  use ($app){
    #echo "hello";
    $app->render('upload.twig', array("table"=>"patient"));
});
$app->post('/patients', $authenticate($app),  function()  use ($app){
    echo "1";
});
$app->put('/patients', $authenticate($app),  function()  use ($app){
    $req = $app->request();
    $value=$req->put("val");
    $col=$req->put("col");
    $id=$req->put("id");
    if($value=="")$value=NULL;
    $c= new patientController();
    $result=$c->editPatient(array("column"=>$col,"value"=>$value,"id"=>$id));
    echo(json_encode($result));
    //echo 1;
});
$app->delete('/individual', $authenticate($app),  function()  use ($app){
});




$app->get('/doctor',  $authenticate($app), function()  use ($app){
    $ajax = $app->request()->get('ajax');
    $length = $app->request()->get('length')?$app->request()->get('length'):10;
    $offset = $app->request()->get('offset')?$app->request()->get('offset'):0;
    $column=$app->request()->get('column')?$app->request()->get('column'):"id";
    $order= $app->request()->get('order')? $app->request()->get('order'):"asc";
    $opts=array($column, $order, $offset, $length, $ajax);
    $c= new doctorController();
    $data=$c->getDoctor($column, $order, $offset, $length, $ajax);
    echo $data;

    //$app->render('doctorView.twig', array("table"=>"DNA"));
});
$app->get('/adddoctor', $authenticate($app),  function()  use ($app){
    #echo "1";
    $app->render('addDoctor.twig', array("table"=>"RNA"));
});
$app->post('/doctor', $authenticate($app),  function()  use ($app){
    $req=$app->request();
    $firstName=$req->post("firstName");
    $lastName=$req->post("lastName");
    $office=$req->post("office");
    $institution=$req->post("institution");
    $city=$req->post("city");
    $country=$req->post("country");
    $c= new doctorController();
    $data=$c->addDoctor(array("firstName"=>$firstName, "lastName"=>$lastName, "office"=>$office, "institution"=>$institution, "city"=>$city, "country"=>$country));
    echo json_encode($data);

});
$app->put('/doctor', $authenticate($app),  function()  use ($app){
});
$app->delete('/doctor', $authenticate($app),  function()  use ($app){
});


$app->get('/family',  $authenticate($app), function()  use ($app){
    $ajax = $app->request()->get('ajax');
    $length = $app->request()->get('length')?$app->request()->get('length'):10;
    $offset = $app->request()->get('offset')?$app->request()->get('offset'):0;
    $column=$app->request()->get('column')?$app->request()->get('column'):"id";
    $order= $app->request()->get('order')? $app->request()->get('order'):"asc";
    $opts=array($column, $order, $offset, $length, $ajax);
    $c= new familyController();
    $data=$c->getFamily($column, $order, $offset, $length, $ajax);
    echo $data;



   // $app->render('familyView.twig', array("table"=>"DNA"));
});
$app->get('/addfamily', $authenticate($app),  function()  use ($app){
    $app->render('addFamily.twig', array("table"=>"RNA"));
});
$app->post('/family', $authenticate($app),  function()  use ($app){
});
$app->put('/family', $authenticate($app),  function()  use ($app){
});
$app->delete('/family', $authenticate($app),  function()  use ($app){
});





$app->get('/test', function () {
    echo "Hello1234";
});

$app->get('/', function () use($app) {
    $app->render('home.twig');
});

$app->get('/home',  $authenticate($app), function () use($app) {
    $app->render('home.twig');
});



$app->post('/validate2', $authenticate($app),  function()use ($app){

    $req = $app->request();
    $postdata=$req->post("data");
    if($postdata=="")$postdata=NULL;

    switch($req->post("table")){
        case "ind":
            $headers=array("a", "id","arrivalDate","project", "clinician", "originalLabel", "familyID", "gender", "dateofbirth", "relationship", "phenotype", "cyrilic","intExt","institute","country","receiverName","alternativeID","notes");
            $ic=new individualsController();
            $col=$req->post("col");
            $reslt=$ic->processAjax(array($headers[$col], $postdata,0));
            echo(json_encode($reslt));
             break;
        case "dna":
            $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "location", "date", "notes", "status");
            $ic=new DNAController();
            $col=$req->post("col");
            $reslt=$ic->processAjax(array($headers[$col], $postdata,0));
            echo(json_encode($reslt));
            break;
        case "rna":
            $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "RIN", "location", "date", "notes", "status");
            $ic=new RNAController();
            $col=$req->post("col");
            $reslt=$ic->processAjax(array($headers[$col], $postdata,0));
            echo(json_encode($reslt));
            break;
        case "viablecell":
            $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "location", "date", "notes", "status");
            $ic=new viableCellController();
            $col=$req->post("col");
            $reslt=$ic->processAjax(array($headers[$col], $postdata,0));
            echo(json_encode($reslt));
            break;
        case "cellline":
            $headers=array("a", "individualID","id","arrivalSource", "biologicalSource", "concentration", "volume", "location", "date", "notes", "status");
            $ic=new cellLineController();
            $col=$req->post("col");
            $reslt=$ic->processAjax(array($headers[$col], $postdata,0));
            echo(json_encode($reslt));
            break;
        case "patient":
            ChromePhp::log("patient validation called");
            $headers=array("a","referralDate","referralComments","firstName","lastName","relationship","referralPatientPhone","referralPatientEmail","medicalChartId","medicalChart","address","dateOfBirth","selfGender","selfEthnicity","familySelfComments","noteIfFamilyRelatives","diagnosisReferral","diagnosisSelf","diagnosisInDb","preGeneticScreening","brainSurgery","brainSampleInBank","phoneConsented","fullConsented","notes","familyId","fatherId","motherId");
            $ic=new patientController();
            $col=$req->post("col");
            $reslt=$ic->processAjax(array($headers[$col], $postdata,0));
            echo(json_encode($reslt));
            break;


    }
    //echo json_encode($req->post("table"));


});

$app->post('/revalidate', $authenticate($app), function()use ($app){
    $req = $app->request();
    $postdata=$req->post();
    ChromePhp::log($postdata);
    //var_dump($postdata);
    $result = readExcelController::revalidate($postdata);
    echo json_encode($result);
});


$app->post('/search', $authenticate($app), function()  use ($app){
    $req = $app->request();
    $postdata=$req->post();
    $table =$postdata["table"];
    $length=$postdata["length"];
    $offset=$postdata["offset"];
    $search=$postdata["search"];
    $column=$postdata["column"];
    $order_=$postdata["order"];
    $order= explode("_", $order_);

//    $req = $app->request();
//    $log = $app->log;
//    $log->info($_SESSION['user'] . " - " . $req->getIp() . " - Search Isolates: " . implode(",", $postdata));

    $c=new baseController();
    $result=$c->search($table, $length, $offset, $search, $column, $order);
    //var_dump($result);
    echo json_encode($result);
});