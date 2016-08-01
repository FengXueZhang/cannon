<?php

namespace Org\Util;


/**
 * 微信表情处理
 * 
 * @author Yi
 * 
 * @date 2016-01-19
 */
class WeChatExpression 
{

    const EXPRESSION_REG	= "/\/::\)|\/::~|\/::B|\/::\||\/:8-\)|\/::&lt;|\/::\$|\/::X|\/::Z|\/::'\(|\/::-\||\/::@|\/::P|\/::D|\/::O|\/::\(|\/::\+|\/:--b|\/::Q|\/::T|\/:,@P|\/:,@-D|\/::d|\/:,@o|\/::g|\/:\|-\)|\/::!|\/::L|\/::&gt;|\/::,@|\/:,@f|\/::-S|\/:\?|\/:,@x|\/:,@@|\/::8|\/:,@!|\/:!!!|\/:xx|\/:bye|\/:wipe|\/:dig|\/:handclap|\/:&amp;-\(|\/:B-\)|\/:&lt;@|\/:@&gt;|\/::-O|\/:&gt;-\||\/:P-\(|\/::'\||\/:X-\)|\/::\*|\/:@x|\/:8\*|\/:pd|\/:&lt;W&gt;|\/:beer|\/:basketb|\/:oo|\/:coffee|\/:eat|\/:pig|\/:rose|\/:fade|\/:showlove|\/:heart|\/:break|\/:cake|\/:li|\/:bome|\/:kn|\/:footb|\/:ladybug|\/:shit|\/:moon|\/:sun|\/:gift|\/:hug|\/:strong|\/:weak|\/:share|\/:v|\/:@\)|\/:jj|\/:@@|\/:bad|\/:lvu|\/:no|\/:ok|\/:love|\/:&lt;L&gt;|\/:jump|\/:shake|\/:&lt;O&gt;|\/:circle|\/:kotow|\/:turn|\/:skip|\/:oY|\/:#-0|\/:hiphot|\/:kiss|\/:&lt;&amp;|\/:&amp;&gt;/";


    /**
     * 从表情库中根据key获取对应的表情信息
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @param  string  	$str 			需要处理的
     * @param  string  	$contentType 	获取类型控制：	图片信息、文字信息、图片地址	默认图片 
     *
     * @return array 	resData
     * 						list 		表情信息、根据contentType控制返回 类型
     * 						new 		替换后的字符
     * 						former 		原字符串
     * 
     */
	public static function resolveWxExpression( $str , $contentType = 'img'){

		if (empty($str)) return false;
		if (!in_array( $contentType , array('img','text','src'))) return false;

		$resData			= array();

		$pregArr 			= preg_match_all(WeChatExpression::EXPRESSION_REG,$str,$resArr);

		$resArr 			= $resArr[0];

		$resData['list']	= self::formatMatchArr( $resArr , $contentType );

		if ($contentType != 'src')
    		$resData['new']		= str_replace($resArr, $resData['list'], $str);
		else
    		$resData['new']		= str_replace($resArr,'', $str);

    	$resData['former']	= $str;

    	return $resData;
	}


    /**
     * 从表情库中根据key获取对应的表情信息
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @param  array     	$array 		表情信息数组
     * @param  string     	$type 		获取类型控制：图片信息、文字信息、图片路径	默认图片 
     *
     * @return self
     */
	public static function formatMatchArr( $array ,$type = 'img'){

		if (empty($array))
			return false;

		$resData = array();

		foreach ( $array as $akey => $aval) {
			switch ($type) {
				case 'img':
					$resData[] 	= self::imgPack(self::getExpression()[$aval]['src']);
					break;
				case 'text':
					$resData[] 	= self::textPack(self::getExpression()[$aval]['text']);
					break;
				case 'src':
					$resData[] 	= self::srcPack(self::getExpression()[$aval]['src']);
					break;
				default:
					break;
			}
		}

		return $resData;
	}

    /**
     * 仅获取图片地址
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @param  string     	$src 		需要包装的img路径
     *
     * @return self
     */
	public static function srcPack($str){
		return $str;
	}

