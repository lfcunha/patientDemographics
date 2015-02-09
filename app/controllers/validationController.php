<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 11/3/14
 * Time: 6:39 PM
 */

define ("REQUIRED_MESSAGE",  "required field");
define ("OUTOFRANGE_MESSAGE",  "invalid value");
define ("INDATABASE_MESSAGE",  "not in database");
define ("INHOSTSDATABASE_MESSAGE",  "Species name not in DB. Please contact us");
define ("NOTINDATABASE_MESSAGE",  "Already in database");
define ("NOTUNIQUE_MESSAGE",  "Duplicate sample Identifier");
define ("NONNUMERIC_MESSAGE", "Provide numeric value without units.");
define ("STATUS_MESSAGE", "not acceptable value");
define ("TYPE_MESSAGE", "Choose from the menu");
define ("BOOL_MESSAGE", "Choose 'yes' or 'no'");
define ("COSTSITE_MESSAGE", "Choose from the menu");
define ("LatLon_MESSAGE", "input in degrees");

date_default_timezone_set('America/New_York');

class validationController extends \Slim\slim {

    public function __construct($data, $rules) {
        parent::__construct();
        $this->app = \Slim\Slim::getInstance();
        $this->env=$this->app->environment();
        $this->data=$data;
        $this->rules=$rules;
        $this->needsFixingFlag=0;
    }

