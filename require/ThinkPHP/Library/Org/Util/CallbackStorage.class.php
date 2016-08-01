<?php

namespace Org\Util;

use redis;
use Org\WeixinApi\Api;
use Think\Storage\Driver\File;
use Org\O365Api\Api as O365Api;
use Org\Util\WeChatExpression;
use Project\Model\MeetingRoomModel;
use Think\Log;

/**
 * 会议信息存储
 * 
 * @author Yi
 * 
 * @date 2015-12-24
 */
class CallbackStorage
{
    private $redis;
    private static $instance;
    private $meetingRoom;

    const KEY  		= 'APP_CORP_STORAGE';
    const SGIN 		= 'APP_CORP_STORAGE_SGIN';


    /**
     * 获取本类实例
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @return self
     */
    public static function getInstance($host = '', $port = '', $outtime = 60)
    {
        if (! self::$instance) {
            !$host && $host = C('REDIS_HOST');
            !$port && $port = C('REDIS_PORT');

            self::$instance = new self($host, $port, $outtime);
        }
        
        return self::$instance;
    }

    /**
     * 连接redis
     *
     * @author Cui
     *
     * @date   2015-12-10
     *
     * @param  string       $host    主机
     * @param  int          $port    端口
     * @param  int          $outtime 超时时间
     */
    private function __construct($host, $port, $outtime)
    {
        $this->redis = new redis();
        $link = $this->redis->connect($host, $port);
        if (! $link) {
            throw new \Exception("Redis connect error", 1);
        }

        $this->meetingRoom 	= new MeetingRoomModel();

        define('CALLBACK_DIE_TIME', 604800);
        define('AUTH_KEY_PRE', 'CALLBACK_CHAT_MEETTING:');
        define('AUTH_SINGLE_KEY', AUTH_KEY_PRE.'SINGLE:');
        define('AUTH_GROUP_KEY', AUTH_KEY_PRE.'GROUP:');
        define('AUTH_MEET_KEY', 'CALLBACK_CHAT_MEETTING_ROOM:');
        define('FILE_SERVER_DOMAIN', 'http://file.service.digilinx.cn/');
    }

    /**
     * 设置聊天室信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$info 		会议消息信息
     *
     * @return array
     */
    public function createMeetDetail( $info ,$cid = false )
    {

    	$redisKey 		= AUTH_GROUP_KEY.$info['chat_id'].':';//测试数据

        //$this->redis->rPush( $redisKey , json_encode( $info ) );
   		$checkRes 		= $this->meetingRoom->getMeetingRoomByChatId($info['chat_id'] ,'chat_id' , true);

    	$localFileUrl 	= $this->getFileUrl($info['chat_id']).$info['chat_id'].'.doc';
    	$fileUrl 		= C('DOMAIN_FILE_SAVE_URL').'meeting/'.$info['chat_id'].'.doc';
    	$meetIng 		= array();



    	if ( $cid != false ) 
	        $meetIng['cid'] 			= $cid;

	    //if ( !empty( $_SESSION[self::KEY]['cid'] ) )
	        //$meetIng['cid'] 			= $_SESSION[self::KEY]['cid'];

   		if ( empty($checkRes) && !empty($meetIng['cid']) ) {
	        $meetIngKey 				= AUTH_MEET_KEY;
	        $meetIng['chat_id'] 		= $info['chat_id'];
	        $meetIng['chat_name'] 		= $info['chat_name'];
	        $meetIng['chat_owner'] 		= $info['chat_owner'];
	        $meetIng['chat_user_list'] 	= $info['chat_user_list'];
	        $meetIng['chat_redis_key'] 	= $redisKey;
	        $meetIng['local_file_url'] 	= $localFileUrl;
	        $meetIng['domain_file_url']	= $fileUrl;
	        $meetIng['create_time'] 	= $info['create_time'];
	        $meetIng['update_time'] 	= time();


	        $this->meetingRoom->data($meetIng)->add($meetIng);

   		} else {
            //throw new \Exception("会议室已经被创建过！", 1);
   		}

        //$this->redis->zAdd( $meetIngKey , $info['create_time'] , json_encode( $meetIng ) );

    }


