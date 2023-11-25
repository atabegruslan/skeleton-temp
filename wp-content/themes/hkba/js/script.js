(function($) {

    $(document).ready(function(){
        $('.bxSlider').bxSlider({
            //mode: 'fade',
            captions: true,
            pager: false,
            minSlides: 3,
            maxSlides: 3,
            moveSlides: 3,
            slideWidth: 1000,
            slideMargin: 50
        });
    });


    $(document).ready(footerToBottom);
    function footerToBottom()
    {
      correctFooterTop = ( $(window).height() - $('#footer').height() );
      if ($("#footer").offset().top < correctFooterTop)
      {
        $("#footer").offset({ top: correctFooterTop });
      }
    }

})(jQuery);
