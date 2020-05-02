<?php
date_default_timezone_set('Europe/Warsaw');
system('clear');
echo "Autor: Stalker".PHP_EOL;
# include files
require_once('inc/ts3admin.class.php');
require_once('inc/config.php');

# start connection to server
$ts = new query($cfg['conn']['ip_address'], $cfg['conn']['query_port']);
if($ts->getElement('success', $ts->connect())){
  echo "[->] Connected to server!".PHP_EOL;
  if($ts->getElement('success', $ts->login($cfg['conn']['query_login'], $cfg['conn']['query_passwd']))){
    echo "[->] Logged to server!".PHP_EOL;
    if($ts->getElement('success', $ts->selectServer($cfg['conn']['voice_port']))){
      echo "[->] Selected server!".PHP_EOL;
    }
    else{
      die("[<-] Error while selecting server!\n");
    }
    if(strpos($cfg['conn']['bot_name'], 'qBot') !== false){
      if($ts->getElement('success', $ts->setName($cfg['conn']['bot_name']))){
        echo "[->] Bot name changed to: ".$cfg['conn']['bot_name'].PHP_EOL;
      }
      else{
        die("[<-] Error while changing bot name!\n");
      }
    }
    else{
      die("[<-] The bot must have in his name phrase 'qBot'\n");
    }
    if($ts->getElement('success', $ts->clientMove($ts->getElement('data',$ts->whoAmI())['client_id'], $cfg['conn']['bot_channel_id']))){
      echo "[->] Channel changed to: ".$cfg['conn']['bot_channel_id'].PHP_EOL;
    }
  }
  else{
    die("Error while logging to server!\n");
  }

  # code
  while(1){

    if(empty($interval) || $interval < time()){

      $interval = time() + 3600;

      $data = json_decode(file_get_contents('cache/data.json'), true);

      foreach($ts->getElement('data', $ts->clientList()) as $client){
        $clients[] = $client['client_database_id'];
      }

      foreach($cfg['settings']['groups'] as $group => $channel){
        if(empty($data) || (time() - max(array_keys($data[$group]))) > 3600){
          $online = 0;
          foreach($ts->getElement('data', $ts->serverGroupClientList($group)) as $client){
            if(in_array($client['cldbid'], $clients)) $online++;
          }
          $data[$group][time()] = $online;
        }
      }

      file_put_contents('cache/data.json', json_encode($data));

      $data = json_decode(file_get_contents('cache/data.json'), true);

      $tab = $cfg['text']['days']['days'];

      foreach($cfg['settings']['groups'] as $group => $channel){
        # colors from hex to rgb
        list($r[1], $g[1], $b[1]) = sscanf($cfg['background']['hex_colour'], "#%02x%02x%02x");

        list($r[2], $g[2], $b[2]) = sscanf($cfg['scale']['lines_colour'], "#%02x%02x%02x");

        list($r[3], $g[3], $b[3]) = sscanf($cfg['chart_lines']['colour'], "#%02x%02x%02x");

        list($r[4], $g[4], $b[4]) = sscanf($cfg['scale']['text_colour'], "#%02x%02x%02x");

        list($r[5], $g[5], $b[5]) = sscanf($cfg['text']['days']['colour'], "#%02x%02x%02x");

        list($r[6], $g[6], $b[6]) = sscanf($cfg['text']['online']['colour'], "#%02x%02x%02x");

        list($r[7], $g[7], $b[7]) = sscanf($cfg['text']['title']['colour'], "#%02x%02x%02x");

        # create image and fill
        $im = imagecreate(500, 220);
        imagefill($im, 0, 0, imagecolorallocate($im, $r[1], $g[1], $b[1]));

        # horizontal
        $j = 0;
        $users = true;
        $scale = intval(max($data[$group])/4);

        if(max($data[$group]) >= 4){
          for($i=175; $i>=55-(120/max($data[$group])); $i=$i-(120/max($data[$group]))){
            if($j==0){
              imageline($im, 93, $i, 421, $i, imagecolorallocate($im, $r[2], $g[2], $b[2]));
              imagettftext($im, 9, 0, 60, 180, imagecolorallocate($im, $r[4], $g[4], $b[4]), $cfg['scale']['font'], 0);
            }
            elseif($j%intval(max($data[$group])/4) == 0 && $i > 55){
              imageline($im, 93, $i, 421, $i, imagecolorallocate($im, $r[2], $g[2], $b[2]));
              imagettftext($im, 9, 0, 60, $i+5, imagecolorallocate($im, $r[4], $g[4], $b[4]), $cfg['scale']['font'], $scale);
              $scale = $scale + intval(max($data[$group])/4);
            }
            $j++;
          }
          imageline($im, 93, 55, 421, 55, imagecolorallocate($im, $r[2], $g[2], $b[2]));
          imagettftext($im, 9, 0, 60, 58, imagecolorallocate($im, $r[4], $g[4], $b[4]), $cfg['scale']['font'], max($data[$group]));
        }

        elseif(max($data[$group]) < 4 && max($data[$group]) > 0){
          $j = 0;
          for($i=175; $i>=55-(120/max($data[$group])); $i=$i-(120/max($data[$group]))){
            if($i >= 55){

              imageline($im, 93, $i, 421, $i, imagecolorallocate($im, $r[2], $g[2], $b[2]));
              imagettftext($im, 9, 0, 60, $i, imagecolorallocate($im, $r[4], $g[4], $b[4]), $cfg['scale']['font'], $j);
              $j++;
            }
          }
          imageline($im, 93, 55, 421, 55, imagecolorallocate($im, $r[2], $g[2], $b[2]));

        }

        else{
          $users = false;
          imagettftext($im, 12, 0, 190, 120, imagecolorallocate($im, 255, 255, 255), $cfg['text']['days']['font'], 'Brak użytkownikow');
        }

      

        if($users){
          # vertical
          for($i=93; $i<503; $i=$i+82){
            imageline($im, $i, 55, $i, 175, imagecolorallocate($im, $r[2], $g[2], $b[2]));
          }

          # days
          $i = 0;
          $j = 85;
          while(1){

            if((((date('N', max(array_keys($data[$group]))))-4)+$i) <= 0){
              imagettftext($im, $cfg['text']['days']['size'], 0, $j, 205, imagecolorallocate($im, $r[5], $g[5], $b[5]), $cfg['text']['days']['font'], $tab[((((date('N', max(array_keys($data[$group]))))-4)+$i)+7)]);
            }
            else{
              imagettftext($im, $cfg['text']['days']['size'], 0, $j, 205, imagecolorallocate($im, $r[5], $g[5], $b[5]), $cfg['text']['days']['font'], $tab[(((date('N', max(array_keys($data[$group]))))-4)+$i)]);
            }

            if($i == 4){
              break;
            }
            $i++;
            $j = $j+82;
          }


        # uzupelnianie

          $i = 0;
          $j = 0;
          foreach($data[$group] as $index => $value){


            if($index-$i > 3600 && $i != 0){

              $j = intval(($index-$i)/3600);
              $k = $index-3600;
              while($j > 1){
                $array[$k] = 0;
                $j--;
                $k = $k-3600;
              }

              $array[$index] = $value;
            }
            else{
              $array[$index] = $value;
            }
            $i = $index;

          }


          # function
          $i = 93;
          $j = 0;
          foreach($array as $index => $value){
            if(max(array_keys($data[$group]))-$index < 345600){
              $table[] = $value;
            }
          }

          foreach($table as $index => $value){

            @imageline($im, $i, 175-(120/max($data[$group])*$value), $i+(82/24), 175-(120/max($data[$group])*$table[$index+1]), imagecolorallocate($im, $r[3], $g[3], $b[3]));
            $i = $i+(82/24);

          }

          imagettftext($im, $cfg['text']['online']['size'], 90, 30, 150, imagecolorallocate($im, $r[6], $g[6], $b[6]), $cfg['text']['online']['font'], $cfg['text']['online']['text']);

          imagettftext($im, $cfg['text']['title']['size'], 0, 175, 30, imagecolorallocate($im, $r[7], $g[7], $b[7]), $cfg['text']['title']['font'], $cfg['text']['title']['text']);

        }



        foreach(scandir($cfg['settings']['path']) as $file){
          if(strpos($file, $group."_") !== false){
            unlink($cfg['settings']['path'].'/'.$file);
          }
        }
        $name = $group.'_'.time();
        imagepng($im, $cfg['settings']['path'].'/'.$name.'.png');

        $replace = str_replace('[chart]', '[img]'.$cfg['settings']['url'].'/'.$name.'.png[/img]', $cfg['settings']['desc']);

        $ts->channelEdit($channel, array('channel_description' => $replace));
      }
    }


    sleep(1);
  }
}
else{
  die("Error while connecting to server!\n");
}