    /**
     * 获取会议室信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$page 		多少页
     * @param  array     	$size 		每页显示多少条
     *
     * @return array
     */
    public function getMeetRoom( $cid , $page = 1 ,$size = 20 )
    {

    	if (!empty($cid))
    		$where['cid'] 	= $cid;

        $resCheck 	= $this->meetingRoom->checkMeeting( $where );

        $checkId 	= array();

        foreach ( $resCheck as $rkey => $rval) 
        {
        	if ($this->checkDieTime($rval['chat_redis_key']) == -1) {
        		$checkId[] 	= $rval['id'];
        	}
        }
        $this->meetingRoom->meetRoomOff( $checkId );

        $res 	= $this->meetingRoom->getPageData( $page ,$size ,$where);
    	//$count 		= $this->redis->zCount(AUTH_MEET_KEY,'+inf','-inf');
    	//$tmpRes 	= $this->redis->zRange(AUTH_MEET_KEY,0,-1);
    	//$offset		= $size * ($page -1);

    	if ( !empty($res['data']) ) {
    		$meetDate 	= $this->formatMeetRoomDate($res['data']);
    	}	

    	return $meetDate;
    }

	/**
     * 对mysql中的会议列表轮循处理
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$data 		会议消息信息
     *
     * @return array
     */
	public function formatMeetRoomDate( $data )
	{
    	foreach ( $data as $dkey => &$dval){
    		//$data[$dkey] 	= json_decode($dval,true);
			$dieTime = ($dval['create_time'] + CALLBACK_DIE_TIME+3600*12);

			$data[$dkey]['word'] 			= file_exists($dval['local_file_url']);
			$data[$dkey]['start_time'] 		= date('Y-m-d H:i:s',$dval['create_time']);
			$data[$dkey]['end_time'] 		= date('Y-m-d H:i:s',$dieTime);
			$data[$dkey]['end_time_type'] 	= false;
    		if ( $data[$dkey]['status'] == 1 && (time() < $dieTime) ) {
				$data[$dkey]['end_time_type'] 	= true;
    		}
    	}
    	return $data;
	}

    /**
     * 获取会议室信息
     *
     * @author Yi
     *
     * @date   2015-12-29
     *
     * @param  array     	$info 		会议消息信息
     *
     * @return array
     */
    public function getMeetInfo( $chat_id , $redis_key )
    {
    	if (!empty($redis_key)) {
    		$res 	= $this->redis->lRange( $redis_key , 0 , -1 );
    	}

    	if ( !empty($res) ) {
    		$meetInfo 	= $this->formatRedisDate($res,true,$chat_id);
    	}

    	return $meetInfo;
    }

    /**
     * 对redis中的数据轮循处理
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$data 		会议消息信息
     * @param  array     	$formatType	是否根据数据类型进行分别处理
     * @param  array     	$chat_id	聊天室id号
     *
     * @return array
     */
    public function formatRedisDate($data , $formatType = false , $chat_id = null)
    {
    	foreach ( $data as $dkey => &$dval){
    		$data[$dkey] 	= json_decode($dval,true);
    		if ( $formatType && !empty($chat_id) ) {
    			$this->filterMsgType($data[$dkey],$chat_id);
    			$userInfo 	= $this->getUserInfoByUserId($data[$dkey]['formUser']);
				$data[$dkey]['userInfo'] = $userInfo;
    		}

    	}
    	return $data;
    }


    /**
     * 修改会议室名称
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  int     	$chatId 			会议室Id
     * @param  string     	$newName 			会议室新名称
     *
     * @return array
     */
    public function updateMeetRoomName( $chatId , $newName )
    {

    }


    /**
     * 修改聊天室信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$info 			会议消息信息
     * @param  array     	$updateType 	退出会话判断 
     *
     * @return array
     */
    public function updateMeetDetail( $info , $updateType = false )
    {
    	$redisKey 	= AUTH_GROUP_KEY.$info['chatId'].':';
        $oldInfo 	= $this->redis->LPOP( $redisKey );
        $oldInfo 	= json_decode( $oldInfo , true );

        if ( $info['chatId'] == $oldInfo['chatId'] ){
	    	if ( $updateType ) {
	    		$this->filterQuitUser( $info['FromUserName'] , $oldInfo );
        		$this->redis->lPush( $redisKey , json_encode( $oldInfo ) );
    		} else {
        		$this->redis->lPush( $redisKey , json_encode( $info ) );
    		}
        }
    }

