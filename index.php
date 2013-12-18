<?php
/*
 * FileSystem Challange with ******
 * created on 24/10/2011 19:07 IST
 * last edit 18/12/2013 22:13 IST
 * elibyy 2011 (c)
 * ktnxbai
 * the concept is A Simple File System all in one page with no extra extenstions.
 * ************************************
 * TODO:
 * *****
 *  not secured :P
 * ************************************
 * Changes:
 * *******
 * added manual settings to __constructor
 * added custom path setting....
 * fixed wrong login shows empty page
 * bug fixes
 */
error_reporting(E_ERROR);

/**
 *
 * @author elibyy
 * @version 0.6b
 * @package FileManager
 *         
 */
class file_system{

    var $cur_path;
    var $settings;

    /**
     *
     * @param
     *        	array(
     *        	'path => your_path,
     *        	username => your user,
     *        	password => your password,
     *        	show_mime => 1/0,
     *        	show_actions =>1/0,
     *        	)
     * @since 0.6
     * @author elibyy
     */
    function __construct($settings){
        $this->settings = $settings;
        session_name("elibyy_FS");
        session_start();
        if(!isset($_SESSION['user'])){
            $this->login();
        }
        else{
            if(isset($_GET['logout']) && $_GET['logout'] == "TRUE"){
                $this->logout();
            }
            $file = isset($_GET['f']) ? urldecode($_GET['f']) : NULL;
            if($file == NULL){
                $this->load_folders($settings['path']);
            }
            echo $this->dl($file);
            echo $this->unzip_files($file);
            echo $this->zip_dir($file);
            $dir = is_dir($file) ? $this->del_dir($file) : $this->del_file($file);
        }
    }

