<?php
// image-form.php : Joshua Lee
// twelve23 backend test : item 3

// instructions :
// 3) Create a form that accepts an image, and gives you an error if it's not jpeg or png, or if the file size is above 2 megs. 
// After post, resize the image and display as a jpeg that fits into 400x300 without changing the aspect ratio 
// (dimensions can be reduced if it's a different aspect ratio). Don't use an all in one library solution.

// form processor class
class form {
	
	public $form_action;
	public $form_method = 'post';
	public $form_enctype = 'multipart/form-data';
	public $form_image_id = 'image';
	
	private $state = 'init';
	private $error = '';
	private $accept_types = array('image/jpg', 'image/jpeg', 'image/png');
	private $file_mb_limit = 2;
	private $image_size_limit = 0;
	private $image_width = 400;
	private $image_height = 300;
	private $final_new_width = 0;
	private $final_new_height = 0;
	private $out_image_path = '';
	
	public function __construct() {
		$this->form_action = $_SERVER['PHP_SELF'];
		$this->size_limit = 1024 * 1024 * $this->file_mb_limit;  // 2 megabytes
	}
	
	private function process_image($in_file, $out_file)
	{
		// jpg, png, gif or bmp?
		$exploded = explode('.', $in_file);
		$ext = $exploded[count($exploded) - 1];
		$quality = 100;
	
		// create jpeg by input type
		if ( preg_match('/jpg|jpeg/i', $ext) ) { // jpeg
			$image_tmp = imagecreatefromjpeg($in_file);
		} elseif ( preg_match('/png/i', $ext) ) { // png
			$image_tmp = imagecreatefrompng($in_file);
		} else {
			return false;
		}
		
		// get base dimensions for conversion
		$width = imagesx($image_tmp);
		$height = imagesy($image_tmp);
		
		$new_width = $this->image_width;
		$new_height = intval(($this->image_width / $width) * $height);
		
		if($new_height <= $this->image_height){
			$this->final_new_height = $new_height;
			$this->final_new_width = $new_width;
		} else {
			$this->final_new_height = $this->image_height;
			$this->final_new_width = intval(($this->image_height / $new_height) * $new_width);
		}
		
		// build final image
		$final_image_tmp = imagecreatetruecolor($this->final_new_width, $this->final_new_height);
		imagecopyresampled($final_image_tmp, $image_tmp, 0, 0, 0, 0, $this->final_new_width, $this->final_new_height, $width, $height);
		imagedestroy($image_tmp);

		imagejpeg($final_image_tmp, $out_file, $quality);
		imagedestroy($final_image_tmp);
		
		return true;
	}	
	
	public function process() {
		$this->state = 'process';
		
		// vars
		$upload_error_msg = "An error occured while uploading your image. Try again.";
		
		// validate image
		if( isset($_FILES[$this->form_image_id]['name']) && $_FILES[$this->form_image_id]['name'] != '' ){
			
			$img = $_FILES[$this->form_image_id];
			
			if($img['error'] === UPLOAD_ERR_OK){
				if( in_array($img['type'], $this->accept_types) ){
					if($img['size'] > $this->size_limit){
						$this->error = "Image upload is too large. Must be {$this->file_mb_limit} MB or less.";
					}
				} else {
					$this->error = "{$img['type']} type not supported. Please select a jpeg or png.";
				}
			} else {
				$this->error = $upload_error_msg;
			}
			
			if($this->error == ''){
				
				// move uploaded image (make sure there are write permissions to /uploads directory)
				$uploads_path = dirname(__FILE__) . '/uploads';
				$target_path = $uploads_path . '/' . $img['name'];
				if( move_uploaded_file($img['tmp_name'], $target_path) ){
					
					// convert to jpeg & resize
					$this->out_image_path = $uploads_path . '/tmp_img.jpg';
					if( !$this->process_image($target_path, $this->out_image_path) ){
						$this->error = 'Could not process image.';
					}
					
					// delete orignal image
					unlink($target_path);
					
				} else {
					$this->error = $upload_error_msg;
				}
			}
			
		} else {
			$this->error = "No image selected";
		}
		
	}
	
	public function display_process() {
		if($this->error != ''){
			
			?>
			<span class="error"><?php echo $this->error; ?></span>
			<br /><br /><a href="javascript:window.history.back();">&laquo; Back</a>
			<?php
			
		} else {
			$img_src = 'uploads/' . basename($this->out_image_path);
			
			?>
			<div id="image-container">
				<div id="image-wrapper" style="padding:10px; width:<?php echo $this->final_new_width; ?>px; height:<?php echo $this->final_new_height; ?>px;">
					<img src="<?php echo $img_src; ?>" />
				</div>
				<br /><br /><a href="javascript:window.history.back();">&laquo; Back</a>
			</div>
			<?php
		}
	}
	
	public function display_init() {
		?>
		<form action="<?php echo $this->form_action; ?>" method="<?php echo $this->form_method; ?>" enctype="<?php echo $this->form_enctype; ?>">
			<h1 class="header">Resize image upload utility (jpeg or png)</h1>
			<div id="inputs-container">
				<input id="image" name="<?php echo $this->form_image_id; ?>" type="file">
				<input id="submit" name="submit" type="submit" value="Upload Image">
			</div>
			<div class="clear"></div>
		</form>
		<?php
	}
	
	public function content() {
		$method = "display_{$this->state}";
		if( method_exists($this, $method) ){
			$this->$method();
		} else {
			echo 'No content available';
		}
	}
	
} // end form class

// instantiate form class
$form = new form();

// process form
if( isset($_POST['submit']) ){
	$form->process();
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Joshua Lee">
		<meta name="description" content="twelve23 backend test : item 3">
		<title>Twelve23 Backend Test - Item 3</title>
		
		<style type="text/css">
			body { background:none; font-family:tahoma, 'Lucida Grande', Verdana, Arial, Sans-Serif; }
			#main-container { position:absolute; top:0; left:0; width:100%; height:100px; }
			#frame-container { margin:32px auto; width:80%; }
			#content-container { width:100%; background:#f3f3f3; border-radius:12px; padding:12px; }
			#inputs-container { width:72%; margin:16px 0 0 0; }
			#image-container { margin:16px 0; }
			#image-wrapper { background:#ddd; }
			
			a { color:#088; text-decoration:none; }
			a:hover { text-decoration:underline; }
			.clear { clear:both; }
			.error { color:red; }
			h1 { padding:0; margin:0; font-size:1.2em; font-weight:bold; color:#555; }
			input { padding:4px; font-size:1em; clear:both; }
			
		</style>
	</head>
	<body>
		<div id="main-container">
			<div id="frame-container">
				<div id="content-container" align="center">
					<?php $form->content(); ?>
				</div>
			</div>
		</div>
	</body>
</html>


