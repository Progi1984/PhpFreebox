<?php
  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  //$oFreebox->_debug  = true;

  $resStorage = $oFreebox->storage_list();
  foreach ($resStorage as $key => $item){
    echo '<ul>';
      echo '<li>#'.$key.'</li>';
      echo '<li>ID : '.$item['partitions'][0]['partition_id'].'</li>';
      echo '<li>Label : '.$item['partitions'][0]['label'].'</li>';
      echo '<li>Etat : '.$item['partitions'][0]['state'].'</li>';
    echo '<ul>';
  }