    function file_mime($file){
        $file = pathinfo($file);
        $mimetype = array("bat" => "application/bat", "sys" => "application/octet-stream", "BAK" => "application/octet-stream", "lnk" => "application/x-ms-shortcut", "ai" => "application/postscript", "aif" => "audio/x-aiff", "aifc" => "audio/x-aiff", "aiff" => "audio/x-aiff", "asf" => "video/x-ms-asf", "asr" => "video/x-ms-asf", "asx" => "video/x-ms-asf", "au" => "audio/basic", "avi" => "video/x-msvideo", "axs" => "application/olescript", "bas" => "text/plain", "bcpio" => "application/x-bcpio",
            "bin" => "application/octet-stream", "bmp" => "image/bmp", "c" => "text/plain", "cat" => "application/vnd.ms-pkiseccat", "cdf" => "application/x-cdf", "cer" => "application/x-x509-ca-cert", "class" => "application/octet-stream", "clp" => "application/x-msclip", "cmx" => "image/x-cmx", "cod" => "image/cis-cod", "cpio" => "application/x-cpio", "crd" => "application/x-mscardfile", "crl" => "application/pkix-crl", "crt" => "application/x-x509-ca-cert", "csh" => "application/x-csh",
            "css" => "text/css", "dcr" => "application/x-director", "der" => "application/x-x509-ca-cert", "dir" => "application/x-director", "dll" => "application/x-msdownload", "dms" => "application/octet-stream", "doc" => "application/msword", "dot" => "application/msword", "dvi" => "application/x-dvi", "dxr" => "application/x-director", "eps" => "application/postscript", "etx" => "text/x-setext", "evy" => "application/envoy", "exe" => "application/octet-stream", "fif" => "application/fractals",
            "flr" => "x-world/x-vrml", "gif" => "image/gif", "gtar" => "application/x-gtar", "gz" => "application/x-gzip", "h" => "text/plain", "hdf" => "application/x-hdf", "hlp" => "application/winhlp", "hqx" => "application/mac-binhex40", "hta" => "application/hta", "htc" => "text/x-component", "htm" => "text/html", "html" => "text/html", "htt" => "text/webviewhtml", "ico" => "image/x-icon", "ief" => "image/ief", "iii" => "application/x-iphone", "ins" => "application/x-internet-signup",
            "isp" => "application/x-internet-signup", "jfif" => "image/pipeg", "jpe" => "image/jpeg", "jpeg" => "image/jpeg", "jpg" => "image/jpeg", "js" => "application/x-javascript", "latex" => "application/x-latex", "lha" => "application/octet-stream", "lsf" => "video/x-la-asf", "lsx" => "video/x-la-asf", "lzh" => "application/octet-stream", "m13" => "application/x-msmediaview", "m14" => "application/x-msmediaview", "m3u" => "audio/x-mpegurl", "man" => "application/x-troff-man",
            "mdb" => "application/x-msaccess", "me" => "application/x-troff-me", "mht" => "message/rfc822", "mhtml" => "message/rfc822", "mid" => "audio/mid", "mny" => "application/x-msmoney", "mov" => "video/quicktime", "movie" => "video/x-sgi-movie", "mp2" => "video/mpeg", "mp3" => "audio/mpeg", "mpa" => "video/mpeg", "mpe" => "video/mpeg", "mpeg" => "video/mpeg", "mpg" => "video/mpeg", "mpp" => "application/vnd.ms-project", "mpv2" => "video/mpeg", "ms" => "application/x-troff-ms",
            "mvb" => "application/x-msmediaview", "nws" => "message/rfc822", "oda" => "application/oda", "p10" => "application/pkcs10", "p12" => "application/x-pkcs12", "p7b" => "application/x-pkcs7-certificates", "p7c" => "application/x-pkcs7-mime", "p7m" => "application/x-pkcs7-mime", "p7r" => "application/x-pkcs7-certreqresp", "p7s" => "application/x-pkcs7-signature", "pbm" => "image/x-portable-bitmap", "pdf" => "application/pdf", "pfx" => "application/x-pkcs12", "pgm" => "image/x-portable-graymap",
            "pko" => "application/ynd.ms-pkipko", "pma" => "application/x-perfmon", "pmc" => "application/x-perfmon", "pml" => "application/x-perfmon", "pmr" => "application/x-perfmon", "pmw" => "application/x-perfmon", "pnm" => "image/x-portable-anymap", "pot" => "application/vnd.ms-powerpoint", "ppm" => "image/x-portable-pixmap", "pps" => "application/vnd.ms-powerpoint", "ppt" => "application/vnd.ms-powerpoint", "prf" => "application/pics-rules", "ps" => "application/postscript",
            "pub" => "application/x-mspublisher", "qt" => "video/quicktime", "ra" => "audio/x-pn-realaudio", "ram" => "audio/x-pn-realaudio", "ras" => "image/x-cmu-raster", "rgb" => "image/x-rgb", "rmi" => "audio/mid", "roff" => "application/x-troff", "rtf" => "application/rtf", "rtx" => "text/richtext", "scd" => "application/x-msschedule", "sct" => "text/scriptlet", "setpay" => "application/set-payment-initiation", "setreg" => "application/set-registration-initiation", "sh" => "application/x-sh",
            "shar" => "application/x-shar", "sit" => "application/x-stuffit", "snd" => "audio/basic", "spc" => "application/x-pkcs7-certificates", "spl" => "application/futuresplash", "src" => "application/x-wais-source", "sst" => "application/vnd.ms-pkicertstore", "stl" => "application/vnd.ms-pkistl", "stm" => "text/html", "svg" => "image/svg+xml", "sv4cpio" => "application/x-sv4cpio", "sv4crc" => "application/x-sv4crc", "t" => "application/x-troff", "tar" => "application/x-tar",
            "tcl" => "application/x-tcl", "tex" => "application/x-tex", "texi" => "application/x-texinfo", "texinfo" => "application/x-texinfo", "tgz" => "application/x-compressed", "tif" => "image/tiff", "tiff" => "image/tiff", "tr" => "application/x-troff", "trm" => "application/x-msterminal", "tsv" => "text/tab-separated-values", "txt" => "text/plain", "uls" => "text/iuls", "ustar" => "application/x-ustar", "vcf" => "text/x-vcard", "vrml" => "x-world/x-vrml", "wav" => "audio/x-wav",
            "wcm" => "application/vnd.ms-works", "wdb" => "application/vnd.ms-works", "wks" => "application/vnd.ms-works", "wmf" => "application/x-msmetafile", "wps" => "application/vnd.ms-works", "wri" => "application/x-mswrite", "wrl" => "x-world/x-vrml", "wrz" => "x-world/x-vrml", "xaf" => "x-world/x-vrml", "xbm" => "image/x-xbitmap", "xla" => "application/vnd.ms-excel", "xlc" => "application/vnd.ms-excel", "xlm" => "application/vnd.ms-excel", "xls" => "application/vnd.ms-excel",
            "xlt" => "application/vnd.ms-excel", "xlw" => "application/vnd.ms-excel", "xof" => "x-world/x-vrml", "xpm" => "image/x-xpixmap", "xwd" => "image/x-xwindowdump", "z" => "application/x-compress", "zip" => "application/zip", 'hqx' => 'application/mac-binhex40', 'cpt' => 'application/mac-compactpro', 'doc' => 'application/msword', 'bin' => 'application/macbinary', 'dms' => 'application/octet-stream', 'lha' => 'application/octet-stream', 'lzh' => 'application/octet-stream',
            'exe' => 'application/octet-stream', 'class' => 'application/octet-stream', 'psd' => 'application/octet-stream', 'so' => 'application/octet-stream', 'sea' => 'application/octet-stream', 'dll' => 'application/octet-stream', 'oda' => 'application/oda', 'pdf' => 'application/pdf', 'ai' => 'application/postscript', 'eps' => 'application/postscript', 'ps' => 'application/postscript', 'smi' => 'application/smil', 'smil' => 'application/smil', 'mif' => 'application/vnd.mif',
            'xls' => 'application/vnd.ms-excel', 'ppt' => 'application/vnd.ms-powerpoint', 'wbxml' => 'application/vnd.wap.wbxml', 'wmlc' => 'application/vnd.wap.wmlc', 'dcr' => 'application/x-director', 'dir' => 'application/x-director', 'dxr' => 'application/x-director', 'dvi' => 'application/x-dvi', 'gtar' => 'application/x-gtar', 'php' => 'application/x-httpd-php', 'php4' => 'application/x-httpd-php', 'php3' => 'application/x-httpd-php', 'phtml' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source', 'js' => 'application/x-javascript', 'swf' => 'application/x-shockwave-flash', 'sit' => 'application/x-stuffit', 'tar' => 'application/x-tar', 'tgz' => 'application/x-tar', 'xhtml' => 'application/xhtml+xml', 'xht' => 'application/xhtml+xml', 'zip' => 'application/zip', 'mid' => 'audio/midi', 'midi' => 'audio/midi', 'mpga' => 'audio/mpeg', 'mp2' => 'audio/mpeg', 'mp3' => 'audio/mpeg', 'aif' => 'audio/x-aiff', 'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff', 'ram' => 'audio/x-pn-realaudio', 'rm' => 'audio/x-pn-realaudio', 'rpm' => 'audio/x-pn-realaudio-plugin', 'ra' => 'audio/x-realaudio', 'rv' => 'video/vnd.rn-realvideo', 'wav' => 'audio/x-wav', 'bmp' => 'image/bmp', 'gif' => 'image/gif', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'jpe' => 'image/jpeg', 'png' => 'image/png', 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'css' => 'text/css', 'html' => 'text/html', 'htm' => 'text/html', 'shtml' => 'text/html',
            'txt' => 'text/plain', 'text' => 'text/plain', 'log' => 'text/plain', 'rtx' => 'text/richtext', 'rtf' => 'text/rtf', 'xml' => 'text/xml', 'xsl' => 'text/xml', 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg', 'mpe' => 'video/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'avi' => 'video/x-msvideo', 'movie' => 'video/x-sgi-movie', 'doc' => 'application/msword', 'word' => 'application/msword', 'xl' => 'application/excel', 'eml' => 'message/rfc822'
        );
        return $mime_type = isset($file['extension']) && isset($mimetype[$file['extension']]) ? $mimetype[$file['extension']] : "application/octet-stream";
    }

