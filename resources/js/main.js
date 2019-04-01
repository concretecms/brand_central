
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


});