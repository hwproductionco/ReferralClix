
// bootstrap tooltips
$('[rel="tooltip"]').tooltip();

// collapsible submenus setup
animatedcollapse.addDiv('profile-submenu', 'fade=1, group=submenus,'),
animatedcollapse.addDiv('messages-submenu', 'fade=1, group=submenus, persist=1'),
animatedcollapse.addDiv('billing-submenu', 'fade=1, group=submenus'),
animatedcollapse.addDiv('support-submenu', 'fade=1, group=submenus')
animatedcollapse.ontoggle=function($, divobj, state){
}
animatedcollapse.init();

(function($) {
    $.fn.goTo = function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top + 'px'
        }, 'fast');
        return this; // for chaining...
    }
})(jQuery);