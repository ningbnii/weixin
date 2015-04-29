<?php
namespace Weixin\Controller;
use Think\Controller;
use Com\Wechat;

class ApiController extends Controller{
    private $token; 
    private $data = array();  //保存微信请求信息
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function index(){

        $this->token = I('get.token'); //微信后台填写的TOKEN
        
        //查询公众号信息
        $wxuserObj = M('Wxuser')->where(array('token'=>$this->token))->find();
		
        //检测token的正确性
        if(!preg_match("/^[a-z]{6}\d{10}$/",$this->token)){
            exit('error token');
        }
        /* 加载微信SDK */

        $wechat = new Wechat($this->token, $wxuserObj['encodingaeskey'], $wxuserObj['appid']);

        /* 获取请求信息 */
        $this->data = $wechat->request();

        if($this->data && is_array($this->data)){
            //获取return回来的content和type
            list($content,$type) = $this->reply($this->data);
            $wechat->response($content, $type);            
        }
    }

    private function reply($data){
        
        if('subscribe' == $data['Event']){
            $where = array();
            $where['token'] = $this->token;
            $where['wecha_id'] = $this->data['FromUserName'];
            $wechaData = M('Wecha')->where($where)->find();
            if($wechaData){
                M('Wecha')->where($where)->setField('flag','1');
            }else{
                //记录wecha_id
                $wechaData = array();
                $wechaData['token'] = $this->token;
                $wechaData['wecha_id'] = $this->data['FromUserName'];
                $wechaData['createtime'] = time();
                M('Wecha')->add($wechaData);                
            }

            $follow_data = M('Areply')->where(array('token'=>$this->token))->find();
            //如果没有配置关注回复
            if($follow_data){           
                //关键词回复
                return $this->keyword($follow_data['keyword']);
            }else{
                return array('感谢您的关注','text');
            }

        }

        if('unsubscribe' == $data['Event']){
            //取消关注
            $where = array();
            $where['token'] = $this->token;
            $where['wecha_id'] = $this->data['FromUserName'];
            $wechaData = M('Wecha')->where($where)->find();
            if($wechaData){
                M('Wecha')->where($where)->setField('flag','0');
            }
        }

        if('CLICK' == $data['Event']){
            $data['Content'] = $data['EventKey'];
        }
        /**
         * 关键词回复
         */
        $key = $data['Content'];
        switch ($key) {
            case '':

                break;
            
            default:
                return $this->keyword($key);
                break;
        }
    }

    private function keyword($key){
        //先精确匹配，没有再模糊匹配
        $like['keyword'] = $key;
        $like['token'] = $this->token;
        $like['type'] = 1;
        $data = M('Keyword')->where($like)->find();
        if($data == false){
            //模糊匹配
            $like['keyword'] = array('like','%'.$key.'%');
            $like['type'] = 2;
            $data = M('Keyword')->where($like)->order('id desc')->limit(9)->select();
        }

        if($data != false){
            if($like['type']==1){
                $module = $data['module'];
            }else{
                $module = 'Img';
            }
            switch ($module) {
                case 'Text':
                    $info = M($module)->find($data['pid']);
                    return array($info['text'], 'text');
                    break;
                case 'Img':
                    
                    if($like['type']==1){

                        //精确匹配
                        $info = M($module)->find($data['pid']);
                        $content = array();
                        $content[] = array($info['title'],$info['text'],C('site_url').$info['pic'],$this->getUrl($info));
                        return array($content,'news'); 
                    }

                    if($like['type']==2){
                        $ids = array();
                        foreach($data as $k=>$v){
                            $ids[] = $v['pid'];
                        }
                        $where =array();
                        $where['id'] = array('in',$ids);
                        $info = M($module)->order('id desc')->where($where)->select();
                        $content = array();
                        foreach ($info as $key => $value) {
                            $content[] = array($value['title'],'',C('site_url').$value['pic'],$this->getUrl($value));
                        }
                        
                        return array($content,'news'); 
                    }
                    
                    break;

                case 'Home':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    if($info['apiurl']){
                        $url = $info['apiurl'];
                    }else{
                        $url = C('site_url').U('Wap/Index/index',array('token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'sgssz'=>'mp.weixin.qq.com'));
                    }
                    $content[] = array($info['title'],$info['info'],C('site_url').$info['picurl'],$url);
                    return array($content,'news');
                    break;

                case 'Magzine':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    $url = C('site_url').U('Wap/Magzine/index',array('token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'id'=>$info['id'],'sgssz'=>'mp.weixin.qq.com'));
                    $content[] = array($info['title'],$info['info'],C('site_url').$info['pic'],$url);
                    return array($content,'news');
                break;

                case 'Silk':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    $url = C('site_url').U('Wap/Silk/index',array('token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'sgssz'=>'mp.weixin.qq.com'));
                    $content[] = array($info['title'],$info['text'],C('site_url').$info['pic'],$url);
                    return array($content,'news');

                    break;

                /**
                 * 语音
                 */
                case 'Music':
                    $info = M($module)->find($data['pid']);
                    return array(array($info['title'],$info['info'],$info['url'],$info['url']),'music');
                break;

                /**
                 * 留言板
                 */
                case 'Reply':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    $url = C('site_url').U('Wap/Reply/index',array('token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'sgssz'=>'mp.weixin.qq.com'));
                    $content[] = array($info['title'],$info['info'],C('site_url').$info['pic'],$url);
                    return array($content,'news');
                    break;
                /**
                 * 微投票
                 */
                case 'Vote':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    $url = C('site_url').U('Wap/Vote/index',array('id'=>$info['id'],'token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'sgssz'=>'mp.weixin.qq.com'));
                    $content[] = array($info['title'],$info['info'],C('site_url').$info['pic'],$url);
                    return array($content,'news');
                    break;
                /**
                 * 微相册
                 */
                case 'Photo':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    $url = C('site_url').U('Wap/Photo/index',array('id'=>$info['id'],'token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'sgssz'=>'mp.weixin.qq.com'));
                    $content[] = array($info['title'],$info['info'],C('site_url').$info['pic'],$url);
                    return array($content,'news');
                    break;
                /**
                 * 微商城
                 */
                case 'Shop':
                    $info = M($module)->find($data['pid']);
                    $content = array();
                    $url = C('site_url').U('Wap/Shop/index',array('id'=>$info['id'],'token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'sgssz'=>'mp.weixin.qq.com'));
                    $content[] = array($info['title'],$info['info'],C('site_url').$info['pic'],$url);
                    return array($content,'news');
                    break;

                    
                default:
                    # code...
                    break;
            }
        }else{
            //如果没有匹配到关键词
            $otherObj = M('Other');
            $where = array();
            $where['token'] = $this->token;
            $otherData = $otherObj->where($where)->find();
            return $this->keyword($otherData['keyword']);
        }
    }

/**
 * 图文连接转换
 */
    private function getUrl($info){
        if($info['url']){
            $url = str_replace(array('{token}','{wecha_id}'), array($this->token, $this->data['FromUserName']), $info['url']);
        }else{
            //如果没有写外链，跳转到微官网详情页
            $url = C('site_url').U('Wap/Index/content',array('token'=>$this->token,'wecha_id'=>$this->data['FromUserName'],'fid'=>$info['classid'],'id'=>$info['id'],'sgssz'=>'mp.weixin.qq.com'));
        }
        
        return $url;
    }

}