    function getsize($file){
        $size = filesize($file);
        $size = stristr($size, "-") ? 0 : $size;
        $units = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"
        );
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, ".", ",") . " " . $units[$power];
    }

    function load_folders($base_folder = NULL){
        // validate the dir path existant
        $folder = isset($_GET['path']) && $_GET['path'] != '' ? $_GET['path'] : $base_folder . "/";
        if(!stristr($folder, $base_folder)){
            $_SESSION['message'] = "No access to other folders";
            header("Location: http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?path=" . $base_folder);
        }
        if(stristr($folder, "//")){
            // verify no hook via //
            $_SESSION['message'] = "No access to //";
            header("Location: http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?path=" . $base_folder);
        }
        if($folder == "/"){
            // verify no hook via //
            $_SESSION['message'] = "No access to /";
            header("Location: http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?path=" . $base_folder);
        }
        if(stristr($folder, "..")){
            // verify no hook via ..
            $ref = str_replace("..", "", $folder);
            $_SESSION['message'] = "No access to ..";
            header("Location: http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?path=" . $base_folder);
        }
        if($folder == NULL){
            header("Location: http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "?path=/");
        }
        $this->cur_path = $folder;
        $logout = isset($_SESSION['user']) ? "\n\t<div id='logout'><a href='http://$_SERVER[SERVER_NAME]$_SERVER[SCRIPT_NAME]?logout=TRUE&path=/'>Log out</a></div>" : NULL;
        $message = $_SESSION['message'];
        echo $this->head();
        $title = "
	 <h1>Listing Directory $folder</h1>
	 <br />
	 $logout
	 <div style='color:red;text-align:center;'>
	 $message
	 </div>
	 <br />
	 <table border=\"1\">
	 <th>File</th>
	 <th>Size</th>";
        if($this->settings['show_mime'] == 1){
            $title .= "<th>Mime-Type</th>";
        }
        $title .= "<th>Last Changed</th>";

        if($this->settings['show_actions'] == 1){
            $title .= "<th>Actions</th>";
        }
        echo $title;
        $file_pic = "<img src=\"data:image/jpg;base64,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\" highet='22' width='27' />";
        $dir_pic = "<img src=\"data:image/jpg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAbABYDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9s/G2qX0Hie5js9SvbKRBHgKweLlR1RsjH0wa4HwF+1FceOLGe40vVLLUks52tbhGtwrwSqcMjABSD79CORkVu/E3WltPiLfRFsFVhP5xivkX9iHxEscnxABIKnxBIOfq9fz1nGcZhRzDFexrziozdkpO3xPpex99l2AoSoU/awT5kt0r7dz76+HPii48XeHPtdykKSiVoyIgQpxjnkn1orG/Z/vVv/Acjr0F5IP/AB1D/Wiv3Dh7EVK+WUK1Z3lKKbfd2PjcxpxhiqkIKyTdvvPC/wBsPxBe+GfF/imREurd30nzrOZQyF2W2I3RnuVcY46HFfLH7E2pRaL8HY7iQTR6nqV5PLfGZmMrurlBuDcggD8SSa/Rj9pTwlpvi74KeIk1G0juRaWM08DElXhcIfmVgQRxwcHkcHIro7/4deH9U0mDTLvRNKu7C1URQwT2qSpEo7DcDj69c89a+IzLgOeKxeInCqlztSV13bunr0ez19D3MNn6pUqcXC9lbftY+ZPhV+0bF8NLaSGBvtkt3GHuYDNvWBg7hG2Z+Qsp5P8AFsHpRX0Nof7OPgbwpez3Vj4Z0xJ7n5ZGlQzAjrgByQACOAMAZbHU5K+6yPAvA4GlhJWvFWdtr9Xst3r/AFc8HG4hV68qy6s//9k=\" />";
        if($handle = opendir($folder)){
            while(FALSE !== ($file = readdir($handle))){
                echo "\t<tr>\n";
                // don't show current dir
                if($file == ".")
                    continue;
                // don't show Doc&Set When Users dir exists (Win Vista/7/8)
                if($file == "Documents and Settings" && file_exists("/Users"))
                    continue;
                // Back One Level Button
                if($file === ".." && $this->cur_path != $base_folder){
                    $path = $path = str_replace(basename($_GET['path']), "", $this->cur_path);
                    $path = str_replace("//", "/", $path);
                    $go_back = "<td><a href=\"?path=$path\">&laquo; go back</a><br /></td>";
                    $go_back .= "<td>4KB</td>";
                    if($this->settings['show_mime'] == 1){
                        $go_back .= "<td>Folder</td>";
                    }
                    $go_back .= "<td>" . date("l, d, M, Y h:i", filemtime($path)) . "</td>";
                    if($this->settings['show_actions'] == 1){
                        $go_back .= "<td></td>\n";
                    }
                    echo $go_back;
                }
                if($file == "..")
                    continue;
                $path = isset($_GET['path']) ? $_GET['path'] : NULL;
                $last_path = "&path=" . $this->cur_path;
                $zip = $this->file_mime($this->cur_path . $file) == "application/zip" ? "/ <a href=\"?f=$this->cur_path$file&unzip=TRUE$last_path\">UnZip</a>" : NULL;
                // $edit = stristr($this->file_mime($this->cur_path.$file),"text") ? "/ <a href=\"?f=$this->cur_path$file&edit=TRUE\">Edit</a>":NULL;
                $name = substr($file, 0, 20);
                if(strlen($name) >= 20)
                    $name .= "..";
                if(!is_dir($this->cur_path . $file) && is_file($this->cur_path . $file)){
                    $row = "<td>$file_pic <a href='?f=$this->cur_path$file&d=TRUE' title='download $file'>$name</a></td>";
                    $row .= "<td>" . $this->getsize($this->cur_path . $file) . "</td>";
                    if($this->settings['show_mime'] == 1){
                        $row .= "<td>" . $this->file_mime($this->cur_path . $file) . "</td>";
                    }
                    $row .= "<td>" . date("l, d, M, Y h:i", filemtime($this->cur_path . $file)) . "</td>";
                    if($this->settings['show_actions'] == 1){
                        $row .= "<td><a href=\"?f=$this->cur_path$file&del=TRUE$last_path\">Delete</a>$zip</td>\n";
                    }
                    echo $row;
                }
                elseif(is_dir($this->cur_path . $file) && !is_file($this->cur_path . $file)){
                    if(opendir("$this->cur_path$file") == '')
                        continue;

                    $frow = "\t<td>$dir_pic <a href=\"?path=$path$file/\" title=\"Open $file folder\">$name</a></td>";
                    $frow .= "<td>" . 4096 / 1024 . "kb</td>";
                    if($this->settings['show_mime'] == 1){
                        $frow .= "<td>Folder</td>";
                    }

                    $frow .= "<td>" . date("l, d, M, Y h:i", filemtime($this->cur_path . $file)) . "</td>";
                    if($this->settings['show_actions'] == 1){
                        $frow .= "<td><a href=\"?f=$this->cur_path$file&del=TRUE&path=$this->cur_path$last_path\">Delete</a> / <a href=\"?f=$this->cur_path$file&zip=TRUE$last_path\" target='_blank'>Zip</a></td>\n";
                    }
                    echo $frow;
                }
            }
            echo "</tr>";
            closedir($handle);
            echo "</table>";
        }
        echo $this->footer();
    }

    function unzip_files($file){
        if(isset($_GET['unzip']) && $_GET['unzip'] == "TRUE" && isset($_GET['f'])){
            echo "Unziping $file\n<br />\n";
            $f = pathinfo($file);
            $zip = new ZipArchive();
            $res = $zip->open($file);
            if($res === TRUE){
                $zip->extractTo($f['dirname'] . "/" . $f['filename']);
                $zip->close();
                $path = $_GET['path'];
                echo $_SESSION['message'] = "successfully unziped " . $file;
                header("Location: ?path=" . $path);
            }
            else{
                echo $_SESSION['message'] = "failed to unzip " . $file;
                header("Location: ?path=" . $path);
            }
        }
    }

    function list_files_recursive($folder){
        $files = array();
        $handle = opendir($folder);
        while(FALSE !== ($file = readdir($handle))){
            if($file != '.' && $file != '..'){
                $path = $folder . '/' . $file;
                if(is_dir($path)){
                    // save the old data
                    $files = array_merge($files, $this->list_files_recursive($path));
                }
                else{
                    $files[] = $path;
                }
            }
        }
        return $files;
    }

    function zip_dir($dir, $exclude = array('.', '..')){
        // verifing zip request
        if(isset($_GET['zip']) && $_GET['zip'] == "TRUE" && isset($_GET['f'])){
            $exclude = array_flip($exclude);
            if(!is_dir($dir)){
                // isn't dir ? return the user to the main after 4 seconds of error
                return "<meta http-equiv=\"refresh\" content=\"4;url=?path=$dir\"> something went wrong while zipping  $folder, you are redirected to previous page for retry.";
            }
            // using a recursive function to list all files also in subfolders
            $file = $this->list_files_recursive($dir);
            // if folder empty return user to main page.
            if($file == array()){
                return "<meta http-equiv=\"refresh\" content=\"4;url=?path=$dir\"> Folder is Empty!";
            }
            $path_to = str_replace(basename($dir), "", $dir);
            // fix path to incase link is /path/to/ <--- with ending /
            $path_to = stristr($path_to, "//") ? str_replace("//", "/", $path_to) : $path_to;
            $zip = new ZipArchive();
            $zip_file = $path_to . basename($dir) . ".zip";
            $num = count($file) - 1;
            $num = $num == 0 ? $num + 1 : $num;
            $n = 0;
            while($n <= $num){
                if($zip->open($zip_file, ZipArchive::CREATE) === TRUE){
                    // increase preformance for long files
                    ini_set("max_execution_time", 120);
                    ini_set("memory_limit", "256M");
                    $value = $file[$n];
                    if($value != '' && $value != NULL)
                        if($path_to == "/"){
                            // path to file in the zip structure
                            $zip_value = substr($value, 1, strlen($value));
                        }
                        else{
                            // path to file in the zip structure
                            $zip_value = str_replace($path_to, "", $value);
                        }
                    $zip->addFile($value, $zip_value);
                    $n++;
                    if($n > $num){
                        $zip->setArchiveComment("Made By Elibyy FS on " . date("d/m/Y"));
                        $zip->close();
                        // pushing the file to the user and deleting on server :)
                        header('Content-Description: File Transfer');
                        header('Content-Type:' . $this->file_mime($zip_file));
                        header('Content-Disposition: attachment; filename=' . basename($zip_file));
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($zip_file));
                        ob_clean();
                        flush();
                        readfile($zip_file);
                        unlink($zip_file);
                        ini_set("max_execution_time", ini_get("max_execution_time"));
                        ini_set("memory_limit", ini_get("memory_limit"));
                    }
                }
                else{
                    $_SESSION['message'] = $zip_file . " was not creaeted!";
                    return FALSE;
                }
            }
        }
    }

    /* non-implemented functions;
      function create_htaccess(){
      not yet implemented;
      }
      function edit_files(){
      not yet implemented;
      }
     */

    function del_dir($directory, $empty = FALSE){
        // verifing deletation request
        if(isset($_GET['del']) && $_GET['del'] == "TRUE" && isset($_GET['f'])){
            if(substr($directory, -1) == '/'){
                $directory = substr($directory, 0, -1);
            }
            if(!file_exists($directory) || !is_dir($directory)){
                echo $_SESSION['message'] = $directory . " not deleted";
                $path = $_GET['path'];
                header("Location: ?path=" . $path);
                return FALSE;
            }
            elseif(is_readable($directory)){
                $handle = opendir($directory);
                while(FALSE !== ($item = readdir($handle))){
                    if($item != '.' && $item != '..'){
                        $path = $directory . '/' . $item;
                        if(is_dir($path)){
                            $this->del_dir($path);
                        }
                        else{
                            unlink($path);
                        }
                    }
                }
                closedir($handle);
                if($empty == FALSE){
                    if(!rmdir($directory)){
                        echo $_SESSION['message'] = $directory . " not deleted";
                        return FALSE;
                        $path = $_GET['path'];
                        header("Location: ?path=" . $path);
                    }
                }
            }
            echo $_SESSION['message'] = $directory . " deleted";
            $path = $_GET['path'];
            header("Location: ?path=" . $path);
            return TRUE;
        }
    }

    function del_file($file){
        // just deleting files after verification of request.
        if($_GET['f'] != NULL && $_GET['del'] == TRUE)
            if(unlink($file)){
                $_SESSION['message'] = $file . " deleted";
                $path = $_GET['path'];
                header("Location: ?path=" . $path);
                return TRUE;
            }
            else{
                $_SESSION['message'] = $file . " not deleted";
                $_GET['path'];
                header("Location: ?path=" . $path);
                return FALSE;
            }
    }

    function login($m = null){
        $this->cur_path = $this->settings['path'];
        $form = <<<eod
				<div style="color:red;text-align:center;">$m</div>
				<form action="?path=$this->cur_path" method="post" id='login'>
				<input type="text" name="user" placeholder="username" />
				<br />
				<input type="password" name="pw" placeholder="password" />
				<br />
				<input type="submit" name="s" value="Login" />
				<br />
				</form>
eod;

        if(!isset($_POST['s'])){
            echo $this->head();
            echo $form;
            echo $this->footer();
        }
        // not really using any security measure
        elseif(htmlentities($_POST['user']) == $this->settings['username'] && htmlentities($_POST['pw']) == $this->settings['password']){
            $_SESSION['user'] = "authed";
            $this->cur_path = $_GET['path'];
            header("Location: ?" . $_SERVER['QUERY_STRING']);
        }
        else{
            unset($_POST['s']);
            $this->login('user or password is incorrect!');
        }
    }

    function head(){
        $var = <<<eov
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>elibyy File Manager</title>
		<style type="text/css">
			form#login{
			margin:0 auto;
			text-align:center;
			}
			div#footer{
			text-align:center;
			font-style:oblique;
			}
			table,h1{
			margin:0 auto;
			text-align:center;
			}
			tr,th,td{
			text-align:left;
			}
			div#logout{
			text-align:center;
			}
		</style>
	</head>
	<body>
eov;
        return $var;
    }

    function footer(){
        $var = "
				<br />
				<div id='footer'>Made by elibyy 2011</div>
				</body>
				</html>";
        return $var;
    }

    function logout(){
        // reverifing the logout request
        if(isset($_GET['logout']) && $_GET['logout'] == "TRUE"){
            // clearing all possible info exists.
            $_SESSION = array();
            session_destroy();
            $file = pathinfo($_SERVER['PHP_SELF']);
            header("Location:$file[dirname]?login=TRUE");
        }
    }

    function dl($file){
        if(isset($_GET['d']) && $_GET['d'] == "TRUE" && isset($_GET['f'])){
            if(file_exists($file)){
                header('Content-Description: File Transfer');
                header('Content-Type:' . $this->file_mime($file));
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit();
            }
        }
    }

}

$settings = array('path' => '/Users/eli/', // define the path by unix syntax.
    'username' => 'elibyy', 'password' => 'elibyy', 'show_mime' => 1, 'show_actions' => 1
);
new file_system($settings);
unset($_SESSION['message']);
?>
