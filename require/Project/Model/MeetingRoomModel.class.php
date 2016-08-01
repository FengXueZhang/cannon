<?php

namespace Project\Model;

/**
 * 授权用户管理模型
 *
 * @author Yi
 *
 * @date 2015-12-09
 */
class MeetingRoomModel extends CommonModel
{

    const MEET_ROOM_STATUS_ON = 1; //会议室表数据状态 启用
    const MEET_ROOM_STATUS_OFF = -1; //会议室表数据状态 禁用、超时

    /**
     * 根据微信个人id 获取 个人信息
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $chat_id         微信个人id
     * @param  string     $field         所需查询字段 默认为 *
     * @param  string     $checkExists     检测数据存在判断 默认为 false
     *
     * @return array
     */
    public function getMeetingRoomByChatId($chat_id, $field = '*', $checkExists = false)
    {

        if (empty($chat_id)) {
            return false;
        }

        $where['chat_id'] = (string) $chat_id;
        if (!$checkExists) {
            $where['status'] = self::MEET_ROOM_STATUS_ON;
        }

        $res = $this->where($where)->field($field)->find();

        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }

    }

    /**
     * 分页获取信息
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @param  string     $page         微信个人id
     * @param  string     $size         所需查询字段 默认为 *
     *
     * @return array
     */
    public function getPageData($page = 1, $size = 20, $where = array(), $order = array('create_time desc'), $status = self::MEET_ROOM_STATUS_ON)
    {
        $data = array();
        $where['status'] = $status;

        $count = $this->getCount($where);
        $Page = new \Think\Page($count, $size);
        $data['show'] = $Page->show();
        //$data['data']    = $this->
        //where($where)->
        //order($order)->
        //limit($Page->firstRow.','.$Page->listRows)->
        //select();
        $data['data'] = $this->where($where)->order($order)->select();
        return $data;
    }

    /**
     * 获取总数
     *
     * @author Yi
     *
     * @date   2016-01-11
     *
     * @return array
     */
    public function getCount($where = array(), $status = self::MEET_ROOM_STATUS_ON)
    {
        $where['status'] = $status;
        return $this->where($where)->count();
    }

    /**
     * 检查数据缓存是否存在，不存在测变更会议室状态
     *
     * @author Yi
     *
     * @date   2016-02-29
     *
     * @return array
     */
    public function checkMeeting($where = array(), $status = self::MEET_ROOM_STATUS_ON)
    {
        $where['status'] = $status;
        $res 	= $this->where($where)->select();
        return $res;
    }

    /**
     * 检查数据缓存是否存在，不存在测变更会议室状态
     *
     * @author Yi
     *
     * @date   2016-02-29
     *
     * @return array
     */
    public function meetRoomOff($checkId = array(), $status = self::MEET_ROOM_STATUS_OFF)
    {
    	$res 	= null;
    	if (!empty($checkId)) {
	        $where['id'] 	= array('in',implode(',',$checkId));
	        $data['status'] = $status;
	        $res 	= $this->where($where)->data($data)->save();
    	}
        return $res;
    }
}
