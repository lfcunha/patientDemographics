<?php

class usersController {
    function run()
    {

        $user = new users();
        $users = $user::all();
        return $users;
        //$users = $user::find(97);
        //return $users->name;
    }
}