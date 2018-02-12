var app = {
  formation_scroll: function () {
    var formation = $('#formation');
    if (formation.length > 0) {

      var scroll = window.scrollY;
      var height = ($(window).height())/2;
      var paris = $('#paris').offset().top;
      var puy = $('#puy').offset().top-height;
      var lyon = $('#lyon').offset().top-height;
      var annecy = $('#annecy').offset().top-height;
      //var bac = $('#bac').offset().top-height;

      if(scroll > puy) {
        $('#puy .bloc-left, #puy .bloc-right--up').addClass('appear');
      }
      if(scroll > lyon) {
        $('#lyon .bloc-left, #lyon .bloc-right--up-r').addClass('appear');
      }
      if(scroll > annecy) {
        $('#annecy .bloc-left, #annecy .bloc-right--up').addClass('appear');
      }
      // if(scroll > bac) {
      //   $('#bac .bloc-left, #bac .bloc-right--up-r').addClass('appear');
      // }
    }
  },
  home_scroll: function () {
    if ($('#1').length > 0 && $('#2').length > 0 && $('#3').length > 0) {

      var headerHeight = 50;
      if($(window).width() >= 1024) {
        headerHeight = 100;
      }
      var midlleWindow = ($(window).height())/2;

      var integ = $('#1').offset().top,
        info = $('#2').offset().top - headerHeight,
        sport = $('#3').offset().top - midlleWindow,
        info2 = $('#2').offset().top - midlleWindow,
        legende_sport = $('#3').offset().top - midlleWindow;

      var scroll = window.scrollY;

      if (scroll >= integ) {
        $(".indic").addClass('hide');
        $(".scrollTo").removeClass('active');
        $("#jsInte").addClass('active');
      }
      if (scroll >= info) {
        $(".scrollTo").removeClass('active');
        $("#jsInfo").addClass('active');
      }
      if (scroll >= info2) {
        $('.section__inner__img--3').addClass('appear');
        $('.section__inner__legende--info').addClass('appear');
      }
      if (scroll >= sport) {
        $(".scrollTo").removeClass('active');
        $("#jsSport").addClass('active');
      }
      if (scroll >= legende_sport) {
        $('.section__inner__legende--bg').addClass('appear');
      }
    }
  },
  headerSmall: function() {
    var scroll = window.scrollY;
    if (scroll > 50) {
      $('.main-header').addClass('dimin');
      $('.logo--1').addClass('hide');
      $('.logo--2').removeClass('hide');
      $('.main-header__inner').addClass('dimin');
      $('.main-header__inner a').addClass('dimin');
      $('.main-header.home svg').addClass('hide');
    } else {
      $('.main-header').removeClass('dimin');
      $('.logo--1').removeClass('hide');
      $('.logo--2').addClass('hide');
      $('.main-header__inner').removeClass('dimin');
      $('.main-header__inner a').removeClass('dimin');
      $('.main-header.home svg').removeClass('hide');
    }
  },
  navActive: function() {
    $('.scrollTo').on('click', function() {
      $('.scrollTo').removeClass('active');
      $(this).addClass('active');
    });
  },
  scrollTo: function (trigger, height) {
    $(trigger).click(function () {
      var haut = $(this).attr('href');
      var speed = 500;
      $('html, body').animate({ scrollTop: $(haut).offset().top - height }, speed);
      return false;
    });
  },
  openMenu: function (trigger, content) {
    $(trigger).click(function () {
      $(content).addClass('open');
    });
  },
  closeMenu: function (trigger, content) {
    $(trigger).click(function () {
      $(content).removeClass('open');
    });
  },
  appear: function (trigger) {
    $(trigger).addClass('appear');
  },
  toggleOpenClose: function(trigger) {
    $(trigger).click(function () {
      $(this).parents('.trigger').toggleClass('close');
    });
  },
  changeText: function() {
    $('#jsToogleAdminToolbar').click(function () {
      if($(this).parents('.trigger').hasClass('close')) {
        $(this).text('[OUVRIR]');
      } else {
        $(this).text('[FERMER]');
      }
    });
  },
  openModal: function (el) {
    $('.' + el).on('click', function() {
      $('#' + el).addClass('show');
      $('.modal-bg').fadeIn();
    });
  },
  closeModal: function (el) {
    $('.modal ' + el).on('click', function() {
      $(this).parents('.modal').removeClass('show');
      $('.modal-bg').fadeOut();
    });
  },
  openProject: function() {
    $('.projects__list__item').on('click', function() {
      $('.overlay').addClass('active');
      $('body').addClass('modal-active');
      $(this).addClass('active');
      var order = $(this).attr('data-order');
      $('#'+order).addClass('open');
    });
  },
  closeProject: function () {
    $('.cancel').on('click', function () {
      $('.overlay').removeClass('active');
      $('body').removeClass('modal-active');
      $(this).parents('.project').prev().removeClass('active');
      $(this).parents('.project').removeClass('open');
    });
  },
  ajaxSendMail: function () {
    $('.main-footer__inner__2 form, .footer-mobile__2 form').submit(function(e) {
      e.preventDefault();
      $('.main-footer__inner__2 form button, .footer-mobile__2 form button').prop('disabled', true );
      var jsUrlSendMail = $('.jsUrlSendMail').val();
      var alert = $('.block-alert.mail');

      console.log('URL : '+jsUrlSendMail);

      $.ajax({
        url: jsUrlSendMail,
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
          if(response.type == 'success') {
            $('.main-footer__inner__2 form button').prop('disabled', false);
            alert.addClass('show');
            alert.append('<div class="alert alert--success" role="alert">' + response.message + '</div>');
          }
        },
        error: function(response) {
          alert.removeClass('show');
          alert.append('<div class="alert alert--error" role="alert">' + response.message + '</div>');
        }
      })
    });
  },
}; // end of app

$(document).ready(function () {

  app.appear('.section__inner__img--1');
  app.appear('.section__inner__img--2');
  app.appear('.section__inner__legende--inte');
  app.appear('.section__inner__maquettes--1');
  app.appear('.section__inner__maquettes--2');
  app.appear('.section__inner__maquettes--3');
  app.navActive();
  app.openMenu('#jsOpenMenu', '.header-mobile');
  app.closeMenu('#jsCloseMenu', '.header-mobile');
  app.toggleOpenClose('#jsToogleAdminToolbar');
  app.changeText();

  window.addEventListener('scroll', app.formation_scroll);
  window.addEventListener('scroll', app.home_scroll);
  window.addEventListener('scroll', app.headerSmall);
  app.scrollTo('.scrollTo', 50);

  if($(window).width() >= 1024) {
    app.scrollTo('.up', 100);
  } else {
      app.scrollTo('.up', 50);
  }

  $('#paris .bloc-left, #paris .bloc-right--up-r').addClass('appear');

  app.closeModal('.cancel');
  app.openModal('jsModalCreateFormation');
  app.openModal('jsModalCreateProject');
  app.openProject();
  app.closeProject();
  app.ajaxSendMail();
});
