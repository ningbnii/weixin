
function systemmsg(msg, fn){
    var msgbox = $("#systemmsg_box");
    msg && msg.length>0 && $('#systemmsg_text').html(msg);    
    $('#systemmsg_btn').bind('click', function(){
      msgbox.hide();
      fn && fn();
    });
    msgbox.show();
}

//鎵嬫満娴嬭瘯
function isMobile(str) {
  return /^1[3-9]{1}[0-9]{9}$/.test(str);
}

//閭娴嬭瘯
function isEmail(str){
    var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    if(reg.test(str)){ 
        return true;
    }
    return false;
}

//杞︾墝娴嬭瘯
function isLicenseNo(str) {
  return /(^[\u4E00-\u9FA5]{1}[A-Z0-9]{6}$)|(^[A-Z]{2}[A-Z0-9]{2}[A-Z0-9\u4E00-\u9FA5]{1}[A-Z0-9]{4}$)|(^[\u4E00-\u9FA5]{1}[A-Z0-9]{5}[鎸傚璀﹀啗娓境]{1}$)|(^[A-Z]{2}[0-9]{5}$)|(^(08|38){1}[A-Z0-9]{4}[A-Z0-9鎸傚璀﹀啗娓境]{1}$)/i.test(str);
}

var dataForWeixin = {
    appid: '', //骞冲畨璐㈠瘜甯瓵PPID
    img_url: '',
    img_width: '100',
    img_height: '100',
    link: '',
    title: '',
    desc: '',
    callback:function(){}
};

// 褰撳井淇″唴缃祻瑙堝櫒瀹屾垚鍐呴儴鍒濆鍖栧悗浼氳Е鍙慦eixinJSBridgeReady浜嬩欢銆�
(function(){
    var onBridgeReady = function(){
        var WJ = WeixinJSBridge;
        
        // 鍙戦€佺粰濂藉弸
        WJ.on('menu:share:appmessage', function() {
            WJ.invoke('sendAppMessage', dataForWeixin, function(res) {
                dataForWeixin.callback();
            });
        });
        // 鍙戦€佸埌鏈嬪弸鍦�
        WJ.on('menu:share:timeline', function() {
            WJ.invoke('shareTimeline', dataForWeixin, function(res) {
                dataForWeixin.callback();
            });
        });

        // 鍙戦€佸埌寰崥
        WJ.on('menu:share:weibo', function() {
            WJ.invoke('shareWeibo', dataForWeixin, function(res) {
                dataForWeixin.callback();
            });
        });
        
    	//鏄剧ず鍙充笂瑙掍笁涓偣鎸夐挳
    	WJ.call('showOptionMenu');
    };
    
    if (document.addEventListener) {
        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
    
})();