<?php
/**
 * Created by luiscunha
 * Date: 7/3/14
 * Time: 3:40 PM
 */


class baseModel{
    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->dbh=$this->app->db;
        $this->env=$this->app->environment;
        $this->tableColumns=$this->env["dbtables"];
        $this->rules=$this->env["rules"];
    }


    protected function get_idUser($user_){
        try{
            $dbh=  $this->dbh;
            $sth=$dbh->prepare('SELECT * FROM `Users` WHERE `Username` LIKE ?');
            $sth->bindParam(1,$user_);
            $sth->execute();
            $row=$sth->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        catch(PDOException $e)
        {
            throw new Exception($e);
            #add code to email admin;
        }
    }

    protected function totalRecords($table){
        $sth=$this->dbh->prepare('SELECT count(*) FROM `'.$table.'`');
        $sth->execute();
        $totalRecords=$sth->fetch(PDO::FETCH_ASSOC);
        return (int)$totalRecords['count(*)'];
    }


    protected function select_($table, $column, $order, $offset, $length){
        $b=array();
        $b['data']=array();
        $query="SELECT * from ".$table." WHERE 1 ORDER BY `". $column ."` ". $order ." LIMIT ". $length ." OFFSET " . $offset ;
        try {
            $sth= $this->dbh->prepare($query);
            $sth->execute();
        }
        catch(PDOException $ex) {
            echo json_encode($ex);
        }
        while($row=$sth->fetch(PDO::FETCH_ASSOC)){
            array_push($b['data'], $row);
        }
        //$b['count']= $this->getNumberRecords($table);
        $b['count']= $this->totalRecords($table);
        $b['size']=$length;
        $b['offset']=$offset;
        $b['total']=$b['count'];
        $b['query']=$query;
        $dbh = null;
        return $b;
    }


    protected function insert_($data){
        $data_=$data["data"];
        $table=$data["table"];
        for($i=0; $i<count($data_); $i++) {
                $query="INSERT INTO `".$table."` (";
                foreach ($this->tableColumns[$table] as $key) {
                    $query.="`";
                    $query.=$key;
                    $query .= "`,";
                }
                $query = rtrim($query, ",");

                $query .=") VALUES ('',";
                foreach (array_slice($this->tableColumns[$table],1,count($this->tableColumns[$data["table"]])-3) as $key) {
                    $query.="'";
                    $query.= $data_[$i][$key];
                    $query.= "',";
                };

                $query.="NOW(),NOW())";

                try {
                    $stmt=$this->dbh->prepare($query);
                    $res=$stmt->execute();
                    if(!$res) return array("error", $stmt);
                }
                catch(PDOException $e)
                {
                    return array(2, $e->getMessage());
                }
        }
        return 1;
    }

    //protected function update($data, $table){
    protected function update_($data, $table){
        try{
            $sth=$this->dbh->prepare('UPDATE `'.$table.'` SET '. $data["column"] . ' = "'. $data["value"] .'" WHERE `id` LIKE "'. $data["id"] .'"');
            $res=$sth->execute();
            return array($res);
        }
        catch(PDOException $e)
        {
            throw new Exception($e);
            #add code to email admin;
        }
    }


    protected function search_($table, $length, $offset, $search, $column, $order){
        $query="";
        if ($table=="patients") $table="patient_doc";
        foreach ($this->tableColumns[$table] as $key) {
            $query.="`";
            $query.=$key;
            $query .= "` LIKE '%";
            $query .= $search;
            $query .= "%' OR ";
        };

        $query = rtrim($query, " OR ");
        $query_="select * from ".$table." WHERE ( ".$query."   )  ORDER BY `". $column ."` ". $order ." LIMIT ". $length ." OFFSET " . $offset ;
        $queryCount = "SELECT count(*) FROM `".$table."` WHERE  ( ".$query."   ) ";
        $sth=$this->dbh->prepare($query_);
        $sthCount=$this->dbh->prepare($queryCount);
        $sth->execute();
        $sthCount->execute();
        $count=$sthCount->fetchColumn();
        $result=array();
        $result['data']=array();
        $result['query']=$sthCount;
        $i=0;
        while ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
            array_push($result['data'], $row);
            $i++;
        }

        $result['query']=$query_;
        $result['count']=$count;
        $result['size']=$length;
        $result['total']=$this->totalRecords($table);
        $result['offset']=$offset;
        $result['queryCount']=$sthCount;
        return $result;
        }



    public function export_($data, $table){
        $search="";

        foreach($data as $value){
            if (strlen($value)<1) $search=$search. " `id` LIKE '%" . $value . "%' or ";
            else                  $search=$search. " `id` LIKE '" . $value . "' or ";
        }

        $search_=rtrim($search, " or ");

        $query  ='SELECT * FROM `'. $table .'` WHERE '. $search_ .' ORDER BY modified DESC';
        $sth=$this->dbh->prepare($query);


        $sth->execute();
        $result=array();
        while ($row=$sth->fetch(PDO::FETCH_ASSOC)) {
            // $user["admin"]!=1?$row['admin']=0:$row['admin']=1;
            array_push($result, $row);
        }
        $this->dbh=null;
        return $result;
    }

}//end class
