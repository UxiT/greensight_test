<?php
        $validation_code = 0;
        $logFile = "./log_file.txt";

        // регулярное выражение для адреса электронной почты https://stackoverflow.com/questions/201323/how-can-i-validate-an-email-address-using-a-regular-expression

        $email_pattern = "/^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|'(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*')@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/";
        
        // И строка для проверки корректности введения имени
        // $name_pattern = "/^[A-Za-z-']+$/";


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

        // Массивы для хранения переменных

        $data= [];
        $errors = [];
        $unique = "";
        $msg = [];

        if (empty($_POST['firstname'])) {
            $errors['firstname'] = 'Введите имя';
        }
        else{
            $data['firstname'] = validate_input($_POST['firstname']);
        }
        
        if (empty($_POST['email'])) {
            $errors['email'] = 'Введите email';

        }else if(!preg_match($email_pattern, $_POST['email'])){
            $errors['email'] = 'Некорректный email';
        }
        else{
            $data['email'] = validate_input($_POST['email']);
        }
        
        if (empty($_POST['password'])) {
            $errors['passwrod'] = 'Введите пароль';
        }
        else{
            $data['password'] = validate_input($_POST['password']);
        }

        if(!empty($_POST["password"]) && !empty("repeat_password")){
            if($_POST["password"] != $_POST["repeat_password"]){
                $errors["repeat_password"] = "Пароли не совпадают";
            }
        }

        // Проверка на наличие введённого email в массиве существующих пользователей

        if(empty($errors)){
            foreach($users as $user){
                if($data["email"] == $user["email"]){
                    $errors["unique"] = "Пользователь с таким email уже существует"; 
                }
            }

            // Запись отчёта в файл

            $validation_msg = "";
        
            if(empty($errors["unique"])){
                $validation_code = 1;
                $validation_msg = "Пользователь не найден. Создан новый пользователь";
            }
            else{
                $validation_code = -1;
                $validation_msg = &$errors["unique"];
            }
        
            $date = date("Y-M-d H:i:s");

                
            $output_string = "$date : first_name: {$data['firstname']}, email: {$data['email']}, validation_stauts: $validation_code ($validation_msg) \n";

            $fp = fopen("./log_file.txt", "a");
            fwrite($fp, $output_string);
            fclose($fp);
        }

        if (!empty($errors)) {
            $msg['success'] = false;
            $msg['errors'] = $errors;
        } else {
            $msg['success'] = true;
            $msg['message'] = 'Success!';
        }

        echo json_encode($msg);
    ?>