    /**
     * 对图片进行html标签包装
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @param  string     	$src 		需要包装的img路径
     *
     * @return self
     */
	public static function imgPack($str){
		return '<img style="height:24px; width:24px;vertical-align: bottom;" src="'.$str.'" />';
	}

    /**
     * 对文字进行处理包装
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @param  string     	$str 		需要转换的字符串
     *
     * @return self
     */
	public static function textPack($str){
		return self::myUnicode2Utf8($str);
	}

    /**
     * Unicode转Utf8
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @param  string     	$str 		需要转换的字符串
     *
     * @return self
     */
	public static function myUnicode2Utf8($str){
	        if(!$str) return $str;
	        $decode = json_decode($str);
	        if($decode) return $decode;
	        $str = '["' . $str . '"]';
	        $decode = json_decode($str);
	        if(count($decode) == 1){
	                return $decode[0];
	        }
	        return $str;
	}

    /**
     * 
     *
     * @author Yi
     *
     * @date   2015-12-10
     *
     * @return self
     */
    public static function getExpression()
    {

		$Expression = array(
			"/::)" => array("text" => "[\u5fae\u7b11]","src"=>"http://cache.soso.com/img/img/e100.gif\r\n"),
			"/::~" => array("text" => "[\u6487\u5634]","src"=>"http://cache.soso.com/img/img/e101.gif\r\n"),
			"/::B" => array("text" => "[\u8272]","src"=>"http://cache.soso.com/img/img/e102.gif\r\n"),
			"/::|" => array("text" => "[\u53d1\u5446]","src"=>"http://cache.soso.com/img/img/e103.gif\r\n"),
			"/:8-)" => array("text" => "[\u5f97\u610f]","src"=>"http://cache.soso.com/img/img/e104.gif\r\n"),
			"/::&lt;" => array("text" => "[\u6d41\u6cea]","src"=>"http://cache.soso.com/img/img/e105.gif\r\n"),
			"/::$" => array("text" => "[\u5bb3\u7f9e]","src"=>"http://cache.soso.com/img/img/e106.gif\r\n"),
			"/::X" => array("text" => "[\u95ed\u5634]","src"=>"http://cache.soso.com/img/img/e107.gif\r\n"),
			"/::Z" => array("text" => "[\u7761]","src"=>"http://cache.soso.com/img/img/e108.gif\r\n"),
			"/::'(" => array("text" => "[\u5927\u54ed]","src"=>"http://cache.soso.com/img/img/e109.gif\r\n"),
			"/::-|" => array("text" => "[\u5c34\u5c2c]","src"=>"http://cache.soso.com/img/img/e110.gif\r\n"),
			"/::@" => array("text" => "[\u53d1\u6012]","src"=>"http://cache.soso.com/img/img/e111.gif\r\n"),
			"/::P" => array("text" => "[\u8c03\u76ae]","src"=>"http://cache.soso.com/img/img/e112.gif\r\n"),
			"/::D" => array("text" => "[\u5472\u7259]","src"=>"http://cache.soso.com/img/img/e113.gif\r\n"),
			"/::O" => array("text" => "[\u60ca\u8bb6]","src"=>"http://cache.soso.com/img/img/e114.gif\r\n"),
			"/::(" => array("text" => "[\u96be\u8fc7]","src"=>"http://cache.soso.com/img/img/e115.gif\r\n"),
			"/::+" => array("text" => "[\u9177]","src"=>"http://cache.soso.com/img/img/e116.gif\r\n"),
			"/:--b" => array("text" => "[\u51b7\u6c57]","src"=>"http://cache.soso.com/img/img/e117.gif\r\n"),
			"/::Q" => array("text" => "[\u6293\u72c2]","src"=>"http://cache.soso.com/img/img/e118.gif\r\n"),
			"/::T" => array("text" => "[\u5410]","src"=>"http://cache.soso.com/img/img/e119.gif\r\n"),
			"/:,@P" => array("text" => "[\u5077\u7b11]","src"=>"http://cache.soso.com/img/img/e120.gif\r\n"),
			"/:,@-D" => array("text" => "[\u53ef\u7231]","src"=>"http://cache.soso.com/img/img/e121.gif\r\n"),
			"/::d" => array("text" => "[\u767d\u773c]","src"=>"http://cache.soso.com/img/img/e122.gif\r\n"),
			"/:,@o" => array("text" => "[\u50b2\u6162]","src"=>"http://cache.soso.com/img/img/e123.gif\r\n"),
			"/::g" => array("text" => "[\u9965\u997f]","src"=>"http://cache.soso.com/img/img/e124.gif\r\n"),
			"/:|-)" => array("text" => "[\u56f0]","src"=>"http://cache.soso.com/img/img/e125.gif\r\n"),
			"/::!" => array("text" => "[\u60ca\u6050]","src"=>"http://cache.soso.com/img/img/e126.gif\r\n"),
			"/::L" => array("text" => "[\u6d41\u6c57]","src"=>"http://cache.soso.com/img/img/e127.gif\r\n"),
			"/::&gt;" => array("text" => "[\u61a8\u7b11]","src"=>"http://cache.soso.com/img/img/e128.gif\r\n"),
			"/::,@" => array("text" => "[\u5927\u5175]","src"=>"http://cache.soso.com/img/img/e129.gif\r\n"),
			"/:,@f" => array("text" => "[\u594b\u6597]","src"=>"http://cache.soso.com/img/img/e130.gif\r\n"),
			"/::-S" => array("text" => "[\u5492\u9a82]","src"=>"http://cache.soso.com/img/img/e131.gif\r\n"),
			"/:?" => array("text" => "[\u7591\u95ee]","src"=>"http://cache.soso.com/img/img/e132.gif\r\n"),
			"/:,@x" => array("text" => "[\u5618]","src"=>"http://cache.soso.com/img/img/e133.gif\r\n"),
			"/:,@@" => array("text" => "[\u6655]","src"=>"http://cache.soso.com/img/img/e134.gif\r\n"),
			"/::8" => array("text" => "[\u6298\u78e8]","src"=>"http://cache.soso.com/img/img/e135.gif\r\n"),
			"/:,@!" => array("text" => "[\u8870]","src"=>"http://cache.soso.com/img/img/e136.gif\r\n"),
			"/:!!!" => array("text" => "[\u9ab7\u9ac5]","src"=>"http://cache.soso.com/img/img/e137.gif\r\n"),
			"/:xx" => array("text" => "[\u6572\u6253]","src"=>"http://cache.soso.com/img/img/e138.gif\r\n"),
			"/:bye" => array("text" => "[\u518d\u89c1]","src"=>"http://cache.soso.com/img/img/e139.gif\r\n"),
			"/:wipe" => array("text" => "[\u64e6\u6c57]","src"=>"http://cache.soso.com/img/img/e140.gif\r\n"),
			"/:dig" => array("text" => "[\u62a0\u9f3b]","src"=>"http://cache.soso.com/img/img/e141.gif\r\n"),
			"/:handclap" => array("text" => "[\u9f13\u638c]","src"=>"http://cache.soso.com/img/img/e142.gif\r\n"),
			"/:&amp;-(" => array("text" => "[\u7cd7\u5927\u4e86]","src"=>"http://cache.soso.com/img/img/e143.gif\r\n"),
			"/:B-)" => array("text" => "[\u574f\u7b11]","src"=>"http://cache.soso.com/img/img/e144.gif\r\n"),
			"/:&lt;@" => array("text" => "[\u5de6\u54fc\u54fc]","src"=>"http://cache.soso.com/img/img/e145.gif\r\n"),
			"/:@&gt;" => array("text" => "[\u53f3\u54fc\u54fc]","src"=>"http://cache.soso.com/img/img/e146.gif\r\n"),
			"/::-O" => array("text" => "[\u54c8\u6b20]","src"=>"http://cache.soso.com/img/img/e147.gif\r\n"),
			"/:&gt;-|" => array("text" => "[\u9119\u89c6]","src"=>"http://cache.soso.com/img/img/e148.gif\r\n"),
			"/:P-(" => array("text" => "[\u59d4\u5c48]","src"=>"http://cache.soso.com/img/img/e149.gif\r\n"),
			"/::'|" => array("text" => "[\u5feb\u54ed\u4e86]","src"=>"http://cache.soso.com/img/img/e150.gif\r\n"),
			"/:X-)" => array("text" => "[\u9634\u9669]","src"=>"http://cache.soso.com/img/img/e151.gif\r\n"),
			"/::*" => array("text" => "[\u4eb2\u4eb2]","src"=>"http://cache.soso.com/img/img/e152.gif\r\n"),
			"/:@x" => array("text" => "[\u5413]","src"=>"http://cache.soso.com/img/img/e153.gif\r\n"),
			"/:8*" => array("text" => "[\u53ef\u601c]","src"=>"http://cache.soso.com/img/img/e154.gif\r\n"),
			"/:pd" => array("text" => "[\u83dc\u5200]","src"=>"http://cache.soso.com/img/img/e155.gif\r\n"),
			"/:&lt;W&gt;" => array("text" => "[\u897f\u74dc]","src"=>"http://cache.soso.com/img/img/e156.gif\r\n"),
			"/:beer" => array("text" => "[\u5564\u9152]","src"=>"http://cache.soso.com/img/img/e157.gif\r\n"),
			"/:basketb" => array("text" => "[\u7bee\u7403]","src"=>"http://cache.soso.com/img/img/e158.gif\r\n"),
			"/:oo" => array("text" => "[\u4e52\u4e53]","src"=>"http://cache.soso.com/img/img/e159.gif\r\n"),
			"/:coffee" => array("text" => "[\u5496\u5561]","src"=>"http://cache.soso.com/img/img/e160.gif\r\n"),
			"/:eat" => array("text" => "[\u996d]","src"=>"http://cache.soso.com/img/img/e161.gif\r\n"),
			"/:pig" => array("text" => "[\u732a\u5934]","src"=>"http://cache.soso.com/img/img/e162.gif\r\n"),
			"/:rose" => array("text" => "[\u73ab\u7470]","src"=>"http://cache.soso.com/img/img/e163.gif\r\n"),
			"/:fade" => array("text" => "[\u51cb\u8c22]","src"=>"http://cache.soso.com/img/img/e164.gif\r\n"),
			"/:showlove" => array("text" => "[\u5634\u5507]","src"=>"http://cache.soso.com/img/img/e165.gif\r\n"),
			"/:heart" => array("text" => "[\u7231\u5fc3]","src"=>"http://cache.soso.com/img/img/e166.gif\r\n"),
			"/:break" => array("text" => "[\u5fc3\u788e]","src"=>"http://cache.soso.com/img/img/e167.gif\r\n"),
			"/:cake" => array("text" => "[\u86cb\u7cd5]","src"=>"http://cache.soso.com/img/img/e168.gif\r\n"),
			"/:li" => array("text" => "[\u95ea\u7535]","src"=>"http://cache.soso.com/img/img/e169.gif\r\n"),
			"/:bome" => array("text" => "[\u70b8\u5f39]","src"=>"http://cache.soso.com/img/img/e170.gif\r\n"),
			"/:kn" => array("text" => "[\u5200]","src"=>"http://cache.soso.com/img/img/e171.gif\r\n"),
			"/:footb" => array("text" => "[\u8db3\u7403]","src"=>"http://cache.soso.com/img/img/e172.gif\r\n"),
			"/:ladybug" => array("text" => "[\u74e2\u866b]","src"=>"http://cache.soso.com/img/img/e173.gif\r\n"),
			"/:shit" => array("text" => "[\u4fbf\u4fbf]","src"=>"http://cache.soso.com/img/img/e174.gif\r\n"),
			"/:moon" => array("text" => "[\u6708\u4eae]","src"=>"http://cache.soso.com/img/img/e175.gif\r\n"),
			"/:sun" => array("text" => "[\u592a\u9633]","src"=>"http://cache.soso.com/img/img/e176.gif\r\n"),
			"/:gift" => array("text" => "[\u793c\u7269]","src"=>"http://cache.soso.com/img/img/e177.gif\r\n"),
			"/:hug" => array("text" => "[\u62e5\u62b1]","src"=>"http://cache.soso.com/img/img/e178.gif\r\n"),
			"/:strong" => array("text" => "[\u5f3a]","src"=>"http://cache.soso.com/img/img/e179.gif\r\n"),
			"/:weak" => array("text" => "[\u5f31]","src"=>"http://cache.soso.com/img/img/e180.gif\r\n"),
			"/:share" => array("text" => "[\u63e1\u624b]","src"=>"http://cache.soso.com/img/img/e181.gif\r\n"),
			"/:v" => array("text" => "[\u80dc\u5229]","src"=>"http://cache.soso.com/img/img/e182.gif\r\n"),
			"/:@)" => array("text" => "[\u62b1\u62f3]","src"=>"http://cache.soso.com/img/img/e183.gif\r\n"),
			"/:jj" => array("text" => "[\u52fe\u5f15]","src"=>"http://cache.soso.com/img/img/e184.gif\r\n"),
			"/:@@" => array("text" => "[\u62f3\u5934]","src"=>"http://cache.soso.com/img/img/e185.gif\r\n"),
			"/:bad" => array("text" => "[\u5dee\u52b2]","src"=>"http://cache.soso.com/img/img/e186.gif\r\n"),
			"/:lvu" => array("text" => "[\u7231\u4f60]","src"=>"http://cache.soso.com/img/img/e187.gif\r\n"),
			"/:no" => array("text" => "[NO]","src"=>"http://cache.soso.com/img/img/e188.gif\r\n"),
			"/:ok" => array("text" => "[OK]","src"=>"http://cache.soso.com/img/img/e189.gif\r\n"),
			"/:love" => array("text" => "[\u7231\u60c5]","src"=>"http://cache.soso.com/img/img/e190.gif\r\n"),
			"/:&lt;L&gt;" => array("text" => "[\u98de\u543b]","src"=>"http://cache.soso.com/img/img/e191.gif\r\n"),
			"/:jump" => array("text" => "[\u8df3\u8df3]","src"=>"http://cache.soso.com/img/img/e192.gif\r\n"),
			"/:shake" => array("text" => "[\u53d1\u6296]","src"=>"http://cache.soso.com/img/img/e193.gif\r\n"),
			"/:&lt;O&gt;" => array("text" => "[\u6004\u706b]","src"=>"http://cache.soso.com/img/img/e194.gif\r\n"),
			"/:circle" => array("text" => "[\u8f6c\u5708]","src"=>"http://cache.soso.com/img/img/e195.gif\r\n"),
			"/:kotow" => array("text" => "[\u78d5\u5934]","src"=>"http://cache.soso.com/img/img/e196.gif\r\n"),
			"/:turn" => array("text" => "[\u56de\u5934]","src"=>"http://cache.soso.com/img/img/e197.gif\r\n"),
			"/:skip" => array("text" => "[\u8df3\u7ef3]","src"=>"http://cache.soso.com/img/img/e198.gif\r\n"),
			"/:oY" => array("text" => "[\u6325\u624b]","src"=>"http://cache.soso.com/img/img/e199.gif\r\n"),
			"/:#-0" => array("text" => "[\u6fc0\u52a8]","src"=>"http://cache.soso.com/img/img/e200.gif\r\n"),
			"/:hiphot" => array("text" => "[\u8857\u821e]","src"=>"http://cache.soso.com/img/img/e201.gif\r\n"),
			"/:kiss" => array("text" => "[\u732e\u543b]","src"=>"http://cache.soso.com/img/img/e202.gif\r\n"),
			"/:&lt;&amp;" => array("text" => "[\u5de6\u592a\u6781]","src"=>"http://cache.soso.com/img/img/e203.gif\r\n"),
			"/:&amp;&gt;" => array("text" => "[\u53f3\u592a\u6781]","src"=>"http://cache.soso.com/img/img/e204.gif")
		);

    	 return $Expression;
    }

}