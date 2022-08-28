<?php require_once('connect.php'); ?>
<!DOCTYPE html>
<html>

<?php
  if (empty($_GET)){
    die('Direct access not permitted.');
  } else if ($_GET["load_sub"] != "yes"){
    die('Direct access not permitted.');
  }
  $access_control = True;
  
  require_once('request.php');

  function drawRadioQuestion($question_id, $result_answers){    
    $a_num = 0;
    while($row_answers = $result_answers->fetch_assoc()){
      $a_num++;
      echo '<br>';
      echo '<input type="radio" id="answer_'.$question_id.'_'.$a_num.'" name="answer_'.$question_id.'" value="'.$row_answers['text'].'" required>';
      echo '<label for="answer_'.$question_id.'_'.$a_num.'">'.$row_answers['text'].'</label>';
    }
  }
  
  function drawCheckboxQuestion($question_id, $result_answers){    
    $a_num = 0;
    echo '<input type="hidden" id="answer_'.$question_id.'_'.$a_num.'" name="answer_'.$question_id.'[]" value="Checkbox_None">';
    while($row_answers = $result_answers->fetch_assoc()){
      $a_num++;
      echo '<br>';
      echo '<input type="checkbox" name="answer_'.$question_id.'[]" value="'.$row_answers['text'].'">';
      echo '<label for="answer_'.$question_id.'_'.$a_num.'">'.$row_answers['text'].'</label>';
    }
  }
  
  function drawArrangeQuestion($question_id, $result_answers){    
    $a_num = 0;
    echo '<ul class="draglist">';
    while($row_answers = $result_answers->fetch_assoc()){
      $a_num++;
      echo '<li class="insertbox" ondrop="onDrop(event)" ondragover="onDragOver(event)"></li>';
      echo '<li class="dragitem" draggable="true" ondragstart="onDragStart(event)" ondragend="onDragEnd(event)" id="answer_'.$question_id.'_'.$a_num.'"># '.$row_answers['text'];
      echo '<input type="hidden" name="answer_'.$question_id.'[]" value="'.$row_answers['text'].'">';
      echo '</li>';
    }
    echo '<li class="insertbox" ondrop="onDrop(event)" ondragover="onDragOver(event)"></li>';
    echo '</ul>';
  }
?>

<head>
  <meta charset="UTF-8">
  <title>Опросник ВНИИГАЗ</title>
  <link rel="stylesheet" href="style.css">
</head>
  
<body>
  <div class="survey">
<?php
  if (!empty($_SESSION)){
    if (isset($_SESSION['username'])){
      $sql_user_check = 'SELECT user_id, login FROM users WHERE username="'.$_SESSION['username'].'"';
      $result_user_check = $conn->query($sql_user_check);
      
      if ($result_user_check->num_rows != 0){
        $user_id = $result_user_check->fetch_assoc()['user_id'];
        $sql_current_survey = 'SELECT current_survey FROM status WHERE current_survey != 0';
        $result_current_survey = $conn->query($sql_current_survey);
        
        if ($result_current_survey->num_rows != 0){
          $current_survey_id = $result_current_survey->fetch_assoc()['current_survey'];
          echo '<iframe id="secret_survey_check" src="status_check.php?current_survey='.$current_survey_id .'" frameBorder="1" height="64" width="64"></iframe>';
          
          $sql_surveys = 'SELECT * FROM surveys WHERE survey_id = '.$current_survey_id;  
          $result_surveys = $conn->query($sql_surveys);
          if ($result_surveys->num_rows != 0){
            $current_survey_data = $result_surveys->fetch_assoc();
            echo '<h1>Тест '.$current_survey_data['survey_id'].'. '.$current_survey_data['title'].'</h1>';
          
            $sql_already_complete = 'SELECT * FROM responses WHERE survey_id = '.$current_survey_id.' AND user_id = '.$user_id;
            $result_already_complete = $conn->query($sql_already_complete);
            
            if ($result_already_complete->num_rows == 0){            
              $sql_questions = 'SELECT * FROM questions WHERE survey_id = '.$current_survey_id;
              $result_question = $conn->query($sql_questions);
              
              if ($result_question->num_rows != 0){
                $q_num = 0;
                echo '<form method="POST">';
                echo '<input type="hidden" name="form_name" value="answer">';
                echo '<input type="hidden" name="survey_id" value="'.$current_survey_id.'">';
                
                while($row_question = $result_question->fetch_assoc()){
                  $q_num++;
                  echo '<div class="question">';
                  echo '<b>Вопрос '.$q_num.': '.$row_question['description'].'</b><br>';
                  
                  $question_id = $row_question['question_id'];
                  if ($row_question['type'] != 'textbox'){
                    $sql_answers = "SELECT * FROM answers WHERE question_id = ".$question_id;
                    $result_answers = $conn->query($sql_answers);
                    
                    if ($result_answers->num_rows != 0){
                      switch($row_question['type']){
                        case 'radio': {
                          drawRadioQuestion($question_id, $result_answers);
                          break;
                        }
                        case 'checkbox': {
                          drawCheckboxQuestion($question_id, $result_answers);
                          break;
                        }
                        case 'arrange': {
                          drawArrangeQuestion($question_id, $result_answers);
                          break;
                        }
                      }
                    }
                  } else {
                    echo '<textarea class="textbox" name="answer_'.$question_id.'" required></textarea>';
                  }
                  echo '<hr>';
                  echo '</div>';
                }
                echo '<input type="submit" value="Отправить">';
                echo '</form>';
              } else {
                echo '<h1>В опросе нет вопросов!</h1>';
              }
            } else {
              echo '<h1>Вы уже прошли этот опрос.</h1>';
            }
          } else {
            echo '<h1>Указанный опрос не найден!</h1>';
          }
        } else {
          echo '<h1>В данный момент нет активных опросов.</h1>';
        }
      } else {
        echo '<h1>Пользователь не найден!</h1>';
      } 
    } else {
      echo '<h1>Пожалуйста, войдите.</h1>';
    }
  } else {
    echo '<h1>Пожалуйста, войдите.</h1>';
  }
?>
  </div>
  <script src="script.js"></script>
  <script>
    window.setInterval(function(){
      document.getElementById('secret_survey_check').src += '';
    }, 5000);
  </script>
</body>
</html>