function comment(obj){
    
    var content = $(obj).parent().find('textarea').val();
    var tarPid = $(obj).attr('pid');
    var thisUrl = $('#usercomment').attr('url');
    var thisAid = $('#usercomment').attr('aid');
    
    
    //点击评论，先删除下面的回复框
    $('#reply-textarea').remove();

    $.ajax({  
                type:"post",  
                url:thisUrl,  
                data:{ "aid":thisAid,"content":content,"pid":tarPid },//这里data传递过去的是序列化以后的字符串  
                success:function(mes){
                    //直接评论，就是评论本文，pid为0，最顶级  
                    var response = JSON.parse(mes);     //回应对象

                    if(response.cmtid == 0){
                        alert('评论错误，请正确评论');
                    }else{
                        //判断是回复文章本身，还是回复用户reply
                        if(tarPid == 0){
                            //回复文章本身
                            $('#usercomment').append("<div id='comment-"+response.cmtid+"\' class='row b-user b-parent'><div class='col-xs-2 col-sm-1 col-md-1 col-lg-1 b-pic-col'><span class='glyphicon glyphicon-user'></span></div><div class='col-xs-10 col-sm-11 col-md-11 col-lg-11 b-content-col b-cc-first' id='reply-"+response.cmtid+"\'><p class='b-content'><span class='b-user-name'></span>"+response.ouip+"回复："+response.content+"</p><p class='b-date'>"+response.date+" <a href='javascript:;' aid=\'"+response.aid+"\' pid=\'"+response.cmtid+"\' username=\'"+response.ouid+"\' onclick='reply(this)'>回复</a></p><div class='b-clear-float'></div></div></div>");
                            $('#usercomment').append("<div class='row'><div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><div class='b-border'></div></div></div>");             
                        
                        }else{
                            //回复用户
                            
                            $('#reply-'+tarPid).append("<div id='comment-"+tarPid+"\' class='row b-user b-parent'><div class='col-xs-2 col-sm-1 col-md-1 col-lg-1 b-pic-col'><span class='glyphicon glyphicon-user'></span></div><div class='col-xs-10 col-sm-11 col-md-11 col-lg-11 b-content-col b-cc-first' id='reply-"+tarPid+"\'><p class='b-content'><span class='b-user-name'></span>"+response.ouip+"回复："+response.content+"</p><p class='b-date'>"+response.date+" <a href='javascript:;' aid=\'"+response.aid+"\' pid=\'"+tarPid+"\' username=\'"+response.ouid+"\' onclick='reply(this)'>回复</a></p><div class='b-clear-float'></div></div></div>");
                            $('#reply-'+tarPid).append("<div class='row'><div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><div class='b-border'></div></div></div>");                
                            
                        }
                    }
                    
                    
                    
                }  
            });
    
}

//对别人的评论做回复
function reply(obj){
    
    var aid = $(obj).attr('aid');
    var pid = $(obj).attr('pid');
    var username = $(obj).attr('username');

    //点击回复后，先增加回复框
    //先删除原来的回复框，再添加新的
    $('#reply-textarea').remove();
    $(obj).parent().parent().append("<div class='b-box-textarea' id='reply-textarea'><textarea class='form-control' rows='3'></textarea><input type='button' value='评 论' aid=\'"+aid+"\' pid=\'"+pid+"\' onclick='comment(this)'></div>");


}