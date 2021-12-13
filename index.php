<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание</title>
    <link rel="stylesheet" href="./styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
</head>
<body>

    <?php
    
        // объявления всех переменный и присваивание им значение пустой строки


        // Код проверки формы. 
        // Принимает значение -1, если пользователь уже зарегестрирован
        // Принимает знчание 1, если проверка прошла успешно и такой email ещё не зарегестрирован

        $validation_code = 0;
        $logFile = "./log_file.txt";


        // Объявление переменных для хранения значений соответсвующих полей форм
        // и переменный, хранящих сообщения для ошибок для каждого поля

        $fName = $lName = $email = $pass = $rPass = "";
        $fNameErr = $lNameErr = $emailErr = $passErr = $rPassErr = "";

        // регулярное выражение для адреса электронной почты https://stackoverflow.com/questions/201323/how-can-i-validate-an-email-address-using-a-regular-expression

        $email_pattern = "/^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|'(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*')@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/";
        
        // И строка для проверки корректности введения имени
        $name_pattern = "/^[A-Za-z-']+$/";


        // Переменная для описания ошибки с случае, если пользователь с введённым email уже существует
        $unique_error = "";

        // Задать массив пользователей. В массиве должны присутствовать поля email, id, name

        $users = array(
            array("id" => 0, "email" => "uxio.taichi@yandex.ru", "name"=>"John"),
            array("id" => 1, "email" => "test2@gmail.com", "name" => "Rayan"),
            array("id" => 2, "email" => "test3@mail.ru", "name" => "Erwin"),
            array("id" => 3, "email" => "test4@mail.ru", "name" => "Mark"),
            array("id" => 4, "email" => "test5@yandex.ru", "name" => "Anthony"),
            array("id" => 5, "email" => "test6@yandex.ru", "name" => "Betty"),
            array("id" => 6, "email" => "test7@gmail.ru", "name" => "Alise"),
            array("id" => 7, "email" => "test8@outlook.com", "name" => "Ivan"),
        );

       
        // Функция, которая убирает все специальные символы, экранированныые символы и т.д.
        // Основная цель - защита от инъекций

        function validate_input($input_data){
            $input_data = trim($input_data); //delete tabs spaces on so on
            $input_data = stripslashes($input_data); // delete slashed symbols
            $input_data = htmlspecialchars($input_data);
            return $input_data;
        }

        if($_SERVER["REQUEST_METHOD"] == 'POST'){

            if(empty($_POST["firstname"])){
                $fNameErr = "Введите имя";
            }
            else{
                $fName = validate_input($_POST["firstname"]);

                if(!preg_match($name_pattern, $fName)){
                    $fNameErr = "Доступны только буквы  латинского алфавита";
                }
            }

            $lName = validate_input($_POST["lastname"]);

            if(!preg_match($name_pattern, $fName)){
                $lNameErr = "Доступны только буквы латинского алфавита";
            }


            if(empty($_POST["email"])){
                $emailErr = "Введить email";
            }
            else {
                $email = validate_input($_POST["email"]);
            
                if(!preg_match($email_pattern, $email)){
                    $emailErr = "Некорректный email";
                }
            }
 
            if(empty($_POST["password"])){
                $passErr = "Это поле необходимо заполнить";
            }
            else{
                $pass = validate_input($_POST["password"]);
            }

            $rPass = validate_input($_POST["repeat_password"]);

            if($rPass != $pass){
                $rPassErr = "Пароли не совпадают";             
            }


            // Проверка на наличие введённого email в массиве существующих пользователей

            if(empty($fNameErr) && empty($lNameErr) && empty($emailErr) && empty($passErr) && empty($rPassErr)){
                foreach($users as $user){
                    if($email == $user["email"]){
                        $unique_error = "Пользователь с таким email уже существует"; 
                    }
                }

                // Запись отчёта в файл

                $validation_msg = "";
        
                if(empty($unique_error)){
                    $validation_code = 1;
                    $validation_msg = "Пользователь не найден. Создан новый пользователь";
                }
                else{
                    $validation_code = -1;
                    $validation_msg = &$unique_error;
                }
        
                $date = date("Y-M-d H:i:s");

                
                $output_string = "$date : first_name: $fName, email: $email, validation_stauts: $validation_code ($validation_msg) \n";

                $fp = fopen("./log_file.txt", "a");
                fwrite($fp, $output_string);
                fclose($fp);

                if($validation_code == 1){
                    header("Location: http://localhost/testtask/success.html");
                }
            }
            
        }

    ?>
    

    <section class="main">
        <div class="main-content">
            <form id="main-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <label  for="firstname">Имя</label>
                <input id="firstname" type="text" name="firstname">
                <span class="error"> <?php echo $fNameErr?> </span>

                <label for="lastname">Фамилия</label>
                <input id="lastname" type="text" name="lastname">
                <span class="error"> <?php echo $lNameErr?> </span>

                <label for="email">email</label>
                <input id="email" type="email" name="email">
                <span class="error"> <?php echo $emailErr?> </span>
                <?php if(!empty($unique_error)){echo "<span class='error'> пользователь с таким email уже зерегестрирован </span>";}?>
 
                <label for="password">Пароль</label>
                <input id="password" type="password" name="password" autocomplete="on">
                <span class="error"> <?php echo $passErr?> </span>

                <label for="password">Повтор пароля</label>
                <input id="repeat_password" type="password" name="repeat_password" autocomplete="on">
                <span class="error"> <?php echo $rPassErr?> </span>
                
                <input type="submit" value="Отправить">
            </form>
        </div>

    </section>

    

    <script src="./main.js"></script>

</body>
</html>