(function ($) {
    "use strict";
    $(document).on('click', function (e) {
        var outside_space = $(".outside");
        if (!outside_space.is(e.target) &&
            outside_space.has(e.target).length === 0) {
            $(".menu-to-be-close").removeClass("d-block");
            $('.menu-to-be-close').css('display', 'none');
        }
    })
    $('.prooduct-details-box .close').on('click', function (e) {
        var tets = $(this).parent().parent().parent().parent().addClass('d-none');
        console.log(tets);
    })
    if ($('.page-wrapper').hasClass('horizontal-wrapper')){
        $(".sidebar-list").hover(
            function () {
              $(this).addClass("hoverd");
            },
            function () {
              $(this).removeClass("hoverd");
            }
        );
        $(window).on('scroll', function () {
            if ($(this).scrollTop() < 600) {
                $(".sidebar-list").removeClass("hoverd");
            }
        });
      }
    /*----------------------------------------
     passward show hide
     ----------------------------------------*/
    $('.show-hide').show();
    $('.show-hide span').addClass('show');
    $('.show-hide span').on("click", function () {
        if ($(this).hasClass('show')) {
            $('input[name="login[password]"]').attr('type', 'text');
            $(this).removeClass('show');
        } else {
            $('input[name="login[password]"]').attr('type', 'password');
            $(this).addClass('show');
        }
    });
    $('form button[type="submit"]').on('click', function () {
        $('.show-hide span').addClass('show');
        $('.show-hide').parent().find('input[name="login[password]"]').attr('type', 'password');
    });
    //landing header //
    $(".toggle-menu").on('click', function (){
        $('.landing-menu').toggleClass('open');
    });   
    $(".menu-back").on('click', function (){
        $('.landing-menu').toggleClass('open');
    });  
    $(".md-sidebar-toggle").on('click', function (){
        $('.md-sidebar-aside').toggleClass('open');
    });
    /*=====================
      02. Background Image js
      ==========================*/
    $(".bg-center").parent().addClass('b-center');
    $(".bg-img-cover").parent().addClass('bg-size');
    $('.bg-img-cover').each(function () {
        var el = $(this),
            src = el.attr('src'),
            parent = el.parent();
        parent.css({
            'background-image': 'url(' + src + ')',
            'background-size': 'cover',
            'background-position': 'center',
            'display': 'block'
        });
        el.hide();
    });
    $(".mega-menu-container").css("display", "none");
    $(".header-search").on("click", function () {
        $(".search-full").addClass("open");
    });
    $(".close-search").on("click", function () {
        $(".search-full").removeClass("open");
        $("body").removeClass("offcanvas");
    });
    $(".mobile-toggle").on("click", function () {
        $(".nav-menus").toggleClass("open");
    });
    $(".mobile-toggle-left").on("click", function () {
        $(".left-header").toggleClass("open");
    });
    $(".bookmark-search").on("click", function () {
        $(".form-control-search").toggleClass("open");
    })
    $(".filter-toggle").on("click", function () {
        $(".product-sidebar").toggleClass("open");
    });
    $(".toggle-data").on("click", function () {
        $(".product-wrapper").toggleClass("sidebaron");
    });
    $(".form-control-search input").keyup(function (e) {
        if (e.target.value) {
            $(".page-wrapper").addClass("offcanvas-bookmark");
        } else {
            $(".page-wrapper").removeClass("offcanvas-bookmark");
        }
    });
    $(".search-full input").keyup(function (e) {
        console.log(e.target.value);
        if (e.target.value) {
            $("body").addClass("offcanvas");
        } else {
            $("body").removeClass("offcanvas");
        }
    });
    $('body').keydown(function (e) {
        if (e.keyCode == 27) {
            $('.search-full input').val('');
            $('.form-control-search input').val('');
            $('.page-wrapper').removeClass('offcanvas-bookmark');
            $('.search-full').removeClass('open');
            $('.search-form .form-control-search').removeClass('open');
            $("body").removeClass("offcanvas");
        }
    });
    $("body").addClass(localStorage.getItem("cion_mode") ? localStorage.getItem("cion_mode") : "light")
    $(".mode").addClass(localStorage.getItem("cion_mode") === "dark-only" ? "active" : " ")
    // sidebar filter
    $('.md-sidebar .md-sidebar-toggle ').on('click', function(e) {
        $(".md-sidebar .md-sidebar-aside ").toggleClass("open");   
    });
    $('.loader-wrapper').fadeOut('slow', function () {
        $(this).remove();
    });
    const button = document.querySelector('.tap-top');
    const displayButton = () => {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
          button.style.display = "block";
        } else {
          button.style.display = "none";
        }
      });
    };
    const scrollToTop = () => {
      button.addEventListener("click", () => {
        window.scroll({
          top: 0,
          left: 0,
          behavior: 'smooth'
        }); 
        console.log(event);
      });
    };
    displayButton();
    scrollToTop();
    // active link
    $(".chat-menu-icons .toogle-bar").on("click", function () {
        $(".chat-menu").toggleClass("show");
    });
    $(".mobile-title svg").on("click", function () {
        $(".header-mega").toggleClass("d-block");
    });
    $(".onhover-dropdown").on("click", function () {
        $(this).children('.onhover-show-div').toggleClass("active");
    });
    // Language
var tnum = 'en';

$(document).ready(function () {

    if (localStorage.getItem("cion_primary") != null) {
        var primary_val = localStorage.getItem("cion_primary");
        $("#ColorPicker1").val(primary_val);
        var secondary_val = localStorage.getItem("cion_secondary");
        $("#ColorPicker2").val(secondary_val);
    }


    $(document).on('click', function (e) {
        $('.translate_wrapper, .more_lang').removeClass('active');
    });
    $('.translate_wrapper .current_lang').on('click', function (e) {
        e.stopPropagation();
        $(this).parent().toggleClass('active');

        setTimeout(function () {
            $('.more_lang').toggleClass('active');
        }, 5);
    });


});


    $("#flip-btn").click(function(){
        $(".flip-card-inner").addClass("flipped")
    });
    $("#flip-back").click(function(){
        $(".flip-card-inner").removeClass("flipped")
    })
    $(".search-box").on("click", function () {
        $(".search-input").toggleClass("open")
    })

    $(".serchbox").on("click", function (e) {
        $(".search-form").toggleClass("open");
        e.preventDefault();
      });
})(jQuery);
