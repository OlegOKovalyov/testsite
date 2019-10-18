/*jslint browser: true, white: true, this: true, long: true */
/*global console,jQuery,megamenu,window,navigator*/

/*! Max Mega Menu jQuery Plugin */
(function ( $ ) {
    "use strict";

    $.maxmegamenu = function(menu, options) {
        var plugin = this;
        var $menu = $(menu);
        var $toggle_bar = $menu.siblings(".mega-menu-toggle");
        var html_body_class_timeout;

        var defaults = {
            event: $menu.attr("data-event"),
            effect: $menu.attr("data-effect"),
            effect_speed: parseInt($menu.attr("data-effect-speed")),
            effect_mobile: $menu.attr("data-effect-mobile"),
            effect_speed_mobile: parseInt($menu.attr("data-effect-speed-mobile")),
            panel_width: $menu.attr("data-panel-width"),
            panel_inner_width: $menu.attr("data-panel-inner-width"),
            mobile_force_width: $menu.attr("data-mobile-force-width"),
            mobile_overlay: $menu.attr("data-mobile-overlay"),
            second_click: $menu.attr("data-second-click"),
            vertical_behaviour: $menu.attr("data-vertical-behaviour"),
            document_click: $menu.attr("data-document-click"),
            breakpoint: $menu.attr("data-breakpoint"),
            unbind_events: $menu.attr("data-unbind")
        };

        plugin.settings = {};

        var items_with_submenus = $("li.mega-menu-megamenu.mega-menu-item-has-children," +
                                    "li.mega-menu-flyout.mega-menu-item-has-children," +
                                    "li.mega-menu-tabbed > ul.mega-sub-menu > li.mega-menu-item-has-children," +
                                    "li.mega-menu-flyout li.mega-menu-item-has-children", menu);



        plugin.addAnimatingClass = function(element) {
            if (plugin.settings.effect === "disabled") {
                return;
            }

            $(".mega-animating").removeClass("mega-animating");

            var timeout = plugin.settings.effect_speed + parseInt(megamenu.timeout, 10);

            element.addClass("mega-animating");

            setTimeout(function() {
               element.removeClass("mega-animating");
            }, timeout );
        };

        plugin.hideAllPanels = function() {
            $(".mega-toggle-on > a.mega-menu-link", $menu).each(function() {
                plugin.hidePanel($(this), false);
            });
        };

        plugin.hideSiblingPanels = function(anchor, immediate) {
            anchor.parent().parent().find(".mega-toggle-on").children("a.mega-menu-link").each(function() { // all open children of open siblings
                plugin.hidePanel($(this), immediate);
            });
        };

        plugin.isDesktopView = function() {
            return Math.max(window.outerWidth, $(window).width()) > plugin.settings.breakpoint; // account for scrollbars
        };

        plugin.isMobileView = function() {
            return !plugin.isDesktopView();
        };

        plugin.showPanel = function(anchor) {
            anchor.parent().triggerHandler("before_open_panel");

            anchor.attr("aria-expanded", "true");

            $(".mega-animating").removeClass("mega-animating");

            if (plugin.isMobileView() && anchor.parent().hasClass("mega-hide-sub-menu-on-mobile")) {
                return;
            }

            if (plugin.isDesktopView() && ( $menu.hasClass("mega-menu-horizontal") || $menu.hasClass("mega-menu-vertical") ) && !anchor.parent().hasClass("mega-collapse-children")) {
                plugin.hideSiblingPanels(anchor, true);
            }

            if ((plugin.isMobileView() && $menu.hasClass("mega-keyboard-navigation")) || plugin.settings.vertical_behaviour === "accordion") {
                plugin.hideSiblingPanels(anchor, false);
            }

            plugin.calculateDynamicSubmenuWidths(anchor);

            // apply jQuery transition (only if the effect is set to "slide", other transitions are CSS based)
            if ( anchor.parent().hasClass("mega-collapse-children") || plugin.settings.effect === "slide" || 
                ( plugin.isMobileView() && ( plugin.settings.effect_mobile === "slide" || plugin.settings.effect_mobile === "slide_left" || plugin.settings.effect_mobile === "slide_right" ) )
               ) {
                var speed = plugin.isMobileView() ? plugin.settings.effect_speed_mobile : plugin.settings.effect_speed;

                anchor.siblings(".mega-sub-menu").css("display", "none").animate({"height":"show", "paddingTop":"show", "paddingBottom":"show", "minHeight":"show"}, speed, function() {
                    $(this).css("display", "");
                });
            }

            anchor.parent().addClass("mega-toggle-on").triggerHandler("open_panel");
        };
        
        plugin.hidePanel = function(anchor, immediate) {
            anchor.parent().triggerHandler("before_close_panel");

            anchor.attr("aria-expanded", "false");

            if ( anchor.parent().hasClass("mega-collapse-children") || ( ! immediate && plugin.settings.effect === "slide" ) || 
                ( plugin.isMobileView() && ( plugin.settings.effect_mobile === "slide" || plugin.settings.effect_mobile === "slide_left" || plugin.settings.effect_mobile === "slide_right" ) )
               ) {
                var speed = plugin.isMobileView() ? plugin.settings.effect_speed_mobile : plugin.settings.effect_speed;

                anchor.siblings(".mega-sub-menu").animate({"height":"hide", "paddingTop":"hide", "paddingBottom":"hide", "minHeight":"hide"}, speed, function() {
                    anchor.siblings(".mega-sub-menu").css("display", "");
                    anchor.parent().removeClass("mega-toggle-on").triggerHandler("close_panel");
                });

                return;
            }

            if (immediate) {
                anchor.siblings(".mega-sub-menu").css("display", "none").delay(plugin.settings.effect_speed).queue(function(){
                    $(this).css("display", "").dequeue();
                });
            }

            // pause video widget videos
            anchor.siblings(".mega-sub-menu").find(".widget_media_video video").each(function() {
                this.player.pause();
            });

            anchor.parent().removeClass("mega-toggle-on").triggerHandler("close_panel");
            plugin.addAnimatingClass(anchor.parent());
        };

        plugin.calculateDynamicSubmenuWidths = function(anchor) {
            // apply dynamic width and sub menu position (only to top level mega menus)
            if (anchor.parent().hasClass("mega-menu-megamenu") && anchor.parent().parent().hasClass("max-mega-menu") && plugin.settings.panel_width && $(plugin.settings.panel_width).length > 0) {
                if (plugin.isDesktopView()) {
                    var submenu_offset = $menu.offset();
                    var target_offset = $(plugin.settings.panel_width).offset();

                    anchor.siblings(".mega-sub-menu").css({
                        width: $(plugin.settings.panel_width).outerWidth(),
                        left: (target_offset.left - submenu_offset.left) + "px"
                    });
                } else {
                    anchor.siblings(".mega-sub-menu").css({
                        width: "",
                        left: ""
                    });
                }
            }

            // apply inner width to sub menu by adding padding to the left and right of the mega menu
            if (anchor.parent().hasClass("mega-menu-megamenu") && anchor.parent().parent().hasClass("max-mega-menu") && plugin.settings.panel_inner_width && $(plugin.settings.panel_inner_width).length > 0) {
                var target_width = 0;

                if ($(plugin.settings.panel_inner_width).length) {
                    // jQuery selector
                    target_width = parseInt($(plugin.settings.panel_inner_width).width(), 10);
                } else {
                    // we"re using a pixel width
                    target_width = parseInt(plugin.settings.panel_inner_width, 10);
                }

                var submenu_width = parseInt(anchor.siblings(".mega-sub-menu").innerWidth(), 10);

                if (plugin.isDesktopView() && target_width > 0 && target_width < submenu_width) {
                    anchor.siblings(".mega-sub-menu").css({
                        "paddingLeft": (submenu_width - target_width) / 2 + "px",
                        "paddingRight": (submenu_width - target_width) / 2 + "px"
                    });
                } else {
                    anchor.siblings(".mega-sub-menu").css({
                        "paddingLeft": "",
                        "paddingRight": ""
                    });
                }
            }
        };

        var bindClickEvents = function() {
            var dragging = false;

            $(document).on({
                "touchmove": function(e) { dragging = true; },
                "touchstart": function(e) { dragging = false; }
            });

            $(document).on("click touchend", function(e) { // hide menu when clicked away from
                if (!dragging && plugin.settings.document_click === "collapse" && ! $(e.target).closest(".max-mega-menu li").length && ! $(e.target).closest(".mega-menu-toggle").length ) {
                    plugin.hideAllPanels();
                    plugin.hideMobileMenu();
                }
                dragging = false;
            });

            var collapse_children_parents = $("li.mega-menu-megamenu li.mega-menu-item-has-children.mega-collapse-children > a.mega-menu-link");
            var clickable_parents = $("> a.mega-menu-link", items_with_submenus).add(collapse_children_parents);

            clickable_parents.on("touchend.megamenu", function(e) {
                plugin.unbindHoverEvents();
                plugin.unbindHoverIntentEvents();
            });

            clickable_parents.on("click.megamenu", function(e) {
                if (plugin.isDesktopView() && $(this).parent().hasClass("mega-toggle-on") && $(this).parent().parent().parent().hasClass("mega-menu-tabbed") ) {
                    if (plugin.settings.second_click === "go") {
                        return;
                    } else {
                        e.preventDefault();
                        return;
                    }
                }
                if (dragging) {
                    return;
                }
                if (plugin.isMobileView() && $(this).parent().hasClass("mega-hide-sub-menu-on-mobile")) {
                    return; // allow all clicks on parent items when sub menu is hidden on mobile
                }
                if ((plugin.settings.second_click === "go" || $(this).parent().hasClass("mega-click-click-go")) && $(this).attr("href") !== undefined) { // check for second click
                    if (!$(this).parent().hasClass("mega-toggle-on")) {
                        e.preventDefault();
                        plugin.showPanel($(this));
                    }
                } else {
                    e.preventDefault();

                    if ($(this).parent().hasClass("mega-toggle-on")) {
                        plugin.hidePanel($(this), false);
                    } else {
                        plugin.showPanel($(this));
                    }
                }
            });
        };

        var bindHoverEvents = function() {
            items_with_submenus.on({
                "mouseenter.megamenu" : function() {
                    plugin.unbindClickEvents();
                    if (! $(this).hasClass("mega-toggle-on")) {
                        plugin.showPanel($(this).children("a.mega-menu-link"));
                    }
                },
                "mouseleave.megamenu" : function() {
                    if ($(this).hasClass("mega-toggle-on") && ! $(this).hasClass("mega-disable-collapse") && ! $(this).parent().parent().hasClass("mega-menu-tabbed")) {
                        plugin.hidePanel($(this).children("a.mega-menu-link"), false);
                    }
                }
            });
        };

        var bindHoverIntentEvents = function() {
            items_with_submenus.hoverIntent({
                over: function () {
                    plugin.unbindClickEvents();
                    if (! $(this).hasClass("mega-toggle-on")) {
                        plugin.showPanel($(this).children("a.mega-menu-link"));
                    }
                },
                out: function () {
                    if ($(this).hasClass("mega-toggle-on") && ! $(this).hasClass("mega-disable-collapse") && ! $(this).parent().parent().hasClass("mega-menu-tabbed")) {
                        plugin.hidePanel($(this).children("a.mega-menu-link"), false);
                    }
                },
                timeout: megamenu.timeout,
                interval: megamenu.interval
            });
        };

        var bindKeyboardEvents = function() {
            var tab_key = 9;
            var escape_key = 27;
            var enter_key = 13;
            var left_arrow_key = 37;
            var right_arrow_key = 39;
            var space_key = 32;

            $menu.parent().on("keyup.megamenu", function(e) {
                var keyCode = e.keyCode || e.which;

                if (keyCode === tab_key) {
                    $menu.parent().addClass("mega-keyboard-navigation");
                }
            });

            $menu.parent().on("keydown.megamenu", function(e) {
                var keyCode = e.keyCode || e.which;
                var active_link = $(e.target);

                if ( keyCode === space_key && active_link.is(".mega-menu-link") && $menu.parent().hasClass("mega-keyboard-navigation") ) {
                    e.preventDefault();

                    // pressing space on a parent item will always toggle the sub menu
                    if ( active_link.parent().is(items_with_submenus) ) {
                        if ( active_link.parent().hasClass("mega-toggle-on") && ! active_link.parent().parent().parent().hasClass("mega-menu-tabbed") ) {
                            plugin.hidePanel(active_link);
                        } else {
                            plugin.showPanel(active_link);
                        }
                    }
                }
            });

            $menu.parent().on("keyup.megamenu", function(e) {
                var keyCode = e.keyCode || e.which;
                var active_link = $(e.target);
                
                if ( keyCode === tab_key && $menu.parent().hasClass("mega-keyboard-navigation") ) {
                    if ( active_link.parent().is(items_with_submenus) && active_link.is("[href]") !== false ) {
                        plugin.showPanel(active_link);
                    } else {
                        if ( ! active_link.parent().parent().parent().hasClass("mega-menu-tabbed") ) {
                            plugin.hideSiblingPanels(active_link);
                        }
                    }
                }

                if ( keyCode === escape_key && $menu.parent().hasClass("mega-keyboard-navigation") ) {
                    var submenu_open = $("> .mega-toggle-on", $menu).length !== 0;

                    $("> .mega-toggle-on > a.mega-menu-link", $menu).focus();

                    plugin.hideAllPanels();

                    if ( plugin.isMobileView() && ! submenu_open ) {
                        plugin.hideMobileMenu();
                        $(".mega-menu-toggle-block, button.mega-toggle-animated", $toggle_bar).first().focus();
                    }
                }

                if ( keyCode === enter_key && $menu.parent().hasClass("mega-keyboard-navigation") ) {
                    if ( active_link.hasClass("mega-menu-toggle-block") ) {
                        if ( $toggle_bar.hasClass("mega-menu-open") ) {
                            plugin.hideMobileMenu();
                        } else {
                            plugin.showMobileMenu();
                        }
                    }

                    // pressing enter on a parent item without a link will toggle the sub menu
                    if ( active_link.parent().is(items_with_submenus) && active_link.is("[href]") === false ) {
                        if ( active_link.parent().hasClass("mega-toggle-on") && ! active_link.parent().parent().parent().hasClass("mega-menu-tabbed") ) {
                            plugin.hidePanel(active_link);
                        } else {
                            plugin.showPanel(active_link);
                        }
                    }
                }

                if ( keyCode === right_arrow_key && plugin.isDesktopView() && $menu.parent().hasClass("mega-keyboard-navigation") && $menu.hasClass("mega-menu-horizontal") ) {
                    var next_top_level_item = $("> .mega-toggle-on", $menu).nextAll("li.mega-menu-item:visible").find("> a.mega-menu-link, .mega-search input[type=text]").first();

                    if (next_top_level_item.length === 0) {
                        next_top_level_item = $(":focus", $menu).parent().nextAll("li.mega-menu-item:visible").find("> a.mega-menu-link, .mega-search input[type=text]").first();
                    }

                    next_top_level_item.focus();

                    if ( next_top_level_item.parent().is(items_with_submenus) && next_top_level_item.is("[href]") !== false ) {
                        plugin.showPanel(next_top_level_item);
                    } else {
                        plugin.hideSiblingPanels(next_top_level_item);
                    }
                }

                if ( keyCode === left_arrow_key && plugin.isDesktopView() && $menu.parent().hasClass("mega-keyboard-navigation") && $menu.hasClass("mega-menu-horizontal") ) {
                    var prev_top_level_item = $("> .mega-toggle-on", $menu).prevAll("li.mega-menu-item:visible").find("> a.mega-menu-link, .mega-search input[type=text]").last();

                    if (prev_top_level_item.length === 0) {
                        prev_top_level_item = $(":focus", $menu).parent().prevAll("li.mega-menu-item:visible").find("> a.mega-menu-link, .mega-search input[type=text]").last();
                    }

                    prev_top_level_item.focus();

                    if ( prev_top_level_item.parent().is(items_with_submenus) && prev_top_level_item.is("[href]") !== false  ) {
                        plugin.showPanel(prev_top_level_item);
                    } else {
                        plugin.hideSiblingPanels(prev_top_level_item);
                    }
                }

            });

            $menu.parent().on("focusout.megamenu", function(e) {
                if ( $menu.parent().hasClass("mega-keyboard-navigation") ) {
                    setTimeout(function() {
                        var menu_has_focus = $menu.parent().find(":focus").length > 0;
                        if (! menu_has_focus) {
                            $menu.parent().removeClass("mega-keyboard-navigation");
                            plugin.hideAllPanels();
                            plugin.hideMobileMenu();
                        }
                    }, 10);
                }
            });
        };

        plugin.unbindAllEvents = function() {
            $("ul.mega-sub-menu, li.mega-menu-item, li.mega-menu-row, li.mega-menu-column, a.mega-menu-link, span.mega-indicator", menu).off().unbind();
        };

        plugin.unbindClickEvents = function() {
            $("> a.mega-menu-link", items_with_submenus).off("click.megamenu touchend.megamenu");
        };

        plugin.unbindHoverEvents = function() {
            items_with_submenus.unbind("mouseenter.megamenu mouseleave.megamenu");
        };

        plugin.unbindHoverIntentEvents = function() {
            items_with_submenus.unbind("mouseenter mouseleave").removeProp("hoverIntent_t").removeProp("hoverIntent_s"); // hoverintent does not allow namespaced events
        };

        plugin.unbindKeyboardEvents = function() {
            $menu.parent().off("keyup.megamenu keydown.megamenu focusout.megamenu");
        };

        plugin.unbindMegaMenuEvents = function() {
            if (plugin.settings.event === "hover_intent") {
                plugin.unbindHoverIntentEvents();
            }

            if (plugin.settings.event === "hover") {
                plugin.unbindHoverEvents();
            }

            plugin.unbindClickEvents();
            plugin.unbindKeyboardEvents();
        };


        plugin.bindMegaMenuEvents = function() {
            if (plugin.isDesktopView() && plugin.settings.event === "hover_intent") {
                bindHoverIntentEvents();
            }

            if (plugin.isDesktopView() && plugin.settings.event === "hover") {
                bindHoverEvents();
            }

            bindClickEvents(); // always bind click events for touch screen devices
            bindKeyboardEvents();
        };

        plugin.monitorView = function() {
            if (plugin.isDesktopView()) {
                $menu.data("view", "desktop");
            } else {
                $menu.data("view", "mobile");
                plugin.switchToMobile();
            }

            plugin.checkWidth();

            $(window).resize(function() {
                plugin.checkWidth();
            });
        };

        plugin.checkWidth = function() {
            if ( plugin.isMobileView() && $menu.data("view") === "desktop" ) {
                $menu.data("view", "mobile");
                plugin.switchToMobile();
            }

            if ( plugin.isDesktopView() && $menu.data("view") === "mobile" ) {
                $menu.data("view", "desktop");
                plugin.switchToDesktop();
            }

            plugin.calculateDynamicSubmenuWidths($("> li.mega-menu-megamenu > a.mega-menu-link", $menu));
        };

        plugin.reverseRightAlignedItems = function() {
            if ( ! $("body").hasClass("rtl") ) {
                $menu.append($menu.children("li.mega-item-align-right").get().reverse());
            }
        };

        plugin.addClearClassesToMobileItems = function() {
            $(".mega-menu-row", $menu).each(function() {
                $("> .mega-sub-menu > .mega-menu-column:not(.mega-hide-on-mobile)", $(this)).filter(":even").addClass("mega-menu-clear"); // :even is 0 based
            });
        };

        plugin.switchToMobile = function() {
            plugin.unbindMegaMenuEvents();
            plugin.bindMegaMenuEvents();
            plugin.reverseRightAlignedItems();
            plugin.addClearClassesToMobileItems();
            plugin.hideAllPanels();
        };

        plugin.switchToDesktop = function() {
            plugin.unbindMegaMenuEvents();
            plugin.bindMegaMenuEvents();
            plugin.reverseRightAlignedItems();
            plugin.hideAllPanels();

            $menu.css({
                width: "",
                left: "",
                display: ""
            });

            $toggle_bar.removeClass("mega-menu-open");
        };

        plugin.initToggleBar = function() {
            // mobile menu
            $toggle_bar.on("click", function(e) {
                if ( $(e.target).is(".mega-menu-toggle, .mega-menu-toggle-block, .mega-menu-toggle-animated-block, .mega-menu-toggle-animated-block *, .mega-toggle-blocks-left, .mega-toggle-blocks-center, .mega-toggle-blocks-right, .mega-toggle-label, .mega-toggle-label span") ) {                    if ($(this).hasClass("mega-menu-open")) {
                        plugin.hideMobileMenu();
                    } else {
                        plugin.showMobileMenu();
                    }
                }
            });
        };

        plugin.hideMobileMenu = function() {
            if ( ! $toggle_bar.is(":visible")) {
                return;
            }

            html_body_class_timeout = setTimeout(function() {
                $("body").removeClass($menu.attr("id") + "-mobile-open");
                $("html").removeClass($menu.attr("id") + "-off-canvas-open");
            }, plugin.settings.effect_speed_mobile);

            $(".mega-toggle-label, .mega-toggle-animated", $toggle_bar).attr("aria-expanded", "false");

            if (plugin.settings.effect_mobile === "slide") {
                $menu.animate({"height":"hide"}, plugin.settings.effect_speed_mobile, function() {
                    $menu.css({
                        width: "",
                        left: "",
                        display: ""
                    });
                });
            }

            $toggle_bar.removeClass("mega-menu-open");
        };

        plugin.showMobileMenu = function() {
            if ( ! $toggle_bar.is(":visible")) {
                return;
            }

            clearTimeout(html_body_class_timeout);

            $("body").addClass($menu.attr("id") + "-mobile-open");

            if ( plugin.settings.effect_mobile === "slide_left" || plugin.settings.effect_mobile === "slide_right" ) {
                $("html").addClass($menu.attr("id") + "-off-canvas-open");
            }

            $(".mega-toggle-label, .mega-toggle-animated", $toggle_bar).attr("aria-expanded", "true");

            plugin.toggleBarForceWidth();

            if (plugin.settings.effect_mobile === "slide") {
                $menu.animate({"height":"show"}, plugin.settings.effect_speed_mobile);
            }

            $toggle_bar.addClass("mega-menu-open");
        };

        plugin.toggleBarForceWidth = function() {

            if ($(plugin.settings.mobile_force_width).length && ( plugin.settings.effect_mobile == 'slide' || plugin.settings.effect_mobile == 'disabled' ) ) {
                var submenu_offset = $toggle_bar.offset();
                var target_offset = $(plugin.settings.mobile_force_width).offset();

                $menu.css({
                    width: $(plugin.settings.mobile_force_width).outerWidth(),
                    left: (target_offset.left - submenu_offset.left) + "px"
                });
            }
        };

        plugin.init = function() {
            $menu.triggerHandler("before_mega_menu_init");
            plugin.settings = $.extend({}, defaults, options);
            $menu.removeClass("mega-no-js");

            plugin.initToggleBar();
            
            if (plugin.settings.unbind_events === "true") {
                plugin.unbindAllEvents();
            }

            $("span.mega-indicator", $menu).on("click.megamenu", function(e) {
                e.preventDefault();
                e.stopPropagation();

                if ( $(this).parent().parent().hasClass("mega-toggle-on") ) {
                    if ( ! $(this).parent().parent().parent().parent().hasClass("mega-menu-tabbed") || plugin.isMobileView() ) {
                        plugin.hidePanel($(this).parent(), false);
                    }
                } else {
                    plugin.showPanel($(this).parent(), false);
                }
            });

            $(window).on("load", function() {
                plugin.calculateDynamicSubmenuWidths($("> li.mega-menu-megamenu > a.mega-menu-link", $menu));
            });

            plugin.bindMegaMenuEvents();
            plugin.monitorView();
            $menu.triggerHandler("after_mega_menu_init");
        };

        plugin.init();
    };

    $.fn.maxmegamenu = function(options) {
        return this.each(function() {
            if (undefined === $(this).data("maxmegamenu")) {
                var plugin = new $.maxmegamenu(this, options);
                $(this).data("maxmegamenu", plugin);
            }
        });
    };

    $(function() {
        $(".max-mega-menu").maxmegamenu();
    });
}( jQuery ));
;jQuery("body.mega-menu-top #mega-menu-top").on("after_mega_menu_init", function() {
    jQuery(this).parent().parent().removeClass("main-navigation");
});
;!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++){if(d=i[c],!d.getAttribute("data-secret"))f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f);if(g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d)if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(~~g<200)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);
;/**
 * Owl Carousel v2.3.4
 * Copyright 2013-2018 David Deutsch
 * Licensed under: SEE LICENSE IN https://github.com/OwlCarousel2/OwlCarousel2/blob/master/LICENSE
 */
