<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See
*  [root]/license.txt for more. This information must remain intact.
*/

class Active {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////

    public $username    = "";
    public $path        = "";
    public $new_path    = "";
    public $actives     = "";

    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //

    //////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct(){
        $this->actives = getJSON('active.php');
    }

    //////////////////////////////////////////////////////////////////
    // List User's Active Files
    //////////////////////////////////////////////////////////////////

    public function ListActive(){
        $active_list = array();
        $tainted = FALSE;
        if($this->actives){
            foreach($this->actives as $active=>$data){
              if($data['username']==$this->username){
                if (file_exists(dirname(__FILE__)."/../../workspace".$data['path'])) {
                    $focus = isset($data['focus']) ? $data['focus'] : false;
                    $active_list[] = array('path'=>$data['path'], 'focus'=>$focus);
                } else {
                    unset($this->actives[$active]);
                    $tainted = TRUE;
                }
              }
            }
        }
        if ($tainted){
            saveJSON('active.php',$this->actives);
        }
        echo formatJSEND("success",$active_list);
    }

    //////////////////////////////////////////////////////////////////
    // Check File
    //////////////////////////////////////////////////////////////////

    public function Check(){
        $cur_users = array();
        foreach($this->actives as $active=>$data){
            if($data['username']!=$this->username && $data['path']==$this->path){
                $cur_users[] = $data['username'];
            }
        }
        if(count($cur_users)!=0){
            echo formatJSEND("error","Warning: File Currently Opened By: " . implode(", ",$cur_users));
        }else{
            echo formatJSEND("success");
        }
    }

    //////////////////////////////////////////////////////////////////
    // Add File
    //////////////////////////////////////////////////////////////////

    public function Add(){
        $process_add = true;
        foreach($this->actives as $active=>$data){
            if($data['username']==$this->username && $data['path']==$this->path){
                $process_add = false;
            }
        }
        if($process_add){
            $this->actives[] = array("username"=>$this->username,"path"=>$this->path);
            saveJSON('active.php',$this->actives);
            echo formatJSEND("success");
        }
    }

    //////////////////////////////////////////////////////////////////
    // Rename File
    //////////////////////////////////////////////////////////////////

    public function Rename(){
        $revised_actives = array();
        foreach($this->actives as $active=>$data){
            $revised_actives[] = array("username"=>$data['username'],"path"=>str_replace($this->path,$this->new_path,$data['path']));
        }
        saveJSON('active.php',$revised_actives);
        echo formatJSEND("success");
    }

    //////////////////////////////////////////////////////////////////
    // Remove File
    //////////////////////////////////////////////////////////////////

    public function Remove(){
        foreach($this->actives as $active=>$data){
            if($this->username==$data['username'] && $this->path==$data['path']){
                unset($this->actives[$active]);
            }
        }
        saveJSON('active.php',$this->actives);
        echo formatJSEND("success");
    }
    
    //////////////////////////////////////////////////////////////////
    // Notify Focus
    //////////////////////////////////////////////////////////////////

    public function NotifyFocus(){
        foreach($this->actives as $active=>$data){
            if($this->username==$data['username']){
                $this->actives[$active]['focus']=false;
                if($this->path==$data['path']){
                    $this->actives[$active]['focus']=true;
                }
            }
        }
        saveJSON('active.php',$this->actives);
        echo formatJSEND("success");
    }

}
