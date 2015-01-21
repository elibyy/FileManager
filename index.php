<?php

/**
 * this still has the same concept of one file
 * this is still not secured
 * this contains the settings now in the property as default by modifiable thru the constructor
 * lots of syntax and performance
 * Class FileSystem
 * @version 1.0
 */
class FileSystem
{

    /**
     * @var array
     * @since 1.0
     */
    protected $settings = array(
        'user' => 'elibyy',
        'password' => 'elibyy',
        'start_path' => '/',
        'show_mime' => true,
        'show_actions' => true,
    );

    /**
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        $this->settings = array_merge($this->settings, $settings);
        $path = $this->settings['start_path'];
        $this->settings['start_path'] = $path !== '/' ? rtrim($path, '/') . '/' : '/';
        $this->initSession();
        $this->checkLogin();
    }

    /**
     * @return string
     * @since 1.0
     */
    protected function getPath()
    {
        if ((isset($_GET['path']) && $_GET['path'] != '')) {
            return realpath($_GET['path']);
        } else {
            return realpath($this->settings['start_path']);
        }
    }

    /**
     * @since 1.0
     */
    protected function initSession()
    {
        if (session_id() == '') {
            session_name('Elibyy_FS');
            session_start();
        }
    }

    /**
     * @since 1.0
     */
    protected function checkLogin()
    {
        if (!empty($_SESSION['user'])) {
            $this->main();
        } else {
            $this->login();
        }
    }

