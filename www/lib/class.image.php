<?php
class image {

    var $app;
    var $cacheFolder;
    var $cacheFolder_orig;

    public function __construct($app) {
      $this->app = $app;
      $this->cacheFolder = $this->app->Conf->WFuserdata . '/dms/' . $this->app->Conf->WFdbname . '/cache';
      $this->cacheFolder_orig = $this->cacheFolder;
    }

  /**
   * @param int         $id
   * @param int         $newwidth
   * @param int         $newheight
   * @param bool        $upscale
   * @param null|string $typ
   *
   * @return bool|false|string
   */
    public function scaledPicByFileId($id, $newwidth, $newheight, $upscale = false, $typ = null) {
        $this->cacheFolder = $this->cacheFolder_orig;
        $filename[] = $id;
        $filename[] = $newwidth;
        $filename[] = $newheight;

        $filename = implode('_', $filename);
        if (!file_exists($this->cacheFolder . '/' . $filename)) {
          $this->cacheFolder = $this->app->erp->CreateDMSPath($this->cacheFolder_orig, $filename, true);
        }
        if (is_file($this->cacheFolder . '/' . $filename)
          && filesize($this->cacheFolder . '/' . $filename) === 0) {
          @unlink($this->cacheFolder . '/' . $filename);
        }
      
        if (!file_exists($this->cacheFolder . '/' . $filename)) {

            if (is_dir($this->cacheFolder) !== true
              && !mkdir($this->cacheFolder,0755,true) && !is_dir($this->cacheFolder)) {
              return '';
            }
            $path = $this->app->erp->GetDateiPfadVersion($id);
            if(!file_exists($path)) {
              return '';
            }
            $str = file_get_contents($path);

            $manipulator = new ImageManipulator($str);
            $type = mime_content_type($path);
            $manipulator->resample($newwidth, $newheight, true, $upscale);

            /*
            $width  = $manipulator->getWidth();
            $height = $manipulator->getHeight();

            $centreX = round($width / 2);
            $centreY = round($height / 2);

            $x1 = $centreX - ($newwidth/2); // 200 / 2
            $y1 = $centreY - ($newheight/2); // 130 / 2

            $x2 = $centreX + ($newwidth/2); // 200 / 2
            $y2 = $centreY + ($newheight/2); // 130 / 2
            $manipulator->crop($x1, $y1, $x2, $y2);
            */
            
            if($typ === 'jpg') {
              $type = 'image/jpg';
            }
            if($typ === 'gif') {
              $type = 'image/gif';
            }
            if($typ === 'png') {
              $type = 'image/png';
            }
            
            switch($type)
            {
              case 'image/jpg':
              case 'image/jpeg':
                $typ = IMAGETYPE_JPEG;
              break;
              case 'image/png':
                $typ = IMAGETYPE_PNG;
              break;
              case 'image/gif':
                $typ = IMAGETYPE_GIF;
              break;
            }
            
            $manipulator->save($this->cacheFolder . '/' . $filename, $typ);
        }

        return file_get_contents($this->cacheFolder .'/' . $filename);
    }
}


