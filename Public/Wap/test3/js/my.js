var _innerHeight = window.innerHeight;
var _innerWidth = window.innerWidth;
var mySwiper;

$(function(){
	$('.swiper-container').css({
    	height: _innerHeight
  	});

  /* swiper */
  mySwiper = new Swiper('.swiper-container',{
    //initialSlide:7,
    // lazyLoading:true,
    longSwipesRatio:0.1,
    updateOnImagesReady:true,
    touchRadio:2,
    direction:'vertical',
    onInit:function(swiper){
      slide0();
    },
    onSlideChangeEnd:function(swiper){
      $('img').stop();
      $('img').attr('style','');
      if(isExistsFunction('slide' + swiper.activeIndex)){
        eval('slide' + swiper.activeIndex)();  
      }



    },
    onSlideChangeStart:function(swiper){
    },
  });

  
})

//是否存在指定的函数
function isExistsFunction(funcName){
  try{
    if(typeof(eval(funcName)) == 'function'){
      return true;
    }
  }catch(e){
    return false;
  }
}

function slide0(){
  $('#p02').animate({opacity:100,top:'40%'},2000);
}

function slide1(){
  $('#p13').animate({opacity:100,top:'-8%',right:'-10%'},2000);
  $('#p14').animate({opacity:100,top:'36%',left:'-10%'},2000);
  $('#p15').animate({opacity:100,top:'67%',right:'-22%'},2000);
  setTimeout(function(){
    $('#p16').animate({opacity:100,width:'38%'},6000);
  },1000)
}

function slide2(){
  $('#p22').animate({left:'-13%'},2000);
  $('#p23').animate({right:'-20%'},2000);
  $('#p24').animate({left:'-20%'},2000);
  setTimeout(function(){
    $('#p25').animate({opacity:100,width:'38%'},6000);    
  },1000)
}

function slide3(){
  $('#p33').fadeIn(2000);
  $('#p34').fadeIn(2000);
  setTimeout(function(){
    $('#p35').animate({width:'34%',left:'37%',top:'28%'},2000);
  },1000);
}

function slide4(){
  var m = $('#p41').width() - _innerWidth;
  $('#p41').animate({left:-m},10000,'linear');
  $('#p46').animate({left:'8%'},2000);
  $('#p42').animate({right:'-15%'},2000,function(){
      $('#p43').animate({left:'-15%'},2000);
      $('#p47').animate({right:'2%'},2000,function(){
        $('#p48').animate({left:'7%'},2000);
        $('#p44').animate({right:'-15%'},2000,function(){
          $('#p45').animate({left:'-15%'},2000);
          $('#p49').animate({right:'-5%'},2000);
        });
      });

  });
}

function slide5(){
  $('#p52').show(function(){
    $(this).animate({top:'10%'});
  })
}

function slide6(){
  $('#p62').show(function(){
    $(this).animate({top:'10%'},function(){
      $('#p63').fadeIn(2000);
    });
  })
}

function slide7(){
  $('#p72').animate({left:'6%'});
}