    /**
     * @since 1.0
     */
    protected function main()
    {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'zip':
                    $this->zipDir();
                    break;
                case 'unzip':
                    $this->unzip();
                    break;
                case 'delete':
                    $this->delete();
                    break;

            }
            return;
        }
        $path = $this->getPath();
        if (is_file($path)) {
            $this->download();
            return;
        }
        $baseDir = $this->settings['start_path'];
        if (stripos($path, $baseDir) === false) {
            $this->error('No access to other folders');
            return;
        }
        if ($path == '//' || stripos($path, '..') !== false) {
            $this->error('No access to ' . $path);
            return;
        }
        $filePic = '<img src="data:image/jpg;base64,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" highet=\'22\' width=\'27\' />';
        $dirPic = '<img src="data:image/jpg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAbABYDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9s/G2qX0Hie5js9SvbKRBHgKweLlR1RsjH0wa4HwF+1FceOLGe40vVLLUks52tbhGtwrwSqcMjABSD79CORkVu/E3WltPiLfRFsFVhP5xivkX9iHxEscnxABIKnxBIOfq9fz1nGcZhRzDFexrziozdkpO3xPpex99l2AoSoU/awT5kt0r7dz76+HPii48XeHPtdykKSiVoyIgQpxjnkn1orG/Z/vVv/Acjr0F5IP/AB1D/Wiv3Dh7EVK+WUK1Z3lKKbfd2PjcxpxhiqkIKyTdvvPC/wBsPxBe+GfF/imREurd30nzrOZQyF2W2I3RnuVcY46HFfLH7E2pRaL8HY7iQTR6nqV5PLfGZmMrurlBuDcggD8SSa/Rj9pTwlpvi74KeIk1G0juRaWM08DElXhcIfmVgQRxwcHkcHIro7/4deH9U0mDTLvRNKu7C1URQwT2qSpEo7DcDj69c89a+IzLgOeKxeInCqlztSV13bunr0ez19D3MNn6pUqcXC9lbftY+ZPhV+0bF8NLaSGBvtkt3GHuYDNvWBg7hG2Z+Qsp5P8AFsHpRX0Nof7OPgbwpez3Vj4Z0xJ7n5ZGlQzAjrgByQACOAMAZbHU5K+6yPAvA4GlhJWvFWdtr9Xst3r/AFc8HG4hV68qy6s//9k=" />';
        $logout = sprintf('<div id="logout"><a href="%s">Log out</a></div>', $this->buildUrl() . '?logout=1');
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
        echo $this->head();

        $files = array();
        if (is_readable($path)) {
            $files = scandir($path);
        }
        $filteredFiles = array();
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {
                $filteredFiles[] = $file;
            }
        }
        sort($filteredFiles);
        ?>
        <h1><?= 'Listing Directory ' . $path ?></h1>
        <br/>
        <?= $logout ?>
        <?php
        if (!empty($message)):
            ?>
            <div style='color:red;text-align:center;'>
                <?= $message ?>
            </div>
        <?php
        endif;
        ?>
        <br/>
        <table border="1">
            <thead>
            <tr>
                <th>File</th>
                <th>Size</th>
                <?php if ($this->settings['show_mime']): ?>
                    <th>Mime Type</th>
                <?php endif; ?>

                <th>Last Changed</th>
                <?php if ($this->settings['show_actions']): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($filteredFiles)) {
                if ($path !== $this->settings['start_path']) {
                    ?>
                    <tr>
                        <td colspan="100" style="text-align: center">
                            <a href="<?= $this->buildUrl() . '?path=' . dirname($path) ?>">&lAarr;Go Back</a>
                        </td>
                    </tr>
                <?php
                }
                foreach ($filteredFiles as $file):
                    $fullFile = rtrim($path, '/') . '/' . $file;
                    if ($file == 'Documents and Settings' && file_exists('/Users')) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><a href="<?= $this->buildUrl() . '?path=' . $fullFile ?>">
                                <?= (is_dir($fullFile) ? $dirPic : $filePic) . ' ' . $file ?>
                            </a>
                        </td>
                        <td><?= $this->getsize($fullFile) ?></td>
                        <?php if ($this->settings['show_actions']): ?>
                            <td><?= $this->getMime($file) ?></td>
                        <?php endif; ?>
                        <td><?= $this->getChanged($fullFile) ?></td>
                        <?php if ($this->settings['show_actions']): ?>
                            <td><?= $this->getActions($fullFile) ?></td>
                        <?php endif;?>
                    </tr>
                <?php endforeach;
            } else {
                ?>
                <tr>
                    <td colspan="100">Directory is empty or inaccessible</td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        echo $this->footer();
        unset($_SESSION['message']);
    }

    /**
     * @param $msg
     * @since 1.0
     */
    protected function error($msg)
    {
        $_SESSION['message'] = $msg;
        $url = $this->buildUrl();
        $url .= '?path=' . $this->settings['start_path'];
        header('Location: ' . $url);
    }

    /**
     * @return string
     * @since 1.0
     */
    protected function buildUrl()
    {
        $url = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url .= 's';
        }
        $url .= '://';
        $url .= $_SERVER['SERVER_NAME'];
        $url .= $_SERVER['SCRIPT_NAME'];
        return $url;
    }

    /**
     * @return string
     * @since 1.0
     */
    protected function head()
    {
        return <<<eov
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
    }

    /**
     * @param $file
     * @return string
     * @since 1.0
     */
    function getsize($file)
    {

        $size = filesize($file);
        $size = stristr($size, "-") ? 0 : $size;
        if (is_dir($file)) {
            $size = 4096;
        }
        $units = array(
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        );
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size/pow(1024, $power), 2, ".", ",") . " " . $units[$power];
    }

    /**
     * @param $filename
     * @return string
     * @since 1.0
     */
    protected function getMime($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $mimetype = array(
            "bat" => "application/bat",
            "sys" => "application/octet-stream",
            "BAK" => "application/octet-stream",
            "lnk" => "application/x-ms-shortcut",
            "ai" => "application/postscript",
            "aif" => "audio/x-aiff",
            "aifc" => "audio/x-aiff",
            "aiff" => "audio/x-aiff",
            "asf" => "video/x-ms-asf",
            "asr" => "video/x-ms-asf",
            "asx" => "video/x-ms-asf",
            "au" => "audio/basic",
            "avi" => "video/x-msvideo",
            "axs" => "application/olescript",
            "bas" => "text/plain",
            "bcpio" => "application/x-bcpio",
            "bin" => "application/octet-stream",
            "bmp" => "image/bmp",
            "c" => "text/plain",
            "cat" => "application/vnd.ms-pkiseccat",
            "cdf" => "application/x-cdf",
            "cer" => "application/x-x509-ca-cert",
            "class" => "application/octet-stream",
            "clp" => "application/x-msclip",
            "cmx" => "image/x-cmx",
            "cod" => "image/cis-cod",
            "cpio" => "application/x-cpio",
            "crd" => "application/x-mscardfile",
            "crl" => "application/pkix-crl",
            "crt" => "application/x-x509-ca-cert",
            "csh" => "application/x-csh",
            "css" => "text/css",
            "dcr" => "application/x-director",
            "der" => "application/x-x509-ca-cert",
            "dir" => "application/x-director",
            "dll" => "application/x-msdownload",
            "dms" => "application/octet-stream",
            "doc" => "application/msword",
            "dot" => "application/msword",
            "dvi" => "application/x-dvi",
            "dxr" => "application/x-director",
            "eps" => "application/postscript",
            "etx" => "text/x-setext",
            "evy" => "application/envoy",
            "exe" => "application/octet-stream",
            "fif" => "application/fractals",
            "flr" => "x-world/x-vrml",
            "gif" => "image/gif",
            "gtar" => "application/x-gtar",
            "gz" => "application/x-gzip",
            "h" => "text/plain",
            "hdf" => "application/x-hdf",
            "hlp" => "application/winhlp",
            "hqx" => "application/mac-binhex40",
            "hta" => "application/hta",
            "htc" => "text/x-component",
            "htm" => "text/html",
            "html" => "text/html",
            "htt" => "text/webviewhtml",
            "ico" => "image/x-icon",
            "ief" => "image/ief",
            "iii" => "application/x-iphone",
            "ins" => "application/x-internet-signup",
            "isp" => "application/x-internet-signup",
            "jfif" => "image/pipeg",
            "jpe" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "jpg" => "image/jpeg",
            "js" => "application/x-javascript",
            "latex" => "application/x-latex",
            "lha" => "application/octet-stream",
            "lsf" => "video/x-la-asf",
            "lsx" => "video/x-la-asf",
            "lzh" => "application/octet-stream",
            "m13" => "application/x-msmediaview",
            "m14" => "application/x-msmediaview",
            "m3u" => "audio/x-mpegurl",
            "man" => "application/x-troff-man",
            "mdb" => "application/x-msaccess",
            "me" => "application/x-troff-me",
            "mht" => "message/rfc822",
            "mhtml" => "message/rfc822",
            "mid" => "audio/mid",
            "mny" => "application/x-msmoney",
            "mov" => "video/quicktime",
            "movie" => "video/x-sgi-movie",
            "mp2" => "video/mpeg",
            "mp3" => "audio/mpeg",
            "mpa" => "video/mpeg",
            "mpe" => "video/mpeg",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpp" => "application/vnd.ms-project",
            "mpv2" => "video/mpeg",
            "ms" => "application/x-troff-ms",
            "mvb" => "application/x-msmediaview",
            "nws" => "message/rfc822",
            "oda" => "application/oda",
            "p10" => "application/pkcs10",
            "p12" => "application/x-pkcs12",
            "p7b" => "application/x-pkcs7-certificates",
            "p7c" => "application/x-pkcs7-mime",
            "p7m" => "application/x-pkcs7-mime",
            "p7r" => "application/x-pkcs7-certreqresp",
            "p7s" => "application/x-pkcs7-signature",
            "pbm" => "image/x-portable-bitmap",
            "pdf" => "application/pdf",
            "pfx" => "application/x-pkcs12",
            "pgm" => "image/x-portable-graymap",
            "pko" => "application/ynd.ms-pkipko",
            "pma" => "application/x-perfmon",
            "pmc" => "application/x-perfmon",
            "pml" => "application/x-perfmon",
            "pmr" => "application/x-perfmon",
            "pmw" => "application/x-perfmon",
            "pnm" => "image/x-portable-anymap",
            "pot" => "application/vnd.ms-powerpoint",
            "ppm" => "image/x-portable-pixmap",
            "pps" => "application/vnd.ms-powerpoint",
            "ppt" => "application/vnd.ms-powerpoint",
            "prf" => "application/pics-rules",
            "ps" => "application/postscript",
            "pub" => "application/x-mspublisher",
            "qt" => "video/quicktime",
            "ra" => "audio/x-pn-realaudio",
            "ram" => "audio/x-pn-realaudio",
            "ras" => "image/x-cmu-raster",
            "rgb" => "image/x-rgb",
            "rmi" => "audio/mid",
            "roff" => "application/x-troff",
            "rtf" => "application/rtf",
            "rtx" => "text/richtext",
            "scd" => "application/x-msschedule",
            "sct" => "text/scriptlet",
            "setpay" => "application/set-payment-initiation",
            "setreg" => "application/set-registration-initiation",
            "sh" => "application/x-sh",
            "shar" => "application/x-shar",
            "sit" => "application/x-stuffit",
            "snd" => "audio/basic",
            "spc" => "application/x-pkcs7-certificates",
            "spl" => "application/futuresplash",
            "src" => "application/x-wais-source",
            "sst" => "application/vnd.ms-pkicertstore",
            "stl" => "application/vnd.ms-pkistl",
            "stm" => "text/html",
            "svg" => "image/svg+xml",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc" => "application/x-sv4crc",
            "t" => "application/x-troff",
            "tar" => "application/x-tar",
            "tcl" => "application/x-tcl",
            "tex" => "application/x-tex",
            "texi" => "application/x-texinfo",
            "texinfo" => "application/x-texinfo",
            "tgz" => "application/x-compressed",
            "tif" => "image/tiff",
            "tiff" => "image/tiff",
            "tr" => "application/x-troff",
            "trm" => "application/x-msterminal",
            "tsv" => "text/tab-separated-values",
            "txt" => "text/plain",
            "uls" => "text/iuls",
            "ustar" => "application/x-ustar",
            "vcf" => "text/x-vcard",
            "vrml" => "x-world/x-vrml",
            "wav" => "audio/x-wav",
            "wcm" => "application/vnd.ms-works",
            "wdb" => "application/vnd.ms-works",
            "wks" => "application/vnd.ms-works",
            "wmf" => "application/x-msmetafile",
            "wps" => "application/vnd.ms-works",
            "wri" => "application/x-mswrite",
            "wrl" => "x-world/x-vrml",
            "wrz" => "x-world/x-vrml",
            "xaf" => "x-world/x-vrml",
            "xbm" => "image/x-xbitmap",
            "xla" => "application/vnd.ms-excel",
            "xlc" => "application/vnd.ms-excel",
            "xlm" => "application/vnd.ms-excel",
            "xls" => "application/vnd.ms-excel",
            "xlt" => "application/vnd.ms-excel",
            "xlw" => "application/vnd.ms-excel",
            "xof" => "x-world/x-vrml",
            "xpm" => "image/x-xpixmap",
            "xwd" => "image/x-xwindowdump",
            "z" => "application/x-compress",
            "zip" => "application/zip",
            'cpt' => 'application/mac-compactpro',
            'psd' => 'application/octet-stream',
            'so' => 'application/octet-stream',
            'sea' => 'application/octet-stream',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'mif' => 'application/vnd.mif',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'php' => 'application/x-httpd-php',
            'php4' => 'application/x-httpd-php',
            'php3' => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source',
            'swf' => 'application/x-shockwave-flash',
            'xhtml' => 'application/xhtml+xml',
            'xht' => 'application/xhtml+xml',
            'midi' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'rv' => 'video/vnd.rn-realvideo',
            'png' => 'image/png',
            'shtml' => 'text/html',
            'text' => 'text/plain',
            'log' => 'text/plain',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'word' => 'application/msword',
            'xl' => 'application/excel',
            'eml' => 'message/rfc822'
        );
        return isset($mimetype[$ext]) ? $mimetype[$ext] : 'application/octet-stream';
    }

    /**
     * @return string
     * @since 1.0
     */
    function footer()
    {
        return '
				<br />
				<div id="footer">Made by elibyy 2011</div>
				</body>
				</html>';
    }

    /**
     * @param null $m
     * @since 1.0
     */
    protected function login($m = null)
    {
        $path = $this->getPath();
        $form = <<<eod
				<div style="color:red;text-align:center;">$m</div>
				<form action="?path={$path}" method="post" id='login'>
				<input type="text" name="user" placeholder="username" />
				<br />
				<input type="password" name="pw" placeholder="password" />
				<br />
				<input type="submit" name="s" value="Login" />
				<br />
				</form>
eod;

        if (!isset($_POST['s'])) {
            echo $this->head();
            echo $form;
            echo $this->footer();
        } else {
            $user = htmlentities($_POST['user'] ?: '');
            $pw = htmlentities($_POST['pw'] ?: '');
            if ($user == $this->settings['user'] && $pw == $this->settings['password']) {
                $_SESSION['user'] = 'authed';
                $this->main();
            } else {
                unset($_POST);
                $this->login('user or password is incorrect!');
            }
        }
    }

    /**
     * @param $file
     * @return bool|string
     * @since 1.0
     */
    protected function getChanged($file)
    {
        return date('Y-m-d H:i:s', filemtime($file));
    }

    /**
     * @param $file
     * @return string
     * @since 1.0
     */
    protected function getActions($file)
    {
        $results = array();
        $results[] = '<a href="' . $this->buildUrl() . '?action=delete&path=' . $file . '">Delete</a>';
        if (is_dir($file)) {
            if ($this->isZipSupported()) {
                $results[] = '<a href="' . $this->buildUrl() . '?action=zip&path=' . $file . '">Zip Dir</a>';
            }
        } else {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext == 'zip') {
                $results[] = '<a href="' . $this->buildUrl() . '?action=unzip&path=' . $file . '">Unzip File</a>';
            }
        }
        return implode(' / ', $results);
    }

    /**
     * @return bool
     * @since 1.0
     */
    protected function isZipSupported()
    {
        $disabled = ini_get('disable_classes');
        $disabled = explode(',', $disabled);
        return class_exists('ZipArchive') && !in_array('ZipArchive', $disabled);
    }

    /**
     * @since 1.0
     */
    protected function zipDir()
    {
        $path = $this->getPath();
        if (!$this->isZipSupported()) {
            echo '<meta http-equiv="refresh" content="4;url=?path=' . $path . '"> something went wrong while zipping  ' . $path . ', you are redirected to previous page for retry.';
            return;
        }
        if (!is_dir($path)) {
            echo '<meta http-equiv="refresh" content="4;url=?path=' . $path . '"> something went wrong while zipping  ' . $path . ', you are redirected to previous page for retry.';
            return;
        }
        // using a recursive function to list all files also in subfolders
        $files = $this->listFilesRecursive($path);
        // if folder empty return user to main page.
        if (empty($files)) {
            echo '<meta http-equiv="refresh" content="4;url=?path=' . $path . '"> Folder is Empty!';
            return;
        }
        $zip = new ZipArchive();
        $zipFile = dirname($path) . '/' . basename($path) . '.zip';
        //error_reporting(0);
        if ($zip->open($zipFile, ZipArchive::CREATE)) {
            foreach ($files as $file) {
                $zip->addFile($file);
            }
            $zip->setArchiveComment("Made By Elibyy FS on " . date("d/m/Y"));
            $zip->close();
            // pushing the file to the user and deleting on server :)
            header('Content-Description: File Transfer');
            header('Content-Type:' . $this->getMime($zipFile));
            header('Content-Disposition: attachment; filename=' . basename($zipFile));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);
            unlink($zipFile);
        }
    }

    /**
     * @param $path
     * @return array
     * @since 1.0
     */
    protected function listFilesRecursive($path)
    {
        $results = array();
        $files = scandir($path);
        foreach ($files as $file) {
            if (in_array($file, array('.', '..'))) {
                continue;
            }
            $lpath = $path . '/' . $file;
            if (is_dir($lpath)) {
                $results = array_merge($results, $this->listFilesRecursive($lpath));
            } else {
                $results[] = $lpath;
            }
        }

        return $results;
    }

    /**
     * @param $path
     * @return array
     * @since 1.0
     */
    protected function listDirsRecursive($path)
    {
        $results = array();
        $files = scandir($path);
        foreach ($files as $file) {
            if (in_array($file, array('.', '..'))) {
                continue;
            }
            $lpath = $path . '/' . $file;
            if (is_dir($lpath)) {
                $results[] = $lpath;
                $results = array_merge($results, $this->listFilesRecursive($lpath));
            }
        }
        return $results;
    }

    /**
     * @since 1.0
     */
    protected function delete()
    {
        $path = $this->getPath();
        if (is_dir($path)) {
            $files = $this->listFilesRecursive($path);
            $dirs = $this->listDirsRecursive($path);
            foreach ($files as $file) {
                unlink($file);
            }
            foreach ($dirs as $dir) {
                rmdir($dir);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
        header('Location: ' . $this->buildUrl() . '?path=' . dirname($path));
    }

    /**
     * @since 1.0
     */
    protected function unzip()
    {
        $path = $this->getPath();
        $archive = new ZipArchive();
        $res = $archive->open($path);
        $destDir = dirname($path) . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME);
        mkdir($destDir);
        if ($res === true) {
            $archive->extractTo($destDir);
        }
        header('Location: ' . $this->buildUrl() . '?path=' . dirname($path));
    }

    /**
     * @since 1.0
     */
    protected function download()
    {
        $path = $this->getPath();
        if (file_exists($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type:' . $this->getMime($path));
            header('Content-Disposition: attachment; filename=' . basename($path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush();
            readfile($path);
            exit();
        }
    }

}

new FileSystem();