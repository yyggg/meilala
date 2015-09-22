

	function share() {
        if($("#share_box").css("display")!="block"){
        var html=   '<div class="share_box bxz" id="share_box" onclick="share_cancel()">'+
                        '<div class="share  bxz">'+
                            '点击右上角分享给朋友'
                        '</div>'+
                    '</div>'
        $("body").append(html);
        }
    }
    function share_cancel () {
        $("#share_box").remove();       
    }

    function empty_num(){

        

            window.location.href="share.php";
                   
    }