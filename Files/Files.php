<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 18.07.16
 * Time: 18:56
 */
namespace isv\Files;
use isv\IS;
/**
 * This class is Deprecated. User Fs component from ISV component repository.
 * @version 1.0
 * Class Files
 * @package isv\Files
 * @deprecated
 */
class Files
{
    /**
     * Upload pictures for product
     * @param $postFile
     * @param $productID
     * @return bool|string
     */
    public function uploadProduct($postFile, $productID)
    {
        $imagesDir = ROOTDIR.DIRSEP.IS::app()->getConfig('config')['publicDir'].DIRSEP.'img_tmp'.DIRSEP.'products';
        if(!is_dir($imagesDir.DIRSEP.$productID)) {
            mkdir($imagesDir.DIRSEP.$productID, 0777);
        }
        $types = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
        ];
        $extension = isset($types[$_FILES[$postFile]['tmp_name']]) ? $types[$_FILES[$postFile]['tmp_name']] : 'png';
        $name = 'upload_'.date('dmyHis').'_'.rand(10, 100).'.'.$extension;
        $fullPath = $imagesDir.DIRSEP.$productID.DIRSEP.$name;
        $webPath = 'img_tmp'.DIRSEP.'products'.DIRSEP.$productID.DIRSEP.$name;
        if( move_uploaded_file($_FILES[$postFile]['tmp_name'], $fullPath) )
            return $webPath;
        else
            return false;
    }

    /**
     * scandir() implementation
     * @param $directory
     * @return array
     */
    public static function scanDir($directory)
    {
        $path = ROOTDIR.DIRSEP.$directory;
        $dir = scandir($path);
        $out = [];
        $out['baseDir'] = str_replace(DIRSEP, '*', $directory);
        $pathPrewDir = str_replace(ROOTDIR, '',  realpath($path.DIRSEP.'..'.DIRSEP));
        $out['prewDir'] = str_replace(DIRSEP, '*',  $pathPrewDir);
        if(strpos($out['prewDir'], '*') === 0)
            $out['prewDir'] = substr($out['prewDir'], 1);

        foreach($dir as $item)
        {
            if($item !=='.' && $item !=='..')
            {
                if(is_dir($path.DIRSEP.$item))
                    $out['content'][$item] = 'dir';
                if(is_file($path.DIRSEP.$item))
                    $out['content'][$item] = 'file';
            }
        }
        asort($out['content']);
        return $out;
    }

}