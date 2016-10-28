<?php
/**
 * CFileHelper class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Sharin\Library\Org\Utils\Yii;

/**
 * CFileHelper provides a set of helper methods for common file system operations.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.utils
 * @since 1.0
 */
class CFileHelper
{
    protected static $mimes = array(
        'ai'=>'application/postscript',
        'aif'=>'audio/x-aiff',
        'aifc'=>'audio/x-aiff',
        'aiff'=>'audio/x-aiff',
        'asc'=>'text/plain',
        'au'=>'audio/basic',
        'avi'=>'video/x-msvideo',
        'bcpio'=>'application/x-bcpio',
        'bin'=>'application/octet-stream',
        'bmp'=>'image/bmp',
        'c'=>'text/plain',
        'cc'=>'text/plain',
        'ccad'=>'application/clariscad',
        'cdf'=>'application/x-netcdf',
        'class'=>'application/octet-stream',
        'cpio'=>'application/x-cpio',
        'cpt'=>'application/mac-compactpro',
        'csh'=>'application/x-csh',
        'css'=>'text/css',
        'dcr'=>'application/x-director',
        'dir'=>'application/x-director',
        'dms'=>'application/octet-stream',
        'doc'=>'application/msword',
        'drw'=>'application/drafting',
        'dvi'=>'application/x-dvi',
        'dwg'=>'application/acad',
        'dxf'=>'application/dxf',
        'dxr'=>'application/x-director',
        'eps'=>'application/postscript',
        'etx'=>'text/x-setext',
        'exe'=>'application/octet-stream',
        'ez'=>'application/andrew-inset',
        'f'=>'text/plain',
        'f90'=>'text/plain',
        'fli'=>'video/x-fli',
        'flv'=>'video/x-flv',
        'gif'=>'image/gif',
        'gtar'=>'application/x-gtar',
        'gz'=>'application/x-gzip',
        'h'=>'text/plain',
        'hdf'=>'application/x-hdf',
        'hh'=>'text/plain',
        'hqx'=>'application/mac-binhex40',
        'htm'=>'text/html',
        'html'=>'text/html',
        'ice'=>'x-conference/x-cooltalk',
        'ief'=>'image/ief',
        'iges'=>'model/iges',
        'igs'=>'model/iges',
        'ips'=>'application/x-ipscript',
        'ipx'=>'application/x-ipix',
        'jpe'=>'image/jpeg',
        'jpeg'=>'image/jpeg',
        'jpg'=>'image/jpeg',
        'js'=>'application/x-javascript',
        'kar'=>'audio/midi',
        'latex'=>'application/x-latex',
        'lha'=>'application/octet-stream',
        'lsp'=>'application/x-lisp',
        'lzh'=>'application/octet-stream',
        'm'=>'text/plain',
        'man'=>'application/x-troff-man',
        'me'=>'application/x-troff-me',
        'mesh'=>'model/mesh',
        'mid'=>'audio/midi',
        'midi'=>'audio/midi',
        'mif'=>'application/vnd.mif',
        'mime'=>'www/mime',
        'mov'=>'video/quicktime',
        'movie'=>'video/x-sgi-movie',
        'mp2'=>'audio/mpeg',
        'mp3'=>'audio/mpeg',
        'mpe'=>'video/mpeg',
        'mpeg'=>'video/mpeg',
        'mpg'=>'video/mpeg',
        'mpga'=>'audio/mpeg',
        'ms'=>'application/x-troff-ms',
        'msh'=>'model/mesh',
        'nc'=>'application/x-netcdf',
        'oda'=>'application/oda',
        'pbm'=>'image/x-portable-bitmap',
        'pdb'=>'chemical/x-pdb',
        'pdf'=>'application/pdf',
        'pgm'=>'image/x-portable-graymap',
        'pgn'=>'application/x-chess-pgn',
        'png'=>'image/png',
        'pnm'=>'image/x-portable-anymap',
        'pot'=>'application/mspowerpoint',
        'ppm'=>'image/x-portable-pixmap',
        'pps'=>'application/mspowerpoint',
        'ppt'=>'application/mspowerpoint',
        'ppz'=>'application/mspowerpoint',
        'pre'=>'application/x-freelance',
        'prt'=>'application/pro_eng',
        'ps'=>'application/postscript',
        'qt'=>'video/quicktime',
        'ra'=>'audio/x-realaudio',
        'ram'=>'audio/x-pn-realaudio',
        'ras'=>'image/cmu-raster',
        'rgb'=>'image/x-rgb',
        'rm'=>'audio/x-pn-realaudio',
        'roff'=>'application/x-troff',
        'rpm'=>'audio/x-pn-realaudio-plugin',
        'rtf'=>'text/rtf',
        'rtx'=>'text/richtext',
        'scm'=>'application/x-lotusscreencam',
        'set'=>'application/set',
        'sgm'=>'text/sgml',
        'sgml'=>'text/sgml',
        'sh'=>'application/x-sh',
        'shar'=>'application/x-shar',
        'silo'=>'model/mesh',
        'sit'=>'application/x-stuffit',
        'skd'=>'application/x-koan',
        'skm'=>'application/x-koan',
        'skp'=>'application/x-koan',
        'skt'=>'application/x-koan',
        'smi'=>'application/smil',
        'smil'=>'application/smil',
        'snd'=>'audio/basic',
        'sol'=>'application/solids',
        'spl'=>'application/x-futuresplash',
        'src'=>'application/x-wais-source',
        'step'=>'application/STEP',
        'stl'=>'application/SLA',
        'stp'=>'application/STEP',
        'sv4cpio'=>'application/x-sv4cpio',
        'sv4crc'=>'application/x-sv4crc',
        'swf'=>'application/x-shockwave-flash',
        't'=>'application/x-troff',
        'tar'=>'application/x-tar',
        'tcl'=>'application/x-tcl',
        'tex'=>'application/x-tex',
        'texi'=>'application/x-texinfo',
        'texinfo'=>'application/x-texinfo',
        'tif'=>'image/tiff',
        'tiff'=>'image/tiff',
        'tr'=>'application/x-troff',
        'tsi'=>'audio/TSP-audio',
        'tsp'=>'application/dsptype',
        'tsv'=>'text/tab-separated-values',
        'txt'=>'text/plain',
        'unv'=>'application/i-deas',
        'ustar'=>'application/x-ustar',
        'vcd'=>'application/x-cdlink',
        'vda'=>'application/vda',
        'viv'=>'video/vnd.vivo',
        'vivo'=>'video/vnd.vivo',
        'vrml'=>'model/vrml',
        'wav'=>'audio/x-wav',
        'wrl'=>'model/vrml',
        'xbm'=>'image/x-xbitmap',
        'xlc'=>'application/vnd.ms-excel',
        'xll'=>'application/vnd.ms-excel',
        'xlm'=>'application/vnd.ms-excel',
        'xls'=>'application/vnd.ms-excel',
        'xlw'=>'application/vnd.ms-excel',
        'xml'=>'application/xml',
        'xpm'=>'image/x-xpixmap',
        'xwd'=>'image/x-xwindowdump',
        'xyz'=>'chemical/x-pdb',
        'zip'=>'application/zip',
    );

