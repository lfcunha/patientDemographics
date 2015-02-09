<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 11/11/14
 * Time: 11:35 AM
 */

class registrationController extends \Slim\slim {

    public function __construct() {
        parent::__construct();
        $this->app = \Slim\Slim::getInstance();
        $this->env=$this->app->environment();
    }


    public function register($data){


        $user=$data["user"];
        $name=$data["name"];
        $email=$data["email"];
        $pass=$data["pass"];
        $pass2=$data["pass2"];
        $salt=$this->env['salt'];

        if ($pass!=$pass2)die("passords do not match");
        if (strlen($user)<6) die("username should countain at least 5 characters");


        $dbh=$this->app->db;

        $sth=$dbh->prepare('SELECT * FROM `users` WHERE `user` LIKE :user');
        $sth->bindParam(":user", $user);
        $sth->execute();
        $result=$sth->fetchAll();

        if($result) die("username taken");



        $sth=$dbh->prepare("INSERT INTO `cunhal01_a`.`users` (`id`, `user`, `pass`, `email`, `name`, `DP`) VALUES ('', :user, AES_ENCRYPT(:pass, SHA2(:salt,512)), :email, :name, '')");

        #$sth=$dbh->prepare('SELECT * FROM `users` WHERE `user` LIKE :user AND `pass` like AES_ENCRYPT(:pass, SHA2(:salt,512))');
        $sth->bindParam(":user", $user);
        $sth->bindParam(":pass", $pass );
        $sth->bindParam(":salt", $salt);
        $sth->bindParam(":name", $name);
        $sth->bindParam(":email", $email);
        $sth->execute();

        if($sth){
            $this->app->redirect('/demographicsDB/login');
        }
        #$result=$sth->fetchAll();


    }



}
