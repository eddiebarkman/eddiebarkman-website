<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "info@eddiebarkman.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "db936e" );

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
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
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
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
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
			'DD89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtCHQQQRNzdHSEiYGdFLV02sqs0FVRYUjug6hzmIqu17UhoAGLGKodWNyCzc0DFX5UhFjcBwDUl85gPLQIewAAAABJRU5ErkJggg==',
			'480A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjCGMExhaEURC2FtZQhlmOqAJMYYItLo6OgQEIAkxjqFtZW1IdBBBMl906atDFu6KjJrGpL7AlDVgWFoqEija0NgaAiKW0B2OKKoY5gCcgsjmhjIzWhiAxV+1INY3AcA+07LMXTmb6kAAAAASUVORK5CYII=',
			'8E6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtcAgIQFPH2sDoIILkvqVRU8OWTl2ZNQ3JfWB1jo4wdUjmBYaGYIqhqIO4BVUvxM2MKGIDFX5UhFjcBwAfZMs2unvTbgAAAABJRU5ErkJggg==',
			'1DDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUMDkMRYHURaWRsdHZDViTqINLo2BDqg6kURAztpZda0lamrIkOzkNzHSFgvPjFMt4Rgunmgwo+KEIv7ABVEyPZOs1B8AAAAAElFTkSuQmCC',
			'F5FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA6YGIIkFNIg0sDYwBIhgiDE6sKCKhYDEkN0XGjV16dLQlVnI7gOa3eiKUIdHTAQshmoHayumWxiB9jKguHmgwo+KEIv7AN5ny/bl358rAAAAAElFTkSuQmCC',
			'5398' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGaY6IIkFNIi0Mjo6BASgiDE0ujYEOoggiQUGMLSyNgTA1IGdFDZtVdjKzKipWcjuawXCkAAU84AijQ5o5gUAxRzRxESmYLqFNQDTzQMVflSEWNwHAI7TzF9tK+c9AAAAAElFTkSuQmCC',
			'E12B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUMdkMQCGhgDGB0dHQJQxFgDWBsCHURQxIB6gWIBSO4LjVoVtWplZmgWkvvA6loZ0cwDik1hxDQvAFOM0QFVb2gIayhraCCKmwcq/KgIsbgPAAo1yZqJChaAAAAAAElFTkSuQmCC',
			'5AE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHVqRxQIaGENYGximooqxtgLFQpHFAgNEGl0bGGB6wU4KmzZtZWroqqUo7mtFUQcVEw1FFwvAok5kCqYYK8jeUIfQgEEQflSEWNwHAERfzHXQuNlUAAAAAElFTkSuQmCC',
			'E49D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYWhlCGUMdkMQCGhimMjo6OgSgioWyNgQ6iKCIMboiiYGdFBq1dOnKzMisaUjuC2gQaWUIQdcrCrQTXYyhlRGbGJpbsLl5oMKPihCL+wChn8vMh5Q36wAAAABJRU5ErkJggg==',
			'E79C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGaYGIIkB2Y2Ojg4BImhirg2BDiyoYq2sQDFk94VGrZq2MjMyC9l9QHUBDCFwdVAxRiAfXYy1gRHDDpEGRjS3hIYAeWhuHqjwoyLE4j4AT0rMJvbF/f8AAAAASUVORK5CYII=',
			'D2E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHRoCkMQCprC2sjYwNKKItYo0ujYwtKKKMYDEpgQguS9q6aqlS0NXRUUhuQ+obgprA6MDmt4AoFhoCIoYowMriEZ1SwO6WGiAaKgrmpsHKvyoCLG4DwCIk87Rlb+n5QAAAABJRU5ErkJggg==',
			'7ED9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGaY6IIu2ijSwNjoEBKCLNQQ6iCCLTUERg7gpamrY0lVRUWFI7mN0AKkLmIqsl7UBLNaALCYCEUOxA6QC3S0BDVjcPEDhR0WIxX0AErvMQ3812OoAAAAASUVORK5CYII=',
			'9826' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEARY21lAIohu2/a1JVhq1ZmpmYhuY/VFaiulRHFPAageQ5TGB1EkMQEQGIBqGJgtzgwoOgFuZk1NADFzQMVflSEWNwHAL1yyvC6DqATAAAAAElFTkSuQmCC',
			'A965' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtQBULaAWJMbo6ILkvaunSpalTV0ZFIbkvoJUx0NXRoUEESW9oKANQbwCKWEArC1As0AFVDOQWh4AAFDGQmxmmOgyC8KMixOI+ALL+zESrMYggAAAAAElFTkSuQmCC',
			'F526' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGaY6IIkFNIg0MDo6BASgibE2BDoIoIqFMADFkN0XGjV16aqVmalZSO4DmtPo0MqIZh5QbAqjgwiqeY0OAehirK2MDgxoehlDWEMDUNw8UOFHRYjFfQDC7syw5D15pwAAAABJRU5ErkJggg==',
			'AADE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUMDkMRYAxhDWBsdHZDViUxhbWVtCEQRC2gVaXRFiIGdFLV02srUVZGhWUjuQ1MHhqGhoqHoYtjUgcXQ3AIWQ3PzQIUfFSEW9wEAiEDMPwiBjgkAAAAASUVORK5CYII=',
			'D9D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGVqRxQKmsLayNjpMRRFrFWl0bQgIxSIG0wt2UtTSpUtTgSSy+wJaGQOR1EHFGBoxxVgwxSBuQRGDujk0YBCEHxUhFvcBAMzazx00V3plAAAAAElFTkSuQmCC',
			'48BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpI37pjCGsIYyhoYgi4WwtrI2Ojogq2MMEWl0bQhEEWOdgqIO7KRp01aGLQ1dGZqF5L6AKZjmhYZimscwBZsYpl6om1HFBir8qAexuA8Aw8vKRqiKjnQAAAAASUVORK5CYII=',
			'3DC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7RANEQxhCHUIdkMQCpoi0MjoEOgQgq2wVaXRtEGgQQRabAhIDqkdy38qoaStTV61amoXsPlR1SOYxoJqHxQ5sbsHm5oEKPypCLO4DAMnyzXiflc8tAAAAAElFTkSuQmCC',
			'8F58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DHaY6IImJTBFpYG1gCAhAEgtoBYkxOoigq5sKVwd20tKoqWFLM7OmZiG5D6QOaAKGeQwNgSjmQewIxLCD0dEBRS9rAFBFKAOKmwcq/KgIsbgPAIWDzHJIJC+NAAAAAElFTkSuQmCC',
			'EC97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGUNDkMQCGlgbHR0dGkRQxEQaXMEkqhgrkAxAcl9o1LRVKzOjVmYhuQ+sKySglQFNL5Ccgi7m2BAQwIDhFkcHLG5GERuo8KMixOI+ACLJzYGT6EpiAAAAAElFTkSuQmCC',
			'8EB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDqIYKprEEFy39KoqWFLQ1etikJyH1RdowOGeQGtDJhiUxiwuAXTzYyhIYMg/KgIsbgPAD+yzQMBIvdKAAAAAElFTkSuQmCC',
			'DA5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUMdkMQCpjCGsDYwOgQgi7WytoLERFDERBpdp8LVgZ0UtXTaytTMzNAsJPeB1Dk0BKKZJxoKEsMwD11sikijo6Mjit7QAKB5oYwobh6o8KMixOI+ALxszbWC4fiyAAAAAElFTkSuQmCC',
			'CFB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WENEQ11DGRoCkMREWkUaWBsdGpHFAhqBYg0BrShiDWB1UwKQ3Be1amrY0tBVUVFI7oOoc3TA0NsQGBqCaQc2t6CIsYYAxdDcPFDhR0WIxX0A2WnPIgZ3lqMAAAAASUVORK5CYII=',
			'EE44' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQxkaHRoCkMQCGkQaGFodGjHEpjq0YogFOkwJQHJfaNTUsJWZWVFRSO4DqWNtdHRA18saGhgagm4eNregiWFz80CFHxUhFvcBAGqSz5t5OxdkAAAAAElFTkSuQmCC',
			'C1BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYAlhDGaYGIImJtDIGsDY6BIggiQU0sgawNgQ6sCCLNQD1Njo6ILsvCoiWhq7MQnYfmjqEGNA8FLFGBgw7RFoZMNzCGsIaiu7mgQo/KkIs7gMAeuDKCsi0ofYAAAAASUVORK5CYII=',
			'56B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGVqRxQIaWFtZGx2mOqCIiTSyNgQEBCCJBQaINLA2OjqIILkvbNq0sKWhK7OmIbuvVbQVSR1UTKTRtSEQRSwALIZqh8gUTLewBmC6eaDCj4oQi/sAM/7M9XjPvs0AAAAASUVORK5CYII=',
			'7C5A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDHVpRRFtZG10bGKY6oIiJNADFAgKQxaaINLBOZXQQQXZf1LRVSzMzs6YhuQ+iIhCmDgxZG8BioSFIYiINIDtQ1QU0sDY6OjqiiTGGMoQyoogNVPhREWJxHwC2d8u7bKR7nwAAAABJRU5ErkJggg==',
			'A5EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMdkMRYA0QaWIEyAUhiIlMgYiJIYgGtIiFI6sBOilo6denS0JWhWUjuC2hlaHRFMy80FCKGZh4WMdZWdLcEtDKGoLt5oMKPihCL+wDXNMtteN3xYwAAAABJRU5ErkJggg==',
			'4AEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpI37pjAEsIY6hgYgi4UwhrA2MDogqwOKtKKLsU4RaXRFiIGdNG3atJWpoStDs5DcF4CqDgxDQ0VD0cUYsKjDKYbu5oEKP+pBLO4DANTcyeR8/I+XAAAAAElFTkSuQmCC',
			'6041' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHVqRxUSmMIYwtDpMRRYLaGFtZZjqEIoi1iDS6BAI1wt2UmTUtJWZmVlLkd0XMkWk0RXNjoBWoFhoAJoY0A5sbkETg7o5NGAQhB8VIRb3AQA/4M0hCSrvXgAAAABJRU5ErkJggg==',
			'A30A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1YQximMLQii7EGiLQyhDJMdUASE5nC0Ojo6BAQgCQW0MrQytoQ6CCC5L6opavClq6KzJqG5D40dWAYGsrQ6NoQGBqCah7QDkcUdQGtILcwoomB3IwqNlDhR0WIxX0AkpnL1qB64wsAAAAASUVORK5CYII=',
			'2AEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHVqRxUSmMIawNjBMdUASC2hlbQWKBQQg624VaXRtYHQQQXbftGkrU0NXZk1Ddl8AijowZHQQDQWKhYYgu6UBU50IFrHQUKBYqCOK2ECFHxUhFvcBAI8syv5WSoMmAAAAAElFTkSuQmCC',
			'2DA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQKtLo6OjoIICsGyjm2hDogOK+adNWpq6KTM1Cdl8AWB2KeYwOQLHQQAcRZLc0QMxDFhNpEGllbQhA0RsaKhoCFENx80CFHxUhFvcBALbqzN4muXsTAAAAAElFTkSuQmCC',
			'2E45' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxkaHUMDkMREpog0MLQ6OiCrC2gFik1FFWMAiQU6ujogu2/a1LCVmZlRUcjuCxBpYG10aBBB0svoABQD2oosxgriNTo6IIuJgMUcApDdFxoKcrPDVIdBEH5UhFjcBwAGjMtvlSqn6wAAAABJRU5ErkJggg==',
			'D93F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUNDkMQCprC2sjY6OiCrC2gVaXRoCMQUQ6gDOylq6dKlWVNXhmYhuS+glTHQAcM8BizmsWCKYXEL1M0oYgMVflSEWNwHANo2zJKxX0fUAAAAAElFTkSuQmCC',
			'EC70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDA1qRxQIaWBsdGgKmOqCIiTQAxQIC0MQYGh0dRJDcFxo1bdWqpSuzpiG5D6xuCiNMHUIsAFPM0YEBzQ7WRtcGBhS3gN3cwIDi5oEKPypCLO4DAL0gzf1OjyXvAAAAAElFTkSuQmCC',
			'0DF3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA0IdkMRYA0RaWYEyAUhiIlNEGl1BNJJYQCtELADJfVFLp61MDV21NAvJfWjqUMRECNiBzS1gNzcwoLh5oMKPihCL+wC/Psye/hUG9QAAAABJRU5ErkJggg==',
			'714D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHUMdkEVbGQMYWh0dAlDEWAMYpjo6iCCLTQHqDYSLQdwUtSpqZWZm1jQk9zE6MASwNqLqZW0AioUGoogB2SC3oIgFQMUCUMRYQzHcPEDhR0WIxX0AtX/JhpT2Z/IAAAAASUVORK5CYII=',
			'90E1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHVqRxUSmMIawNjBMRRYLaGVtBYqFooqJNLo2MMD0gp00beq0lamhq5Yiu4/VFUUdBLZiiglA7MDmFhQxqJtDAwZB+FERYnEfAH/cytFY4Yp3AAAAAElFTkSuQmCC',
			'2932' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGaY6IImJTGFtZW10CAhAEgtoFWl0aAh0EEHWDRIDioogu2/a0qVZU1etikJ2XwBjoANYJUIvowMDkB/QiuKWBhaQ2BRkMZEGiFuQxUJDQW5mDA0ZBOFHRYjFfQBXVM0J5fNLQwAAAABJRU5ErkJggg==',
			'1B23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUIdkMRYHURaGR0dHQKQxEQdRBpdGwIaRFD0irQyAMUCkNy3MmtqGJBYmoXkPrA6sEoUvY0OUxjQzWt0CMAQa2V0YER1S4hoCGtoAIqbByr8qAixuA8AL13Jyy8l8XUAAAAASUVORK5CYII=',
			'2127' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QwQ2AIAxF20M3YCDc4JvYiyMwBRc2IO4AU1rjpUSPGuXdXtr0BeqXl+lPvNInIJCyLs6FyuAp5uAcikAyBkfFds3B92197S215PuOG8VwuxzNVcO3nJPwzu6Bo007pyrGPLiv/u9Bbvp2sbDIRzePB0gAAAAASUVORK5CYII=',
			'BCCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCHVqRxQKmsDY6OgRMdUAWaxVpcG0QCAhAUSfSwNrA6CCC5L7QqGmrlq5amTUNyX1o6uDmAcVCQzDsEERVB3ZLIIoYxM2OKGIDFX5UhFjcBwAKyc15ezg4IgAAAABJRU5ErkJggg==',
			'0A14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YAhimMDQEIImxBjCGMIQwNCKLiUxhbQWKtiKLBbSKNDpMYZgSgOS+qKXTVmZNWxUVheQ+iDpGB1S9oqFAsdAQFDvA5qG5BVOM0UGk0THUAUVsoMKPihCL+wB4TM2gtGp6mAAAAABJRU5ErkJggg==',
			'0B24' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGRoCkMRYA0RaGR0dGpHFRKaINLo2BLQiiwW0irQCySkBSO6LWjo1bNXKrKgoJPeB1bUyOqDpbXSYwhgagmaHQwAWtzigioHczBoagCI2UOFHRYjFfQBt8c0r5hroIQAAAABJRU5ErkJggg==',
			'54E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYWllDHUNDkMQCGhimsgJpEVSxUHSxwABGV1aIHNx9YdOWLl0aumplFrL7WkVagepaUWxuFQ11bWCYgiwW0MoAUheALCYyBSTG6IAsxhoAdjOK2ECFHxUhFvcBAAN1yumBTlUiAAAAAElFTkSuQmCC',
			'72B4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ3AIAwETeENyD6koHck3DCNXbABK9AwZSgNSZko8Xenf+lk6JcT+FNe8WN2CRmELC1YUIPOzGsUKhOroFFDJeuXe2vcczZ+LkBF3YPdogChHJwM86OJw8T2aDSHy8I2jqvzR/97MDd+J7ztzlHJXd+5AAAAAElFTkSuQmCC',
			'DE1B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIY6IIkFTBFpYAhhdAhAFmsVaWAEiomgiQH1wtSBnRS1dGrYqmkrQ7OQ3IemDkUMm3ki6G5B0wtyM2OoI4qbByr8qAixuA8AA2XMHjsaK5kAAAAASUVORK5CYII=',
			'4174' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nM2QsQ2AMAwE7cJ7ZYQv4ibTOAUbRGxA4ylJOpNQgsDfnf6lk8mXM/pT3vFrBFEYIssMMtTIOMtgW2TSt1RTQ/Dbdy9+eCnBD6PXOMWtamdgzZMLJ7q6DD+bmejCvvrfc7nxOwFXnMtnNKbJZwAAAABJRU5ErkJggg==',
			'C55B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WENEQ1lDHUMdkMREWkUaWBsYHQKQxAIaIWIiyGINIiGsU+HqwE6KWjV16dLMzNAsJPcB5RsdGgJRzYOKiaDa0eiKJibSytrK6OiIopc1hDGEIZQRxc0DFX5UhFjcBwC8YMve8f7V7AAAAABJRU5ErkJggg==',
			'FFFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAQ0lEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA0NDkMQCGkQaWBsYHRhIEwM7KTRqatjS0JWhWUjuI9M8ksQGKvyoCLG4DwD0Zso/lL96HwAAAABJRU5ErkJggg==',
			'0E68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaY6IImxBog0MDo6BAQgiYlMEWlgbXB0EEESC2gFiTHA1IGdFLV0atjSqaumZiG5D6wOzTyI3kAU8yB2oIphcws2Nw9U+FERYnEfAPusy0Qtd1npAAAAAElFTkSuQmCC',
			'7B97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM3QsRGAMAhA0aRwA9wnKewpgkU20ClIwQZxiDilnBU5LfUUutfwD7dfht2f9pU+ojE58pSsCoiPgaG3MjH2VkEGNbR9eZvbkttq+nwAcQnF3h0YSmCs1kAtMqI1vagtMfR2Nnf21f8e3Ju+A+fAy9o5gpKrAAAAAElFTkSuQmCC',
			'9E9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaYGIImJTBFpYHR0CBBBEgtoFWlgbQh0YMEihuy+aVOnhq3MjMxCdh+rK9CkELg6CGwFmY4qJgAUY0SzA5tbsLl5oMKPihCL+wDha8pEQKmYFAAAAABJRU5ErkJggg==',
			'F745' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHUMDkMSA7EaHVkcHBnSxqRhirQyBjq4OSO4LjVo1bWVmZlQUkvuA6gJYGx0aRFD0MjqwAm1FFWMF2uLogComArI5IABTbKrDIAg/KkIs7gMACnHNwQVjD7oAAAAASUVORK5CYII=',
			'DDC4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCHRoCkMQCpoi0MjoENKKItYo0ujYItGKKMUwJQHJf1NJpK1OBVBSS+yDqgCZi6GUMDcG0A5tbUMSwuXmgwo+KEIv7AHVj0GjQe9baAAAAAElFTkSuQmCC',
			'151B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxOog0MIQwOgQgiYkCxRiBYiIoekVCgHph6sBOWpk1demqaStDs5Dcx+jA0OgwBdU8mBiaeVjEWFsZ0PSKhgBdEuqI4uaBCj8qQizuAwDlgcgfUaGgTQAAAABJRU5ErkJggg==',
			'074B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHUMdkMRYAxgaHVodHQKQxESmAMWmOjqIIIkFtDK0MgTC1YGdFLV01bSVmZmhWUjuA6oLYG1ENS+gldGBNTQQxTyRKawNDI2odrAGiIDFkPUyOoDFUNw8UOFHRYjFfQDEa8u899uLBAAAAABJRU5ErkJggg==',
			'404F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjAEMDQ6hoYgi4UwhjC0Ojogq2MMYW1lmIoqxjpFpNEhEC4GdtK0adNWZmZmhmYhuS8AqM61EVVvaChQLDTQAdUtQDvQ1DFMAboFQwzsZlSxgQo/6kEs7gMAcS/J/pdqJfkAAAAASUVORK5CYII=',
			'8527' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsRGAMAhFP0U2wH1wA7xLGkdwCiyyQRzBQqc0neG01FN+9+DgHdgvZfhTXvEL2iUkSrFhXNioF+OGaWYLpo7VuVg7NaffOi7rvk3b1PhxwSwZGW5fZQXFM55FoXA3QiYh8c4UQxoc++p/D+bG7wDbR8uq5lsUEAAAAABJRU5ErkJggg==',
			'FA96' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGaY6IIkFNDCGMDo6BASgiLG2sjYEOgigiIk0ugLFkN0XGjVtZWZmZGoWkvtA6hxCAtHMEw11AOoVQTPPEZsYhluA5qG5eaDCj4oQi/sAsEPNwzwb1boAAAAASUVORK5CYII=',
			'EF9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUNDkMQCGkQaGB0dHRjQxFgbAvGJgZ0UGjU1bGVmZGgWkvtA6hhCMPUyYDGPEZsYmltCQ4B6QxlRxAYq/KgIsbgPAHX1yqrK8/84AAAAAElFTkSuQmCC',
			'86B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMdUASC2gVaWRtCAgIQFEn0sDa6OggguS+pVHTwpaGrsyahuQ+kSmirUjq4Oa5NgRiEUO3A9Mt2Nw8UOFHRYjFfQAe/s0LtwvGaQAAAABJRU5ErkJggg==',
			'44BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjC0soYyhjogi4UwTGVtdHQIQBJjDGEIZW0IdBBBEmOdwugKUieC5L5p05YuXRq6MmsakvsCpoi0IqkDw9BQ0VBXNPPAbsEmhuYWrG4eqPCjHsTiPgDdw8tN941dXQAAAABJRU5ErkJggg==',
			'325D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHUMdkMQCprC2sjYwOgQgq2wVaXQFiokgi01haHSdChcDO2ll1KqlSzMzs6Yhu28KEDYEouptZQjAFGN0YEUTA7qlgdHREcUtogGioQ6hjChuHqjwoyLE4j4AVH7Kp9RSSgcAAAAASUVORK5CYII=',
			'0551' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHVqRxVgDRBpYGximIouJTAGLhSKLBbSKhLBOZYDpBTspaunUpUszs5Yiuy+glaHRAUSi6MUUA9rR6IomxhrA2sroiOo+RgfGEKBLQgMGQfhREWJxHwBo78utjMVsCwAAAABJRU5ErkJggg==',
			'832F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUNDkMREpoi0Mjo6OiCrC2hlaHRtCEQRE5nC0MqAEAM7aWnUqrBVKzNDs5DcB1bXyohhnsMULGIBjGh2AN3igCoGcjNrKKpbBir8qAixuA8AuOzJQdzxjWwAAAAASUVORK5CYII=',
			'F458' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZWllDHaY6IIkFNDBMZW1gCAhAFQtlbWB0EEERY3RlnQpXB3ZSaNTSpUszs6ZmIbkvoEGkFUiimSca6tAQiGYe0C1YxBgdHdDd0soQyoDi5oEKPypCLO4DAD+rzR98eZcKAAAAAElFTkSuQmCC',
			'B4A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYWhmmADGSWMAUhqkMoQxTHZDFWhlCGR0dAgJQ1DG6sjYEOogguS80aunSpasis6YhuS9gikgrkjqoeaKhrqHoYgxAdQFodoDFUNwCcjMrSPUgCD8qQizuAwAXbc39tSll1gAAAABJRU5ErkJggg==',
			'A3AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1YQximMIY6IImxBoi0MoQyOgQgiYlMYWh0dHR0EEESC2hlaGVtCISpAzspaumqsKWrIkOzkNyHpg4MQ0MZGl1DA9HNa3RtQBcTwdAb0MoaAhRDcfNAhR8VIRb3AQASBcygsgLzGQAAAABJRU5ErkJggg==',
			'0A37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUNDkMRYAxhDWBsdGkSQxESmsLYyNASgiAW0ijQ6ANUFILkvaum0lVlTV63MQnIfVF0rA4pe0VCgzikMKHaIgEwLYEBxi0ija6OjA6qbRRodQxlRxAYq/KgIsbgPABO6zPkHQmm/AAAAAElFTkSuQmCC',
			'E383' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGUIdkMQCGkRaGR0dHQJQxBgaXUEyqGJAdQ4NAUjuC41aFbYqdNXSLCT3oanDZx4WMUy3YHPzQIUfFSEW9wEADbbNvpYTpYYAAAAASUVORK5CYII=',
			'0C5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHUNDkMRYA1gbXYEyyOpEpog0oIsFtIo0sE6Fi4GdFLV02qqlmZmhWUjuA6ljaAjE0IsuBrEDVQzkFkdHRxQxkJsZQlHdMlDhR0WIxX0AEZnJk8floDcAAAAASUVORK5CYII=',
			'0EEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMDkMRYA0QaWIEyyOpEpmCKBbSiiIGdFLV0atjS0JWhWUjuQ1OHUwybHdjcgs3NAxV+VIRY3AcAZhTIXEHqw84AAAAASUVORK5CYII=',
			'CEAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WENEQxmmMIYGIImJtIo0MIQyOiCrC2gUaWB0dEQVaxBpYG0IhImBnRS1amrY0lWRoVlI7kNThxALDcSwA10dyC3oYiA3A8VQ3DxQ4UdFiMV9ACaFysg7mqMqAAAAAElFTkSuQmCC',
			'60C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHaY6IImJTGEMYXQICAhAEgtoYW1lbRB0EEEWaxBpdAWpR3JfZNS0lalAOgrJfSFTwOoake0IaAWLtTKgiIHsEJjCgMUtmG52DA0ZBOFHRYjFfQBPecw+3RdbgAAAAABJRU5ErkJggg==',
			'1E1E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIYGIImxOog0MIQwOiCrEwWKMaKJMYLUTYGLgZ20Mmtq2KppK0OzkNyHpo5iMdEQ0VDGUEcUNw9U+FERYnEfAHlFxiSRAPWKAAAAAElFTkSuQmCC',
			'814A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHVqRxUSmMAYwtDpMdUASC2gFqpzqEBCAog6oN9DRQQTJfUujVkWtzMzMmobkPpA61ka4Oqh5QLHQwNAQNDEGNHVgO9DEWIE60cUGKvyoCLG4DwAWrMpejUSnWAAAAABJRU5ErkJggg==',
			'4681' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjCGMIQytKKIhbC2Mjo6TEUWYwwRaWRtCAhFFmOdItIAVAfTC3bStGnTwlaFrlqK7L6AKaKtSOrAMDRUpNG1IQDV3inYxFgx9ELdHBowGMKPehCL+wAGnMuESxM4lgAAAABJRU5ErkJggg==',
			'667A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsAL5AVMdkMQCWkQagWRAALJYg0gDQ6OjgwiS+yKjpoWtWroyaxqS+0KmiLYyTGGEqYPobRVpdAhgDA1BE3N0QFUHcgtrA6oY2M1oYgMVflSEWNwHABNry8Ti/zT2AAAAAElFTkSuQmCC',
			'F9CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMdkMQCGlhbGR0CHQJQxEQaXRsEHUQwxBhhYmAnhUYtXZq6amXWNCT3BTQwBiKpg4oxNGKKsWCxA5tbMN08UOFHRYjFfQC6MsyiHhwLSAAAAABJRU5ErkJggg==',
			'51D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUNDkMQCGhgDWBsdGkRQxFgDWIEkslhgAANYLADJfWHTVkUtXRW1MgvZfa1gda0oNkPEpiCLBUDEApDFRKYAxRodHZDFgC4JBboZRWygwo+KEIv7AJ7sypO3fyyfAAAAAElFTkSuQmCC',
			'B43B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGUMdkMQCpjBMZW10dAhAFmtlCGVoCHQQQVHH6MqAUAd2UmjU0qWrpq4MzUJyX8AUkVYGDPNEgXaimdfK0IppB0MruluwuXmgwo+KEIv7AKJXzWQMsG3DAAAAAElFTkSuQmCC',
			'AE32' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlDGaY6IImxBog0sDY6BAQgiYlMEQGSgQ4iSGIBrUBeo0ODCJL7opZODVs1FUgjuQ+qrhHZjtBQEbAMA7p5DQFT0MVAbkEVA7mZMTRkEIQfFSEW9wEAc1TNb9Zg68oAAAAASUVORK5CYII=',
			'D399' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaY6IIkFTBFpZXR0CAhAFmtlaHRtCHQQQRVrZUWIgZ0UtXRV2MrMqKgwJPeB1DGEBExF09vo0BDQgC7m2BCAagcWt2Bz80CFHxUhFvcBANfbzZR9y4HGAAAAAElFTkSuQmCC',
			'8F73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0IdkMREpogAyUCHACSxgFaQWECDCLq6RoeGACT3LY2aGrZq6aqlWUjuA6ubwtCAYV4AA4p5IDFGBwYMO1iBosh6WQNAYgwobh6o8KMixOI+AL+ezTLb9WhxAAAAAElFTkSuQmCC',
			'454D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37poiGMjQ6hjogi4WINDC0OjoEIIkxgsSmOjqIIImxThEJYQiEi4GdNG3a1KUrMzOzpiG5L2AKQ6NrI6reUKCtrqGBKGIMU0QaHdDUMUxhbQW6D8UtDFMYQzDcPFDhRz2IxX0A17vMElaqanQAAAAASUVORK5CYII=',
			'8338' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANYQxhDGaY6IImJTBFpZW10CAhAEgtoZWh0aAh0EEFRxwAShakDO2lp1KqwVVNXTc1Cch+aOpzmYbcD0y3Y3DxQ4UdFiMV9AMXIzX8eD1nvAAAAAElFTkSuQmCC',
			'8A5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjCGsDYwOgQgiQW0sraCxERQ1Ik0uk6Fi4GdtDRq2srUzMysaUjuA6lzaAhE0RvQKhqKKQY0D00MpNfR0RHFLawBQPNCGVHcPFDhR0WIxX0AnrrMDKSf0DEAAAAASUVORK5CYII=',
			'3610' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYQximMLQiiwVMYW1lCGGY6oCsslWkEagyIABZbIoIEDM6iCC5b2XUtLBV01ZmTUN23xTRViR1cPMcsIqh2gF2yxRUt4DczBjqgOLmgQo/KkIs7gMA0yHLQVkf/AgAAAAASUVORK5CYII=',
			'A145' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUMDkMRYAxgDGFodHZDViUxhDWCYiioW0ArUG+jo6oDkvqilq6JWZmZGRSG5D6SOtdGhQQRJb2goUAxoqwi6eY2ODphiDgEBKGKsoUCxqQ6DIPyoCLG4DwC1Y8qtZvKEjQAAAABJRU5ErkJggg==',
			'23BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANYQ1hDGUMdkMREpoi0sjY6OgQgiQW0MjS6NgQ6iCDrbmVAVgdx07RVYUtDV4ZmIbsvgAHDPEYHTPNYGzDFRBow3RIaiunmgQo/KkIs7gMATrDLg0de50sAAAAASUVORK5CYII=',
			'7AFE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MDkEVbGUNYGxgdUFS2srZiiE0RaXRFiEHcFDVtZWroytAsJPcxOqCoA0PWBtFQdDGRBkx1AbjFUN08QOFHRYjFfQDWScnZRrphvwAAAABJRU5ErkJggg==',
			'29FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0MdkMREprC2sjYwOgQgiQW0ijS6AsVEkHWjikHcNG3p0tTQlVnTkN0XwBiIrpfRgQHDPNYGFgwxkQZMt4SGAt3cwIji5oEKPypCLO4DAMGByixeX5fyAAAAAElFTkSuQmCC',
			'C029' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGaY6IImJtDKGMDo6BAQgiQU0srayNgQ6iCCLNYg0OiDEwE6KWjVtZdbKrKgwJPeB1bUyTMXQOwVoF5odQNeg2AF2iwMDiltAbmYNDUBx80CFHxUhFvcBAFsIy4Nn+s8vAAAAAElFTkSuQmCC',
			'523E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGUMDkMQCGlhbWRsdHRhQxEQaHRoCUcQCAxgaHRDqwE4Km7Zq6aqpK0OzkN3XyjCFAc08oFgAA5p5Aa2MDuhiIlNYG9DdwhogGuqI5uaBCj8qQizuAwDmd8r5I/55tAAAAABJRU5ErkJggg==',
			'9EB7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUNDkMREpog0sDY6NIggiQW0AsUaAjDFgOoCkNw3berUsKWhq1ZmIbmP1RWsrhXFZoh5U5DFBCBiAQwYbnF0wOJmFLGBCj8qQizuAwAi18vY1bBFKwAAAABJRU5ErkJggg==',
			'172A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgdGBodHR2mOiCJiQLFXBsCAgJQ9IL0BTqIILlvZdaqaatWZmZNQ3IfUF0AQysjTB1UDCg6hTE0BEWMtYEhAF2dCFgtsphoiEgDa2ggithAhR8VIRb3AQBK4MfvrSe96wAAAABJRU5ErkJggg==',
			'FBF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA0NDkMQCGkRaWYG0CKpYoyumGFhdAJL7QqOmhi0NXbUyC8l9UHWtDJjmTcEiFsCAYQejA6oY0M1oYgMVflSEWNwHAEv3zNq75mtQAAAAAElFTkSuQmCC'        
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