<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "marerodrigu@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "d7320f" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'C017' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYAhimMIaGIImJtDKGMIQAaSSxgEZWkCiqWINIo8MUEI1wX9SqaSuzpq1amYXkPqi6VgZMvVMY0OwAigQwoLtlCqMDupsZQx1RxAYq/KgIsbgPAF3Ey2K+chgBAAAAAElFTkSuQmCC',
			'3AD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGUIdkMQCpjCGsDY6OgQgq2xlbWVtCGgQQRabItLoChQLQHLfyqhpK1NXRS3NQnYfqjqoeaKhrujmtULUiaC4BSiG5hbRAKAYmpsHKvyoCLG4DwCJf85hFILFGwAAAABJRU5ErkJggg==',
			'7984' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGRoCkEVbWVsZHR0aUcVEGl0bAlpRxKaINDo6OkwJQHZf1NKlWaGroqKQ3MfowBjoCFSIrJe1gQFoXmBoCJKYSAMLyA4UtwQ0gN2CJobFzQMUflSEWNwHAOJpza4lIlFCAAAAAElFTkSuQmCC',
			'17C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHaY6IImxOjA0OjoEBAQgiYkCxVwbBB1EUPQytLICaREk963MWjVtKZCOQnIfUF0AUF2jA4peRgegWCuqW1gbWBsEpqCKiQBxQACymGgI0MZQx9CQQRB+VIRY3AcAxL3JSs8u+RAAAAAASUVORK5CYII=',
			'DAD2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGaY6IIkFTGEMYW10CAhAFmtlbWVtCHQQQRETaXRtCGgQQXJf1NJpK1NXRQEhwn1QdY0odrSKhgLFWhkwzZuCIjYFKAZ0C6qbgWKhjKEhgyD8qAixuA8AasnPzgW0JMIAAAAASUVORK5CYII=',
			'2429' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaY6IImJTGGYyujoEBCAJBYAVMXaEOgggqy7ldGVASEGcdO0pUtXrcyKCkN2X4BIK9CWqch6GR1EQx2mAO1CdgvQRIYABhQ7REC2ODCguCU0lKGVNTQAxc0DFX5UhFjcBwDx3cpp2bKOhQAAAABJRU5ErkJggg==',
			'DBFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA6YGIIkFTBFpZW1gCBBBFmsVaXRtYHRgQRUDqmN0QHZf1NKpYUtDV2Yhuw9NHYp52MRQ7MDiFrCbGxhQ3DxQ4UdFiMV9ALGizI0cd1pOAAAAAElFTkSuQmCC',
			'B3A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNYQximMIaGIIkFTBFpZQhlaBBBFmtlaHR0dEAVm8LQytoQAIQI94VGrQpbuipqZRaS+6DqWhnQzHMNBcqgizUEBDCguYW1IdAB3c3oYgMVflSEWNwHAID1zgs+4iINAAAAAElFTkSuQmCC',
			'F2FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6YGIIkFNLC2sjYwBIigiIk0ujYwOrCgiDGAxZDdFxq1aunS0JVZyO4DqpvCilAHEwvAFGN0YMWwg7UB0y2ioa4NDChuHqjwoyLE4j4AlKzLpAdlmk8AAAAASUVORK5CYII=',
			'6A8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0sLayNgQ6iCCLNYg0OiLUgZ0UGTVtZVboytAsJPeFTEFRB9HbKhrqim5eq0gjupgIFr2sASKNDmhuHqjwoyLE4j4Ay3jMNqXUmrYAAAAASUVORK5CYII=',
			'5DB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGaY6IIkFNIi0sjY6BASgijW6NgQ6iCCJBQYAxRodGkSQ3Bc2bdrK1NBVq6KQ3dcKVteIbAdYrCGgFdktARCxKchiIlMgbkEWYw0AuZkxNGQQhB8VIRb3AQAMsM5OhF+EiwAAAABJRU5ErkJggg==',
			'47B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QMQ6AMAgA6cAP6n/q0B0T+YSvoIM/wP7Awb7S2Amro0a57ULCBSiXEfgT7/Rpx5FhPrkRUkxhCca5wwkRGYcKM6Y+eNOXc8krb1M2faRAZq/C7ALKcHKgKNjcAPWCTUt1bfNX/3uOm74dxnPMp5gbCB0AAAAASUVORK5CYII=',
			'6B53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHUIdkMREpoi0sjYwOgQgiQW0iDS6guSQxRqA6qaCaIT7IqOmhi3NzFqaheS+EKB5IFUo5rWKNDqATEATc0UTA7mF0dERxS0gNzOEMqC4eaDCj4oQi/sAy5/Nh6gyyoQAAAAASUVORK5CYII=',
			'AECD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHUMdkMRYA0SA4oEOAUhiIlNEGlgbBB1EkMQCWkFijDAxsJOilk4NW7pqZdY0JPehqQPD0FBMMYg6TDvQ3RLQiunmgQo/KkIs7gMAfFbLGn5ZlmcAAAAASUVORK5CYII=',
			'F094' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGRoCkMQCGhhDGB0dGlHFWFtZGwJaUcVEGl0bAqYEILkvNGrayszMqKgoJPeB1DmEBDqg63VoCAwNQbODEUhicQuaGKabByr8qAixuA8ATSHOwq4I4ZEAAAAASUVORK5CYII=',
			'2D46' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQxgaHaY6IImJTBFpZWh1CAhAEgtoFQGqcnQQQNYNEgt0dEBx37RpKzMzM1OzkN0XINLo2uiIYh6jA1AsNNBBBNktDUDzGh1RxEQagG5pRHVLaCimmwcq/KgIsbgPAD1gzRIdy4ifAAAAAElFTkSuQmCC',
			'4DC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37poiGMIQ6hoYgi4WItDI6BDSIIIkxhog0ujYIoIixTgGJMTQEILlv2rRpK1NXrVqZheS+AIi6VmR7Q0PBYlNQ3QK2IwBNDOiWQAcsbkYVG6jwox7E4j4Amk3MYdow8ckAAAAASUVORK5CYII=',
			'96A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQximMDQEIImJTGFtZQhlaEQWC2gVaWR0dGhFE2tgbQiYEoDkvmlTp4UtXRUVFYXkPlZX0VbWhkAHZL0MQPNcQwNDQ5DEBEBiQJegu4UVTQzkZnSxgQo/KkIs7gMAao3ONRfIgOMAAAAASUVORK5CYII=',
			'AC03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMIQ6IImxBrA2OoQyOgQgiYlMEWlwdHRoEEESC2gVaWBtCGgIQHJf1NJpq5YCySwk96GpA8PQUIgYunmYdmC6JaAV080DFX5UhFjcBwD9PM3s8jHK+AAAAABJRU5ErkJggg==',
			'D32C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaYGIIkFTBFpZXR0CBBBFmtlaHRtCHRgQRVrZQCKIbsvaumqsFUrM7OQ3QdW18rowIBmnsMULGIBjKh2gNziwIDiFpCbWUMDUNw8UOFHRYjFfQBMacw1AfT1kgAAAABJRU5ErkJggg==',
			'47F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37poiGuoYGNAQgi4UwNLo2MDQiizFCxFqRxVinMLSyAk0IQHLftGmrpi0NXRUVheS+gCkMAawNjA7IekNDGR2AYqEhKG5hbWAFqUcREyFObKDCj3oQi/sAOJ7M9U9WX9YAAAAASUVORK5CYII=',
			'5F95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMDkMQCGkQaGB0dHRjQxFgbAlHEAgPAYq4OSO4LmzY1bGVmZFQUsvtaRRoYQkAmIOkGiTWgigUAxRiBdiCLiUwBucUhANl9rEB7GUIZpjoMgvCjIsTiPgBfrMuOXk70jQAAAABJRU5ErkJggg==',
			'DAF3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0IdkMQCpjCGsDYwOgQgi7WytrICaREUMZFGVxCN5L6opdNWpoauWpqF5D40dVAx0VBXHOahiE0BiaG6JTQArA7FzQMVflSEWNwHAAYhzrPylqaEAAAAAElFTkSuQmCC',
			'4E1A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37poiGMkxhaEURCxEBYoapDkhijEAxxhCGgAAkMdYpQHVTGB1EkNw3bdrUsFXTVmZNQ3JfAKo6MAwNBYuFhqC4BVMddjHRUMZQR1SxgQo/6kEs7gMA0K3KT4B8AxMAAAAASUVORK5CYII=',
			'3DA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQximMEx1QBILmCLSyhDKEBCArLJVpNHR0dFBBFlsikija0MATB3YSSujpq1MXRU1NQvZfajq4Oa5hgaimgcSa0AVA7mFFU0vyM1AMRQ3D1T4URFicR8A8mfN0t1YiB0AAAAASUVORK5CYII=',
			'1863' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUIdkMRYHVhbGR0dHQKQxEQdRBpdGxwaRFD0srayAukAJPetzFoZtnTqqqVZSO4Dq3N0aAhA0QsyLwDNPGxiWNwSgunmgQo/KkIs7gMASArJ+pXrYAIAAAAASUVORK5CYII=',
			'CD68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WENEQxhCGaY6IImJtIq0Mjo6BAQgiQU0ijS6Njg6iCCLNYDEGGDqwE6KWjVtZerUVVOzkNwHVoduHlhvIKp5jZhi2NyCzc0DFX5UhFjcBwD3u82liMhyEgAAAABJRU5ErkJggg==',
			'F83C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkMZQxhDGaYGIIkFNLC2sjY6BIigiIk0OjQEOrCgqWNodHRAdl9o1MqwVVNXZiG7D00dinnYxNDtwHQLppsHKvyoCLG4DwC0c815rqA5JwAAAABJRU5ErkJggg==',
			'8311' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANYQximMLQii4lMEWllCGGYiiwW0MrQ6BjCEIqqDqgPoRfspKVRq8JWTVu1FNl9aOrg5jkQIQZ2C5oYyM2MoQ6hAYMg/KgIsbgPAJVfy+HZf6caAAAAAElFTkSuQmCC',
			'AC19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMEx1QBJjDWBtdAhhCAhAEhOZItLgGMLoIIIkFtAK5E2Bi4GdFLV02qpV01ZFhSG5D6KOYSqy3tBQsFgDunkOUxjQ7AC6ZQqqWwJaGUMZQx1Q3DxQ4UdFiMV9AEj8zK2oE/HOAAAAAElFTkSuQmCC',
			'A4A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QMQ6AIAxFPwM3wPvAwF4Tu3gaHHoDOIILpxQna2DUhP7t5ad9KWo3CTPlFz/jIcgoXjFLKGAQKeYy2ITgnWIkJtpEySm//WxT95bHj8RJ6x36BvPCkUnw2oe7lweMerbyNsH/PszA7wLvqc1JMw3O9AAAAABJRU5ErkJggg==',
			'10DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUNDkMRYHRhDWBsdHZDViTqwtrI2BDqg6hVpdEWIgZ20MmvaytRVkaFZSO5DU4dHDJsdWNwSAnYzithAhR8VIRb3AQAdAcdFYGALvAAAAABJRU5ErkJggg==',
			'6F8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMdkMREpog0MDo6OgQgiQW0iDSwNgQ6iCCLNUDUiSC5LzJqatiq0JVZ05DcFzIFRR1EbysW87CIYXMLawBQBZqbByr8qAixuA8A67nLM+aJI7oAAAAASUVORK5CYII=',
			'C2D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QOw7AIAhAceAG9D4s3Wmii6fBwRvYI7h4ytIN045tIiQML3xegPEIhZXyFz+MIWKCkx2jihULizgmhcquB5NnCsZEyfnlMXq3mp2f9TW0DTzPirEK043AxhrMLnq7zM5b2lNIcYH/fZgvfhchAs2liQ4JMwAAAABJRU5ErkJggg==',
			'2638' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGaY6IImJTGFtZW10CAhAEgtoFWlkaAh0EEHW3QrkIdRB3DRtWtiqqaumZiG7L0C0lQHNPEYHkUYHNPNYGzDFRBow3RIaiunmgQo/KkIs7gMAigTMiM+tBz8AAAAASUVORK5CYII=',
			'5978' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA6Y6IIkFNLC2AsmAABQxkUaHhkAHESSxwACgWKMDTB3YSWHTli7NWrpqahay+1oZAx2mMKCYx9DKANTJiGJeQCtLo6MDqpjIFNZW1gZUvawBQDc3MKC4eaDCj4oQi/sAjv3M4gGg79kAAAAASUVORK5CYII=',
			'46F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37pjCGsIYGhoYgi4WwtrICaREkMcYQkUZ0MdYpIg0gsQAk902bNi1saeiqlVlI7guYIgoyrxXZ3tBQkUZXoO2obgGLBaCKgdzC6IDhZnSxgQo/6kEs7gMAjcHKxJdO7eIAAAAASUVORK5CYII=',
			'AE77' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0NDkMRYA0SAZECDCJKYyBRMsYBWIK/RASiKcF/U0qlhq5auWpmF5D6wuikMrcj2hoYCxQKAomjmMToARdHEWEGiKGJAN6OJDVT4URFicR8AKtjL+bI9zlsAAAAASUVORK5CYII=',
			'5695' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMDkMQCGlhbGR0dHRhQxEQaWRsCUcQCA0QagGKuDkjuC5s2LWxlZmRUFLL7WkVbGUKAJiDb3CrS6NCAKhYAFHME2oEsJjIF5BaHAGT3sQaA3Mww1WEQhB8VIRb3AQAdf8twR+tbFAAAAABJRU5ErkJggg==',
			'D3B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGVqRxQKmiLSyNjpMRRFrZWh0bQgIRRMDqYPpBTspaumqsKWhq5Yiuw9NHbJ5hMUgbkERg7o5NGAQhB8VIRb3AQAj8s6hU9c5EgAAAABJRU5ErkJggg==',
			'4CFE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjCGsoYGhgYgi4WwNro2MDogq2MMEWlAF2OdItLAihADO2natGmrloauDM1Ccl8AqjowDA3FFGOYgmkHwxRMt4Dd3MCI6uaBCj/qQSzuAwAOIMm6MvYTOAAAAABJRU5ErkJggg==',
			'04F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDA6Y6IImxBjBMZW1gCAhAEhOZwhDKClQtgCQW0MroChJDdl/UUiAIXZmaheS+gFaRVqA6FPMCWkVDXYF6RVDtAKlDEQO6pRXdLWA3NzCguHmgwo+KEIv7AI8nyiFBRONoAAAAAElFTkSuQmCC',
			'E723' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUIdkMQCGhgaHR0dHQLQxFyBpAiqWCuIDEByX2jUqmmrVmYtzUJyH1A+AKISWS+jA8MUBjTzWEEq0cREgCoZUdwSGiLSwBoagOLmgQo/KkIs7gMA/trNb2peCMIAAAAASUVORK5CYII=',
			'E56A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGVqRxQIaRBoYHR2mOqCJsTY4BASgioWwNjA6iCC5LzRq6tKlU1dmTUNyH1BPo6ujI0wdQqwhMDQE1TyQGJo61lZGNL2hIYwhDKGMKGIDFX5UhFjcBwAB+cy+6BXhigAAAABJRU5ErkJggg==',
			'C81F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYQximMIaGIImJtLK2MoQwOiCrC2gUaXREF2sAqpsCFwM7KWrVyrBV01aGZiG5D00dVEyk0QFdrBFTDOwWNDGQmxlDHVHEBir8qAixuA8ACHzJpsH2PX8AAAAASUVORK5CYII=',
			'5DA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEASCwwQaXRtCHRAdl/YtGkrU1dFpmYhu68VrA7FPLBYaKCDCLIdEHUoYiJTRFpZGwJQ9LIGiIYAxVDcPFDhR0WIxX0A2M/NpcH+UykAAAAASUVORK5CYII=',
			'E420' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYWhlCgRhJLKCBYSqjo8NUB1SxUNaGgIAAFDFGV4aGQAcRJPeFRi1dumplZtY0JPcFNIi0MrQywtRBxURDHaagiwHdEcCAZgdIJwOKW0BuZg0NQHHzQIUfFSEW9wEAWBDMTMrCqk8AAAAASUVORK5CYII=',
			'0733' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1DGUIdkMRYAxgaXRsdHQKQxESmMDQ6NAQ0iCCJBbQytEJEEe6LWrpq2qqpq5ZmIbkPqC4ASR1UjNGBAc08kSmsDehirAEiDaxobmF0EGlgRHPzQIUfFSEW9wEAo+TNPk8RLRYAAAAASUVORK5CYII=',
			'F420' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZWhlAGEksoIFhKqOjw1QHVLFQ1oaAgAAUMUZXhoZABxEk94VGLV26amVm1jQk9wU0iLQytDLC1EHFREMdpqCLAd0RwIBmB0gnA7pbWllDA1DcPFDhR0WIxX0AfSfMdBKYAxAAAAAASUVORK5CYII=',
			'8A9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjCGMDo6OiCrC2hlbWVtCEQRE5ki0uiKEAM7aWnUtJWZmZGhWUjuA6lzCAlEM0801KEBXUyk0RGLHY5obmENAJqH5uaBCj8qQizuAwA6xsrua+rU7QAAAABJRU5ErkJggg==',
			'9CEF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHUNDkMREprA2ujYwOiCrC2gVacAmxooQAztp2tRpq5aGrgzNQnIfqyuKOghsxRQTwGIHNrdA3Yxq3gCFHxUhFvcBANCKyUWeexc9AAAAAElFTkSuQmCC',
			'46EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjCGsIY6hjogi4WwtrI2MDoEIIkxhog0gsREkMRYp4g0IKkDO2natGlhS0NXhmYhuS9giiiGeaGhIo2uaOYxTMEmhukWrG4eqPCjHsTiPgCqe8pW6NNuZQAAAABJRU5ErkJggg==',
			'D469' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGaY6IIkFTGGYyujoEBCALAZUxdrg6CCCIsboytrACBMDOylqKRBMXRUVhuS+gFaRVlZHh6moekVDXRsCGlDFGFpZGwJQ7ZjC0IruFmxuHqjwoyLE4j4AVD/NN/3jYMoAAAAASUVORK5CYII=',
			'3447' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RAMYWhkaHUNDkMQCpjBMZWh1aBBBVtnKEMowFU1sCqMrQ6BDQwCS+1ZGLV26MjNrZRay+6aItLI2OrSi2NwqGuoaCrQJ1Q6gWxwCGFDdAnKfAxY3o4gNVPhREWJxHwBcNcwjk8MGfwAAAABJRU5ErkJggg==',
			'C8DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUMDkMREWllbWRsdHZDVBTSKNLo2BKKKNQDVIcTATopatTJs6arI0Cwk96Gpg4phMQ+LHdjcgs3NAxV+VIRY3AcAP2TLeLRzueQAAAAASUVORK5CYII=',
			'7DF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA0NDkEVbRVpZgbQIqlijK7rYFIhYALL7oqatTA1dtTILyX2MDmB1rcj2sjaAxaYgi4lAxAKQxQIaQG5hdEAVA7oZTWygwo+KEIv7APXuy9MM8KueAAAAAElFTkSuQmCC',
			'3A2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGaYGIIkFTGEMYXR0CBBBVtnK2sraEOjAgiw2RaTRASiG7L6VUdNWZq3MzEJxH0hdK6MDis2toqEOU9DFgOoCGFHsCADqdXRgQHGLaIBIo2toAIqbByr8qAixuA8AjL3LF3DXJ/IAAAAASUVORK5CYII=',
			'E43E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYWhlDGUMDkMSA7KmsjY4ODKhioQwNgWhijK4MCHVgJ4VGLV26aurK0Cwk9wU0iLQyYJgnGuqAYR5DK6YdDK3obsHm5oEKPypCLO4DAIICy7G6XFwIAAAAAElFTkSuQmCC',
			'9B4A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxgaHVqRxUSmiLQytDpMdUASC2gVaQSKBASgirUyBDo6iCC5b9rUqWErMzOzpiG5j9VVpJW1Ea4OAoHmuYYGhoYgiQmA7EBTB3YLmhjEzWjmDVD4URFicR8A393MhcG8N7kAAAAASUVORK5CYII=',
			'D7AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMEwNQBILmMLQ6BDKECCCLNbK0Ojo6OjAgirWytoQ6IDsvqilq6YtXRWZhew+oLoAJHVQMUYH1lB0MdYGkDoUO6aIAMUCUNwSGgAWQ3HzQIUfFSEW9wEAzdvNjVHwi+AAAAAASUVORK5CYII=',
			'D089' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtCHQQQRETaXR0dISJgZ0UtXTayqzQVVFhSO6DqHOYiq7XtSGgQQTDjgBUO7C4BZubByr8qAixuA8AL0rNEKsmp2AAAAAASUVORK5CYII=',
			'8632' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGaY6IImJTGFtZW10CAhAEgtoFWlkaAh0EEFRB+Q1OjSIILlvadS0sFVTV62KQnKfyBTRVqC6Rgc08xyAJAOm2BQGLG7BdDNjaMggCD8qQizuAwC5js1zazN6SAAAAABJRU5ErkJggg==',
			'C006' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WEMYAhimMEx1QBITaWUMYQhlCAhAEgtoZG1ldHR0EEAWaxBpdG0IdEB2X9SqaStTV0WmZiG5D6oO1TyoXhEsdogQcAs2Nw9U+FERYnEfAITvy7Rv26bkAAAAAElFTkSuQmCC',
			'456B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37poiGMoQyhjogi4WINDA6OjoEIIkxAsVYGxwdRJDEWKeIhLA2MMLUgZ00bdrUpUunrgzNQnJfwBSGRlc080JDgWINgSjmMUwRwSLG2oruFoYpjCEYbh6o8KMexOI+AMRryzoA2W/cAAAAAElFTkSuQmCC',
			'E607' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYQximMIaGIIkFNLC2MoQyNIigiIk0Mjo6oIs1sALJACT3hUZNC1u6KmplFpL7AhpEW4HqWhnQzHNtCJiCLubo6BDAgOEWRgcsbkYRG6jwoyLE4j4ABpnMsSmpEDEAAAAASUVORK5CYII=',
			'9E29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEETY0CIgZ00berUsFUrs6LCkNzH6gpU0cowFVkvA0jvFKBdSGICILEABhQ7wG5xYEBxC8jNrKEBKG4eqPCjIsTiPgBpRcqrOBvnvQAAAABJRU5ErkJggg==',
			'7763' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUIdkEVbGRodHR0dAtDEXBscGkSQxaYwtLIC6QBk90WtmrZ06qqlWUjuY3RgCGB1dGhANo8VKMoKFEE2TwQoii4G4jGiuQWsAt3NAxR+VIRY3AcAluPMg1c1vFgAAAAASUVORK5CYII=',
			'09B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWl0bQh0EEESC2gFijU6NIgguS9q6dKlqaFAGsl9Aa2MgUB1jQ4oehmA5gFJFDtYQGJTGLC4BdPNjKEhgyD8qAixuA8AM6/M9F650uIAAAAASUVORK5CYII=',
			'1981' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgdWFsZHR2mIouJOog0ujYEhKLqFWl0dHSA6QU7aWXW0qVZoauWIrsPaEcgkjqoGAPIPDQxFixiYLegiImGgN0cGjAIwo+KEIv7AGi4yUeXoqhUAAAAAElFTkSuQmCC',
			'B878' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA6Y6IIkFTGFtBZIBAchirSKNDg2BDiLo6hodYOrATgqNWhm2aumqqVlI7gOrm8KAaV4AI6p5QDFHB0YMO1gbUPWC3dzAgOLmgQo/KkIs7gMAud/N/lHuQwIAAAAASUVORK5CYII=',
			'900A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMLQii4lMYQxhCGWY6oAkFtDK2sro6BAQgCIm0ujaEOggguS+aVOnrUxdFZk1Dcl9rK4o6iAQojc0BElMAGyHI4o6iFsYUcQgbkYVG6jwoyLE4j4AjDDKpINwg6cAAAAASUVORK5CYII=',
			'5B36' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQxhDGaY6IIkFNIi0sjY6BASgijU6NAQ6CCCJBQaItDI0Ojoguy9s2tSwVVNXpmYhu68VrA7FPKAY2DwRZDuwiIlMwXQLawCmmwcq/KgIsbgPAKE8zUV5vl0wAAAAAElFTkSuQmCC',
			'ED99' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6iOAWAzspNGrayszMqKgwJPeB1DmEBExF1+sAItHEHBsC0O3AcAs2Nw9U+FERYnEfAGblzgYhQOqCAAAAAElFTkSuQmCC',
			'503A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGVqRxQIaGENYGx2mOqCIsQLVBAQEIIkFBog0OjQ6OogguS9s2rSVWVNXZk1Ddl8rijqEWENgaAiyHa0gOwJR1IlMAbkFVS9rAMjNjKjmDVD4URFicR8AlxbMOBf9e7kAAAAASUVORK5CYII=',
			'E799' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGaY6IIkFNDA0Ojo6BASgibk2BDqIoIq1siLEwE4KjVo1bWVmVFQYkvuA6gIYQgKmoupldACRqGKsDYwNAWh2iDQworklNASoAs3NAxV+VIRY3AcAWPDNCcBMRgMAAAAASUVORK5CYII=',
			'0136' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGaY6IImxBjAGsDY6BAQgiYlMYQ1gaAh0EEASC2hlCGBodHRAdl/U0lVRq6auTM1Cch9UHYp5YDGgeSIodmCKAW3FcAujA2soupsHKvyoCLG4DwAmL8nRJ7VXPgAAAABJRU5ErkJggg==',
			'1FB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGaY6IImxOog0sDY6BAQgiYmCxBoCHQRQ9ILUOTogu29l1tSwpaErU7OQ3AdVh2IeI9Q8EWLE0N0SAhRDc/NAhR8VIRb3AQDlBsmg4s13YQAAAABJRU5ErkJggg==',
			'ABC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHUMDkMRYA0RaGR0CHZDViUwRaXRtEEQRC2gVaWVtYHR1QHJf1NKpYUtXrYyKQnIfRB3QDCS9oaEg81DFgOrAdqCJAd0SEBCAIgZys8NUh0EQflSEWNwHAL2PzEx3MsmqAAAAAElFTkSuQmCC',
			'831B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANYQximMIY6IImJTBFpZQhhdAhAEgtoZWh0BIqJoKhjaAXqhakDO2lp1KqwVdNWhmYhuQ9NHdw8hymo5mETA7sFTS/IzYyhjihuHqjwoyLE4j4ApL/LEl3IlWAAAAAASUVORK5CYII=',
			'293E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUMDkMREprC2sjY6OiCrC2gVaXRoCEQRYwCJIdRB3DRt6dKsqStDs5DdF8AY6IBmHqMDA4Z5rA0sGGIiDZhuCQ3FdPNAhR8VIRb3AQBIisqkhK/KdQAAAABJRU5ErkJggg==',
			'197A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgdWIH8gKkOSGKiDiKNDg0BAQEoeoFijY4OIkjuW5m1dGnW0pVZ05DcB7Qj0GEKI0wdVIyh0SGAMTQERYwFaBq6OtZW1gZUMdEQoJvRxAYq/KgIsbgPAJvzyPC8CtiFAAAAAElFTkSuQmCC',
			'59A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYQximMLQiiwU0sLYyhDJMRRUTaXR0dAhFFgsMEGl0bQiA6QU7KWza0qWpq6KWorivlTEQSR1UjKHRNRRVLKCVpRFdncgU1lZWNDHWAMYQoFhowCAIPypCLO4DAI9kzWpeZr0GAAAAAElFTkSuQmCC',
			'E6EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUMdkMQCGlhbWRsYHQJQxEQaQWIiqGINSOrATgqNmha2NHRlaBaS+wIaRLGa54ppHhYxTLdgc/NAhR8VIRb3AQCKqMu7F+oEQgAAAABJRU5ErkJggg==',
			'BC84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGRoCkMQCprA2Ojo6NKKItYo0uAJJVHUiDYyODlMCkNwXGjUNSKyKikJyH0SdowO6eawNgaEhmHZgcwuKGDY3D1T4URFicR8AmnjPwkkbJ/IAAAAASUVORK5CYII=',
			'A11C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YAhimMEwNQBJjDWAMYAhhCBBBEhOZAhQNYXRgQRILaAXpBZqA5L6opauiVk1bmYXsPjR1YBgaiikGU4dpB6pbAlpZQxlDHVDcPFDhR0WIxX0AhN3I0lqJY0oAAAAASUVORK5CYII=',
			'525B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUMdkMQCGlhbWRsYHQJQxEQaXYFiIkhigQEMja5T4erATgqbtmrp0szM0Cxk97UyTAGqRjEPKBYAEkM2L6CV0YEVTUxkCtAljo4oelkDREMdQhlR3DxQ4UdFiMV9AER6y0SdrewoAAAAAElFTkSuQmCC',
			'D42B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGUMdkMQCpjBMZXR0dAhAFgOqYm0IdBBBEWN0ZQCKBSC5L2rp0qWrVmaGZiG5L6BVpJWhlRHNPNFQhymMaOYB3RKAJjYFpBNVL8jNrKGBKG4eqPCjIsTiPgAVpcwQNdkRgQAAAABJRU5ErkJggg==',
			'1844' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHRoCkMRYHVhbGVodGpHFRB1EGh2mOrQGoOgFqgt0mBKA5L6VWSvDVmZmRUUhuQ+kjrXR0QFVr0ija2hgaAiamAOaW8B2oImJhmC6eaDCj4oQi/sA3/zMC8DctXsAAAAASUVORK5CYII=',
			'B909' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMEx1QBILmMLayhDKEBCALNYq0ujo6OgggqJOpNG1IRAmBnZSaNTSpamroqLCkNwXMIUx0LUhYCqK3lYGoN6ABlQxFqAdDmh2YLoFm5sHKvyoCLG4DwAEnc2whRjGQQAAAABJRU5ErkJggg==',
			'5E51' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHVqRxQIaRBpYGximYhELRRYLDACKTWWA6QU7KWza1LClmVlLUdzXKgIyAcUObGIBrSA7UMVEpog0MDqiuo81QDQU6JLQgEEQflSEWNwHAMQgy8biFOGUAAAAAElFTkSuQmCC',
			'3E95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANEQxlCGUMDkMQCpog0MDo6OqCobBVpYG0IRBWbAhZzdUBy38qoqWErMyOjopDdB1THEBLQIIJmHtAmDDFGoB3IYhC3OAQguw/iZoapDoMg/KgIsbgPAClAyr5+cXnCAAAAAElFTkSuQmCC',
			'EEC2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHaY6IIkFNIg0MDoEBASgibE2CDqIYIgxNIgguS80amrYUiAdheQ+qLpGdDuAYq0MGGICU9DFQG7BdLNjaMggCD8qQizuAwBDcMz7cr0oUgAAAABJRU5ErkJggg==',
			'1C55' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHUMDkMRYHVgbXYEyyOpEHUQa0MUYgWKsUxldHZDctzJr2qqlmZlRUUjuA6ljaAhoEEHTi03MtSHQAVWMtdHR0SEA2X2iIYyhDKEMUx0GQfhREWJxHwAOSckYcF9T8gAAAABJRU5ErkJggg==',
			'821C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDCECCCJBbQKtLoGMLowIKijqHRYQqjA7L7lkatWrpq2sosZPcB1QEhXB3UPIYATDEgfwq6HawNQN0obmENEA11DHVAcfNAhR8VIRb3AQBT1sq+5zFEkgAAAABJRU5ErkJggg==',
			'0196' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjAGMDo6BAQgiYlMYQ1gbQh0EEASC2hlAIshuy9q6aqolZmRqVlI7gOpYwgJRDEPLAbUK4JiB0MAI5oYawADhlsYHVhD0d08UOFHRYjFfQDaK8jHBQTPKgAAAABJRU5ErkJggg==',
			'8819' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDCEBCAJBbQKtLoGMLoIIKubgpcDOykpVErw1ZNWxUVhuQ+iDqGqSJo5jlMAcphimGxA9UtIDczhjqguHmgwo+KEIv7AIqoy9lslbLiAAAAAElFTkSuQmCC',
			'30EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHaYGIIkFTGEMYW1gCBBBVtnK2srawOjAgiw2RaTRFSiG7L6VUdNWpoauzEJxH6o6qHnYxDDtwOYWbG4eqPCjIsTiPgBNbsngTI3NXAAAAABJRU5ErkJggg==',
			'DA60' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMIYwOjpMdUAWa2VtZW1wCAhAERNpdG1gdBBBcl/U0mkrU6euzJqG5D6wOkdHmDqomGioa0MgmhjIvABUO6aINDqiuSU0QKTRAc3NAxV+VIRY3AcAsjfOhEtUvSQAAAAASUVORK5CYII=',
			'FF5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHUNDkMQCGkQaWBsYHRiIEZsKFwM7KTRqatjSzMzQLCT3gdQxNARi6MUmxopFjNHREVNvKKpbBir8qAixuA8Ak27Ky/82Y6cAAAAASUVORK5CYII=',
			'5E2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGaYGIIkFNIg0MDo6BIigibE2BDqwIIkFglUEOiC7L2za1LBVKzOzUNzXClTXyuiAYjNIbAqqWABILIARxQ6RKUC3ODCguIU1QDSUNTQAxc0DFX5UhFjcBwAQlcpHss8BmgAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>