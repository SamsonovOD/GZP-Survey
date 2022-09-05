<?php
  if (!isset($access_control)){
    die('Direct access not permitted.');
  }
  
  require_once('connect.php');
  
  function remove_question($conn, $id){
    $sql_remove_question = 'DELETE FROM answers WHERE question_id = '.$id;
    if ($conn->query($sql_remove_question) != TRUE) {
      echo 'Error: '.$sql_remove_question.'<br>'.$conn->error;
    }
    $sql_remove_question = 'DELETE FROM questions WHERE question_id = '.$id;
    if ($conn->query($sql_remove_question) != TRUE) {
      echo 'Error: '.$sql_remove_question.'<br>'.$conn->error;
    }        
  }
  
  if (!empty($_POST)) {
    if (isset($_POST['form_name'])){
      if ($_POST['form_name'] == 'register'){
        $login = $_POST['login'];
        $username = $_POST['username'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        
        if (isset($_POST['admin_confirm'])){
          $admin_level = $_POST['admin_confirm'];
        } else {
          $admin_level = "0";
        }
        
        if ($password == $password_confirm){
          $sql_user_check = 'SELECT login FROM users WHERE login="'.$login.'"';          
          $result_user_check = $conn->query($sql_user_check);
          
          if ($result_user_check->num_rows == 0){
            $password = hash('sha256', $password);
            $sql_register = 'INSERT INTO users (login, username, age, gender, password, admin_level) VALUES ("'.$login.'", "'.$username.'", "'.$age.'", "'.$gender.'", "'.$password.'", '.$admin_level.')';

            if ($conn->query($sql_register) === TRUE) {
              echo 'Регистрация успешна.';
            } else {
              echo 'Error: '.$sql_register.'<br>'.$conn->error;
            }
          } else {
            echo('<script>alert("Пользователь уже существует!")</script>'); 
          }
        } else {
          echo('<script>alert("Пароли не совпадают!")</script>'); 
        } 
      } else if ($_POST['form_name'] == 'login') {
        $login = $_POST['login'];
        $password = hash('sha256', $_POST['password']);

        if (isset($_POST['remember'])){
          $remember_time = 60*60;
        } else {
          $remember_time = 60;
        }

        $sql_login = 'SELECT username, admin_level FROM users WHERE login="'.$login.'" AND password="'.$password.'"';
        $result_login = $conn->query($sql_login);
        if ($result_login->num_rows != 0){
          $user_data = $result_login->fetch_assoc();
          setcookie('username', $user_data['username'], time()+$remember_time, "/");
          $_SESSION['username'] = $user_data['username'];
          setcookie('level', $user_data['admin_level'], time()+$remember_time, "/");
          $_SESSION['level'] = $user_data['admin_level'];
        } else {
          echo('<script>alert("Пользователь не найден!")</script>'); 
        }
      } else if ($_POST['form_name'] == 'answer'){
        if (!empty($_SESSION)){
          if (isset($_SESSION['username'])){                
            $sql_user_check = 'SELECT user_id, login FROM users WHERE username="'.$_SESSION['username'].'"';
            $result_user_check = $conn->query($sql_user_check);
            
            if ($result_user_check->num_rows != 0) {
              $user_id = $result_user_check->fetch_assoc()['user_id'];              
              $answers = $_POST;
              unset($answers['form_name'], $answers['survey_id']);
              foreach($answers as $key => &$answer){
                $sql_answer = 'INSERT INTO responses (user_id, survey_id, question_id, answers) VALUES (';
                $sql_answer .= $user_id.', ';
                $sql_answer .= $_POST['survey_id'].', ';
                $sql_answer .= str_replace('answer_', '', $key).', ';
                if (is_array($answer)){
                  $sql_answer .= '"'.implode("; ", $answer).'"';
                } else {
                  $sql_answer .= '"'.$answer.'"';
                }
                if (str_contains($sql_answer, 'Checkbox_None, ')) { 
                  $sql_answer = str_replace('Checkbox_None, ', '', $sql_answer);
                }
                $sql_answer .= ')';
                if ($conn->query($sql_answer) != TRUE) {
                  echo 'Error: '.$sql_answer.'<br>'.$conn->error;
                }
              }
            } else{
              echo '<h1>Пользователь не найден!</h1>';
            }
          }
        }
      } else if ($_POST['form_name'] == 'active_survey'){
        $sql_set_survey = 'UPDATE status SET current_survey='.$_POST['survey_select'];
        if ($conn->query($sql_set_survey) != TRUE) {
          echo 'Error: '.$sql_set_survey.'<br>'.$conn->error;
        }
      } else if ($_POST['form_name'] == 'new_survey'){
        $sql_new_survey = 'INSERT INTO surveys (title) VALUES ("'.$_POST['new_survey'].'")';
        if ($conn->query($sql_new_survey) != TRUE) {
          echo 'Error: '.$sql_new_survey.'<br>'.$conn->error;
        }
      } else if ($_POST['form_name'] == 'new_question'){
        $sql_new_question = 'INSERT INTO questions (description, survey_id, type) VALUES ("'.$_POST['new_question'].'", "'.$_POST['survey_id'].'", "'.$_POST['question_type'].'")';
        if ($conn->query($sql_new_question) != TRUE) {
          echo 'Error: '.$sql_new_question.'<br>'.$conn->error;
        }
      } else if ($_POST['form_name'] == 'new_answer'){
        $sql_new_answer = 'INSERT INTO answers (text, question_id) VALUES ("'.$_POST['new_answer'].'", "'.$_POST['question_id'].'")';
        if ($conn->query($sql_new_answer) != TRUE) {
          echo 'Error: '.$sql_new_answer.'<br>'.$conn->error;
        }
      } else if ($_POST['form_name'] == 'remove_answer'){
        $sql_remove_answer = 'DELETE FROM answers WHERE answer_id = '.$_POST['id'];
        if ($conn->query($sql_remove_answer) != TRUE) {
          echo 'Error: '.$sql_remove_answer.'<br>'.$conn->error;
        }
      } else if ($_POST['form_name'] == 'remove_question'){
        remove_question($conn, $_POST['id']);
      } else if ($_POST['form_name'] == 'remove_survey'){
        $sql_get_questions = 'SELECT question_id FROM questions WHERE survey_id = '.$_POST['id'];
        $result_questions = $conn->query($sql_get_questions);
        if ($result_questions->num_rows != 0){
          while($surveys_row = $result_questions->fetch_assoc()){
            remove_question($conn, $surveys_row['question_id']);
          }
        }
        $sql_remove_survey = 'DELETE FROM surveys WHERE survey_id = '.$_POST['id'];
        if ($conn->query($sql_remove_survey) != TRUE) {
          echo 'Error: '.$sql_remove_survey.'<br>'.$conn->error;
        }
      } else {
        echo '<h1>Неизвестная формат!</h1>';
      }
    } else {
      echo '<h1>Неизвестный запрос!</h1>';
    }
  }
  
  function MainPageLoginDisplay(){
    if(isset($_SESSION['username'])){
      echo '<div class="profile">';
      echo '<hr>';
      echo '<b>Добро пожаловать,<br>'.$_SESSION['username'].'!</b><br>';
      $e_str = "'profile.php';";
      echo '<button onclick="ShowProfile();">Профиль</button><br>';
      echo '<button onclick="Logout();">Выйти</button>';
      echo '</div>';
      echo '<script>document.getElementById("login_form_window").remove();</script>';
    }
  }
?>