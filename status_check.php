<?php require_once('connect.php'); ?>
<!DOCTYPE html>
<html>
  <head>
  </head>
  <body>
  <?php
    if (!empty($_GET)){      
      if (isset($_GET['current_survey'])){
        $sql_current_survey = 'SELECT current_survey FROM status WHERE current_survey != 0';
        $result_current_survey = $conn->query($sql_current_survey);
        if ($result_current_survey->num_rows != 0){
          $current_survey_id = $result_current_survey->fetch_assoc()['current_survey'];
        }
        
        if ($_GET['current_survey'] != $current_survey_id){
          echo '<script>alert("Опрос был закрыт. Страница будет перезагружена. Ответ не будет сохранён."); parent.location.reload();</script>';
        }
      } else {
        die('Direct access not permitted.');
      }
    } else {
      die('Direct access not permitted.');
    }
  ?>
  </body>
</html>