
$(function() {


    $('.hamburger-icon').click(function(e){
        e.preventDefault();
        $('.main-nav-menu-mobil').slideToggle(50);
        $('#nav-icon').toggleClass('open');
    })


    $('.dropdown-item').click(function(e){
        e.preventDefault();
        let selected = $(this).text();
        $('.search-btn-label').text(selected);
        $("form[data-form=search] input[name=filter]").val($(this).data('filterValue'));

    });

    let bk = $('.home-search').data('bk');
    $('.home-search').css('background-image', 'url('+bk+')');

    $('.search-clear').click(function(e){
        e.preventDefault();
        $("form[data-form=search] input[name=keywords]").val();
        $("form[data-form=search] input[name=keywords]").focus();
        $(this).hide();
    });

    $("form[data-form=search] input[name=keywords]").one('keypress', function(){
        $('.search-clear').show();
    });

    if($("form[data-form=search] input[name=keywords]").val()){
        $('.search-clear').show();
    }

    $("form[data-form=search] input[name=keywords]").focus();

    $('.add-to-lightbox').click(function(e){
        e.preventDefault();
        window.lightbox.run($(this).data('asset'));
    });

    $('[data-tooltip]').tooltip();

    $('.btn-remove-asset').click(function(e){
        e.preventDefault();
        $('#modal-asset-id').val($(this).data('asset'));
        $('#remove-asset-modal').modal('show');
    });

    $('.btn-edit-lightbox').click(function(e){
        e.preventDefault();
        $('#modal-lightbox-id').val($(this).data('lightbox'))
        $('#modal-lightbox-name').val($(this).data('lightboxName')).focus();
        $('#rename-lightbox-modal').modal('show');
    });

    /*
     * Initialize the grid view switcher component
     */

    $(".switch-view a").click(function(e) {
        e.preventDefault();

        $(".grid-view").addClass("d-none");

        $(".switch-view a").removeClass("active");

        $(this).addClass("active");

        switch($(this).data("gridView")) {
            case "regular":
                $(".grid-view-regular").removeClass("d-none");

                break;

            case "masonry":
                $(".grid-view-masonry").removeClass("d-none");

                var Masonry = require('masonry-layout');

                new Masonry('.grid', {
                    itemSelector: '.grid-item'
                });

                break;
        };

        if (typeof(Storage) !== "undefined") {
            localStorage.setItem("selectedGridView", $(this).data("gridView"))
        }
    });

    var selectedGridView = "regular";

    if (typeof(Storage) !== "undefined") {
        selectedGridView = localStorage.getItem("selectedGridView") || "regular";
    }

    $(".switch-view a[data-grid-view='" + selectedGridView + "']").trigger("click");
});