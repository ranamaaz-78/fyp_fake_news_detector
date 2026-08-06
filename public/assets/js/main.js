/*=== Javascript function indexing hear===========

01. rtsJs.swiperActivation();
02. rtsJs.wowActive();
03. rtsJs.customSelectActive();
04. rtsJs.videoActivation();
05. rtsJs.odoMeter();
06. rtsJs.searchOption();
07. rtsJs.backToTopInit();
08. rtsJs.stickyHeader();
09. rtsJs.sideMenu();
10. rtsJs.metismenu();
11. rtsJs.preloader();
12. rtsJs.smoothScroll();
13. rtsJs.isotop();
14. rtsJs.countDown();
15. rtsJs.slider_drag_cursor();
16. rtsJs.rtlToggle();


==================================================*/

(function ($) {
  'use strict';
  let device_width = window.innerWidth;
  $.exists = function (selector) {
    return $(selector).length > 0;
  };

  var rtsJs = {
    m: function (e) {
      rtsJs.d();
      rtsJs.methods();
    },
    d: function (e) {
      this._window = $(window),
        this._document = $(document),
        this._body = $('body'),
        this._html = $('html')
    },
    methods: function (e) {
      rtsJs.swiperActivation();
      rtsJs.splitText();
      rtsJs.textTitleAnimation__1();
      rtsJs.wowActive();
      rtsJs.customSelectActive();
      rtsJs.countDown();
      rtsJs.videoActivation();
      rtsJs.preloader();
      rtsJs.searchOption();
      rtsJs.odoMeter();
      rtsJs.backToTopInit();
      rtsJs.stickyHeader();
      rtsJs.sideMenu();
      rtsJs.sideMenu2();
      rtsJs.smoothScroll();
      rtsJs.isotop();
      rtsJs.metismenu();
      rtsJs.rtlToggle();
      rtsJs.regionAreaActive();
      rtsJs.projectBlockActive();
      rtsJs.itineraryAccordion();
      rtsJs.priceRangeSlider();
      rtsJs.animationOnHover();
      rtsJs.accordionActive();
      rtsJs.mesonaryTab();
      rtsJs.fonklsAnimation();
    },
    fonklsAnimation: function () {
          let endTl = gsap.timeline({
              repeat: -1,
              delay: 0.2,
              scrollTrigger: {
                  trigger: '.end',
                  start: 'bottom 100%-=50px'
              }
          });
          gsap.set('.end', {
              opacity: 0
          });
          gsap.to('.end', {
              opacity: 1,
              duration: 1,
              ease: 'power2.out',
              scrollTrigger: {
                  trigger: '.end',
                  start: 'bottom 100%-=50px',
                  once: true
              }
          });
          let mySplitText = new SplitText(".end", {
              type: "words,chars"
          });
          let chars = mySplitText.chars;
          endTl.to(chars, {
              duration: 0.5,
              scaleY: 0.9,
              ease: "power3.out",
              stagger: 0.04,
              transformOrigin: 'center bottom'
          });
          endTl.to(chars, {
              yPercent: -10,
              ease: "elastic",
              stagger: 0.03,
              duration: 0.8
          }, 0.5);
          endTl.to(chars, {
              scaleY: 1,
              ease: "elastic.out(2.5, 0.2)",
              stagger: 0.03,
              duration: 1.5
          }, 0.5);
          endTl.to(chars, {

              ease: "power2.out",
              stagger: 0.03,
              duration: 0.3
          }, 0.5);
          endTl.to(chars, {
              yPercent: 0,
              ease: "back",
              stagger: 0.03,
              duration: 0.8
          }, 0.7);
          endTl.to(chars, {
              // color: '#b19777',
              duration: 1.4,
              stagger: 0.05
          });
    },
    swiperActivation: function () {
      $(document).ready(function () {
        var swiper = new Swiper(".banner-slider-active", {
          slidesPerView: 1,
          speed: 1200,
          effect: "fade",
          autoplay: {
            delay: 6000,
            disableOnInteraction: false,
          },
          loop: true,
          pagination: {
            el: ".slider-dots",
            clickable: true,
          }
        });
      })
      $(document).ready(function () {
        var swiper = new Swiper('.rts-brand-slide', {
          slidesPerView: 'auto',
          spaceBetween: 30,
          speed: 1200,
          loop: true,
          autoplay: {
            delay: 2000,
            disableOnInteraction: false,
          },
          breakpoints: {
            1200: {
              slidesPerView: 5,
            },
            992: {
              slidesPerView: 4,
            },
            767: {
              slidesPerView: 3,
            },
            0: {
              slidesPerView: 2,
            },
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper(".testimonialsSlider", {
          slidesPerView: 2,
          spaceBetween: 30,
          speed: 1200,
          loop: true,
          navigation: {
            nextEl: ".swiper-btn-next",
            prevEl: ".swiper-btn-prev",
          },
          breakpoints: {
            992: {
              slidesPerView: 2,
              autoplay: true,
            },
            767: {
              slidesPerView: 1,
              autoplay: true,
            },
            0: {
              slidesPerView: 1,
            },
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper(".testimonialsSlider2", {
          slidesPerView: 1,
          spaceBetween: 30,
          speed: 1200,
          loop: true,
          navigation: {
            nextEl: ".swiper-btn-next",
            prevEl: ".swiper-btn-prev",
          },
          breakpoints: {
            767: {
              slidesPerView: 1,
              autoplay: true,
            },
            0: {
              slidesPerView: 1,
            },
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper(".testimonialsSlider3", {
          slidesPerView: 2,
          spaceBetween: 28,
          speed: 1200,
          loop: true,
          navigation: {
            nextEl: ".swiper-btn-next",
            prevEl: ".swiper-btn-prev",
          },
          breakpoints: {
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
              autoplay: true,
            },
            0: {
              slidesPerView: 1,
            },
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper(".testimonialsSlider4", {
          slidesPerView: 1,
          spaceBetween: 30,
          speed: 1200,
          loop: true,
          navigation: {
            nextEl: ".swiper-btn-next",
            prevEl: ".swiper-btn-prev",
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper(".testimonialsSlider5", {
          slidesPerView: 2.3,
          spaceBetween: 30,
          centeredSlides: true,
          speed: 1200,
          loop: true,
          navigation: {
            nextEl: ".swiper-btn-next",
            prevEl: ".swiper-btn-prev",
          },
          breakpoints: {
            1200: {
              slidesPerView: 2,
            },
            992: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
            },
            576: {
              slidesPerView: 1.5,
            },
            450: {
              slidesPerView: 1.2,
            },
            0: {
              slidesPerView: 1,
            },
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper(".testimonialsSlider10", {
          slidesPerView: 2,
          spaceBetween: 30,
          speed: 1200,
          loop: true,
          navigation: {
            nextEl: ".swiper-btn-next",
            prevEl: ".swiper-btn-prev",
          },
          breakpoints: {
            1200: {
              slidesPerView: 2,
            },
            992: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            },
          },
        })
      })
      $(document).ready(function () {
        var swiper = new Swiper('.rts-brand-slide-two', {
          slidesPerView: 'auto',
          spaceBetween: 30,
          speed: 1200,
          loop: true,
          autoplay: {
            delay: 2000,
            disableOnInteraction: false,
          },
          breakpoints: {
            1200: {
              slidesPerView: 6,
            },
            992: {
              slidesPerView: 5,
            },
            767: {
              slidesPerView: 4,
            },
            0: {
              slidesPerView: 2,
            },
          },
        })
      })
    },
    accordionActive: function () {
      const accordionHeaders = document.querySelectorAll('.accordion-header');
      if (!accordionHeaders.length) return;

      accordionHeaders.forEach(function (header, index) {
        header.addEventListener('click', function () {
          const accordionItems = document.querySelectorAll('.accordion-item');

          accordionItems.forEach(function (item) {
            item.classList.remove('active');
          });

          const clickedItem = accordionItems[index];
          if (clickedItem) {
            clickedItem.classList.add('active');
          }
        });
      });
    },
    wowActive: function () {
      new WOW().init();
    },
    customSelectActive: function () {
      document.querySelectorAll('.custom-select').forEach(select => {
        const trigger = select.querySelector('.custom-select-trigger');
        const options = select.querySelector('.custom-options');
        const hiddenInput = select.querySelector('input[type="hidden"]');

        // Toggle dropdown
        trigger.addEventListener('click', (e) => {
          e.stopPropagation(); // prevent triggering document click
          const isActive = select.classList.contains('active');

          // Close all other selects
          document.querySelectorAll('.custom-select').forEach(s => {
            s.classList.remove('active');
            s.querySelector('.custom-options').style.height = '0';
          });

          if (!isActive) {
            select.classList.add('active'); // ✅ add active class
            options.style.height = '250px';
          } else {
            select.classList.remove('active'); // remove active class
            options.style.height = '0';
          }
        });

        // Select option
        options.querySelectorAll('.option').forEach(option => {
          option.addEventListener('click', () => {
            trigger.textContent = option.textContent;
            hiddenInput.value = option.dataset.value;

            options.querySelectorAll('.option').forEach(o => o.classList.remove('selected'));
            option.classList.add('selected');

            options.style.height = '0';
            select.classList.remove('active'); // ✅ remove active after selection
          });
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', e => {
          if (!select.contains(e.target)) {
            options.style.height = '0';
            select.classList.remove('active'); // ✅ remove active class
          }
        });
      });
    },
    splitText: function (e) {
      if ($('.rts-text-anime-style-1').length) {
        let animatedTextElements = document.querySelectorAll('.rts-text-anime-style-1');

        animatedTextElements.forEach((element) => {
          //Reset if needed
          if (element.animation) {
            element.animation.progress(1).kill();
            element.split.revert();
          }

          element.split = new SplitText(element, {
            type: "lines,words,chars",
            linesClass: "split-line",
          });
          gsap.set(element, { perspective: 400 });

          gsap.set(element.split.chars, {
            opacity: 0,
            x: "50",
          });

          element.animation = gsap.to(element.split.chars, {
            scrollTrigger: { trigger: element, start: "top 95%" },
            x: "0",
            y: "0",
            rotateX: "0",
            opacity: 1,
            duration: 1,
            ease: Back.easeOut,
            stagger: 0.02,
          });
        });
      }
    },
    textTitleAnimation__1: function () {
      if (window.innerWidth > 650) {

        const quotes = document.querySelectorAll(".quote");
        const quotes2 = document.querySelectorAll(".quote-2");

        function setupSplits() {
          $(document).ready(function () {
            $(".split-line").wrap('<div class="split-parent"></div>');
          });


          quotes.forEach(quote => {

            quote.split = new SplitText(quote, {
              type: "lines,words,chars",
              linesClass: "split-line"
            });

            // Set up the anim
            quote.anim = gsap.from(quote.split.lines, {
              scrollTrigger: {
                trigger: quote,
                toggleActions: "play none none none",
                once: true,
                start: "bottom 100%",
                markers: true,
              },
              duration: 0.6,
              delay: 0.3,
              ease: "circ.out",
              yPercent: 100,
              stagger: 0.2,
            });
          });



          quotes2.forEach(quote2 => {

            quote2.split = new SplitText(quote2, {
              type: "lines"
            });

            // Set up the anim
            quote2.anim = gsap.from(quote2.split.lines, {
              scrollTrigger: {
                trigger: quote2,
                toggleActions: "play none none none",
                once: true,
                start: "50% 60%",
                markers: true,
              },
              duration: 0.6,
              autoAlpha: 0,
              ease: "circ.out",
              yPercent: 100,
              stagger: 0.2,
            });
          });
        }

        // ScrollTrigger.addEventListener("refresh", setupSplits);
        setupSplits();
      }



    },
    countDown: function () {
      $(function () {
        countDown.init();
        updateCountdowns();
        setInterval(updateCountdowns, 1000);

        function updateCountdowns() {
          countDown.validElements.forEach((element, i) => {
            countDown.changeTime(element, countDown.endDate[i], i);
          });
        }
      });

      const countDown = {
        endDate: [],
        validElements: [],
        display: [],
        initialHeight: undefined,
        initialInnerDivMarginTop: undefined,
        originalBorderTopStyle: undefined,

        init: function () {
          $('.countDown').each(function () {
            const regex_match = $(this).text().match(/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/);
            if (!regex_match) return;

            const end = new Date(regex_match[3], regex_match[2] - 1, regex_match[1], regex_match[4], regex_match[5], regex_match[6]);

            if (end > new Date()) {
              countDown.validElements.push($(this));
              countDown.endDate.push(end);
              countDown.changeTime($(this), end, countDown.validElements.length - 1);
              $(this).html(countDown.display.next.map(item => `<div class='container'><div class='a'><div>${item}</div></div></div>`).join(''));
            } else {
              // Display your message when the countdown expires
              $(this).html("<p class='end'>Sorry, your session has expired.</p>");
            }
          });
        },

        reset: function (element) {
          // This function appears to be incomplete, as it currently doesn't do anything.
        },

        changeTime: function (element, endTime) {
          if (!endTime) return;

          const today = new Date();
          if (today.getTime() <= endTime.getTime()) {
            countDown.display = {
              'last': this.calcTime(endTime.getTime() - today.getTime() + 1000),
              'next': this.calcTime(endTime.getTime() - today.getTime())
            };
            countDown.display.next = countDown.display.next.map(item => item.toString().padStart(2, '0'));
            countDown.display.last = countDown.display.last.map(item => item.toString().padStart(2, '0'));

            element.find('div.container div.a div').each((index, div) => {
              $(div).text(countDown.display.last[index]);
            });

            this.reset(element.find('div.container'));
          } else {
            element.html("<p class='end'>Sorry, your session has expired.</p>");
          }
        },

        calcTime: function (milliseconds) {
          const secondsTotal = Math.floor(milliseconds / 1000);
          const days = Math.floor(secondsTotal / 86400);
          const hours = Math.floor((secondsTotal % 86400) / 3600);
          const minutes = Math.floor((secondsTotal % 3600) / 60);
          const seconds = secondsTotal % 60;
          return [days, hours, minutes, seconds];
        }
      };

    },
    animationOnHover: function () {
      let cards = document.querySelectorAll('.rtsonhover');
      cards.forEach((rtsOnHover) => {
        rtsOnHover.onmousemove = function (e) {
          let rect = rtsOnHover.getBoundingClientRect();
          let x = e.clientX - rect.left; // element X position
          let y = e.clientY - rect.top;  // element Y position
          rtsOnHover.style.setProperty('--x', `${x}px`);
          rtsOnHover.style.setProperty('--y', `${y}px`);
        };
      });
    },
    videoActivation: function (e) {
      $(document).ready(function () {
        $('.popup-youtube, .popup-video').magnificPopup({
          disableOn: 700,
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,
          fixedContentPos: false
        });
      });
    },
    preloader: function () {
      window.addEventListener('load', function () {
        document.querySelector('body').classList.add("loaded")
      });
    },
    // search popup
    searchOption: function () {
      $(document).on('click', '#search', function () {
        $(".search-input-area").addClass("show");
        $("#anywhere-home").addClass("bgshow");
      });
      $(document).on('click', '#close', function () {
        $(".search-input-area").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '#anywhere-home', function () {
        $(".search-input-area").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
    },
    odoMeter: function () {
      $(document).ready(function () {
        function isInViewport(element) {
          const rect = element.getBoundingClientRect();
          return (
            rect.top >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
          );
        }

        function triggerOdometer(element) {
          const $element = $(element);
          if (!$element.hasClass('odometer-triggered')) {
            const countNumber = $element.attr('data-count');
            $element.html(countNumber);
            $element.addClass('odometer-triggered'); // Add a class to prevent re-triggering
          }
        }

        function handleOdometer() {
          $('.odometer').each(function () {
            if (isInViewport(this)) {
              triggerOdometer(this);
            }
          });
        }

        // Check on page load
        handleOdometer();

        // Check on scroll
        $(window).on('scroll', function () {
          handleOdometer();
        });
      });
    },
    // BACK TO TOP BUTTON JS
    backToTopInit: function () {
      $(document).ready(function () {
        "use strict";

        var progressPath = document.querySelector('.progress-wrap path');
        var pathLength = progressPath.getTotalLength();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
        var updateProgress = function () {
          var scroll = $(window).scrollTop();
          var height = $(document).height() - $(window).height();
          var progress = pathLength - (scroll * pathLength / height);
          progressPath.style.strokeDashoffset = progress;
        }
        updateProgress();
        $(window).scroll(updateProgress);
        var offset = 50;
        var duration = 550;
        jQuery(window).on('scroll', function () {
          if (jQuery(this).scrollTop() > offset) {
            jQuery('.progress-wrap').addClass('active-progress');
            jQuery('.rts-switcher').addClass('btt__visible');
          } else {
            jQuery('.progress-wrap').removeClass('active-progress');
            jQuery('.rts-switcher').removeClass('btt__visible');
          }
        });
        jQuery('.progress-wrap').on('click', function (event) {
          event.preventDefault();
          jQuery('html, body').animate({ scrollTop: 0 }, duration);
          return false;
        })


      });

    },
    // sticky header activation
    stickyHeader: function (e) {
      $(window).scroll(function () {
        if ($(this).scrollTop() > 150) {
          $('.header--sticky').addClass('sticky')
        } else {
          $('.header--sticky').removeClass('sticky')
        }
      })
    },
    // side menu desktop
    sideMenu: function () {
      // metismenu active
      $('#mobile-menu-active2').metisMenu();

      // collups menu side right
      $(document).on('click', '.menu-btn-toggle2', function () {
        $("#side-bar2").addClass("show");
        $("#anywhere-home").addClass("bgshow");
      });
      $(document).on('click', '.close-icon-menu', function () {
        $("#side-bar2").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '#anywhere-home', function () {
        $("#side-bar2").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '.onepage .mainmenu li a', function () {
        $("#side-bar2").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
    },
    sideMenu2: function () {
      // metismenu init only once under 992px
      if ($(window).width() < 992) {
        $('#mobile-menu-active').metisMenu();
      }

      // open sidebar
      $(document).on('click', '.menu-btn-toggle', function () {
        if ($(window).width() < 992) {
          $("#side-bar").addClass("show");
          $("#anywhere-home").addClass("bgshow");
        }
      });

      // close sidebar via close button
      $(document).on('click', '.close-icon-menu', function () {
        if ($(window).width() < 992) {
          $("#side-bar").removeClass("show");
          $("#anywhere-home").removeClass("bgshow");
        }
      });

      // close sidebar via overlay
      $(document).on('click', '#anywhere-home', function () {
        if ($(window).width() < 992) {
          $("#side-bar").removeClass("show");
          $("#anywhere-home").removeClass("bgshow");
        }
      });

      // close sidebar on menu item click
      $(document).on('click', '.onepage .mainmenu li a', function () {
        if ($(window).width() < 992) {
          $("#side-bar").removeClass("show");
          $("#anywhere-home").removeClass("bgshow");
        }
      });

      // remove sidebar classes when screen >= 992px
      $(window).on("resize", function () {
        if ($(window).width() >= 992) {
          $("#side-bar").removeClass("show");
          $("#anywhere-home").removeClass("bgshow");
        }
      });
    },
    smoothScroll: function (e) {
      $(document).on('click', '.onepage a[href^="#"]', function (event) {
        event.preventDefault();

        const target = $.attr(this, 'href');

        // prevent error if href is just "#"
        if (target.length > 1 && $(target).length) {
          $('html, body').animate({
            scrollTop: $(target).offset().top
          }, 300);
        }
      });
    },
    isotop: function (e) {
      $(document).ready(function () {


        var isotope = $(".main-isotop");

        if (isotope.length) {
          var iso = new Isotope('.filter', {
            itemSelector: '.element-item',
            layoutMode: 'fitRows'
          });

          // filter functions
          var filterFns = {
            // show if name ends with -ium
            ium: function (itemElem) {
              var name = itemElem.querySelector('.name').textContent;
              return name.match(/ium$/);
            }
          };

          // bind filter button click
          var filtersElem = document.querySelector('.filters-button-group');
          filtersElem.addEventListener('click', function (event) {
            // only work with buttons
            if (!matchesSelector(event.target, 'button')) {
              return;
            }
            var filterValue = event.target.getAttribute('data-filter');
            // use matching filter function
            filterValue = filterFns[filterValue] || filterValue;
            iso.arrange({ filter: filterValue });
          });

          // change is-checked class on buttons
          var buttonGroups = document.querySelectorAll('.button-group');
          for (var i = 0, len = buttonGroups.length; i < len; i++) {
            var buttonGroup = buttonGroups[i];
            radioButtonGroup(buttonGroup);
          }
          function radioButtonGroup(buttonGroup) {
            buttonGroup.addEventListener('click', function (event) {
              // only work with buttons
              if (!matchesSelector(event.target, 'button')) {
                return;
              }
              buttonGroup.querySelector('.is-checked').classList.remove('is-checked');
              event.target.classList.add('is-checked');
            });
          }
        }

        if ($('.grid-masonary').length) {

          // image loaded portfolio init
          $('.grid-masonary').imagesLoaded(function () {
            $('.portfolio-filter').on('click', 'button', function () {
              var filterValue = $(this).attr('data-filter');
              $grid.isotope({
                filter: filterValue
              });
            });
            var $grid = $('.grid-masonary').isotope({
              itemSelector: '.grid-item-p',
              percentPosition: true,
              masonry: {
                columnWidth: '.grid-item-p',
              }
            });
          });
        }

        // portfolio Filter
        $('.portfolio-filter button').on('click', function (event) {
          $(this).siblings('.is-checked').removeClass('is-checked');
          $(this).addClass('is-checked');
          event.preventDefault();
        });


      });

    },
    metismenu: function () {
      $('#mobile-menu-active').metisMenu();
    },
    // slider_drag_cursor: function () {
    //   const cursor = document.querySelector(".slider-drag-cursor");
    //   const dragWraps = document.querySelectorAll(".slider-drag-wrap");

    //   if (!cursor) return;

    //   let pos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
    //   let mouse = { x: pos.x, y: pos.y };
    //   const speed = 0.15; // smoothness (lower = smoother, higher = faster)

    //   // Center offset
    //   cursor.style.position = "fixed";
    //   cursor.style.left = "0px";
    //   cursor.style.top = "0px";
    //   cursor.style.transform = "translate(-50%, -50%)";
    //   cursor.style.pointerEvents = "none";

    //   // Update mouse position
    //   window.addEventListener("pointermove", (e) => {
    //     mouse.x = e.clientX;
    //     mouse.y = e.clientY;
    //   });

    //   // Smooth cursor movement using requestAnimationFrame
    //   function animate() {
    //     pos.x += (mouse.x - pos.x) * speed;
    //     pos.y += (mouse.y - pos.y) * speed;
    //     cursor.style.transform = `translate(${pos.x}px, ${pos.y}px) translate(-50%, -50%)`;
    //     requestAnimationFrame(animate);
    //   }

    //   animate();

    //   // Hover effects for .slider-drag-wrap
    //   dragWraps.forEach((wrap) => {
    //     wrap.addEventListener("mouseenter", () => {
    //       cursor.classList.add("active");
    //     });
    //     wrap.addEventListener("mouseleave", () => {
    //       cursor.classList.remove("active");
    //     });

    //     // Links inside .slider-drag-wrap
    //     wrap.querySelectorAll("a").forEach((link) => {
    //       link.addEventListener("mouseenter", () => {
    //         cursor.classList.remove("active");
    //       });
    //       link.addEventListener("mouseleave", () => {
    //         cursor.classList.add("active");
    //       });
    //     });
    //   });
    // },
    rtlToggle: function () {

      $(document).ready(function () {
        // Retrieve the saved direction from localStorage
        const savedDir = localStorage.getItem("pageDirection") || "ltr"; // Default to "ltr"
        $("body").attr("dir", savedDir);

        // Update button visibility based on saved direction
        if (savedDir === "rtl") {
          $(".rtl").removeClass("show");
          $(".ltr").addClass("show");
        } else {
          $(".rtl").addClass("show");
          $(".ltr").removeClass("show");
        }

        // Toggle direction and save state on button click
        $(".rtl-ltr-switcher-btn").on("click", function () {
          const currentDir = $("body").attr("dir");
          const newDir = currentDir === "rtl" ? "ltr" : "rtl";

          // Update body direction
          $("body").attr("dir", newDir);

          // Toggle button visibility
          $(".rtl").toggleClass("show");
          $(".ltr").toggleClass("show");

          // Save the new direction in localStorage
          localStorage.setItem("pageDirection", newDir);
        });
      });

    },
    projectBlockActive: function () {
      const projectBlocks = document.querySelectorAll('.project-block-four');

      projectBlocks.forEach(block => {
        block.addEventListener('click', function () {
          // Remove active from all blocks
          projectBlocks.forEach(b => b.classList.remove('active'));

          // Add active to the clicked block
          this.classList.add('active');
        });
      });
    },
    regionAreaActive: function () {
      const regionItems = document.querySelectorAll('.region-area ul li');
      const destLists = document.querySelectorAll('.destlist');

      regionItems.forEach(item => {
        item.addEventListener('click', () => {
          const region = item.getAttribute('data-region');

          // Remove active from all items and add to clicked
          regionItems.forEach(li => li.classList.remove('active'));
          item.classList.add('active');

          // Smoothly switch destination lists
          destLists.forEach(list => {
            if (list.getAttribute('data-region') === region) {
              list.classList.add('active'); // ✅ show selected list
            } else {
              list.classList.remove('active'); // hide others
            }
          });
        });
      });
    },
    itineraryAccordion: function () {
      const items = document.querySelectorAll('.itinerary-item');
      const expandAllToggle = document.getElementById('expandAll');
      const expandAllToggle2 = document.getElementById('expandAll2');

      // Handle individual accordion toggles
      items.forEach(item => {
        const title = item.querySelector('.itinerary-title');
        if (title) {
          title.addEventListener('click', () => {
            item.classList.toggle('active');
          });
        }
      });

      // Helper function for expanding/collapsing all
      const toggleAll = (checked) => {
        items.forEach(item => {
          if (checked) {
            item.classList.add('active');
          } else {
            item.classList.remove('active');
          }
        });
      };

      // Handle "Expand all" toggles
      if (expandAllToggle) {
        expandAllToggle.addEventListener('change', e => {
          toggleAll(e.target.checked);
        });
      }

      if (expandAllToggle2) {
        expandAllToggle2.addEventListener('change', e => {
          toggleAll(e.target.checked);
        });
      }
    },
    priceRangeSlider: function () {
      const minRange = document.getElementById('minRange');
      const maxRange = document.getElementById('maxRange');
      const range = document.getElementById('range');
      const minPrice = document.getElementById('minPrice');
      const maxPrice = document.getElementById('maxPrice');

      if (!minRange || !maxRange || !range || !minPrice || !maxPrice) return;

      const updateRange = () => {
        const minValue = parseInt(minRange.value);
        const maxValue = parseInt(maxRange.value);

        // Update highlighted range
        range.style.left = `${minValue}%`;
        range.style.right = `${100 - maxValue}%`;

        // Map 0–100 → 0–5000
        const minPriceValue = Math.round((minValue / 100) * 5000);
        const maxPriceValue = Math.round((maxValue / 100) * 5000);

        // Update displayed prices
        minPrice.textContent = `$${minPriceValue}`;
        maxPrice.textContent = `$${maxPriceValue}`;
      };

      // Keep a minimum gap between sliders
      minRange.addEventListener('input', () => {
        if (parseInt(minRange.value) > parseInt(maxRange.value) - 10) {
          minRange.value = maxRange.value - 10;
        }
        updateRange();
      });

      maxRange.addEventListener('input', () => {
        if (parseInt(maxRange.value) < parseInt(minRange.value) + 10) {
          maxRange.value = parseInt(minRange.value) + 10;
        }
        updateRange();
      });

      // Initialize
      updateRange();
    },
    // mesonary tab
    mesonaryTab: function () {
      $(window).on("load", function () {
        var isotope = $(".main-isotop");

        if (isotope.length) {
          var iso = new Isotope(".filter", {
            itemSelector: ".element-item",
            layoutMode: "fitRows",
            fitRows: {
              equalheight: true,
            },
          });

          // filter functions
          var filterFns = {
            // show if name ends with -ium
            ium: function (itemElem) {
              var name = itemElem.querySelector(".name").textContent;
              return name.match(/ium$/);
            },
          };

          // bind filter button click
          var filtersElem = document.querySelector(".filters-button-group");
          filtersElem.addEventListener("click", function (event) {
            // only work with buttons
            if (!matchesSelector(event.target, "button")) {
              return;
            }
            var filterValue = event.target.getAttribute("data-filter");
            // use matching filter function
            filterValue = filterFns[filterValue] || filterValue;
            iso.arrange({ filter: filterValue });
          });

          // change is-checked class on buttons
          var buttonGroups = document.querySelectorAll(".button-group");
          for (var i = 0, len = buttonGroups.length; i < len; i++) {
            var buttonGroup = buttonGroups[i];
            radioButtonGroup(buttonGroup);
          }
          function radioButtonGroup(buttonGroup) {
            buttonGroup.addEventListener("click", function (event) {
              // only work with buttons
              if (!matchesSelector(event.target, "button")) {
                return;
              }
              buttonGroup
                .querySelector(".is-checked")
                .classList.remove("is-checked");
              event.target.classList.add("is-checked");
            });
          }
        }

        if ($(".grid-masonary").length) {
          // image loaded portfolio init
          $(".grid-masonary").imagesLoaded(function () {
            $(".portfolio-filter").on("click", "button", function () {
              var filterValue = $(this).attr("data-filter");
              $grid.isotope({
                filter: filterValue,
              });
            });
            var $grid = $(".grid-masonary").isotope({
              itemSelector: ".grid-item-p",
              percentPosition: true,
              masonry: {
                columnWidth: ".grid-item-p",
              },
            });
          });
        }

        // portfolio Filter
        $(".portfolio-filter button").on("click", function (event) {
          $(this).siblings(".is-checked").removeClass("is-checked");
          $(this).addClass("is-checked");
          event.preventDefault();
        });
      });
    },

  }

  rtsJs.m();
  $(document).ready(function () {
    $('#ce-toggle').change(function () {
      const isChecked = $(this).is(':checked');

      // Toggle active class based on checked state
      if (isChecked) {
        $('.plan-toggle-wrap').removeClass('active');
        $('#monthly').show();
        $('#yearly').hide();
      } else {
        $('.plan-toggle-wrap').addClass('active');
        $('#monthly').hide();
        $('#yearly').show();
      }
    });

    // Optional: Set initial state on page load
    $('#ce-toggle').trigger('change');
  });
  /* magnificPopup img view */
  $('.gallery-image').magnificPopup({
    type: 'image',
    gallery: {
      enabled: true
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bg-src]').forEach(function (el) {
      const bg = el.getAttribute('data-bg-src');
      if (bg) {
        el.style.backgroundImage = `url(${bg})`;
        el.style.backgroundSize = 'cover';        // Optional
        el.style.backgroundPosition = 'center';   // Optional
        el.style.backgroundRepeat = 'no-repeat';  // Optional
      }
    });
  });

})(jQuery, window)  