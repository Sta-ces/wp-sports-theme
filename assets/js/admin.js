jQuery(function($){
    // on upload button click
    $('.staces-upl').on( 'click', function(e){
        e.preventDefault();
        let button = $(this),
        custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                type : ['image','application/pdf']
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false
        }).on('select', function() { // it also has "open" and "close" events
            let attachment = custom_uploader.state().get('selection').first().toJSON();
            const regexp = new RegExp("(\.pdf)$");
            let url = (regexp.test(attachment.url)) ? admin.includes_media + "/document.png" : attachment.url;
            button.html('<img src="' + url + '" width="100" height="auto">').next().show().next().val(attachment.id);
        }).open();
    });

    // on remove button click
    $('.staces-rmv').on('click', function(e){
        e.preventDefault();
        let button = $(this);
        button.next().val(''); // emptying the hidden field
        button.hide().prev().html('Upload image');
    });
});