    /**
     * 去除退出用户
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$info 			会议消息信息
     * @param  array     	$updateType 	退出会话判断 
     *
     * @return array
     */
    public function filterQuitUser( $fromUserName , &$info )
    {
    	$userList		= $info['UserList'];
    	$tmpUserList 	= array_flip($userList);
    	$tmpKey 		= $tmpUserList[$fromUserName];

    	if ( !empty($tmpUserList[$fromUserName]) && $tmpKey >= 0 ) {
    		$info['UserList'][$tmpKey] 	= null;
    		unset($info['UserList'][$tmpKey]);
    		$info['UserList']			= array_value($info['UserList']);
    	}

    }

    /**
     * 退出聊天室信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$info 		会议消息信息
     *
     * @return array
     */
    public function quitMeetDetail($info)
    {

    }

    /**
     * 设置聊天室信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$info 		会议消息信息
     * @param  string     	$groupChat 	是否为群聊信息 默认：false 为群组聊天
     *
     * @return array
     */
    public function setMeetDetail($info,$groupChat = false)
    {
    	$redisKey 		= AUTH_GROUP_KEY.$info['receiver'].':';//测试数据


    	//如果是单聊的话，根据收发用户获取唯一redisKey
    	if ( $groupChat == true ) {
    		$redisKey 	= $this->getSingleChatKey($info['formUser'],$info['receiver']);
    	}else{
    		//判断消息类型，并对不同类型数据进行处理并返回
    		$this->filterMsgType( $info , $info['receiver'] );
    	}

        $this->redis->rPush( $redisKey , json_encode( $info ) );
		$this->redis->expire($redisKey,CALLBACK_DIE_TIME);

		$checkTimeRes 	= $this->checkDieTime($redisKey);
		$setTimeRes 	= $this->setExpireTime($redisKey,$checkTimeRes);

    }

    /**
     * 获取word信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$chat_id 		会议消息信息
     * @param  string     	$groupChat 	是否为群聊信息 默认：false 为群组聊天
     *
     * @return array
     */
    public function getWordFile( $chatId ,$redisKey )
    {
        $res 		= self::getMeetInfo($chatId,$redisKey);
        $data 		= $this->meetingRoom->getMeetingRoomByChatId( $chatId );

    	vendor('PHPWord.PHPword');
    	vendor('PHPWord.PHPWord.Style');
    	vendor('PHPWord.PHPWord.IOFactory');

    	$PHPWord  	= new \PHPWord();
		$section 	= $PHPWord->createSection();

		$section->addText('会议室：'.$data['chat_name'],array('size'=>18,'bold'=>true,'align'=>'center'));
		$section->addTextBreak(2);

		foreach ($res as $rkey => $rval) {
			switch ($rval['msgType']) {
	    		case 'text':

        			$wxBiaoQing = WeChatExpression::resolveWxExpression($rval['content'],'text');

					$section->addText($rval['userInfo']['name'].' : '.date('Y-m-d H:i:s',$rval['createTime']),array('bold'=>true));

        			if ( falas === $wxBiaoQing['list'] ) {
						$section->addText($rval['content']);
        			}else{
						$section->addText($wxBiaoQing['new']);
        			}

					$section->addTextBreak(2);
	    			break;
	    		case 'image':
					$section->addText($rval['userInfo']['name'].' : '.date('Y-m-d H:i:s',$rval['createTime']),array('bold'=>true));
					$section->addImage($rval['content']['LocalPicUrl'], array('width'=>210, 'height'=>210));
					$section->addLink($rval['content']['FilePicUrl'],'查看原图',array('color'=>'0000FF','underline'=>\PHPWord_Style_Font::UNDERLINE_SINGLE));
					$section->addTextBreak(2);
	    			break;
	    		case 'voice':
					$section->addText($rval['userInfo']['name'].' : '.date('Y-m-d H:i:s',$rval['createTime']),array('bold'=>true));
					$section->addLink($rval['content']['FileMp3Url'],'点击播放',array('color'=>'0000FF','underline'=>\PHPWord_Style_Font::UNDERLINE_SINGLE));
					$section->addTextBreak(2);
	    			break;
	    		case 'video':
	    		case 'shortvideo':
	    			break;
	    		case 'event':
	    			break;
	    		default:
	    			break;
			}
		}


		$tmpData 	= 'tmp';

		if ( !file_exists($data['local_file_url']) ) {
	    	$file 		= new \Think\Storage\Driver\File();
	    	$num 		= $file->put($data['local_file_url'],$tmpData);
		}

		$objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007'); 
        //return $data['local_file_url']; 
        //return $objWriter->save('./bbbbbbbbb.doc'); 
        $objWriter->save($data['local_file_url']); 

		O365Api::init();
		return O365Api::fileUpload($data['local_file_url'],date('YmdHis',time()),'meeting');

    }

