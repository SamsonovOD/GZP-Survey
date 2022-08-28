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
  <link rel="icon" type="image/x-icon" href="favicon.circ">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <br>
  <div class="header">
    <a href="https://vniigaz.gazprom.ru/"><img src="logo.jpg" height="80px"></img></a>
    <h1>Портал Анкетирования ВНИИГАЗ</h1>
    <a href="index.php">Вернуться на главную</a>.
  </div>

  <div class="page">
    <div id="main_page">
      <h1>Текущий опрос:</h1>
      <hr>
      <iframe class="survey_window" src="survey.php?load_sub=yes" frameBorder="0"></iframe>
    </div>
    
    <div id="login_bar">
      <div id="login_form_window">
      <h1>Вход</h1>
        <form class="login_form" method="POST">
          <input type="hidden" name="form_name" value="login">
          <label for="login">Логин / адрес электронной почты</label><br>
          <input type="text" name="login" required><br>
          <label for="password">Пароль</label><br>
          <input type="password" name="password" required><br>
          <input type="checkbox" id="login_remember" name="remember" value="1">
          <label for="remember">Запомнить логин</label><br>
          <input type="submit" value="Вход">
        </form>
        <a href="javascript:void(0);" onclick="ShowModal();">Регистрация</a>
      </div>
<?php MainPageLoginDisplay(); ?>
    </div>
  </div>
  
  <div id="register">
    <a href="javascript:void(0);" onclick="CloseModal();" style="float:right;">[X]</a>
    <form method="POST">
      <input type="hidden" name="form_name" value="register">
      <label for="login">Логин / адрес электронной почты</label><br>
      <input type="text" name="login" required><br>
      <label for="username">Имя</label><br>
      <input type="text" name="username" required><br>
      <label for="password">Пароль</label><br>
      <input type="password" name="password" required><br>
      <label for="password_confirm">Подвердить пароль</label><br>
      <input type="password" name="password_confirm" required><br>
      <input type="checkbox" name="admin_confirm" value="1">
      <label for="admin_confirm">Администратор (тестовый режим)</label><br>
      <label for="age">Возраст</label><br>
      <input type="number" name="age" min="10" max="100" required><br>
      <label for="gender">Пол</label>
      <select name="gender"><option value="М">М</option><option value="Ж">Ж</option></select><br>
      <input type="submit" value="Отправить">
    </form>
  </div>
  
  <div class="footer">
    <hr>
    <p>Самсонов Олег, Алексей Кулешин, Москва 2022  <a href="https://github.com/SamsonovOD/GZP-Task1-Survey">GitHub код</a></p>
  <div>
  <script src="script.js"></script>
  <script>
    function ShowProfile(){
      var main_page = document.getElementById("main_page");
      if (main_page != null){
        main_page.outerHTML = "<div id='profile_page'><h1>Профиль:</h1><hr><iframe class='profile_window' src='profile.php' frameBorder='0'></iframe></div>";
      }
    }
  </script>
</body>

</html>
<?php $conn->close(); ?>