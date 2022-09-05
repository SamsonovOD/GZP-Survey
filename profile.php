<?php require_once('connect.php'); ?>
<!DOCTYPE html>
<html>

<?php
  $access_control = True;
  require_once('request.php');
?>

<head>
  <meta charset="UTF-8">
  <title>Опросник ВНИИГАЗ</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php
  function drawTable($full, $user_id, $conn){    
    $sql_responses = 'SELECT responses.survey_id, title, responses.question_id, description, type, user_id, answers FROM responses ';
    $sql_responses .= 'INNER JOIN surveys ON responses.survey_id = surveys.survey_id ';
    $sql_responses .= 'INNER JOIN questions ON responses.question_id = questions.question_id ';
    if(!$full){
      $sql_responses .= 'WHERE user_id = '.$user_id.' ';
    }
    $sql_responses .= 'ORDER BY survey_id, responses.question_id ';
    $result_responses = $conn->query($sql_responses);
    if ($result_responses->num_rows != 0){
      $first_row = True;
      $current_title = "";
      if($full){
        echo '<table class="full_table">';
      } else {
        echo '<table>';
      }
      while($response_row = $result_responses->fetch_assoc()){
        if($first_row){
          $current_title = $response_row['title'];
        }
        if($current_title != $response_row['title']){          
          $first_row = True;
          $current_title = $response_row['title'];
          echo '</table>';
          if($full){
            echo '<table class="full_table">';
          } else {
            echo '<table>';
          }
        }
        if($first_row){
          echo '<caption><b>'.$response_row['title'].'</b></caption>';
          echo '<tr><th>Вопрос</th>';
          echo '<th>Тип</th>';
          if($full){
            echo '<th>Пользователь</th>';
          }
          echo '<th>Ответ</th></tr>';    
          $first_row = False;
        }
        echo '<tr>';
        echo '<td>'.$response_row['description'].'</td>';
        echo '<td>'.$response_row['type'].'</td>';
        if($full){
          echo '<td>'.$response_row['user_id'].'</td>';
        }
        echo '<td>'.$response_row['answers'].'</td>';
        echo '</tr>';
      }
      echo '</table>';
    } else {
      echo "<p>Нет сохранённых ответов.</p>";
    }
  }
  
  function addButton($btn_name, $type, $parent){
    echo '<li class="question_list"><form method="POST">';
    echo '<input type="hidden" name="form_name" value="'.$btn_name.'">';
    echo '<input type="text" name="'.$btn_name.'" placeholder="Название '.$type.'а" required>';
    if ($btn_name == "new_question"){
      echo '<input type="hidden" name="survey_id" value="'.$parent.'">';
      echo '<select name="question_type">';
      echo '<option value="radio">radio</option>';
      echo '<option value="checkbox">checkbox</option>';
      echo '<option value="textbox">textbox</option>';
      echo '<option value="arrange">arrange</option>';
      echo '</select>';
    }
    if ($btn_name == "new_answer"){
      echo '<input type="hidden" name="question_id" value="'.$parent.'">';
    }
    echo '<input type="submit" value="Добавить новый '.$type.'">';
    echo '</form></li>';
  }
  
  function removeButton($btn_name, $id){
    echo '<form class="line_btn" method="POST">';
    echo '<input type="hidden" name="form_name" value="'.$btn_name.'">';
    echo '<input type="hidden" name="id" value="'.$id.'">';
    echo '<input type="submit" value="Удалить">';
    echo '</form>';
  }
  
  function getCurretnSurvey($conn){
    $sql_current_survey = 'SELECT current_survey FROM status';
    $result_current_survey = $conn->query($sql_current_survey);
    
    if ($result_current_survey->num_rows != 0){
      $current_survey_id = $result_current_survey->fetch_assoc()['current_survey'];
      return $current_survey_id;
    } else {
      return 0;
    }
  }

  if (!empty($_SESSION)){
    if (isset($_SESSION['username'])){
      $username = $_SESSION['username'];
      $user_level = $_SESSION['level'];
      
      $sql_user_check = 'SELECT * FROM users WHERE username="'.$username.'"';          
      $result_user_check = $conn->query($sql_user_check);      
      if ($result_user_check->num_rows != 0){
        $user_data = $result_user_check->fetch_assoc();
        $user_id = $user_data['user_id'];

        echo 'Имя: '.$user_data['username'].'<br>';
        echo 'Возраст: '.$user_data['age'].'<br>';
        echo 'Пол: '.$user_data['gender'].'<br>';
        echo '<hr>';
        
        echo '<h1>Ваши ответы:</h1>';
        drawTable(False, $user_id, $conn);
        echo '<hr>';
        
        if ($user_level == 1){
          $sql_surveys = "SELECT * FROM surveys";
          $result_surveys = $conn->query($sql_surveys);
          if ($result_surveys->num_rows != 0){
            echo '<h1>Активный опрос.</h1>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="form_name" value="active_survey">';
            echo '<select name="survey_select">';
            echo '<option value="0">Отключить.</option>';
            while($surveys_row = $result_surveys->fetch_assoc()){
              echo '<option value="'.$surveys_row['survey_id'].'"';
              if (getCurretnSurvey($conn) == $surveys_row['survey_id']){
                echo ' selected="selected" ';
              }
              echo '>'.$surveys_row['title'].'</option>';
            }
            echo '</select>';
            echo '<input type="submit" value="Сохранить">';
            echo '</form>';
          }
          
          echo '<h1>Все ответы</h1>';
          drawTable(True, $user_id, $conn);
          
          echo '<h1>Статистика</h1>';
          echo '<div id="canvasContainer">';
          echo '<canvas id="graphCanvas"></canvas>';
          echo '</div>';
          
          echo '<h1>Все вопросы</h1>';
          $sql_surveys = 'SELECT * FROM surveys';
          $result_surveys = $conn->query($sql_surveys);
          if ($result_surveys->num_rows != 0){
            while($surveys_row = $result_surveys->fetch_assoc()){
              removeButton("remove_survey", $surveys_row['survey_id']);
              echo '<li class="question_list">'.$surveys_row['title'];
              echo '<ul>';
              
              $sql_questions = 'SELECT * FROM questions WHERE survey_id = '.$surveys_row['survey_id'];
              $result_questions = $conn->query($sql_questions);
              if ($result_questions->num_rows != 0){
                while($questions_row = $result_questions->fetch_assoc()){
                  removeButton("remove_question", $questions_row['question_id']);
                  echo '<li class="question_list">'.$questions_row['description'].' ('.$questions_row['type'].')';
                  echo '<ul>';
                  
                  $sql_answers = 'SELECT * FROM answers WHERE question_id = '.$questions_row['question_id'];$result_answers = $conn->query($sql_answers);
                  if ($result_answers->num_rows != 0){
                    while($answers_row = $result_answers->fetch_assoc()){
                      removeButton("remove_answer", $answers_row['answer_id']);
                      echo '<li class="question_list">'.$answers_row['text'];
                      echo '</li>';
                    }
                  }
                  if ($questions_row['type'] != "textbox"){
                    addButton('new_answer', 'ответ', $questions_row['question_id']);
                  }
                  echo '</ul>';
                  echo '</li>';
                }
              }
              addButton('new_question', 'вопрос', $surveys_row['survey_id']);
              echo '</ul>';
              echo '</li>';
            }
          }
          addButton('new_survey', 'опрос', '0');
        }        
      } else {
        echo '<h1>Пользователь не найден.</h1>';
      }
    } else {
      echo '<h1>Пожалуйста, войдите.</h1>';
    }
  } else {
    echo '<h1>Пожалуйста, войдите.</h1>';
  }
?>
</body>
  <script src="script.js"></script>
  <script>
    displayStats();
  </script>
</html>
<?php $conn->close(); ?>