    /**
     * 生成word文件
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$chat_id 		会议消息信息
     * @param  string     	$groupChat 	是否为群聊信息 默认：false 为群组聊天
     *
     * @return array
     */
    public function createWordFile( $chatId ,$redisKey )
    {


    }

	/**
     * 获取聊天室信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     	$info 		会议消息信息
     * @param  string     	$groupChat 	是否为群聊信息 默认：false 为群组聊天
     *
     * @return array
     */
    public function getMeetDetail($info,$groupChat = false)
    {
    	$redisKey 		= AUTH_GROUP_KEY.$info['receiver'].':';//测试数据

    	//如果是单聊的话，根据收发用户获取唯一redisKey
    	if ( $groupChat == true ) {
    		$redisKey 	= $this->getSingleChatKey($info['formUser'],$info['receiver']);
    	}

        $this->redis->rPush( $redisKey , json_encode( $info ) );


    //    $res = $this->redis->hGet(AUTH_USER_KEY, $authId);
    //    if ($res) {
    //        $res = json_decode($res, true);
    //    }

    //    return $res;
    }

	/**
     * 查看队列是否设置超时时间
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $redisKey 队列key值
     *
     * @return array
     */
    public function checkDieTime($redisKey)
    {
        if (! $redisKey ) {
            throw new \Exception("参数错误!", 1);
        }

        return $this->redis->ttl($redisKey);
    }


	/**
     * 设置队列超时时间
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $redisKey 队列key值
     * @param  string     $checkTimeRes 队列key值
     *
     * @return array
     */
    public function setExpireTime($redisKey,$checkTimeRes)
    {
        if (! $redisKey ) {
            throw new \Exception("参数错误!", 1);
        }

    	if ($checkTimeRes == -1) {
        	return $this->redis->expire($redisKey,CALLBACK_DIE_TIME);
    	}

    }


	/**
     * 检查MsgType是否存在，与类型区分
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $msgType 微信消息类型
     *
     * @return string
     */
    public static function checkMsgType($msgType)
    {
    	if (! isset($msgType) ) {
    		return true;
        }
		return false;
    }

	/**
     * 检查MsgType是否存在，与类型区分
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $authId 微信消息类型
     *
     * @return string
     */
    public static function checkContent($content)
    {
    	if (! isset($content))
    		return true;

		return false;
    }

	/**
     * 根据MsgType类型，选择对应的方式 处理数据
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  array     $info 		参数数组
     * @param  array     $chat_id 	会议室redisKey
     *
     * @return array
     */
    public function filterMsgType(&$info , $chat_id)
    {
    	switch($info['msgType'])
    	{
    		case 'text':
    			$info['content'] 	= $info['content'];
    			break;
    		case 'image':
    			$this->handleImg($info,$chat_id);
    			break;
    		case 'voice':
    			$this->handleVoice($info,$chat_id);
    			break;
    		case 'video':
    		case 'shortvideo':
    			$this->handleVideo($info);
    			break;
    		case 'event':
    			break;
    		default:
    			$info['content'] 	= $info['content'];
    			break;
    	}

    }

