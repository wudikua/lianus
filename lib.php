<?php
define("WEBSITE", "lian.us");
/**
 * 加密
 * @param string $key 密钥
 * @param string $str 需加密的字符串
 * @return type 
 */
function aes_encode( $string, $skey="1234" ){
  $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 解密
 * @param type $key
 * @param type $str
 * @return type 
 */
function aes_decode( $string, $skey="1234" ){
  $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

function getAlexaRank($Domain) {
	$line = "";
	$data = "";
	$URL = "http://data.alexa.com/data/?cli=10&dat=snba&url=". $Domain ;
	$fp = fopen ($URL ,"r");
	if ($fp ) {
		while (!feof ($fp )){
		    $line = fgets ($fp );
		    $data .= $line ;
		}
		$p= xml_parser_create ();
		xml_parse_into_struct ($p , $data , $vals );
		xml_parser_free ($p );
    	for ($i =0 ;$i <count ($vals );$i ++) {
			if ($vals [$i ]["tag"]=="POPULARITY") {
	      		return  $vals [$i ]["attributes"]["TEXT"];
	  		}
		}
	}
	return 0;
}

function getBdSearchCount($domain) {
	$URL = "http://www.baidu.com/s?wd=site:". $domain ;
	$fp = fopen ($URL ,"r");
	$line = "";
	$data = "";
	if ($fp ) {
		while (!feof ($fp )){
		    $line = fgets ($fp );
		    $data .= $line ;
		}
		if (preg_match("~该网站共有 (.*) 个网页被百度收录~s", 
			$data, $matches)) {
			$count = trim(str_replace([",", "\n"], "", $matches[1]));
			if (preg_match("~<.*>(.*)<.*~", $count, $m2)) {
				return intval($m2[1]);
			}
			return intval($count);
		}
	}
	return 0;
}
?>
