<?php

/**
 * Класс для работы с изображениями
 *
 * @author Павел
 */
class GetImageResize
{
    protected $allowType = [1 => "gif", 2 => "jpeg", 3 => "png"]; //Определяем поддерживаемые типы
    protected $lCroppedImageWidth = 0;
    protected $lCroppedImageHeight = 0;
    protected $lInitialImageCroppingX = 0;
    protected $lInitialImageCroppingY = 0;

    public function dump($str)
    {
        echo '<pre>'. print_r($str, true) .'</pre>';
    }
    public function dumpImg($img)
    {
        $this->dump(getimagesize($img));
    }
    
    protected function resizeImgExact ($width, $height, $oldWidth, $oldHeight)
    {
        // Определяем отображаемую область

        if ($width / $height > $oldWidth / $oldHeight) 
        {
            $this->lCroppedImageWidth = floor($oldWidth);
            $this->lCroppedImageHeight = floor($oldWidth * $height / $width);
            $this->lInitialImageCroppingY = floor(($oldHeight - $this->lCroppedImageHeight) / 2);
        } else {
            $this->lCroppedImageWidth = floor($oldHeight * $width / $height);
            $this->lCroppedImageHeight = floor($oldHeight);
            $this->lInitialImageCroppingX = floor(($oldWidth - $this->lCroppedImageWidth) / 2);
        }
    }
    /**
    * @param string $image - строка, представляющая путь до обрезаемого изображения
    * @param string $filePath - строка, представляющая путь к новому обрезанному изображению
    * @param array $arSize - массив с длиной и шириной обрезаемого изображения
    * @param array $constResize - константа отвечающая за тип масштабирования, 1 - resizeImgExact
    */
    public function resizeCenter ($image, $filePath, $arSize, $constResize = 1) 
    {
        $width = $arSize["width"];
        $height = $arSize["height"];
        if (($width < 0) || ($height < 0)) {
            throw new Exception('Значения ширины и высоты не могут быть отрицательными');
        }
        //Создаем переменные старых значений
        list($oldWidth, $oldHeight, $typeImgId) = getimagesize($image);
        
        if (!array_key_exists($typeImgId, $this->allowType)) 
        {
            throw new Exception('Данный тип файла не поддерживается');
        }
        $ImageExtension = $this->allowType[$typeImgId];
        
        // Получаем название функции, соответствующую типу, для создания изображения
        $func = 'imagecreatefrom' . $ImageExtension;
        // Создаём дескриптор исходного изображения
        $InitialImageDescriptor = $func($image);
        
        if($constResize == 1)
        {
            $this->resizeImgExact($width, $height, $oldWidth, $oldHeight);
        } else {
            throw new Exception('Такой константы не существует');
        }
        
        // Создаём дескриптор для выходного изображения
        $NewImageDescriptor = imagecreatetruecolor($width, $height);
        imagecopyresampled($NewImageDescriptor, $InitialImageDescriptor, 0, 0, $this->lInitialImageCroppingX, $this->lInitialImageCroppingY, $width, $height, $this->lCroppedImageWidth, $this->lCroppedImageHeight);
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
$newImg = $_SERVER['DOCUMENT_ROOT'] . "/img/newImg.jpg";
$call = new GetImageResize();
$call->resizeCenter('http://temp.loc/img/test.jpg', $newImg, ["width" => 800, "height" => 600], 1);
//вывод в браузер <img src="/img/newImg.jpg">

