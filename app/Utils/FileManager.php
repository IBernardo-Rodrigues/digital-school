<?php
namespace App\Utils;

class FileManager {
  private $name;
  private $extension;
  private $type;
  private $tmpName;
  private $error;
  private $size;

  public function __construct($file) {
    $this->type = $file['type'] ?? '';
    $this->tmpName = $file['tmp_name'] ?? '';
    $this->error = $file['error'] ?? '';
    $this->size = $file['size'] ?? '';

    $info = pathinfo($file['name']);
    $this->name = $info['filename'] ?? '';
    $this->extension = $info['extension'] ?? '';
  }
  
  public function upload($folder) {
    $dir = __DIR__."/../../files/$folder";
    
    $this->verifyError();
    $this->verifyExtension();
    
    $newName = $this->generateNewName();
    $extension = strlen($this->extension) ? "{$this->extension}" : '';

    $path = "$dir/$newName.$extension";
    $pathHTML = URL."/files/$folder/$newName.$extension";

    return move_uploaded_file($this->tmpName, $path) ? $pathHTML : false ;
  }

  public static function uploadBase64Image($encodedImg, $folder) {
    $decodedImg = base64_decode($encodedImg);
  
    $sizeBytes = (int)(strlen(rtrim($decodedImg, '=')) * 1);
    $sizeMb = ($sizeBytes / 1024) / 1024;

    if ($sizeMb > 2) {
      throw new \Exception("Envie uma imagem menor que 2MB", 400);
    }

    $imgType = explode("/", (new \finfo)->buffer($decodedImg, FILEINFO_MIME_TYPE))[1];
    $allowedExtensions = ['png', 'jpeg', 'jpg'];

    if (!in_array($imgType, $allowedExtensions)) throw new \Exception("Extensão não permitida", 400);

    $dir = __DIR__."/../../files/$folder";
    $fileName = time() .'-'. uniqid() .'-'. rand(100000, 999999) .".$imgType";

    $filePath = "$dir/$fileName";

    if (!fopen($filePath, 'w')) {
      throw new \Exception("Não foi possivel criar o arquivo", 1);
    }

    return file_put_contents($filePath, $decodedImg) ? URL."/files/modules/$fileName" : false;
  }

  public static function delete($httpFilePath) {
    $filePath = explode("/files/", $httpFilePath);
    $file = __DIR__ .'/../../files/'. end($filePath);
    
    if (file_exists($file)) {
      unlink($file);
      return true;
    }

    return false;
  }
  
  public function generateNewName() {
    return time().'-'.uniqid().'-'.rand(100000, 999999);
  }

  public function verifyError() {
    if ($this->error == 0) return false;

    switch ($this->error) {
      case 1:
        throw new \Exception("Use uma foto com no máximo 2MB", 400);
        break;
      case 2:
        throw new \Exception("Quantidade de arquivos excedida", 400);
        break;
      case 3:
        throw new \Exception("A imagem não foi completamente enviada", 400);
        break;
      case 4:
        throw new \Exception("Nenhuma imagem foi enviada", 400);
        break;
      case 6:
        throw new \Exception("Não foi possivel enviar a imagem, tente novamente", 400);
        break;
      case 7:
        throw new \Exception("Não foi possivel enviar a imagem, tente novamente", 400);
        break;
      case 8:
        throw new \Exception("Algo deu errado ao enviar a imagem, tente novamente", 400);
        break;
    }
  }

  public function verifyExtension() {
    $allowedExtensions = [
      'png',
      'jpeg',
      'jpg',
    ];

    if (!in_array($this->extension, $allowedExtensions)) {
      throw new \Exception("Extensão não permitida", 400);
    }

    return true;
  }
}