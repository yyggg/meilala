

	function share() {
        var html=   '<div class="share_box bxz" id="share_box" onclick="share_cancel()">'+
                        '<div class="share  bxz">'+
                            '点击右上角分享给朋友'
                        '</div>'+
                    '</div>'
        $("body").append(html);
    }
    function share_cancel () {
        $("#share_box").remove();       
    }