    /**
     * Returns the extension name of a file path.
     * For example, the path "path/to/something.php" would return "php".
     * @param string $path the file path
     * @return string the extension name without the dot character.
     * @since 1.1.2
     */
    public static function getExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Copies a directory recursively as another.
     * If the destination directory does not exist, it will be created.
     * @param string $src the source directory
     * @param string $dst the destination directory
     * @param array $options options for directory copy. Valid options are:
     * <ul>
     * <li>fileTypes: array, list of file name suffix (without dot). Only files with these suffixes will be copied.</li>
     * <li>exclude: array, list of directory and file exclusions. Each exclusion can be either a name or a path.
     * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
     * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
     * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
     * </li>
     * <li>level: integer, recursion depth, default=-1.
     * Level -1 means copying all directories and files under the directory;
     * Level 0 means copying only the files DIRECTLY under the directory;
     * level N means copying those directories that are within N levels.
     * </li>
     * </ul>
     */
    public static function copyDirectory($src,$dst,$options=array())
    {
        $fileTypes=array();
        $exclude=array();
        $level=-1;
        extract($options);
        self::copyDirectoryRecursive($src,$dst,'',$fileTypes,$exclude,$level,$options);
    }

    /**
     * Returns the files found under the specified directory and subdirectories.
     * @param string $dir the directory under which the files will be looked for
     * @param array $options options for file searching. Valid options are:
     * <ul>
     * <li>fileTypes: array, list of file name suffix (without dot). Only files with these suffixes will be returned.</li>
     * <li>exclude: array, list of directory and file exclusions. Each exclusion can be either a name or a path.
     * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
     * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
     * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
     * </li>
     * <li>level: integer, recursion depth, default=-1.
     * Level -1 means searching for all directories and files under the directory;
     * Level 0 means searching for only the files DIRECTLY under the directory;
     * level N means searching for those directories that are within N levels.
     * </li>
     * </ul>
     * @return array files found under the directory. The file list is sorted.
     */
    public static function findFiles($dir,$options=array())
    {
        $fileTypes=array();
        $exclude=array();
        $level=-1;
        extract($options);
        $list=self::findFilesRecursive($dir,'',$fileTypes,$exclude,$level);
        sort($list);
        return $list;
    }