    /*
     * Validate a single rule and return the result (either ok(1) or validation fail message
     * If needed from a different class, this could be moved its own class
     */
    protected function applyRules($data){
        switch ($data[1][0]) {
            case null:
                return true;
                break;
            case "required":
                return ($data[0]==null || $data[0]=="")?REQUIRED_MESSAGE:1;
                break;
            case "between":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $range=explode(",", end($data[1]));
                if(strpos($range[1], "Year")) {$upperRange=strtotime(date("Y-m-d"));} else $upperRange=$range[1];
                return ( $data[0]=="" || $data[0]==null || ((strtotime($data[0])) > strtotime($range[0])) && (intval($data[0]) < intval($upperRange)+1))?1:strtotime($range[0]);
                break;
            case "notin":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $table=$data[1][1];
                $dbh=$this->app->db;
                try{
                    $query="SELECT * from ".$table." WHERE `".end($data[1])."` LIKE '".$data[0] ."'";
                    $sth=$dbh->prepare($query);
                    //$sth->bindParam(1,$table);
                    //$sth->bindParam(2,$data[0]);
                    $sth->execute();
                    $row=$sth->fetch(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e)
                {
                    throw new Exception($e);
                    #add code to email admin;
                }
                return (!$row)?1:NOTINDATABASE_MESSAGE;
                break;
            case "in":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $table=$data[1][1];
                $dbh=$this->app->db;
                try{
                    $query="SELECT * from ".$table." WHERE `".end($data[1])."` LIKE '".$data[0] ."'";
                    $sth=$dbh->prepare($query);
                    //$sth->bindParam(1,$table);
                    //$sth->bindParam(2,$data[0]);
                    $sth->execute();
                    $row=$sth->fetch(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e)
                {
                    throw new Exception($e);
                    #add code to email admin;
                }
                return ($row)?1:INDATABASE_MESSAGE;
                break;
            case "numeric":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                //return 123;
                return is_numeric(($data[0]))? 1: NONNUMERIC_MESSAGE;
                break;
            case "gender":
                if($data[0]==null || $data[0]=="") return 1; break;
                $s=strtolower($data[0]);
                $options=array("m", "f");
                return in_array($s,$options)? 1: STATUS_MESSAGE;
                break;
            case "intExt":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array("internal","external" ,"int" ,"ext", "int.", "ext.");
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "cyrilic":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array("yes","y" ,"no" ,"n");
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "clinician":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array_map('strtolower', array('Ana Berta Sousa', 'Ana Isabel Dias', 'Ana Fortuna', 'Cristina Martins', 'Gabriela Soares', 'Guiomar Oliveira', 'Hilary Coon', 'Isabel Fineza', 'Joao Silva', 'Joaquim Sa', 'Joseph Buxbaum', 'Juliette Dupont', 'Mafalda Barbosa', 'Margarida Venâncio', 'Maria Joao Sa', 'Miguel Rocha', 'Oana Moldovan', 'Pedro Cabral', 'Renata Oliveira', 'Scherer', 'Teresa Temudo'));
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "receiver":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array_map('strtolower',array('Mafalda Barbosa', 'Qin Yao', 'Nancy Francoeur'));
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "institute":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array_map('strtolower',array('Utah', 'SickKids', 'Seaver', 'ICVS_Braga', 'IGM_Porto', 'HPC_Coimbra', 'MSSM'));
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "country":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array_map('strtolower',array('USA', 'Portugal'));
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "project":
                if($data[0]==null || $data[0]=="") {return 1; break;}
                $s=strtolower($data[0]);
                $options=array_map('strtolower',array('Utah', 'NRXN1', 'Universal', 'Rett_like', 'NDD', 'EIEE', 'Migraine'));
                return in_array($s,$options)? 1: TYPE_MESSAGE;
                break;
            case "relationship":
                $relationshipCodes=array("01", "02", "03", "04", "05", "06", "07");
                if (substr(strtolower($data[0]),0,1) =="*"){
                    $s=substr(strtolower($data[0]),1); //slicing to remove first character because must add a character to excel fields that start with 0, even if set to string on excel
                }
                else {
                    $s=strtolower($data[0]);
                }
                ChromePhp::log($s);
                return in_array($s,$relationshipCodes)? 1: TYPE_MESSAGE;
                break;





            /*
            case "bool": //chimeric
                $options=["yes", "no"];
                return in_array(strtolower($data[0]), $options)? 1: BOOL_MESSAGE;
                break;
            case "costSite":
                $options=["crip", "invoice"];
                return in_array(strtolower($data[0]),$options)? 1: COSTSITE_MESSAGE;
                break;
            case "extractsSampleIdentifier":
                $dbh= $this->getPDO();
                $query="select `idIsolates` from Isolates WHERE `Sample_Identifier` LIKE '" . $data[0] . "'";
                $sth=$dbh->prepare($query);
                $sth->execute();
                $row=$sth->fetch(PDO::FETCH_ASSOC);
                return ($row)?1:INDATABASE_MESSAGE;
                break;
            case "LatLon":
                if ($data[0]=="") return 1;
                return floatval($data[0])!=0?1:LatLon_MESSAGE;
            */
        }
        return 0;
    }



    /*
     * For each cell, get all rules that apply
     * request each and collect results
     * if any validation fail, turn $data[$row][$cell][$value] into array ($value, $messagesToUser)
     * mark flag that form did not pass validation and needs to be send back to the user
     */


    public function validateRow($field, $ajax=0){
        $_field=implode("_", (explode(" ",$field[0])));                                                                 #array (size=2)
        $rules=explode("|",$this->rules[$_field]);                                                                      # 0 => string 'required' (length=8)
        for($i=0; $i<count($rules); $i++){                                                                              # 1 => string 'between:2000,currentYear'*/
            $rules[$i] = explode(":", $rules[$i]);
        }

        /*array to collect results of all rules that apply to a field (e.g. required, and date range)*/
        $messages=array("a"=>1);
        #array (size=2)
        foreach($rules as $rule) {                                                                                      # 0 =>

            $result= $this->applyRules(array($field[1],$rule)) ;

            $messages[$rule[0]]=$result ;

            if(is_string($result)) {
                $this->needsFixingFlag=1;
            }                                                          # 1 =>
        }

        if ($ajax==1) {                                                                                                 #     1 => string '2000,currentYear'*/
            if($this->needsFixingFlag==0) {return 1;}
            else {return $messages;}
        }

        return array($field[1], $messages);                                                  #     0 => string 'between' (length=7)


      #  if($this->needsFixingFlag==0) {
      #      return 1;
      #  }
      #  else {
      #      return "BAD";
      #  }
    }


    public function validateExcelData(){
        $data2=$this->data;   #was not modifying original $this->data, so make a deep copy here
        //var_dump($data2);
        foreach ($data2 as $index=>$row) {
            foreach($data2[$index] as $key=>$value){
                $result=$this->validateRow(array($key,$value, $index));
                $data2[$index][$key]=$result;
            }
        }

        if ($this->needsFixingFlag==0) {
            return array(1);
        }
        else {
            return array(3, $data2, $this->needsFixingFlag);
        }
    }

}