	/**
     * 处理语音文件
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $info 授权ID
     *
     * @return array
     */
    public function handleVoice(&$info , $chat_id)
    {
    	$res 		= $this->getMediaDate($info['content']['MediaId']);

    	if ( $res !== false ) {
    		$date 	= base64_decode($res['content']);
    	}

    	$url 		= $this->getFileUrl($chat_id);

    	$pathAmr 	= $url.$info['msgId'].'.amr';
    	$pathMp3 	= $url.$info['msgId'].'.mp3';


    	if (!file_exists($pathAmr) && !file_exists($pathMp3)) {

	    	$file 		= new \Think\Storage\Driver\File();

	    	$num 		= $file->put($pathAmr,$date);

	    	$command	= 'ffmpeg -i '.$pathAmr.' '.$pathMp3;

	    	$execRes 	= $this->myExec($command);
    	}

		$info['content']['LocalAmr']	= $pathAmr;
		$info['content']['LocalMp3']	= $pathMp3;
		$info['content']['FileAmrUrl']	= C('DOMAIN_FILE_SAVE_URL').'meeting/'.$chat_id.'/'.$info['msgId'].'.amr';
		$info['content']['FileMp3Url']	= C('DOMAIN_FILE_SAVE_URL').'meeting/'.$chat_id.'/'.$info['msgId'].'.mp3';

    }


	/**
     * 处理图片文件
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $info 授权ID
     *
     * 41@return array
     */
    public function handleImg(&$info , $chat_id)
    {
    	$res 	= $this->getMediaDate($info['content']['MediaId']);

    	if ( $res !== false ) {
    		$date = base64_decode($res['content']);
    	}

    	$url 	= $this->getFileUrl($chat_id);

    	$path 	= $url.$info['msgId'].'.jpg';

    	if (!file_exists($path)) {
	    	$file 		= new \Think\Storage\Driver\File();
	    	$num 		= $file->put($path,$date);
    	}

		$info['content']['LocalPicUrl']	= $path;
		$info['content']['FilePicUrl']	= C('DOMAIN_FILE_SAVE_URL').'meeting/'.$chat_id.'/'.$info['msgId'].'.jpg';
    }


	/**
     * 处理视频文件
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $info 授权ID
     *
     * @return array
     */
    public function handleVideo($info)
    {

    }

	/**
     * 执行linux命令
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $info 授权ID
     *
     * @return array
     */
    public function myExec($command)
    {
    	if (!empty($command)) {
    		exec($command,$array);
    	}
    	return $array;
    }

	/**
     * 获取存储的url
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $chat_id 
     *
     * @return array
     */
    public function getFileUrl($chat_id)
    {
    	//$tmpUrl = explode('/',APP_ROOT);
    	//$url 	= str_replace(end($tmpUrl),'file',APP_ROOT).'/meeting/'.$chat_id.'/';
    	$url 	= C('LOCAL_FILE_SAVE_URL').'meeting/'.$chat_id.'/';

    	//$res 	= $this->makeDir($url);

    	//if ( file_exists($url) ) {
    		return $url;
    	//}

    }

	/**
     * 生成对应文件夹
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $url 
     *
     * @return array
     */
    public function makeDir($url , $mode = 0777)
    {
		if(!file_exists($url)) {
			return mkdir($url,$mode,true);
		} else {
			return true;
		}
    }

	/**
     * 通过MediaId获取信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $MediaId MediaId
     *
     * @return array
     */
    public function getMediaDate($MediaId)
    {
    	$MediaApi 	= \Org\WeixinApi\Api::factory('Media');
    	$res 		= $MediaApi->get($MediaId);
    	return $res;
    }

	/**
     * 通过MediaId获取信息
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $userId userId
     *
     * @return array
     */
    public function getUserInfoByUserId($userId)
    {
    	$MediaApi 	= \Org\WeixinApi\Api::factory('User');
    	$res 		= $MediaApi->getInfoById($userId);
    	return $res;
    }

	/**
     * 单个聊天获取唯一redisKey
     *
     * @author Yi
     *
     * @date   2015-12-24
     *
     * @param  string     $formUser 发送用户
     * @param  string     $receiver 接受用户
     *
     * @return array
     */
    public function getSingleChatKey( $formUser,$receiver )
    {
    	if ( !empty($formUser) && !empty($receiver) ){

    		$redisKey 	= AUTH_SINGLE_KEY.md5( $formUser.':'.$receiver ).':';

    		return $redisKey;
    	}
    }

    /**
     * 关闭redis
     *
     * @author Cui
     *
     * @date   2015-12-10
     */
    public function __destruct()
    {
        $this->redis->close();
    }
}