    /**
     * Copies a directory.
     * This method is mainly used by {@link copyDirectory}.
     * @param string $src the source directory
     * @param string $dst the destination directory
     * @param string $base the path relative to the original source directory
     * @param array $fileTypes list of file name suffix (without dot). Only files with these suffixes will be copied.
     * @param array $exclude list of directory and file exclusions. Each exclusion can be either a name or a path.
     * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
     * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
     * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
     * @param integer $level recursion depth. It defaults to -1.
     * Level -1 means copying all directories and files under the directory;
     * Level 0 means copying only the files DIRECTLY under the directory;
     * level N means copying those directories that are within N levels.
     * @param array $options additional options. The following options are supported:
     * newDirMode - the permission to be set for newly copied directories (defaults to 0777);
     * newFileMode - the permission to be set for newly copied files (defaults to the current environment setting).
     */
    protected static function copyDirectoryRecursive($src,$dst,$base,$fileTypes,$exclude,$level,$options)
    {
        if(!is_dir($dst))
            mkdir($dst);
        if(isset($options['newDirMode']))
            @chmod($dst,$options['newDirMode']);
        else
            @chmod($dst,0777);
        $folder=opendir($src);
        while(($file=readdir($folder))!==false)
        {
            if($file==='.' || $file==='..')
                continue;
            $path=$src.DIRECTORY_SEPARATOR.$file;
            $isFile=is_file($path);
            if(self::validatePath($base,$file,$isFile,$fileTypes,$exclude))
            {
                if($isFile)
                {
                    copy($path,$dst.DIRECTORY_SEPARATOR.$file);
                    if(isset($options['newFileMode']))
                        @chmod($dst.DIRECTORY_SEPARATOR.$file, $options['newFileMode']);
                }
                else if($level)
                    self::copyDirectoryRecursive($path,$dst.DIRECTORY_SEPARATOR.$file,$base.'/'.$file,$fileTypes,$exclude,$level-1,$options);
            }
        }
        closedir($folder);
    }

