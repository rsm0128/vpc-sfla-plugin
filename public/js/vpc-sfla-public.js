(function( $ ) {
	'use strict';
        $(document).ready(function () {

            $("#vpc-container").append("<div class='loader'><img src='" + myPluginVars.pluginUrl +"loader.gif'></div>");
            
            var popup = new $.Popup();
            $(document).on('click', '#vpc-save-btn', function () {
                
                    popup.open('<div class="modal-header">'+
                                    '<div class="wpc_heade line">'+vpc.modal_title+' </div>'+
                                '</div>'+
                                '<div class="modal-body">'+
                                    '<div class="error"></div>'+
                                    '<div class="line">'+
                                        '<div><input id="config_name" type="text" value="" placeholder="'+vpc.placeholder_name+'"/></div>'+
                                     '</div>'+
                                     '<div class="line">'+
                                        '<div class="btn">'+
                                           '<button id="save_configuration" class="vpc-sfla-button">'+vpc.send_label+'</button>'+
                                        '</div>', 'html', $('a.popup'));
            });
            
            $(document).on('click', '#save_configuration', function () {
                popup.close();
                $('.loader').show('slow');
                   
                var pid=$('#vpc-save-btn').data('pid');
                var config_name=$('#config_name').val();
                var recap = $('#vpc-container').find(':input').serializeJSON();
                if(!vpc.log){
                    $.post(
                        ajax_object.ajax_url,
                        {
                            action: "save_in_cookies",
                            pid:pid,
                            recap:recap,
                            config_name:config_name
                        },
                        function(data){
                            $('.loader').hide(); 
                             window.location=vpc.login_page;
                        }
                    );
                }
                else{
                    $.post(
                        ajax_object.ajax_url,
                        {
                            action: "save_for_later",
                            pid:pid,
                            recap:recap,
                            config_name:config_name
                        },
                        function(id){
                            $('.loader').hide();

                            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?edit_config=' + id;
                            var newHtml = '<div class="saved_bloc"><a class="save_later" href="' + newUrl +'">' + config_name + '</a><span id="delete_saved" data-id="' + id +'">x</span></div>';
                            $('.saved_panel div').eq(1).append(newHtml);

                            $('#debug').html('<div class="vpc-success f-right">'+vpc.success_msg+'</div>').show().delay(1000).fadeOut(1000);
                            // location.reload();
                        }
                    );
                }
            });
            
             $(document).on('click', '#delete_saved', function () {
                $('.loader').show('slow');
                var id=$(this).data('id');
                var obj = $(this);
                $.post(
                    ajax_object.ajax_url,
                    {
                        action: "delete_config",
                        id:id
                    },
                    function(data){
                        $('.loader').hide();
                        obj.parent().remove();

                        $('#debug').html('<div class="vpc-success f-right">'+vpc.delete_msg+'</div>').show().delay(1000).fadeOut(1000);
                        // location.reload();
                    }
                );
             });
            
        });

})( jQuery );