!function(a,b,c,d){function e(b,c){this.settings=null,this.options=a.extend({},e.Defaults,c),this.$element=a(b),this._handlers={},this._plugins={},this._supress={},this._current=null,this._speed=null,this._coordinates=[],this._breakpoint=null,this._width=null,this._items=[],this._clones=[],this._mergers=[],this._widths=[],this._invalidated={},this._pipe=[],this._drag={time:null,target:null,pointer:null,stage:{start:null,current:null},direction:null},this._states={current:{},tags:{initializing:["busy"],animating:["busy"],dragging:["interacting"]}},a.each(["onResize","onThrottledResize"],a.proxy(function(b,c){this._handlers[c]=a.proxy(this[c],this)},this)),a.each(e.Plugins,a.proxy(function(a,b){this._plugins[a.charAt(0).toLowerCase()+a.slice(1)]=new b(this)},this)),a.each(e.Workers,a.proxy(function(b,c){this._pipe.push({filter:c.filter,run:a.proxy(c.run,this)})},this)),this.setup(),this.initialize()}e.Defaults={items:3,loop:!1,center:!1,rewind:!1,checkVisibility:!0,mouseDrag:!0,touchDrag:!0,pullDrag:!0,freeDrag:!1,margin:0,stagePadding:0,merge:!1,mergeFit:!0,autoWidth:!1,startPosition:0,rtl:!1,smartSpeed:250,fluidSpeed:!1,dragEndSpeed:!1,responsive:{},responsiveRefreshRate:200,responsiveBaseElement:b,fallbackEasing:"swing",slideTransition:"",info:!1,nestedItemSelector:!1,itemElement:"div",stageElement:"div",refreshClass:"owl-refresh",loadedClass:"owl-loaded",loadingClass:"owl-loading",rtlClass:"owl-rtl",responsiveClass:"owl-responsive",dragClass:"owl-drag",itemClass:"owl-item",stageClass:"owl-stage",stageOuterClass:"owl-stage-outer",grabClass:"owl-grab"},e.Width={Default:"default",Inner:"inner",Outer:"outer"},e.Type={Event:"event",State:"state"},e.Plugins={},e.Workers=[{filter:["width","settings"],run:function(){this._width=this.$element.width()}},{filter:["width","items","settings"],run:function(a){a.current=this._items&&this._items[this.relative(this._current)]}},{filter:["items","settings"],run:function(){this.$stage.children(".cloned").remove()}},{filter:["width","items","settings"],run:function(a){var b=this.settings.margin||"",c=!this.settings.autoWidth,d=this.settings.rtl,e={width:"auto","margin-left":d?b:"","margin-right":d?"":b};!c&&this.$stage.children().css(e),a.css=e}},{filter:["width","items","settings"],run:function(a){var b=(this.width()/this.settings.items).toFixed(3)-this.settings.margin,c=null,d=this._items.length,e=!this.settings.autoWidth,f=[];for(a.items={merge:!1,width:b};d--;)c=this._mergers[d],c=this.settings.mergeFit&&Math.min(c,this.settings.items)||c,a.items.merge=c>1||a.items.merge,f[d]=e?b*c:this._items[d].width();this._widths=f}},{filter:["items","settings"],run:function(){var b=[],c=this._items,d=this.settings,e=Math.max(2*d.items,4),f=2*Math.ceil(c.length/2),g=d.loop&&c.length?d.rewind?e:Math.max(e,f):0,h="",i="";for(g/=2;g>0;)b.push(this.normalize(b.length/2,!0)),h+=c[b[b.length-1]][0].outerHTML,b.push(this.normalize(c.length-1-(b.length-1)/2,!0)),i=c[b[b.length-1]][0].outerHTML+i,g-=1;this._clones=b,a(h).addClass("cloned").appendTo(this.$stage),a(i).addClass("cloned").prependTo(this.$stage)}},{filter:["width","items","settings"],run:function(){for(var a=this.settings.rtl?1:-1,b=this._clones.length+this._items.length,c=-1,d=0,e=0,f=[];++c<b;)d=f[c-1]||0,e=this._widths[this.relative(c)]+this.settings.margin,f.push(d+e*a);this._coordinates=f}},{filter:["width","items","settings"],run:function(){var a=this.settings.stagePadding,b=this._coordinates,c={width:Math.ceil(Math.abs(b[b.length-1]))+2*a,"padding-left":a||"","padding-right":a||""};this.$stage.css(c)}},{filter:["width","items","settings"],run:function(a){var b=this._coordinates.length,c=!this.settings.autoWidth,d=this.$stage.children();if(c&&a.items.merge)for(;b--;)a.css.width=this._widths[this.relative(b)],d.eq(b).css(a.css);else c&&(a.css.width=a.items.width,d.css(a.css))}},{filter:["items"],run:function(){this._coordinates.length<1&&this.$stage.removeAttr("style")}},{filter:["width","items","settings"],run:function(a){a.current=a.current?this.$stage.children().index(a.current):0,a.current=Math.max(this.minimum(),Math.min(this.maximum(),a.current)),this.reset(a.current)}},{filter:["position"],run:function(){this.animate(this.coordinates(this._current))}},{filter:["width","position","items","settings"],run:function(){var a,b,c,d,e=this.settings.rtl?1:-1,f=2*this.settings.stagePadding,g=this.coordinates(this.current())+f,h=g+this.width()*e,i=[];for(c=0,d=this._coordinates.length;c<d;c++)a=this._coordinates[c-1]||0,b=Math.abs(this._coordinates[c])+f*e,(this.op(a,"<=",g)&&this.op(a,">",h)||this.op(b,"<",g)&&this.op(b,">",h))&&i.push(c);this.$stage.children(".active").removeClass("active"),this.$stage.children(":eq("+i.join("), :eq(")+")").addClass("active"),this.$stage.children(".center").removeClass("center"),this.settings.center&&this.$stage.children().eq(this.current()).addClass("center")}}],e.prototype.initializeStage=function(){this.$stage=this.$element.find("."+this.settings.stageClass),this.$stage.length||(this.$element.addClass(this.options.loadingClass),this.$stage=a("<"+this.settings.stageElement+">",{class:this.settings.stageClass}).wrap(a("<div/>",{class:this.settings.stageOuterClass})),this.$element.append(this.$stage.parent()))},e.prototype.initializeItems=function(){var b=this.$element.find(".owl-item");if(b.length)return this._items=b.get().map(function(b){return a(b)}),this._mergers=this._items.map(function(){return 1}),void this.refresh();this.replace(this.$element.children().not(this.$stage.parent())),this.isVisible()?this.refresh():this.invalidate("width"),this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass)},e.prototype.initialize=function(){if(this.enter("initializing"),this.trigger("initialize"),this.$element.toggleClass(this.settings.rtlClass,this.settings.rtl),this.settings.autoWidth&&!this.is("pre-loading")){var a,b,c;a=this.$element.find("img"),b=this.settings.nestedItemSelector?"."+this.settings.nestedItemSelector:d,c=this.$element.children(b).width(),a.length&&c<=0&&this.preloadAutoWidthImages(a)}this.initializeStage(),this.initializeItems(),this.registerEventHandlers(),this.leave("initializing"),this.trigger("initialized")},e.prototype.isVisible=function(){return!this.settings.checkVisibility||this.$element.is(":visible")},e.prototype.setup=function(){var b=this.viewport(),c=this.options.responsive,d=-1,e=null;c?(a.each(c,function(a){a<=b&&a>d&&(d=Number(a))}),e=a.extend({},this.options,c[d]),"function"==typeof e.stagePadding&&(e.stagePadding=e.stagePadding()),delete e.responsive,e.responsiveClass&&this.$element.attr("class",this.$element.attr("class").replace(new RegExp("("+this.options.responsiveClass+"-)\\S+\\s","g"),"$1"+d))):e=a.extend({},this.options),this.trigger("change",{property:{name:"settings",value:e}}),this._breakpoint=d,this.settings=e,this.invalidate("settings"),this.trigger("changed",{property:{name:"settings",value:this.settings}})},e.prototype.optionsLogic=function(){this.settings.autoWidth&&(this.settings.stagePadding=!1,this.settings.merge=!1)},e.prototype.prepare=function(b){var c=this.trigger("prepare",{content:b});return c.data||(c.data=a("<"+this.settings.itemElement+"/>").addClass(this.options.itemClass).append(b)),this.trigger("prepared",{content:c.data}),c.data},e.prototype.update=function(){for(var b=0,c=this._pipe.length,d=a.proxy(function(a){return this[a]},this._invalidated),e={};b<c;)(this._invalidated.all||a.grep(this._pipe[b].filter,d).length>0)&&this._pipe[b].run(e),b++;this._invalidated={},!this.is("valid")&&this.enter("valid")},e.prototype.width=function(a){switch(a=a||e.Width.Default){case e.Width.Inner:case e.Width.Outer:return this._width;default:return this._width-2*this.settings.stagePadding+this.settings.margin}},e.prototype.refresh=function(){this.enter("refreshing"),this.trigger("refresh"),this.setup(),this.optionsLogic(),this.$element.addClass(this.options.refreshClass),this.update(),this.$element.removeClass(this.options.refreshClass),this.leave("refreshing"),this.trigger("refreshed")},e.prototype.onThrottledResize=function(){b.clearTimeout(this.resizeTimer),this.resizeTimer=b.setTimeout(this._handlers.onResize,this.settings.responsiveRefreshRate)},e.prototype.onResize=function(){return!!this._items.length&&(this._width!==this.$element.width()&&(!!this.isVisible()&&(this.enter("resizing"),this.trigger("resize").isDefaultPrevented()?(this.leave("resizing"),!1):(this.invalidate("width"),this.refresh(),this.leave("resizing"),void this.trigger("resized")))))},e.prototype.registerEventHandlers=function(){a.support.transition&&this.$stage.on(a.support.transition.end+".owl.core",a.proxy(this.onTransitionEnd,this)),!1!==this.settings.responsive&&this.on(b,"resize",this._handlers.onThrottledResize),this.settings.mouseDrag&&(this.$element.addClass(this.options.dragClass),this.$stage.on("mousedown.owl.core",a.proxy(this.onDragStart,this)),this.$stage.on("dragstart.owl.core selectstart.owl.core",function(){return!1})),this.settings.touchDrag&&(this.$stage.on("touchstart.owl.core",a.proxy(this.onDragStart,this)),this.$stage.on("touchcancel.owl.core",a.proxy(this.onDragEnd,this)))},e.prototype.onDragStart=function(b){var d=null;3!==b.which&&(a.support.transform?(d=this.$stage.css("transform").replace(/.*\(|\)| /g,"").split(","),d={x:d[16===d.length?12:4],y:d[16===d.length?13:5]}):(d=this.$stage.position(),d={x:this.settings.rtl?d.left+this.$stage.width()-this.width()+this.settings.margin:d.left,y:d.top}),this.is("animating")&&(a.support.transform?this.animate(d.x):this.$stage.stop(),this.invalidate("position")),this.$element.toggleClass(this.options.grabClass,"mousedown"===b.type),this.speed(0),this._drag.time=(new Date).getTime(),this._drag.target=a(b.target),this._drag.stage.start=d,this._drag.stage.current=d,this._drag.pointer=this.pointer(b),a(c).on("mouseup.owl.core touchend.owl.core",a.proxy(this.onDragEnd,this)),a(c).one("mousemove.owl.core touchmove.owl.core",a.proxy(function(b){var d=this.difference(this._drag.pointer,this.pointer(b));a(c).on("mousemove.owl.core touchmove.owl.core",a.proxy(this.onDragMove,this)),Math.abs(d.x)<Math.abs(d.y)&&this.is("valid")||(b.preventDefault(),this.enter("dragging"),this.trigger("drag"))},this)))},e.prototype.onDragMove=function(a){var b=null,c=null,d=null,e=this.difference(this._drag.pointer,this.pointer(a)),f=this.difference(this._drag.stage.start,e);this.is("dragging")&&(a.preventDefault(),this.settings.loop?(b=this.coordinates(this.minimum()),c=this.coordinates(this.maximum()+1)-b,f.x=((f.x-b)%c+c)%c+b):(b=this.settings.rtl?this.coordinates(this.maximum()):this.coordinates(this.minimum()),c=this.settings.rtl?this.coordinates(this.minimum()):this.coordinates(this.maximum()),d=this.settings.pullDrag?-1*e.x/5:0,f.x=Math.max(Math.min(f.x,b+d),c+d)),this._drag.stage.current=f,this.animate(f.x))},e.prototype.onDragEnd=function(b){var d=this.difference(this._drag.pointer,this.pointer(b)),e=this._drag.stage.current,f=d.x>0^this.settings.rtl?"left":"right";a(c).off(".owl.core"),this.$element.removeClass(this.options.grabClass),(0!==d.x&&this.is("dragging")||!this.is("valid"))&&(this.speed(this.settings.dragEndSpeed||this.settings.smartSpeed),this.current(this.closest(e.x,0!==d.x?f:this._drag.direction)),this.invalidate("position"),this.update(),this._drag.direction=f,(Math.abs(d.x)>3||(new Date).getTime()-this._drag.time>300)&&this._drag.target.one("click.owl.core",function(){return!1})),this.is("dragging")&&(this.leave("dragging"),this.trigger("dragged"))},e.prototype.closest=function(b,c){var e=-1,f=30,g=this.width(),h=this.coordinates();return this.settings.freeDrag||a.each(h,a.proxy(function(a,i){return"left"===c&&b>i-f&&b<i+f?e=a:"right"===c&&b>i-g-f&&b<i-g+f?e=a+1:this.op(b,"<",i)&&this.op(b,">",h[a+1]!==d?h[a+1]:i-g)&&(e="left"===c?a+1:a),-1===e},this)),this.settings.loop||(this.op(b,">",h[this.minimum()])?e=b=this.minimum():this.op(b,"<",h[this.maximum()])&&(e=b=this.maximum())),e},e.prototype.animate=function(b){var c=this.speed()>0;this.is("animating")&&this.onTransitionEnd(),c&&(this.enter("animating"),this.trigger("translate")),a.support.transform3d&&a.support.transition?this.$stage.css({transform:"translate3d("+b+"px,0px,0px)",transition:this.speed()/1e3+"s"+(this.settings.slideTransition?" "+this.settings.slideTransition:"")}):c?this.$stage.animate({left:b+"px"},this.speed(),this.settings.fallbackEasing,a.proxy(this.onTransitionEnd,this)):this.$stage.css({left:b+"px"})},e.prototype.is=function(a){return this._states.current[a]&&this._states.current[a]>0},e.prototype.current=function(a){if(a===d)return this._current;if(0===this._items.length)return d;if(a=this.normalize(a),this._current!==a){var b=this.trigger("change",{property:{name:"position",value:a}});b.data!==d&&(a=this.normalize(b.data)),this._current=a,this.invalidate("position"),this.trigger("changed",{property:{name:"position",value:this._current}})}return this._current},e.prototype.invalidate=function(b){return"string"===a.type(b)&&(this._invalidated[b]=!0,this.is("valid")&&this.leave("valid")),a.map(this._invalidated,function(a,b){return b})},e.prototype.reset=function(a){(a=this.normalize(a))!==d&&(this._speed=0,this._current=a,this.suppress(["translate","translated"]),this.animate(this.coordinates(a)),this.release(["translate","translated"]))},e.prototype.normalize=function(a,b){var c=this._items.length,e=b?0:this._clones.length;return!this.isNumeric(a)||c<1?a=d:(a<0||a>=c+e)&&(a=((a-e/2)%c+c)%c+e/2),a},e.prototype.relative=function(a){return a-=this._clones.length/2,this.normalize(a,!0)},e.prototype.maximum=function(a){var b,c,d,e=this.settings,f=this._coordinates.length;if(e.loop)f=this._clones.length/2+this._items.length-1;else if(e.autoWidth||e.merge){if(b=this._items.length)for(c=this._items[--b].width(),d=this.$element.width();b--&&!((c+=this._items[b].width()+this.settings.margin)>d););f=b+1}else f=e.center?this._items.length-1:this._items.length-e.items;return a&&(f-=this._clones.length/2),Math.max(f,0)},e.prototype.minimum=function(a){return a?0:this._clones.length/2},e.prototype.items=function(a){return a===d?this._items.slice():(a=this.normalize(a,!0),this._items[a])},e.prototype.mergers=function(a){return a===d?this._mergers.slice():(a=this.normalize(a,!0),this._mergers[a])},e.prototype.clones=function(b){var c=this._clones.length/2,e=c+this._items.length,f=function(a){return a%2==0?e+a/2:c-(a+1)/2};return b===d?a.map(this._clones,function(a,b){return f(b)}):a.map(this._clones,function(a,c){return a===b?f(c):null})},e.prototype.speed=function(a){return a!==d&&(this._speed=a),this._speed},e.prototype.coordinates=function(b){var c,e=1,f=b-1;return b===d?a.map(this._coordinates,a.proxy(function(a,b){return this.coordinates(b)},this)):(this.settings.center?(this.settings.rtl&&(e=-1,f=b+1),c=this._coordinates[b],c+=(this.width()-c+(this._coordinates[f]||0))/2*e):c=this._coordinates[f]||0,c=Math.ceil(c))},e.prototype.duration=function(a,b,c){return 0===c?0:Math.min(Math.max(Math.abs(b-a),1),6)*Math.abs(c||this.settings.smartSpeed)},e.prototype.to=function(a,b){var c=this.current(),d=null,e=a-this.relative(c),f=(e>0)-(e<0),g=this._items.length,h=this.minimum(),i=this.maximum();this.settings.loop?(!this.settings.rewind&&Math.abs(e)>g/2&&(e+=-1*f*g),a=c+e,(d=((a-h)%g+g)%g+h)!==a&&d-e<=i&&d-e>0&&(c=d-e,a=d,this.reset(c))):this.settings.rewind?(i+=1,a=(a%i+i)%i):a=Math.max(h,Math.min(i,a)),this.speed(this.duration(c,a,b)),this.current(a),this.isVisible()&&this.update()},e.prototype.next=function(a){a=a||!1,this.to(this.relative(this.current())+1,a)},e.prototype.prev=function(a){a=a||!1,this.to(this.relative(this.current())-1,a)},e.prototype.onTransitionEnd=function(a){if(a!==d&&(a.stopPropagation(),(a.target||a.srcElement||a.originalTarget)!==this.$stage.get(0)))return!1;this.leave("animating"),this.trigger("translated")},e.prototype.viewport=function(){var d;return this.options.responsiveBaseElement!==b?d=a(this.options.responsiveBaseElement).width():b.innerWidth?d=b.innerWidth:c.documentElement&&c.documentElement.clientWidth?d=c.documentElement.clientWidth:console.warn("Can not detect viewport width."),d},e.prototype.replace=function(b){this.$stage.empty(),this._items=[],b&&(b=b instanceof jQuery?b:a(b)),this.settings.nestedItemSelector&&(b=b.find("."+this.settings.nestedItemSelector)),b.filter(function(){return 1===this.nodeType}).each(a.proxy(function(a,b){b=this.prepare(b),this.$stage.append(b),this._items.push(b),this._mergers.push(1*b.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)},this)),this.reset(this.isNumeric(this.settings.startPosition)?this.settings.startPosition:0),this.invalidate("items")},e.prototype.add=function(b,c){var e=this.relative(this._current);c=c===d?this._items.length:this.normalize(c,!0),b=b instanceof jQuery?b:a(b),this.trigger("add",{content:b,position:c}),b=this.prepare(b),0===this._items.length||c===this._items.length?(0===this._items.length&&this.$stage.append(b),0!==this._items.length&&this._items[c-1].after(b),this._items.push(b),this._mergers.push(1*b.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)):(this._items[c].before(b),this._items.splice(c,0,b),this._mergers.splice(c,0,1*b.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)),this._items[e]&&this.reset(this._items[e].index()),this.invalidate("items"),this.trigger("added",{content:b,position:c})},e.prototype.remove=function(a){(a=this.normalize(a,!0))!==d&&(this.trigger("remove",{content:this._items[a],position:a}),this._items[a].remove(),this._items.splice(a,1),this._mergers.splice(a,1),this.invalidate("items"),this.trigger("removed",{content:null,position:a}))},e.prototype.preloadAutoWidthImages=function(b){b.each(a.proxy(function(b,c){this.enter("pre-loading"),c=a(c),a(new Image).one("load",a.proxy(function(a){c.attr("src",a.target.src),c.css("opacity",1),this.leave("pre-loading"),!this.is("pre-loading")&&!this.is("initializing")&&this.refresh()},this)).attr("src",c.attr("src")||c.attr("data-src")||c.attr("data-src-retina"))},this))},e.prototype.destroy=function(){this.$element.off(".owl.core"),this.$stage.off(".owl.core"),a(c).off(".owl.core"),!1!==this.settings.responsive&&(b.clearTimeout(this.resizeTimer),this.off(b,"resize",this._handlers.onThrottledResize));for(var d in this._plugins)this._plugins[d].destroy();this.$stage.children(".cloned").remove(),this.$stage.unwrap(),this.$stage.children().contents().unwrap(),this.$stage.children().unwrap(),this.$stage.remove(),this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class",this.$element.attr("class").replace(new RegExp(this.options.responsiveClass+"-\\S+\\s","g"),"")).removeData("owl.carousel")},e.prototype.op=function(a,b,c){var d=this.settings.rtl;switch(b){case"<":return d?a>c:a<c;case">":return d?a<c:a>c;case">=":return d?a<=c:a>=c;case"<=":return d?a>=c:a<=c}},e.prototype.on=function(a,b,c,d){a.addEventListener?a.addEventListener(b,c,d):a.attachEvent&&a.attachEvent("on"+b,c)},e.prototype.off=function(a,b,c,d){a.removeEventListener?a.removeEventListener(b,c,d):a.detachEvent&&a.detachEvent("on"+b,c)},e.prototype.trigger=function(b,c,d,f,g){var h={item:{count:this._items.length,index:this.current()}},i=a.camelCase(a.grep(["on",b,d],function(a){return a}).join("-").toLowerCase()),j=a.Event([b,"owl",d||"carousel"].join(".").toLowerCase(),a.extend({relatedTarget:this},h,c));return this._supress[b]||(a.each(this._plugins,function(a,b){b.onTrigger&&b.onTrigger(j)}),this.register({type:e.Type.Event,name:b}),this.$element.trigger(j),this.settings&&"function"==typeof this.settings[i]&&this.settings[i].call(this,j)),j},e.prototype.enter=function(b){a.each([b].concat(this._states.tags[b]||[]),a.proxy(function(a,b){this._states.current[b]===d&&(this._states.current[b]=0),this._states.current[b]++},this))},e.prototype.leave=function(b){a.each([b].concat(this._states.tags[b]||[]),a.proxy(function(a,b){this._states.current[b]--},this))},e.prototype.register=function(b){if(b.type===e.Type.Event){if(a.event.special[b.name]||(a.event.special[b.name]={}),!a.event.special[b.name].owl){var c=a.event.special[b.name]._default;a.event.special[b.name]._default=function(a){return!c||!c.apply||a.namespace&&-1!==a.namespace.indexOf("owl")?a.namespace&&a.namespace.indexOf("owl")>-1:c.apply(this,arguments)},a.event.special[b.name].owl=!0}}else b.type===e.Type.State&&(this._states.tags[b.name]?this._states.tags[b.name]=this._states.tags[b.name].concat(b.tags):this._states.tags[b.name]=b.tags,this._states.tags[b.name]=a.grep(this._states.tags[b.name],a.proxy(function(c,d){return a.inArray(c,this._states.tags[b.name])===d},this)))},e.prototype.suppress=function(b){a.each(b,a.proxy(function(a,b){this._supress[b]=!0},this))},e.prototype.release=function(b){a.each(b,a.proxy(function(a,b){delete this._supress[b]},this))},e.prototype.pointer=function(a){var c={x:null,y:null};return a=a.originalEvent||a||b.event,a=a.touches&&a.touches.length?a.touches[0]:a.changedTouches&&a.changedTouches.length?a.changedTouches[0]:a,a.pageX?(c.x=a.pageX,c.y=a.pageY):(c.x=a.clientX,c.y=a.clientY),c},e.prototype.isNumeric=function(a){return!isNaN(parseFloat(a))},e.prototype.difference=function(a,b){return{x:a.x-b.x,y:a.y-b.y}},a.fn.owlCarousel=function(b){var c=Array.prototype.slice.call(arguments,1);return this.each(function(){var d=a(this),f=d.data("owl.carousel");f||(f=new e(this,"object"==typeof b&&b),d.data("owl.carousel",f),a.each(["next","prev","to","destroy","refresh","replace","add","remove"],function(b,c){f.register({type:e.Type.Event,name:c}),f.$element.on(c+".owl.carousel.core",a.proxy(function(a){a.namespace&&a.relatedTarget!==this&&(this.suppress([c]),f[c].apply(this,[].slice.call(arguments,1)),this.release([c]))},f))})),"string"==typeof b&&"_"!==b.charAt(0)&&f[b].apply(f,c)})},a.fn.owlCarousel.Constructor=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._interval=null,this._visible=null,this._handlers={"initialized.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoRefresh&&this.watch()},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers)};e.Defaults={autoRefresh:!0,autoRefreshInterval:500},e.prototype.watch=function(){this._interval||(this._visible=this._core.isVisible(),this._interval=b.setInterval(a.proxy(this.refresh,this),this._core.settings.autoRefreshInterval))},e.prototype.refresh=function(){this._core.isVisible()!==this._visible&&(this._visible=!this._visible,this._core.$element.toggleClass("owl-hidden",!this._visible),this._visible&&this._core.invalidate("width")&&this._core.refresh())},e.prototype.destroy=function(){var a,c;b.clearInterval(this._interval);for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(c in Object.getOwnPropertyNames(this))"function"!=typeof this[c]&&(this[c]=null)},a.fn.owlCarousel.Constructor.Plugins.AutoRefresh=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._loaded=[],this._handlers={"initialized.owl.carousel change.owl.carousel resized.owl.carousel":a.proxy(function(b){if(b.namespace&&this._core.settings&&this._core.settings.lazyLoad&&(b.property&&"position"==b.property.name||"initialized"==b.type)){var c=this._core.settings,e=c.center&&Math.ceil(c.items/2)||c.items,f=c.center&&-1*e||0,g=(b.property&&b.property.value!==d?b.property.value:this._core.current())+f,h=this._core.clones().length,i=a.proxy(function(a,b){this.load(b)},this);for(c.lazyLoadEager>0&&(e+=c.lazyLoadEager,c.loop&&(g-=c.lazyLoadEager,e++));f++<e;)this.load(h/2+this._core.relative(g)),h&&a.each(this._core.clones(this._core.relative(g)),i),g++}},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers)};e.Defaults={lazyLoad:!1,lazyLoadEager:0},e.prototype.load=function(c){var d=this._core.$stage.children().eq(c),e=d&&d.find(".owl-lazy");!e||a.inArray(d.get(0),this._loaded)>-1||(e.each(a.proxy(function(c,d){var e,f=a(d),g=b.devicePixelRatio>1&&f.attr("data-src-retina")||f.attr("data-src")||f.attr("data-srcset");this._core.trigger("load",{element:f,url:g},"lazy"),f.is("img")?f.one("load.owl.lazy",a.proxy(function(){f.css("opacity",1),this._core.trigger("loaded",{element:f,url:g},"lazy")},this)).attr("src",g):f.is("source")?f.one("load.owl.lazy",a.proxy(function(){this._core.trigger("loaded",{element:f,url:g},"lazy")},this)).attr("srcset",g):(e=new Image,e.onload=a.proxy(function(){f.css({"background-image":'url("'+g+'")',opacity:"1"}),this._core.trigger("loaded",{element:f,url:g},"lazy")},this),e.src=g)},this)),this._loaded.push(d.get(0)))},e.prototype.destroy=function(){var a,b;for(a in this.handlers)this._core.$element.off(a,this.handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Lazy=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(c){this._core=c,this._previousHeight=null,this._handlers={"initialized.owl.carousel refreshed.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoHeight&&this.update()},this),"changed.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoHeight&&"position"===a.property.name&&this.update()},this),"loaded.owl.lazy":a.proxy(function(a){a.namespace&&this._core.settings.autoHeight&&a.element.closest("."+this._core.settings.itemClass).index()===this._core.current()&&this.update()},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers),this._intervalId=null;var d=this;a(b).on("load",function(){d._core.settings.autoHeight&&d.update()}),a(b).resize(function(){d._core.settings.autoHeight&&(null!=d._intervalId&&clearTimeout(d._intervalId),d._intervalId=setTimeout(function(){d.update()},250))})};e.Defaults={autoHeight:!1,autoHeightClass:"owl-height"},e.prototype.update=function(){var b=this._core._current,c=b+this._core.settings.items,d=this._core.settings.lazyLoad,e=this._core.$stage.children().toArray().slice(b,c),f=[],g=0;a.each(e,function(b,c){f.push(a(c).height())}),g=Math.max.apply(null,f),g<=1&&d&&this._previousHeight&&(g=this._previousHeight),this._previousHeight=g,this._core.$stage.parent().height(g).addClass(this._core.settings.autoHeightClass)},e.prototype.destroy=function(){var a,b;for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.AutoHeight=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._videos={},this._playing=null,this._handlers={"initialized.owl.carousel":a.proxy(function(a){a.namespace&&this._core.register({type:"state",name:"playing",tags:["interacting"]})},this),"resize.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.video&&this.isInFullScreen()&&a.preventDefault()},this),"refreshed.owl.carousel":a.proxy(function(a){a.namespace&&this._core.is("resizing")&&this._core.$stage.find(".cloned .owl-video-frame").remove()},this),"changed.owl.carousel":a.proxy(function(a){a.namespace&&"position"===a.property.name&&this._playing&&this.stop()},this),"prepared.owl.carousel":a.proxy(function(b){if(b.namespace){var c=a(b.content).find(".owl-video");c.length&&(c.css("display","none"),this.fetch(c,a(b.content)))}},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers),this._core.$element.on("click.owl.video",".owl-video-play-icon",a.proxy(function(a){this.play(a)},this))};e.Defaults={video:!1,videoHeight:!1,videoWidth:!1},e.prototype.fetch=function(a,b){var c=function(){return a.attr("data-vimeo-id")?"vimeo":a.attr("data-vzaar-id")?"vzaar":"youtube"}(),d=a.attr("data-vimeo-id")||a.attr("data-youtube-id")||a.attr("data-vzaar-id"),e=a.attr("data-width")||this._core.settings.videoWidth,f=a.attr("data-height")||this._core.settings.videoHeight,g=a.attr("href");if(!g)throw new Error("Missing video URL.");if(d=g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com|be\-nocookie\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/),d[3].indexOf("youtu")>-1)c="youtube";else if(d[3].indexOf("vimeo")>-1)c="vimeo";else{if(!(d[3].indexOf("vzaar")>-1))throw new Error("Video URL not supported.");c="vzaar"}d=d[6],this._videos[g]={type:c,id:d,width:e,height:f},b.attr("data-video",g),this.thumbnail(a,this._videos[g])},e.prototype.thumbnail=function(b,c){var d,e,f,g=c.width&&c.height?"width:"+c.width+"px;height:"+c.height+"px;":"",h=b.find("img"),i="src",j="",k=this._core.settings,l=function(c){e='<div class="owl-video-play-icon"></div>',d=k.lazyLoad?a("<div/>",{class:"owl-video-tn "+j,srcType:c}):a("<div/>",{class:"owl-video-tn",style:"opacity:1;background-image:url("+c+")"}),b.after(d),b.after(e)};if(b.wrap(a("<div/>",{class:"owl-video-wrapper",style:g})),this._core.settings.lazyLoad&&(i="data-src",j="owl-lazy"),h.length)return l(h.attr(i)),h.remove(),!1;"youtube"===c.type?(f="//img.youtube.com/vi/"+c.id+"/hqdefault.jpg",l(f)):"vimeo"===c.type?a.ajax({type:"GET",url:"//vimeo.com/api/v2/video/"+c.id+".json",jsonp:"callback",dataType:"jsonp",success:function(a){f=a[0].thumbnail_large,l(f)}}):"vzaar"===c.type&&a.ajax({type:"GET",url:"//vzaar.com/api/videos/"+c.id+".json",jsonp:"callback",dataType:"jsonp",success:function(a){f=a.framegrab_url,l(f)}})},e.prototype.stop=function(){this._core.trigger("stop",null,"video"),this._playing.find(".owl-video-frame").remove(),this._playing.removeClass("owl-video-playing"),this._playing=null,this._core.leave("playing"),this._core.trigger("stopped",null,"video")},e.prototype.play=function(b){var c,d=a(b.target),e=d.closest("."+this._core.settings.itemClass),f=this._videos[e.attr("data-video")],g=f.width||"100%",h=f.height||this._core.$stage.height();this._playing||(this._core.enter("playing"),this._core.trigger("play",null,"video"),e=this._core.items(this._core.relative(e.index())),this._core.reset(e.index()),c=a('<iframe frameborder="0" allowfullscreen mozallowfullscreen webkitAllowFullScreen ></iframe>'),c.attr("height",h),c.attr("width",g),"youtube"===f.type?c.attr("src","//www.youtube.com/embed/"+f.id+"?autoplay=1&rel=0&v="+f.id):"vimeo"===f.type?c.attr("src","//player.vimeo.com/video/"+f.id+"?autoplay=1"):"vzaar"===f.type&&c.attr("src","//view.vzaar.com/"+f.id+"/player?autoplay=true"),a(c).wrap('<div class="owl-video-frame" />').insertAfter(e.find(".owl-video")),this._playing=e.addClass("owl-video-playing"))},e.prototype.isInFullScreen=function(){var b=c.fullscreenElement||c.mozFullScreenElement||c.webkitFullscreenElement;return b&&a(b).parent().hasClass("owl-video-frame")},e.prototype.destroy=function(){var a,b;this._core.$element.off("click.owl.video");for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Video=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this.core=b,this.core.options=a.extend({},e.Defaults,this.core.options),this.swapping=!0,this.previous=d,this.next=d,this.handlers={"change.owl.carousel":a.proxy(function(a){a.namespace&&"position"==a.property.name&&(this.previous=this.core.current(),this.next=a.property.value)},this),"drag.owl.carousel dragged.owl.carousel translated.owl.carousel":a.proxy(function(a){a.namespace&&(this.swapping="translated"==a.type)},this),"translate.owl.carousel":a.proxy(function(a){a.namespace&&this.swapping&&(this.core.options.animateOut||this.core.options.animateIn)&&this.swap()},this)},this.core.$element.on(this.handlers)};e.Defaults={animateOut:!1,
animateIn:!1},e.prototype.swap=function(){if(1===this.core.settings.items&&a.support.animation&&a.support.transition){this.core.speed(0);var b,c=a.proxy(this.clear,this),d=this.core.$stage.children().eq(this.previous),e=this.core.$stage.children().eq(this.next),f=this.core.settings.animateIn,g=this.core.settings.animateOut;this.core.current()!==this.previous&&(g&&(b=this.core.coordinates(this.previous)-this.core.coordinates(this.next),d.one(a.support.animation.end,c).css({left:b+"px"}).addClass("animated owl-animated-out").addClass(g)),f&&e.one(a.support.animation.end,c).addClass("animated owl-animated-in").addClass(f))}},e.prototype.clear=function(b){a(b.target).css({left:""}).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut),this.core.onTransitionEnd()},e.prototype.destroy=function(){var a,b;for(a in this.handlers)this.core.$element.off(a,this.handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Animate=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._call=null,this._time=0,this._timeout=0,this._paused=!0,this._handlers={"changed.owl.carousel":a.proxy(function(a){a.namespace&&"settings"===a.property.name?this._core.settings.autoplay?this.play():this.stop():a.namespace&&"position"===a.property.name&&this._paused&&(this._time=0)},this),"initialized.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoplay&&this.play()},this),"play.owl.autoplay":a.proxy(function(a,b,c){a.namespace&&this.play(b,c)},this),"stop.owl.autoplay":a.proxy(function(a){a.namespace&&this.stop()},this),"mouseover.owl.autoplay":a.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"mouseleave.owl.autoplay":a.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.play()},this),"touchstart.owl.core":a.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"touchend.owl.core":a.proxy(function(){this._core.settings.autoplayHoverPause&&this.play()},this)},this._core.$element.on(this._handlers),this._core.options=a.extend({},e.Defaults,this._core.options)};e.Defaults={autoplay:!1,autoplayTimeout:5e3,autoplayHoverPause:!1,autoplaySpeed:!1},e.prototype._next=function(d){this._call=b.setTimeout(a.proxy(this._next,this,d),this._timeout*(Math.round(this.read()/this._timeout)+1)-this.read()),this._core.is("interacting")||c.hidden||this._core.next(d||this._core.settings.autoplaySpeed)},e.prototype.read=function(){return(new Date).getTime()-this._time},e.prototype.play=function(c,d){var e;this._core.is("rotating")||this._core.enter("rotating"),c=c||this._core.settings.autoplayTimeout,e=Math.min(this._time%(this._timeout||c),c),this._paused?(this._time=this.read(),this._paused=!1):b.clearTimeout(this._call),this._time+=this.read()%c-e,this._timeout=c,this._call=b.setTimeout(a.proxy(this._next,this,d),c-e)},e.prototype.stop=function(){this._core.is("rotating")&&(this._time=0,this._paused=!0,b.clearTimeout(this._call),this._core.leave("rotating"))},e.prototype.pause=function(){this._core.is("rotating")&&!this._paused&&(this._time=this.read(),this._paused=!0,b.clearTimeout(this._call))},e.prototype.destroy=function(){var a,b;this.stop();for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.autoplay=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){"use strict";var e=function(b){this._core=b,this._initialized=!1,this._pages=[],this._controls={},this._templates=[],this.$element=this._core.$element,this._overrides={next:this._core.next,prev:this._core.prev,to:this._core.to},this._handlers={"prepared.owl.carousel":a.proxy(function(b){b.namespace&&this._core.settings.dotsData&&this._templates.push('<div class="'+this._core.settings.dotClass+'">'+a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot")+"</div>")},this),"added.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.dotsData&&this._templates.splice(a.position,0,this._templates.pop())},this),"remove.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.dotsData&&this._templates.splice(a.position,1)},this),"changed.owl.carousel":a.proxy(function(a){a.namespace&&"position"==a.property.name&&this.draw()},this),"initialized.owl.carousel":a.proxy(function(a){a.namespace&&!this._initialized&&(this._core.trigger("initialize",null,"navigation"),this.initialize(),this.update(),this.draw(),this._initialized=!0,this._core.trigger("initialized",null,"navigation"))},this),"refreshed.owl.carousel":a.proxy(function(a){a.namespace&&this._initialized&&(this._core.trigger("refresh",null,"navigation"),this.update(),this.draw(),this._core.trigger("refreshed",null,"navigation"))},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this.$element.on(this._handlers)};e.Defaults={nav:!1,navText:['<span aria-label="Previous">&#x2039;</span>','<span aria-label="Next">&#x203a;</span>'],navSpeed:!1,navElement:'button type="button" role="presentation"',navContainer:!1,navContainerClass:"owl-nav",navClass:["owl-prev","owl-next"],slideBy:1,dotClass:"owl-dot",dotsClass:"owl-dots",dots:!0,dotsEach:!1,dotsData:!1,dotsSpeed:!1,dotsContainer:!1},e.prototype.initialize=function(){var b,c=this._core.settings;this._controls.$relative=(c.navContainer?a(c.navContainer):a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"),this._controls.$previous=a("<"+c.navElement+">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click",a.proxy(function(a){this.prev(c.navSpeed)},this)),this._controls.$next=a("<"+c.navElement+">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click",a.proxy(function(a){this.next(c.navSpeed)},this)),c.dotsData||(this._templates=[a('<button role="button">').addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]),this._controls.$absolute=(c.dotsContainer?a(c.dotsContainer):a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"),this._controls.$absolute.on("click","button",a.proxy(function(b){var d=a(b.target).parent().is(this._controls.$absolute)?a(b.target).index():a(b.target).parent().index();b.preventDefault(),this.to(d,c.dotsSpeed)},this));for(b in this._overrides)this._core[b]=a.proxy(this[b],this)},e.prototype.destroy=function(){var a,b,c,d,e;e=this._core.settings;for(a in this._handlers)this.$element.off(a,this._handlers[a]);for(b in this._controls)"$relative"===b&&e.navContainer?this._controls[b].html(""):this._controls[b].remove();for(d in this.overides)this._core[d]=this._overrides[d];for(c in Object.getOwnPropertyNames(this))"function"!=typeof this[c]&&(this[c]=null)},e.prototype.update=function(){var a,b,c,d=this._core.clones().length/2,e=d+this._core.items().length,f=this._core.maximum(!0),g=this._core.settings,h=g.center||g.autoWidth||g.dotsData?1:g.dotsEach||g.items;if("page"!==g.slideBy&&(g.slideBy=Math.min(g.slideBy,g.items)),g.dots||"page"==g.slideBy)for(this._pages=[],a=d,b=0,c=0;a<e;a++){if(b>=h||0===b){if(this._pages.push({start:Math.min(f,a-d),end:a-d+h-1}),Math.min(f,a-d)===f)break;b=0,++c}b+=this._core.mergers(this._core.relative(a))}},e.prototype.draw=function(){var b,c=this._core.settings,d=this._core.items().length<=c.items,e=this._core.relative(this._core.current()),f=c.loop||c.rewind;this._controls.$relative.toggleClass("disabled",!c.nav||d),c.nav&&(this._controls.$previous.toggleClass("disabled",!f&&e<=this._core.minimum(!0)),this._controls.$next.toggleClass("disabled",!f&&e>=this._core.maximum(!0))),this._controls.$absolute.toggleClass("disabled",!c.dots||d),c.dots&&(b=this._pages.length-this._controls.$absolute.children().length,c.dotsData&&0!==b?this._controls.$absolute.html(this._templates.join("")):b>0?this._controls.$absolute.append(new Array(b+1).join(this._templates[0])):b<0&&this._controls.$absolute.children().slice(b).remove(),this._controls.$absolute.find(".active").removeClass("active"),this._controls.$absolute.children().eq(a.inArray(this.current(),this._pages)).addClass("active"))},e.prototype.onTrigger=function(b){var c=this._core.settings;b.page={index:a.inArray(this.current(),this._pages),count:this._pages.length,size:c&&(c.center||c.autoWidth||c.dotsData?1:c.dotsEach||c.items)}},e.prototype.current=function(){var b=this._core.relative(this._core.current());return a.grep(this._pages,a.proxy(function(a,c){return a.start<=b&&a.end>=b},this)).pop()},e.prototype.getPosition=function(b){var c,d,e=this._core.settings;return"page"==e.slideBy?(c=a.inArray(this.current(),this._pages),d=this._pages.length,b?++c:--c,c=this._pages[(c%d+d)%d].start):(c=this._core.relative(this._core.current()),d=this._core.items().length,b?c+=e.slideBy:c-=e.slideBy),c},e.prototype.next=function(b){a.proxy(this._overrides.to,this._core)(this.getPosition(!0),b)},e.prototype.prev=function(b){a.proxy(this._overrides.to,this._core)(this.getPosition(!1),b)},e.prototype.to=function(b,c,d){var e;!d&&this._pages.length?(e=this._pages.length,a.proxy(this._overrides.to,this._core)(this._pages[(b%e+e)%e].start,c)):a.proxy(this._overrides.to,this._core)(b,c)},a.fn.owlCarousel.Constructor.Plugins.Navigation=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){"use strict";var e=function(c){this._core=c,this._hashes={},this.$element=this._core.$element,this._handlers={"initialized.owl.carousel":a.proxy(function(c){c.namespace&&"URLHash"===this._core.settings.startPosition&&a(b).trigger("hashchange.owl.navigation")},this),"prepared.owl.carousel":a.proxy(function(b){if(b.namespace){var c=a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");if(!c)return;this._hashes[c]=b.content}},this),"changed.owl.carousel":a.proxy(function(c){if(c.namespace&&"position"===c.property.name){var d=this._core.items(this._core.relative(this._core.current())),e=a.map(this._hashes,function(a,b){return a===d?b:null}).join();if(!e||b.location.hash.slice(1)===e)return;b.location.hash=e}},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this.$element.on(this._handlers),a(b).on("hashchange.owl.navigation",a.proxy(function(a){var c=b.location.hash.substring(1),e=this._core.$stage.children(),f=this._hashes[c]&&e.index(this._hashes[c]);f!==d&&f!==this._core.current()&&this._core.to(this._core.relative(f),!1,!0)},this))};e.Defaults={URLhashListener:!1},e.prototype.destroy=function(){var c,d;a(b).off("hashchange.owl.navigation");for(c in this._handlers)this._core.$element.off(c,this._handlers[c]);for(d in Object.getOwnPropertyNames(this))"function"!=typeof this[d]&&(this[d]=null)},a.fn.owlCarousel.Constructor.Plugins.Hash=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){function e(b,c){var e=!1,f=b.charAt(0).toUpperCase()+b.slice(1);return a.each((b+" "+h.join(f+" ")+f).split(" "),function(a,b){if(g[b]!==d)return e=!c||b,!1}),e}function f(a){return e(a,!0)}var g=a("<support>").get(0).style,h="Webkit Moz O ms".split(" "),i={transition:{end:{WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",transition:"transitionend"}},animation:{end:{WebkitAnimation:"webkitAnimationEnd",MozAnimation:"animationend",OAnimation:"oAnimationEnd",animation:"animationend"}}},j={csstransforms:function(){return!!e("transform")},csstransforms3d:function(){return!!e("perspective")},csstransitions:function(){return!!e("transition")},cssanimations:function(){return!!e("animation")}};j.csstransitions()&&(a.support.transition=new String(f("transition")),a.support.transition.end=i.transition.end[a.support.transition]),j.cssanimations()&&(a.support.animation=new String(f("animation")),a.support.animation.end=i.animation.end[a.support.animation]),j.csstransforms()&&(a.support.transform=new String(f("transform")),a.support.transform3d=j.csstransforms3d())}(window.Zepto||window.jQuery,window,document);