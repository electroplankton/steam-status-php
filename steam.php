<?php
header('content-type: image/png');

$str_input=$_GET['profiles'];

$str_api_key='put ur api kee here phag';

$str_res_dir='./resources/';

$str_font_reg='TerminusTTF-4.47.0.ttf';
$str_font_bold='TerminusTTF-Bold-4.47.0.ttf';

$num_font_size=12;

$str_border='border.png';
$str_bkgnd='bkgnd.png';

$text_offset_x=8;
$text_offset_y=0;
$text_spacing=2;

$bool_grayscale=true;

$res_image_main=imagecreatefrompng($str_res_dir.$str_bkgnd);
//imagesavealpha($res_image_main,true);
$res_font_colour=imagecolorallocate($res_image_main,114,63,140);
$res_grey_colour=imagecolorallocate($res_image_main,11,10,13);
if(empty($str_api_key)) {
	$str_error='missing steam api key';
} elseif(empty($str_input)) {
	$str_error='.php?profiles= missing; requires steam64 id';
} elseif(!is_numeric($str_input)) {
	$str_error='invalid variable input; numbers only';
} elseif(strlen($str_input)!=17) {
	$str_error='invalid variable length; must be 17 numbers';
} else {
	$str_url_profile='http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$str_api_key.'&steamids='.$str_input;
	$str_content=file_get_contents($str_url_profile);

	if($str_content==false) {
		$str_error='no response from server or invalid api key';
	} else {
		$str_json=json_decode($str_content,true);

		foreach($str_json['response']['players'] as $var_item) {
			$str_name=$var_item['personaname'];
			$str_avatar=$var_item['avatarmedium'];
			$str_game=@$var_item['gameextrainfo'];
			$num_persona=$var_item['personastate'];
			$num_visible=$var_item['communityvisibilitystate'];
			$str_steamid=$var_item['steamid'];
		}

		if(empty($str_steamid)) {
			$str_error='invalid profile';
		} else {
			if($num_visible!=3) {
				$arr_privacy=array("","private","friendsonly");
				$str_state=$arr_privacy[$num_visible];
			} else {
				if(!empty($str_game)) {
					$str_state='Playing '.$str_game;
				} else {
					$arr_state=array("Offline","Online","Busy","Away");
					$str_state=$arr_state[$num_persona];
				}

				if($num_persona!=0) {
					$bool_grayscale=false;
				}
			}

			$res_avatar=imagecreatefromjpeg($str_avatar);
			imagecopy($res_image_main,$res_avatar,5,5,0,0,64,64);

			$res_border=imagecreatefrompng($str_res_dir.$str_border);
			imagecopy($res_image_main,$res_border,4,4,0,0,66,66);

			imagettftext($res_image_main,$num_font_size,0,74+$text_offset_x,22+$text_offset_y,$res_font_colour,$str_res_dir.$str_font_bold,$str_name);
			imagettftext($res_image_main,$num_font_size,0,74+$text_offset_x,40+$text_offset_y+$text_spacing,$res_font_colour,$str_res_dir.$str_font_reg,$str_state);
			imagettftext($res_image_main,$num_font_size,0,74+$text_offset_x,58+$text_offset_y+$text_spacing+$text_spacing,$res_grey_colour,$str_res_dir.$str_font_reg,"<electroplankton.net>");
		}
	}
}

if(!empty($str_error)) {
	imagettftext($res_image_main,$num_font_size,0,10,20,$res_font_colour,$str_res_dir.$str_font_reg,'error:');
	imagettftext($res_image_main,$num_font_size,0,10,40,$res_font_colour,$str_res_dir.$str_font_reg,$str_error);
	imagettftext($res_image_main,$num_font_size,0,10,60,$res_font_colour,$str_res_dir.$str_font_reg,'[electroplankton.net]');
}

if($bool_grayscale==true) {
	imagefilter($res_image_main,IMG_FILTER_GRAYSCALE);
}

imagepng($res_image_main,NULL,9);
imagedestroy($res_image_main);
?>
