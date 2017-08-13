<?php

/**
 * Класс для работы с изображениями
 *
 * @author Павел
 */
class PK_ImageResize
{
    public function dump($str)
    {
        echo '<pre>'. print_r($str, true) .'</pre>';
    }
    public function dumpImg($img)
    {
        $this->dump(getimagesize($img));
    }
    
    /**
    * @param string $image - строка, представляющая путь до обрезаемого изображения
    * @param string $filePath - строка, представляющая путь к новому обрезанному изображению
    * @param int $width - ширина обрезанного изображения
    * @param int $height - высота обрезанного изображения
    */
    public function resizeCenter ($image, $filePath, $width, $height) 
    {
        if (($width < 0) || ($height < 0)) {
            return false;
        }
        //Определяем поддерживаемые типы
        $allowType = [1 => "gif", 2 => "jpeg", 3 => "png"];
        //Создаем переменные старых значений
        list($oldWidth, $oldHeight, $typeImgId) = getimagesize($image);
        
        if (!array_key_exists($typeImgId, $allowType)) {
            return false;
        }
        $ImageExtension = $allowType[$typeImgId];
        
        // Получаем название функции, соответствующую типу, для создания изображения
        $func = 'imagecreatefrom' . $ImageExtension;
        // Создаём дескриптор исходного изображения
        $InitialImageDescriptor = $func($image);
        
        // Определяем отображаемую область
        $lCroppedImageWidth = 0;
        $lCroppedImageHeight = 0;
        $lInitialImageCroppingX = 0;
        $lInitialImageCroppingY = 0;
        if ($width / $height > $oldWidth / $oldHeight) {
            $lCroppedImageWidth = floor($oldWidth);
            $lCroppedImageHeight = floor($oldWidth * $height / $width);
            $lInitialImageCroppingY = floor(($oldHeight - $lCroppedImageHeight) / 2);
        } else {
            $lCroppedImageWidth = floor($oldHeight * $width / $height);
            $lCroppedImageHeight = floor($oldHeight);
            $lInitialImageCroppingX = floor(($oldWidth - $lCroppedImageWidth) / 2);
        }
        
        // Создаём дескриптор для выходного изображения
        $NewImageDescriptor = imagecreatetruecolor($width, $height);
        imagecopyresampled($NewImageDescriptor, $InitialImageDescriptor, 0, 0, $lInitialImageCroppingX, $lInitialImageCroppingY, $width, $height, $lCroppedImageWidth, $lCroppedImageHeight);
        $func = 'image' . $ImageExtension;
        
        // сохраняем полученное изображение в указанный файл
        // Для сжатия изображения необходимо третьим параметром установить размер,
        // Если png от 0 до 9, если jpeg от 0 до 100
        return $func($NewImageDescriptor, $filePath);
    }
//    public function resizeCoord ($image, $size, $coord)
//    {
//        
//    }
}

// Применение
$newImg = $_SERVER['DOCUMENT_ROOT'] . "/img/newImg.png";
$call = new PK_ImageResize();
$call->resizeCenter('http://temp.loc/img/The_Witcher_3.png', $newImg, 500, 700);


//вывод в брайзер <img src="/img/newImg.png">