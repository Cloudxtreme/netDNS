<?php
        error_reporting(0);
        require('db.php');
        if(isset($_GET['u']))
            $u=$_GET['u'];
        if(isset($_GET['p']))
            $p=$_GET['p'];
        try {
            $usercheck=$db->query('SELECT * FROM user_list WHERE name =\''.$u.'\'');
            $usercheck_arr=$usercheck->fetchAll();

            if(count($usercheck_arr)==1)
            {
                if($usercheck_arr[0]['name']==$u && $usercheck_arr[0]['password']==$p)
                    echo $usercheck_arr[0]['hostname'];
                else
                    echo 'ERR1';
            }
            else
                echo 'ERR2';
            $db=null;
        } catch (PDOException $e) {
            $err = 'Database operation failed!'.$e->getMessage();
            echo 'ERR0';
            $db=null;
        }
?>