    /**
     * Returns the files found under the specified directory and subdirectories.
     * This method is mainly used by {@link findFiles}.
     * @param string $dir the source directory
     * @param string $base the path relative to the original source directory
     * @param array $fileTypes list of file name suffix (without dot). Only files with these suffixes will be returned.
     * @param array $exclude list of directory and file exclusions. Each exclusion can be either a name or a path.
     * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
     * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
     * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
     * @param integer $level recursion depth. It defaults to -1.
     * Level -1 means searching for all directories and files under the directory;
     * Level 0 means searching for only the files DIRECTLY under the directory;
     * level N means searching for those directories that are within N levels.
     * @return array files found under the directory.
     */
    protected static function findFilesRecursive($dir,$base,$fileTypes,$exclude,$level)
    {
        $list=array();
        $handle=opendir($dir);
        while(($file=readdir($handle))!==false)
        {
            if($file==='.' || $file==='..')
                continue;
            $path=$dir.DIRECTORY_SEPARATOR.$file;
            $isFile=is_file($path);
            if(self::validatePath($base,$file,$isFile,$fileTypes,$exclude))
            {
                if($isFile)
                    $list[]=$path;
                else if($level)
                    $list=array_merge($list,self::findFilesRecursive($path,$base.'/'.$file,$fileTypes,$exclude,$level-1));
            }
        }
        closedir($handle);
        return $list;
    }

    /**
     * Validates a file or directory.
     * @param string $base the path relative to the original source directory
     * @param string $file the file or directory name
     * @param boolean $isFile whether this is a file
     * @param array $fileTypes list of file name suffix (without dot). Only files with these suffixes will be copied.
     * @param array $exclude list of directory and file exclusions. Each exclusion can be either a name or a path.
     * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
     * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
     * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
     * @return boolean whether the file or directory is valid
     */
    protected static function validatePath($base,$file,$isFile,$fileTypes,$exclude)
    {
        foreach($exclude as $e)
        {
            if($file===$e || strpos($base.'/'.$file,$e)===0)
                return false;
        }
        if(!$isFile || empty($fileTypes))
            return true;
        if(($type=pathinfo($file, PATHINFO_EXTENSION))!=='')
            return in_array($type,$fileTypes);
        else
            return false;
    }

    /**
     * Determines the MIME type of the specified file.
     * This method will attempt the following approaches in order:
     * <ol>
     * <li>finfo</li>
     * <li>mime_content_type</li>
     * <li>{@link getMimeTypeByExtension}, when $checkExtension is set true.</li>
     * </ol>
     * @param string $file the file name.
     * @param string $magicFile name of a magic database file, usually something like /path/to/magic.mime.
     * This will be passed as the second parameter to {@link http://php.net/manual/en/function.finfo-open.php finfo_open}.
     * This parameter has been available since version 1.1.3.
     * @param boolean $checkExtension whether to check the file extension in case the MIME type cannot be determined
     * based on finfo and mime_content_type. Defaults to true. This parameter has been available since version 1.1.4.
     * @return string the MIME type. Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeType($file,$magicFile=null,$checkExtension=true)
    {
        if(function_exists('finfo_open'))
        {
            $options=defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $info=$magicFile===null ? finfo_open($options) : finfo_open($options,$magicFile);

            if($info && ($result=finfo_file($info,$file))!==false)
                return $result;
        }

        if(function_exists('mime_content_type') && ($result=mime_content_type($file))!==false)
            return $result;

        return $checkExtension ? self::getMimeTypeByExtension($file) : null;
    }

    /**
     * Determines the MIME type based on the extension name of the specified file.
     * This method will use a local map between extension name and MIME type.
     * @param string $file the file name.
     * @param string $magicFile the path of the file that contains all available MIME type information.
     * If this is not set, the default 'system.utils.mimeTypes' file will be used.
     * This parameter has been available since version 1.1.3.
     * @return string the MIME type. Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeTypeByExtension($file,$magicFile=null)
    {
        static $extensions;
        if($extensions===null)
            $extensions=$magicFile===null ? self::$mimes : $magicFile;
        if(($ext=pathinfo($file, PATHINFO_EXTENSION))!=='')
        {
            $ext=strtolower($ext);
            if(isset($extensions[$ext]))
                return $extensions[$ext];
        }
        return null;
    }
}