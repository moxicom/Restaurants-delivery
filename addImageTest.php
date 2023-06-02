<?php
$db_hostname = 'localhost';
$db_database = 'php_learning';
$db_username = 'root';
$db_password = '';

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, был ли загружен файл без ошибок
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Проверяем тип файла
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        if (strpos($mime, 'image') === 0) {
            // Создаем уникальное имя файла
            $filename = uniqid() . '_' . $_FILES['image']['name'];

            // Сохраняем загруженный файл на удаленный сервер
            $remoteDir = 'uploaded_images/'; // Замените на нужный путь на удаленном сервере
            $remotePath = $remoteDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $remotePath)) {
                // Сохраняем имя файла в базе данных
                // Для этого используйте связь с MySQL, например, с помощью расширения mysqli

                // Показываем пользователю сообщение об успешной загрузке
                echo 'File uploaded successfully.';
            } else {
                echo 'Error uploading file.';
            }
        } else {
            echo 'Invalid file type.';
        }
    } else {
        echo 'Error uploading file.';
    }
}
?>

<form action="addImageTest.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="image">
    <input type="submit" value="Upload">
</form>
