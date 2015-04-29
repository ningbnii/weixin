$(function(){
	$('.swiper-container').css({
		height: window.innerHeight
	});
  $('.star-static').css({
    height: window.innerHeight*0.3,
    width:window.innerWidth,
  });
  $('.clound').css({
    height:window.innerHeight+35,
    width:window.innerWidth,
    bottom:-window.innerHeight*0.9,
  })




  /* swiper */
	var mySwiper = new Swiper('.swiper-container',{
    // initialSlide:7,
    // lazyLoading:true,
    longSwipesRatio:0.1,
    touchRadio:2,
    direction:'vertical',
    onInit:function(swiper){
      $('.swiper-container').css({
        position: 'absolute',
        top: '0px',
        left: '0px',
        right: '0px',
        bottom: '0px',
      });
      $('#p19').addClass('animated infinite swing1');
      $('#p86').addClass('animated infinite swing1');
      setTimeout(function(){
        $('#p18').animate({
          top:-100,
        },1000)
        $('#p19').animate({opacity:'0'},2000);
        $('.star').animate({opacity:'1'},2000);
        $('.moon').animate({top:20},2000);
        $('#p15').animate({left:-15},2000);
        setTimeout(function(){
          $('#p11,#p12,#p13,#p14').fadeIn(2000);
          $('#p112,#p113,#p114').hide(100);      
        },1000);

        $('.slide0').removeClass('swiper-no-swiping');    
      },3000);
    },
    onSlideChangeEnd:function(swiper){
      eval('slide' + swiper.activeIndex)();
    },
    onSlideChangeStart:function(swiper){
      if(swiper.activeIndex == 7){
        $('#p85').css('display','block');
      }
    },

	});

  $('.slide0').click(function(){
    $('#p18').animate({
      top:-100,
    },1000)
    $('#p19').animate({opacity:'0'},2000);
    $('.star').animate({opacity:'1'},2000);
    $('.moon').animate({top:20},2000);
    $('#p15').animate({left:-15},2000);
    setTimeout(function(){
      $('#p11,#p12,#p13,#p14').fadeIn(2000);
      $('#p112,#p113,#p114').hide(100);      
    },1000);

    $('.slide0').removeClass('swiper-no-swiping');
  });
})

/* 更换动画效果 */
function change_animation(bindname,animation1,animation2,time){
  setTimeout(function(){
    bindname.removeClass(animation1).addClass(animation2);
  },time);
}

function slide0(){

}

function slide1(){
  $('#p29').animate({left:window.innerWidth*0.1,bottom:window.innerHeight*0.3},2000);
  $('#p25').animate({left:window.innerWidth*0.1},2000);
  $('#p26').animate({left:window.innerWidth*0.1},2000);
  $('#p27').animate({bottom:window.innerWidth*0.40},2000);
  $('#p28').animate({bottom:window.innerWidth*0.35},2000);
  setTimeout(function(){
    $('#p210').animate({opacity:1},1000);
  },2500);
  setTimeout(function(){
    $('#p211').animate({opacity:1},1000);
  },2000);
  setTimeout(function(){
    $('#p212').animate({opacity:1},1000);
  },3000);
}

function slide2(){
  $('.slide2 .row').prepend('<img src="Public/Wap/images/sleep/P3/6.png" alt="" class="img-responsive" id="p36"><img src="Public/Wap/images/sleep/P3/clock.gif" alt="" class="img-responsive" id="clock">');
  $('#p38').animate({bottom:window.innerHeight*0.20},1000);
  $('#p39').animate({bottom:window.innerHeight*0.17},1000);
  setTimeout(function(){
    $('#p35').animate({opacity:1},100);
    $('#p35').animate({top:window.innerHeight*0.2},1000);
  },1000)
}

function slide3(){
  $('#p45').animate({opacity:1},100);
  $('#p45').animate({top:window.innerHeight*0.2},1000);
}

function slide4(){
  $('.slide4 .row').prepend('<img src="Public/Wap/images/sleep/P5/light.gif" alt="" class="img-responsive" id="light">');
  $('#p55').animate({opacity:1},100);
  $('#p55').animate({top:window.innerHeight*0.2},1000);  
}

function slide5(){
  $('.slide5 .row').prepend('<img src="Public/Wap/images/sleep/P6/before-sleep.gif" alt="" class="img-responsive" id="before-sleep">');
  $('#p65').animate({opacity:1},100);
  $('#p65').animate({top:window.innerHeight*0.2},1000);
}

function slide6(){
  $('#p75').animate({opacity:1},100);
  $('#p75').animate({top:window.innerHeight*0.2},1000);
  setTimeout(function(){
    $('#p714').animate({opacity:1},100);    
    $('#p714').animate({top:window.innerHeight*0.4},1000);    
  },1500);
  setTimeout(function(){
    $('#p713').animate({opacity:1},100);
    $('#p713').animate({top:window.innerHeight*0.4},1000);
  },2000);
  setTimeout(function(){
    $('#p712').animate({opacity:1},100);
    $('#p712').animate({top:window.innerHeight*0.43},1000);
  },1000);

}

function slide7(){
  
  $('#p85').click(function(){    
    $(this).animate({
      top:window.innerHeight*0.0005,
    },1000)
    $('#p86').animate({opacity:'0'},1000);
    $('.clound').animate({bottom:0},2000);
    setTimeout(function(){
      $('#sun').fadeIn(1000);
      $('#p81').fadeIn(1000);
      $('#sleep').fadeIn(1000);
    },2000);
    $('.slide7').removeClass('swiper-no-swiping');
  });
}