class ImageManipulator
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var resource
     */
    protected $image;

    /**
     * Image manipulator constructor
     * 
     * @param string $file OPTIONAL Path to image file or image data as string
     * @return void
     */
    public function __construct($file = null)
    {
        if (null !== $file) {
            if (is_file($file)) {
                $this->setImageFile($file);
            } else {
                $this->setImageString($file);
            }
        }
    }

    /**
     * Set image resource from file
     * 
     * @param string $file Path to image file
     * @return ImageManipulator for a fluent interface
     * @throws InvalidArgumentException
     */
    public function setImageFile($file)
    {
        if (!(is_readable($file) && is_file($file))) {
            throw new InvalidArgumentException("Image file $file is not readable");
        }

        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }

        list ($this->width, $this->height, $type) = getimagesize($file);

        switch ($type) {
            case IMAGETYPE_GIF  :
                $this->image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_JPEG :
                $this->image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG  :
                $this->image = imagecreatefrompng($file);
                break;
            default             :
                throw new InvalidArgumentException("Image type $type not supported");
        }

        return $this;
    }
    
    /**
     * Set image resource from string data
     * 
     * @param string $data
     * @return ImageManipulator for a fluent interface
     * @throws RuntimeException
     */
    public function setImageString($data)
    {
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }
        if (!$this->image = imagecreatefromstring($data)) {
            throw new RuntimeException('Cannot create image from data string');
        }
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        return $this;
    }

    /**
     * Resamples the current image
     *
     * @param int  $width                New width
     * @param int  $height               New height
     * @param bool $constrainProportions Constrain current image proportions when resizing
     * @return ImageManipulator for a fluent interface
     * @throws RuntimeException
     */
    public function resample($width, $height, $constrainProportions = true, $upscale = false, $keepformat = false)
    {
      if (!is_resource($this->image)) {
        throw new RuntimeException('No image set');
      }
      if($keepformat)
      {
        $facx = $height / $this->height;
        $facy = $width / $this->width;
        $offsetx = 0;
        $offsety = 0;
        if($facx > $facy)
        {
          $fac = $facy;
        }else{
          $fac = $facx;
        }
        if(!$upscale && $fac > 1)
        {
          $fac = 1;
        }
        
        $offsetx = ($width - $fac * $this->width)/2;
        $offsety = ($height - $fac * $this->height)/2;
        $temp = imagecreatetruecolor($width, $height);
        imagealphablending($temp, false);
        imagesavealpha($temp,true);
        $transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
        imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);
        imagecopyresampled($temp, $this->image, $offsetx, $offsety, 0, 0, $fac * $this->width, $fac * $this->height, $this->width, $this->height);
        return $this->_replace($temp);
      }
      
      
      if($upscale || $width < $this->width || $height < $this->height)
      {
        if ($constrainProportions) {
          if ($this->height >= $this->width) {
            $width  = round($height / $this->height * $this->width);
          } else {
            $height = round($width / $this->width * $this->height);
          }
        }
        $temp = imagecreatetruecolor($width, $height);
        imagealphablending($temp, false);
        imagesavealpha($temp,true);
        $transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
        imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);
        imagecopyresampled($temp, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        return $this->_replace($temp);
      }else{
        $temp = imagecreatetruecolor($width, $height);
        /*$white = imagecolorallocate($temp, 255, 255, 255);
        imagefill($temp, 0, 0, $white);*/
        imagealphablending($temp, false);
        imagesavealpha($temp,true);
        $transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
        imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);
        imagecopyresampled($temp, $this->image, round(($width-$this->width)/2), round(($height-$this->height)/2), 0, 0, $this->width, $this->height, $this->width, $this->height);
        return $this->_replace($temp);          
      }
    }
    
    /**
     * Enlarge canvas
     * 
     * @param int   $width  Canvas width
     * @param int   $height Canvas height
     * @param array $rgb    RGB colour values
     * @param int   $xpos   X-Position of image in new canvas, null for centre
     * @param int   $ypos   Y-Position of image in new canvas, null for centre
     * @return ImageManipulator for a fluent interface
     * @throws RuntimeException
     */
    public function enlargeCanvas($width, $height, array $rgb = array(), $xpos = null, $ypos = null)
    {
        if (!is_resource($this->image)) {
            throw new RuntimeException('No image set');
        }
        
        $width = max($width, $this->width);
        $height = max($height, $this->height);
        
        $temp = imagecreatetruecolor($width, $height);
        if (count($rgb) == 3) {
            $bg = imagecolorallocate($temp, $rgb[0], $rgb[1], $rgb[2]);
            imagefill($temp, 0, 0, $bg);
        }
        
        if (null === $xpos) {
            $xpos = round(($width - $this->width) / 2);
        }
        if (null === $ypos) {
            $ypos = round(($height - $this->height) / 2);
        }
        
        imagecopy($temp, $this->image, (int) $xpos, (int) $ypos, 0, 0, $this->width, $this->height);
        return $this->_replace($temp);
    }
    
    /**
     * Crop image
     * 
     * @param int|array $x1 Top left x-coordinate of crop box or array of coordinates
     * @param int       $y1 Top left y-coordinate of crop box
     * @param int       $x2 Bottom right x-coordinate of crop box
     * @param int       $y2 Bottom right y-coordinate of crop box
     * @return ImageManipulator for a fluent interface
     * @throws RuntimeException
     */
    public function crop($x1, $y1 = 0, $x2 = 0, $y2 = 0)
    {
        if (!is_resource($this->image)) {
            throw new RuntimeException('No image set');
        }
        if (is_array($x1) && 4 == count($x1)) {
            list($x1, $y1, $x2, $y2) = $x1;
        }
        
        $x1 = max($x1, 0);
        $y1 = max($y1, 0);
        
        $x2 = min($x2, $this->width);
        $y2 = min($y2, $this->height);
        
        $width = $x2 - $x1;
        $height = $y2 - $y1;
        
        $temp = imagecreatetruecolor($width, $height);
        imagecopy($temp, $this->image, 0, 0, $x1, $y1, $width, $height);
        
        return $this->_replace($temp);
    }
    
    /**
     * Replace current image resource with a new one
     * 
     * @param resource $res New image resource
     * @return ImageManipulator for a fluent interface
     * @throws UnexpectedValueException
     */
    protected function _replace($res)
    {
        if (!is_resource($res)) {
            throw new UnexpectedValueException('Invalid resource');
        }
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }
        $this->image = $res;
        $this->width = imagesx($res);
        $this->height = imagesy($res);
        return $this;
    }
    
    /**
     * Save current image to file
     * 
     * @param string $fileName
     * @return void
     * @throws RuntimeException
     */
    public function save($fileName, $type = IMAGETYPE_JPEG)
    {
        $dir = dirname($fileName);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new RuntimeException('Error creating directory ' . $dir);
            }
        }
        
        try {
            switch ($type) {
                case IMAGETYPE_GIF  :
                    if (!imagegif($this->image, $fileName)) {
                        throw new RuntimeException;
                    }
                    break;
                case IMAGETYPE_PNG  :
                    if (!imagepng($this->image, $fileName)) {
                        throw new RuntimeException;
                    }
                    break;
                case IMAGETYPE_JPEG :
                default             :
                    if (!imagejpeg($this->image, $fileName, 95)) {
                        throw new RuntimeException;
                    }
            }
        } catch (Exception $ex) {
            throw new RuntimeException('Error saving image file to ' . $fileName);
        }
    }

    /**
     * Returns the GD image resource
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->image;
    }

    /**
     * Get current image resource width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get current image height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
